<?php
/**
 * 门店结算配置管理工具（Q3）
 * ------------------------------------------------------------------
 * 用途：门店结算比例与开关的查看/设置 + 表结构迁移（幂等）
 * 说明：本项目缓存驱动为 Redis，直接改库不会让 ConfigService 缓存失效，
 *       故本脚本通过框架 ConfigService 读写，保证缓存与库一致。
 *
 * 用法（在 server 目录下执行）：
 *   php migrations/store_settlement_config.php                 # 查看当前配置
 *   php migrations/store_settlement_config.php migrate         # 表结构迁移(幂等)
 *   php migrations/store_settlement_config.php mode 1          # 结算模式 1=按比例 0=按商品协商价
 *   php migrations/store_settlement_config.php ratio 70        # 全局比例(%)，仅取门店未单独设置时生效
 *   php migrations/store_settlement_config.php shop-ratio 2 65 # 指定门店(id=2)的比例(%)，0=跟随全局
 *   php migrations/store_settlement_config.php shops          # 列出全部门店及其比例
 *
 * ⚠️ 必须用 PHP 8.0 或更低版本执行：
 *   ThinkPHP 6.0 的 think\Paginator 未声明 ArrayAccess 的返回类型，PHP 8.1+ 会
 *   产生弃用告警，而 ThinkPHP 的错误处理器会把它升级为 ErrorException，
 *   导致 CLI 下 App::initialize() 直接失败。PHP 8.0 无此告警，可正常启动。
 *   本机命令：/www/server/php/80/bin/php migrations/store_settlement_config.php <命令>
 *
 * 缓存说明：本项目 config/cache.php 的 default 硬编码为 file（.env 中的
 *   [CACHE] DRIVER=redis 实际未生效），缓存在 runtime/cache/08/。
 *   ⚠️ 该目录属主是 www（Web 运行用户）。若本脚本以 root 运行并触发缓存写入，
 *      会生成 root 属主的缓存文件，导致 Web（www）之后无法覆盖它 —— 表现为
 *      该缓存相关的请求报 500（例如后台 token 临近过期触发续期时）。
 *   → 因此本脚本【只删缓存、不写缓存】：读配置直接查库，写配置走 ConfigService::set
 *     （set 内部仅 Cache::delete，不产生新缓存文件）。可安全以 root 执行。
 *   建议优先用 www 执行：sudo -u www /www/server/php/80/bin/php migrations/store_settlement_config.php
 */

// ---------- 引导框架 ----------
// 说明：ThinkPHP 的错误处理器会把 PHP 8.2 对 ArrayAccess 返回类型的弃用告警
//       升级为 ErrorException，导致 CLI 下 initialize() 失败；此处先行屏蔽弃用类告警。
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require __DIR__ . '/../vendor/autoload.php';

$rootPath = dirname(__DIR__);
$app = new \think\App($rootPath);
$app->initialize();

use app\common\model\Config;
use app\common\model\SelffetchShop;
use app\common\model\StoreSettlementOrder;
use app\common\service\ConfigService;
use think\facade\Db;

const CFG_TYPE = 'store_settlement';
const CFG_MODE = 'mode';      // 1=按「成交价×比例」 0=按商品协商价(cost_price)
const CFG_RATIO = 'ratio';    // 全局比例(%)

// ---------- 辅助 ----------
/**
 * 直读配置表：刻意不用 ConfigService::get()，因为它会把值写入文件缓存，
 * 若脚本以 root 运行会产生 root 属主缓存文件，导致 Web(www) 之后无法覆盖
 * 该缓存从而报 500（见文件头"缓存说明"）。
 */
function cfgValue(string $name, $default = null)
{
    $v = Config::where(['type' => CFG_TYPE, 'name' => $name])->value('value');
    return is_null($v) ? $default : $v;
}

function showConfig(): void
{
    $mode  = cfgValue(CFG_MODE, 1);
    $ratio = cfgValue(CFG_RATIO, 0);
    echo "当前结算配置（ls_config type=store_settlement，直读数据库）\n";
    echo "  结算模式 mode  = " . var_export($mode, true) . "   (" . (intval($mode) === 1 ? '按成交价×比例' : '按商品协商价') . ")\n";
    echo "  全局比例 ratio = " . var_export($ratio, true) . " %\n";
}

