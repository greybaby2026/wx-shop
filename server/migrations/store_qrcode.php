<?php
/**
 * 门店专属小程序码 生成/查看工具
 * ------------------------------------------------------------------
 * 用途：为「自提门店」生成小程序码（scene = store_id=N），供门店张贴投放。
 *       用户扫码进入小程序后，App.vue 的 bindCode() 会自动解析 scene 并调用
 *       user/bindStore 完成「首绑定终身」的门店归属绑定。
 *
 * 背景：后台 selffetch_shop 模块本身有 qrCode() 接口，但**后台前端没有对应按钮**
 *       （admin 前端源码不在本工程内），故用本脚本代替该入口。
 *
 * 用法（在 server 目录下执行，⚠️ 必须用 www）：
 *   sudo -u www /www/server/php/80/bin/php migrations/store_qrcode.php            # 列出门店与已有码
 *   sudo -u www /www/server/php/80/bin/php migrations/store_qrcode.php gen        # 为全部启用门店生成
 *   sudo -u www /www/server/php/80/bin/php migrations/store_qrcode.php gen 2      # 只生成门店 2
 *
 * ⚠️ 为什么必须用 www：本脚本会读配置（ConfigService::get），而框架会把值写入
 *    文件缓存 runtime/cache/08/（属主 www）。若以 root 运行会生成 root 属主缓存文件，
 *    导致 Web(www) 之后无法覆盖该缓存 —— 表现为相关请求报 500。
 *    脚本已内置运行身份检查，非 www 会直接拒绝执行。
 *
 * 生成结果：public/uploads/qr_code/store/store_{id}.{png|jpg}
 *   微信 wxacode.getUnlimited 实际返回 JPEG（实测魔数 ffd8ffe0），
 *   故本脚本按魔数自动决定扩展名，避免"扩展名与内容不符"。
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// ---------- 运行身份检查（防止缓存属主污染）----------
$currentUser = function_exists('posix_getpwuid') ? (posix_getpwuid(posix_geteuid())['name'] ?? '') : '';
if ($currentUser !== 'www') {
    fwrite(STDERR, "[拒绝执行] 当前身份为 {$currentUser}，必须用 www 运行，否则会污染文件缓存属主。\n");
    fwrite(STDERR, "请改用：sudo -u www /www/server/php/80/bin/php migrations/store_qrcode.php\n");
    exit(1);
}

$ROOT = dirname(__DIR__);
require $ROOT . '/vendor/autoload.php';

$app = new \think\App($ROOT);
$app->initialize();

use app\common\model\SelffetchShop;
use app\common\service\WeChatService;

const QR_DIR_REL = 'uploads/qr_code/store/';   // 相对 public
const QR_PAGE    = 'pages/index/index';        // 与 App.vue 首页一致

// ---------- 辅助 ----------
function qrAbsDir(): string
{
    return dirname(__DIR__) . '/public/' . QR_DIR_REL;
}

/** 按魔数判定真实图片类型，返回扩展名 */
function imageExt(string $binary): string
{
    $magic = bin2hex(substr($binary, 0, 4));
    if ($magic === '89504e47') return 'png';
    if ($magic === 'ffd8ffe0' || $magic === 'ffd8ffe1' || $magic === 'ffd8ffdb') return 'jpg';
    if (substr($magic, 0, 6) === '474946') return 'gif';
    return 'bin';
}

/** 找出门店已有的码文件（store_{id}.*） */
function existingCodes(int $shopId): array
{
    $dir = qrAbsDir();
    if (!is_dir($dir)) return [];
    $files = glob($dir . 'store_' . $shopId . '.*') ?: [];
    $files = array_merge($files, glob($dir . 'store_' . $shopId . '_*.png') ?: []);
    return array_values(array_unique($files));
}

function listShops(): array
{
    return SelffetchShop::field('id,name,contact,status')->order('id asc')->select()->toArray();
}

