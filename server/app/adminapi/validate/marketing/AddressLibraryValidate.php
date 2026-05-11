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

namespace app\adminapi\validate\marketing;


use app\common\model\AddressLibrary;
use app\common\validate\BaseValidate;

class AddressLibraryValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'contact' => 'require',
        'province_id' => 'require',
        'city_id' => 'require',
        'district_id' => 'require',
        'address' => 'require',
        'mobile' => 'requireWithout:phone_number|mobile',
        'phone_code' => 'requireWithout:mobile',
        'phone_number' => 'requireWithout:mobile',
        'phone_extension' => 'requireWithout:mobile',
        'default_type' => 'require|in:1,2',
        'is_default' => 'require|in:0,1',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'contact.require' => '请输入联系人',
        'province_id.require' => '请选择省',
        'city_id.require' => '请选择市',
        'district_id.require' => '请选择区',
        'address.require' => '请输入详细地址',
        'mobile.requireWithout' => '请输入手机号码',
        'mobile.mobile' => '手机号码错误',
        'phone_code.requireWithout' => '请输入区号',
        'phone_number.requireWithout' => '请输入电话',
        'phone_extension.requireWithout' => '请输入分机号码',
    ];

    public function sceneAdd()
    {
        return $this->only(['contact','province_id','city_id','district_id','address','mobile','phone_code','phone_number','phone_extension'])
            ->append('contact','checkAdd');
    }

    public function sceneEdit()
    {
        return $this->only(['id','contact','province_id','city_id','district_id','address','mobile','phone_code','phone_number','phone_extension']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneDel()
    {
        return $this->only(['id'])
            ->append('id','checkDel');
    }

    public function sceneDefault()
    {
        return $this->only(['id','default_type','is_default']);
    }

    /**
     * @notes 校验新增
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author ljj
     * @date 2024/9/9 下午3:37
     */
    public function checkAdd($value,$rule,$data)
    {
        $count = AddressLibrary::count();
        if($count >= 50){
            return '最多可添加50条地址';
        }
        return true;
    }

    /**
     * @notes 校验删除
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author ljj
     * @date 2024/9/9 下午3:37
     */
    public function checkDel($value,$rule,$data)
    {
        $result = AddressLibrary::where(['is_deliver_default|is_return_default'=>1,'id'=>$data['id']])->findOrEmpty();
        if(!$result->isEmpty()){
            return '不能删除默认发货、退货地址，请先取消默认选项';
        }
        return true;
    }
}