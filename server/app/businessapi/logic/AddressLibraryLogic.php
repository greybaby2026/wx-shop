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

namespace app\businessapi\logic;

use app\common\logic\BaseLogic;
use app\common\model\AddressLibrary;

class AddressLibraryLogic extends BaseLogic
{
    /**
     * @notes 新增
     * @param $params
     * @return true
     * @author ljj
     * @date 2024/9/9 下午3:18
     */
    public function add($params)
    {
        AddressLibrary::create([
            'contact' => $params['contact'],
            'mobile' => $params['mobile'] ?? '',
            'phone_code' => $params['phone_code'] ?? '',
            'phone_number' => $params['phone_number'] ?? '',
            'phone_extension' => $params['phone_extension'] ?? '',
            'province_id' => $params['province_id'],
            'city_id' => $params['city_id'],
            'district_id' => $params['district_id'],
            'address' => $params['address'],
            'remarks' => $params['remarks'] ?? '',
        ]);
        return true;
    }

    /**
     * @notes 编辑
     * @param $params
     * @return true
     * @author ljj
     * @date 2024/9/9 下午3:29
     */
    public function edit($params)
    {
        AddressLibrary::update([
            'id' => $params['id'],
            'contact' => $params['contact'],
            'mobile' => $params['mobile'] ?? '',
            'phone_code' => $params['phone_code'] ?? '',
            'phone_number' => $params['phone_number'] ?? '',
            'phone_extension' => $params['phone_extension'] ?? '',
            'province_id' => $params['province_id'],
            'city_id' => $params['city_id'],
            'district_id' => $params['district_id'],
            'address' => $params['address'],
            'remarks' => $params['remarks'] ?? '',
        ]);
        return true;
    }

    /**
     * @notes 详情
     * @param $params
     * @return array
     * @author ljj
     * @date 2024/9/9 下午3:33
     */
    public function detail($params)
    {
        $result = AddressLibrary::where(['id'=>$params['id']])->append(['province','city','district'])->findOrEmpty()->toArray();
        return $result;
    }

    /**
     * @notes 删除
     * @param $params
     * @return bool
     * @author ljj
     * @date 2024/9/9 下午3:40
     */
    public function del($params)
    {
        return AddressLibrary::destroy($params['id']);
    }

    /**
     * @notes 设置默认地址
     * @param $params
     * @return true
     * @author ljj
     * @date 2024/9/9 下午3:52
     */
    public function default($params)
    {
        $AddressLibrary = AddressLibrary::findOrEmpty($params['id']);
        if ($params['default_type'] == 1) {
            if ($params['is_default']) {
                AddressLibrary::where(true)->update(['is_deliver_default' => 0]);
            }
            $AddressLibrary->is_deliver_default = $params['is_default'];
        } else {
            if ($params['is_default']) {
                AddressLibrary::where(true)->update(['is_return_default' => 0]);
            }
            $AddressLibrary->is_return_default = $params['is_default'];
        }
        $AddressLibrary->save();

        return true;
    }
}