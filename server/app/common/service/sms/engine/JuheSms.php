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
namespace app\common\service\sms\engine;

/**
 * 聚合数据短信
 * Class JuheSms
 * @package app\common\service\sms\engine
 */
class JuheSms
{
    protected $error = null;
    protected $config;
    protected $mobile;
    protected $templateId;
    protected $templateParams;

    /**
     * @notes 架构方法
     * @param $config
     * @author Tab
     * @date 2021/8/19 17:46
     */
    public function __construct($config)
    {
        if(empty($config)) {
            $this->error = '请联系管理员配置参数';
            return false;
        }
        $this->config = $config;
    }

    /**
     * @notes 设置手机号
     * @param $mobile
     * @return $this
     * @author Tab
     * @date 2021/8/19 16:52
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * @notes 设置模板id
     * @param $templateId
     * @return $this
     * @author Tab
     * @date 2021/8/19 16:54
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }

    /**
     * @notes 设置模板参数
     * @param $templateParams
     * @return $this
     * @author Tab
     * @date 2021/8/19 16:56
     */
    public function setTemplateParams($templateParams)
    {
        // 聚合数据的模板参数格式是 #code#=1234
        $tplValue = '';
        if (!empty($templateParams)) {
            $params = [];
            foreach ($templateParams as $key => $value) {
                $params[] = "#{$key}#={$value}";
            }
            $tplValue = implode('&', $params);
        }
        $this->templateParams = $tplValue;
        return $this;
    }

    /**
     * @notes 获取错误信息
     * @return mixed
     * @author Tab
     * @date 2021/8/19 18:12
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @notes 发送短信
     * @return false|mixed
     * @author Tab
     * @date 2021/8/19 17:46
     */
    public function send()
    {
        try {
            $url = 'https://v.juhe.cn/sms/send';
            
            $params = [
                'key' => $this->config['app_key'],
                'mobile' => $this->mobile,
                'tpl_id' => $this->templateId,
                'tpl_value' => $this->templateParams,
            ];

            $response = $this->httpRequest($url, $params);
            
            if ($response === false) {
                throw new \Exception('聚合数据短信请求失败');
            }
            
            $result = json_decode($response, true);
            
            if (!isset($result['error_code'])) {
                throw new \Exception('聚合数据短信返回格式错误');
            }
            
            if ($result['error_code'] != 0) {
                $message = $result['reason'] ?? '未知错误';
                throw new \Exception('聚合数据短信错误：' . $message);
            }
            
            return $result;
        } catch(\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes HTTP请求
     * @param $url
     * @param $params
     * @param $method
     * @return bool|string
     */
    protected function httpRequest($url, $params, $method = 'GET')
    {
        $ch = curl_init();
        if ($method == 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 200) {
            return $response;
        }
        return false;
    }
}