function showShop(SelffetchShop|array $shop, int $index = -1): void
{
    $id = intval($shop['id']);
    printf("  门店 %-3d %-24s 店长=%-8s %s\n", $id, $shop['name'], $shop['contact'] ?: '-',
        intval($shop['status']) === 1 ? '启用' : '停用');
    $codes = existingCodes($id);
    if (!$codes) {
        echo "          小程序码：❌ 尚未生成\n";
    } else {
        foreach ($codes as $f) {
            printf("          小程序码：%s  (%d 字节)\n", basename($f), filesize($f));
            printf("          访问地址：https://jiangjunshijia.com/%s%s\n", QR_DIR_REL, basename($f));
        }
    }
}

/** 生成单个门店的码，返回 [成功?, 说明] */
function genShop(array $shop): array
{
    $id = intval($shop['id']);
    if (intval($shop['status']) !== 1) {
        return [false, '门店已停用，跳过'];
    }

    $ok = WeChatService::makeMpQrCode([
        'page'  => QR_PAGE,
        'scene' => 'store_id=' . $id,
    ], 'base64');

    if (!$ok) {
        return [false, '微信接口失败: ' . var_export(WeChatService::getReturnData(), true)];
    }

    $data = WeChatService::getReturnData();
    if (!is_string($data) || strpos($data, 'data:image/') !== 0) {
        return [false, '返回内容不是图片: ' . substr(var_export($data, true), 0, 120)];
    }
    $binary = base64_decode(substr($data, strpos($data, ',') + 1));
    if (!$binary) {
        return [false, 'base64 解码失败'];
    }

    $ext  = imageExt($binary);
    $dir  = qrAbsDir();
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        return [false, '目录创建失败: ' . $dir];
    }

    // 清理该门店的旧码（避免堆积）
    foreach (existingCodes($id) as $old) {
        @unlink($old);
    }

    $file = $dir . 'store_' . $id . '.' . $ext;
    if (!@file_put_contents($file, $binary)) {
        return [false, '写入失败: ' . $file];
    }

    return [true, sprintf('%s  (%d 字节, 实际格式 %s)  →  https://jiangjunshijia.com/%s%s',
        basename($file), strlen($binary), strtoupper($ext), QR_DIR_REL, basename($file))];
}

// ---------- 主流程 ----------
$cmd = $argv[1] ?? '';

try {
    if ($cmd === 'gen') {
        $targetId = intval($argv[2] ?? 0);
        $shops = listShops();
        echo "生成门店专属小程序码（scene = store_id=N）\n";
        echo str_repeat('-', 66) . "\n";
        $done = 0; $fail = 0;
        foreach ($shops as $shop) {
            if ($targetId > 0 && intval($shop['id']) !== $targetId) {
                continue;
            }
            printf("门店 %-3d %s ...\n", intval($shop['id']), $shop['name']);
            list($ok, $msg) = genShop($shop);
            if ($ok) {
                $done++;
                echo "    ✓ {$msg}\n";
            } else {
                $fail++;
                echo "    ✗ {$msg}\n";
            }
        }
        echo str_repeat('-', 66) . "\n";
        printf("完成：成功 %d 个，失败 %d 个\n", $done, $fail);
        if ($done > 0) {
            echo "\n投放建议：将图片打印为门店立牌/台卡，放在收银台等显眼位置；\n";
            echo "          用户扫码进入小程序即自动完成「首绑定终身」的门店归属绑定。\n";
        }
    } else {
        echo "门店专属小程序码\n";
        echo str_repeat('=', 66) . "\n";
        $shops = listShops();
        if (!$shops) {
            echo "  （无门店）\n";
        }
        foreach ($shops as $shop) {
            showShop($shop);
        }
        echo str_repeat('=', 66) . "\n";
        echo "可用命令：\n";
        echo "  gen       为全部启用门店生成小程序码\n";
        echo "  gen <id>  只为指定门店生成\n";
        echo "\n绑定链路：扫码 → App.vue 解析 scene(store_id) → user/bindStore → 写入 bind_store_id\n";
    }
} catch (\Throwable $e) {
    echo "[异常] " . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n";
    exit(1);
}
