<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

declare(strict_types=1);

namespace app\common\service;

use app\common\model\Config;
use think\facade\Cache;

class ConfigService
{

    /**
     * @notes
     * @param string $type
     * @param string $name
     * @return string
     * @author 令狐冲
     * @date 2022/10/19 16:13
     */
    public static function getCacheKey($type, $name): string
    {
        return 'config' . '-' . $type . '-' . $name;
    }

    /**
     * @notes 设置配置值
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @author Tab
     * @date 2021/7/15 14:54
     */
    public static function set($type, $name, $value)
    {
        //刷新缓存
        $cacheKey = self::getCacheKey($type, $name);
        Cache::delete($cacheKey);

        $original = $value;
        // 数组数据进行json编码
        if (is_array($value)) {
            // JSON_UNESCAPED_UNICODE 不对中文进行unicode编码
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        $data = Config::where(['type' => $type, 'name' => $name])->findOrEmpty();
        if ($data->isEmpty()) {
            // 没有则添加
            Config::create([
                'type' => $type,
                'name' => $name,
                'value' => $value,
            ]);
        } else {
            // 有则修改
            $data->value = $value;
            $data->save();
        }
        // 返回原始值
        return $original;
    }

    /**
     * @notes
     * @param $type
     * @param string $name
     * @param null $defaultValue
     * @return mixed
     * @author 令狐冲
     * @date 2022/10/19 16:16
     */
    public static function get($type, $name = '', $defaultValue = null)
    {
        //获取缓存
        $CacheKey = self::getCacheKey($type, $name);
        $result = Cache::get($CacheKey);
        $value = $result['config_server'] ?? null;
        if ($value !== null) {
            return $value;
        }

        //单项配置
        if ($name) {
            $result = Config::where(['type' => $type, 'name' => $name])->value('value');
            //json类型解析
            if (!is_null($result)) {
                $json = json_decode($result, true);
                $result = json_last_error() === JSON_ERROR_NONE ? $json : $result;
            }
            //获取默认配置
            if ($result === null) {
                $result = $defaultValue;
            }
            //获取本地配置文件配置
            if ($result === null) {
                $result = config('project.' . $type . '.' . $name);
            }
            Cache::set($CacheKey, ['config_server' => $result]);
            return $result;
        }

        //多项配置
        $result = Config::where(['type' => $type])->column('value', 'name');
        if (is_array($result)) {
            foreach ($result as $k => $v) {
                $json = json_decode($v, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $result[$k] = $json;
                }
            }
        }
        //读取默认配置
        if ($result === null) {
            $result = $defaultValue;
        }
        Cache::set($CacheKey, ['config_server' => $result]);
        return $result;
    }

}