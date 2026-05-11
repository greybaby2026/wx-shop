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

namespace app\common\model;


use app\common\enum\PayEnum;
use app\common\service\FileService;

class PayConfig extends BaseModel
{
    protected $name = 'dev_pay';

    // 设置json类型字段
    // protected $json = ['config'];

    // 设置JSON数据返回数组
    // protected $jsonAssoc = true;
    
    
    function setConfigAttr($config)
    {
        return json_encode($config, JSON_UNESCAPED_UNICODE);
    }
    
    function getConfigAttr($config, $data)
    {
        $config = (array) json_decode($config, true);
        
        switch ($data['pay_way']) {
            case PayEnum::WECHAT_PAY:
                $config['interface_version']    = $config['interface_version'] ?? '';
                $config['merchant_type']        = $config['merchant_type'] ?? '';
                $config['mch_id']               = $config['mch_id'] ?? '';
                $config['pay_sign_key']         = $config['pay_sign_key'] ?? '';
                $config['apiclient_cert']       = $config['apiclient_cert'] ?? '';
                $config['apiclient_key']        = $config['apiclient_key'] ?? '';
                $config['wechat_public_serial'] = $config['wechat_public_serial'] ?? '';
                $config['wechat_public_cert']   = $config['wechat_public_cert'] ?? '';
                break;
            case PayEnum::ALI_PAY:
                $config['mode']                 = $config['mode'] ?? 'normal_mode';
                $config['merchant_type']        = $config['merchant_type'] ?? 'ordinary_merchant';
                $config['app_id']               = $config['app_id'] ?? '';
                $config['private_key']          = $config['private_key'] ?? '';
                $config['ali_public_key']       = $config['ali_public_key'] ?? '';
                break;
        }
        
        return $config;
    }
    
    /**
     * @notes 支付图标获取器 - 路径添加域名
     * @param $value
     * @return string
     * @author ljj
     * @date 2021/7/28 2:12 下午
     */
    public function getIconAttr($value)
    {
        return empty($value) ? '' : FileService::getFileUrl($value);
    }

    /**
     * @notes 支付方式名称获取器
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2021/7/31 2:24 下午
     */
    public function getPayWayNameAttr($value,$data)
    {
        return PayEnum::getPayDesc($data['pay_way']);
    }
}