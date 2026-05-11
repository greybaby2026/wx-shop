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

namespace app\adminapi\logic\wechat;

use app\common\logic\BaseLogic;
use app\common\model\UploadMnpLog;
use app\common\service\ConfigService;
use app\common\service\FileService;
use think\facade\Db;

/**
 * 微信小程序设置逻辑层
 * Class MiniProgramSettingLogic
 * @package app\adminapi\logic\wechat
 */
class MiniProgramSettingLogic extends BaseLogic
{
    public static function getConfig()
    {
        $domainName = $_SERVER['HTTP_HOST'];
        $qrCode = ConfigService::get('mini_program', 'qr_code', '');
        $qrCode = empty($qrCode) ? $qrCode : FileService::getFileUrl($qrCode);
        $config = [
            'name'                  => ConfigService::get('mini_program', 'name', ''),
            'original_id'           => ConfigService::get('mini_program', 'original_id', ''),
            'qr_code'               => $qrCode,
            'app_id'                => ConfigService::get('mini_program', 'app_id', ''),
            'app_secret'            => ConfigService::get('mini_program', 'app_secret', ''),
            'request_domain'        => 'https://'.$domainName,
            'socket_domain'         => 'wss://'.$domainName,
            'upload_file_domain'    => 'https://'.$domainName,
            'download_file_domain'  => 'https://'.$domainName,
            'udp_domain'            => 'udp://'.$domainName,
            'tcp_domain'            => 'tcp://'.$domainName,
            'business_domain'       => $domainName,
            // 小程序同步发货开关 1开启 0关闭
            'express_send_sync'     => ConfigService::get('mini_program', 'express_send_sync', 1),
            'private_key'           => ConfigService::get('mini_program', 'private_key', ''),
        ];

        return $config;
    }

    /**
     * @notes 微信小程序设置
     * @param $params
     * @author Tab
     * @date 2021/7/28 19:01
     */
    public static function setConfig($params)
    {
        $qrCode = isset($params['qr_code']) ? FileService::setFileUrl($params['qr_code']) : '';

        ConfigService::set('mini_program','name', $params['name'] ?? '');
        ConfigService::set('mini_program','original_id',$params['original_id'] ?? '');
        ConfigService::set('mini_program','qr_code',$qrCode);
        ConfigService::set('mini_program','app_id',$params['app_id']);
        ConfigService::set('mini_program','app_secret',$params['app_secret']);
    
        ConfigService::set('mini_program','express_send_sync', $params['express_send_sync'] ?? 1);

        if (isset($params['private_key']) && !empty($params['private_key'])) {
            $saveDir = '../extend/miniprogram-ci/';
            if (!file_exists($saveDir)) {
                mkdir($saveDir, 0775, true);
            }
            //保存文件
            $savePath = $saveDir.'private.'.$params['app_id'].'.key';
            $f = fopen($savePath, 'w');
            fwrite($f, $params['private_key']);
            fclose($f);

            ConfigService::set('mini_program','private_key',$params['private_key']);
        }
    }


    /**
     * @notes 上传小程序代码
     * @param $params
     * @return bool
     * @author ljj
     * @date 2025/4/9 下午3:09
     */
    public function uploadMnp($params)
    {
        Db::startTrans();
        try {
            //校验是否已安装miniprogram-ci工具
            if (!file_exists('../extend/miniprogram-ci/node_modules/miniprogram-ci')) {
                throw new \think\Exception('请先安装miniprogram-ci工具');
            }

            $baseUrl = 'mp-weixin/common/vendor.js';
            $exampleUrl = '../runtime/miniprogram-ci/vendor_example.js';

            //检查vendor.js是否已替换域名，未替换域名说明是新版本首次提交
            $baseUrlData = file_get_contents($baseUrl);
            if (strpos($baseUrlData, "[baseUrl]") !== false) {
                //复制一份vendor.js
                if (!file_exists('../runtime/miniprogram-ci')) {
                    mkdir('../runtime/miniprogram-ci', 0775, true);
                }
                copy($baseUrl, $exampleUrl);
            }
            if(file_exists($exampleUrl)){
                $baseUrlData = file_get_contents($exampleUrl);
            }

            //更换小程序域名
            $domain = request()->domain(true).'/';
            $baseUrlData = str_replace("[baseUrl]",$domain,$baseUrlData);
            $f = fopen($baseUrl,"w");
            fwrite($f,$baseUrlData);
            fclose($f);

            //插入上传日志
            $uploadMnpLog = UploadMnpLog::create([
                'admin_id' => $params['admin_id'],
                'version' => config('project.version'),
            ]);

            //上传小程序代码
            $data = [
                'version' => config('project.version'),
                'desc' => $params['upload_desc'] ?? '',
                'appid' => ConfigService::get('mini_program', 'app_id', ''),
            ];
            $json_data = json_encode($data);
            $command = 'node ../extend/miniprogram-ci/upload.js '.escapeshellarg($json_data).' 2>&1';
            $output=null;
            $retval = null;
            exec($command, $output, $retval);

            //获取错误信息
            $errors = preg_grep('/\[error\]/', is_array($output) ? $output : [$output]);
            if ($retval || !empty($errors)) {
                //错误
                UploadMnpLog::update([
                    'status' => 2,
                    'fail_reason' => is_array($output) ? json_encode($output) : $output,
                ],['id'=>$uploadMnpLog->id]);
            } else {
                //成功
                UploadMnpLog::update([
                    'status' => 1,
                ],['id'=>$uploadMnpLog->id]);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }
}