function showShops(): void
{
    $rows = SelffetchShop::field('id,name,contact,settlement_ratio,status')->order('id')->select()->toArray();
    echo "门店结算比例（ls_selffetch_shop.settlement_ratio）\n";
    printf("  %-4s %-24s %-10s %-10s %s\n", 'id', '门店', '店长', '结算比例', '状态');
    foreach ($rows as $r) {
        printf("  %-4s %-24s %-10s %-10s %s\n",
            $r['id'], $r['name'], $r['contact'],
            floatval($r['settlement_ratio']) > 0 ? $r['settlement_ratio'] . ' %（独立）' : '0（跟随全局）',
            $r['status'] == 1 ? '启用' : '停用');
    }
}

function migrate(): void
{
    // 表名从模型取，自动带上项目前缀
    $shopTable  = (new SelffetchShop())->getTable();
    $orderTable = (new StoreSettlementOrder())->getTable();
    echo "表结构迁移（幂等）\n";

    // 1) 门店比例
    $col = Db::query("SHOW COLUMNS FROM `{$shopTable}` LIKE 'settlement_ratio'");
    if (!$col) {
        Db::execute("ALTER TABLE `{$shopTable}`
            ADD COLUMN `settlement_ratio` decimal(5,2) NOT NULL DEFAULT 0 COMMENT '结算比例(%)，0=跟随全局' AFTER `remark`");
        echo "  [新增] {$shopTable}.settlement_ratio\n";
    } else {
        echo "  [已存在] {$shopTable}.settlement_ratio\n";
    }

    // 2) 结算明细：结算基数与当时比例（base_price 须先于 ratio，因 ratio 的 AFTER 依赖它）
    foreach ([
        'base_price' => "ADD COLUMN `base_price` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '结算基数(成交价总额)' AFTER `amount`",
        'ratio'      => "ADD COLUMN `ratio` decimal(5,2) NOT NULL DEFAULT 0 COMMENT '结算比例(%)' AFTER `base_price`",
    ] as $name => $ddl) {
        $col = Db::query("SHOW COLUMNS FROM `{$orderTable}` LIKE '{$name}'");
        if (!$col) {
            Db::execute("ALTER TABLE `{$orderTable}` {$ddl}");
            echo "  [新增] {$orderTable}.{$name}\n";
        } else {
            echo "  [已存在] {$orderTable}.{$name}\n";
        }
    }

    echo "  迁移完成\n\n";
    showConfig();
}

// ---------- 参数解析 ----------
$argv = $_SERVER['argv'] ?? [];
$cmd  = $argv[1] ?? '';

try {
    switch ($cmd) {
        case 'migrate':
            migrate();
            break;

        case 'mode':
            $v = intval($argv[2] ?? 1);
            ConfigService::set(CFG_TYPE, CFG_MODE, $v);
            echo "已设置 结算模式 mode = {$v}（" . ($v === 1 ? '按成交价×比例' : '按商品协商价') . "）\n\n";
            showConfig();
            break;

        case 'ratio':
            $v = floatval($argv[2] ?? 0);
            if ($v < 0 || $v > 100) {
                echo "[错误] 比例必须在 0~100 之间\n";
                exit(1);
            }
            ConfigService::set(CFG_TYPE, CFG_RATIO, $v);
            echo "已设置 全局比例 ratio = {$v}%\n\n";
            showConfig();
            break;

        case 'shop-ratio':
            $sid = intval($argv[2] ?? 0);
            $v   = floatval($argv[3] ?? 0);
            $shop = SelffetchShop::findOrEmpty($sid);
            if ($shop->isEmpty()) {
                echo "[错误] 门店 id={$sid} 不存在\n";
                exit(1);
            }
            if ($v < 0 || $v > 100) {
                echo "[错误] 比例必须在 0~100 之间\n";
                exit(1);
            }
            $shop->settlement_ratio = $v;
            $shop->save();
            echo "已设置 门店[{$sid} {$shop->name}] settlement_ratio = {$v}%" . ($v > 0 ? '（独立比例）' : '（跟随全局）') . "\n\n";
            showShops();
            break;

        case 'shops':
            showShops();
            break;

        default:
            showConfig();
            echo "\n";
            showShops();
            echo "\n可用命令：migrate | mode <1|0> | ratio <0-100> | shop-ratio <门店id> <0-100> | shops\n";
            break;
    }
} catch (\Throwable $e) {
    echo "[异常] " . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
    exit(1);
}
