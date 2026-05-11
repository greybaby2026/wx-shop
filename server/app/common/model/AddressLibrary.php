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

use app\common\service\RegionService;
use think\model\concern\SoftDelete;

class AddressLibrary extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


    /**
     * @notes 省名
     * @param $value
     * @param $data
     * @return mixed|string
     * @author ljj
     * @date 2024/9/9 下午2:49
     */
    public function getProvinceAttr($value, $data)
    {
        return RegionService::getAddress($data['province_id']);
    }

    /**
     * @notes 市名
     * @param $value
     * @param $data
     * @return mixed|string
     * @author ljj
     * @date 2024/9/9 下午2:49
     */
    public function getCityAttr($value, $data)
    {
        return RegionService::getAddress($data['city_id']);
    }

    /**
     * @notes 区名
     * @param $value
     * @param $data
     * @return mixed|string
     * @author ljj
     * @date 2024/9/9 下午2:49
     */
    public function getDistrictAttr($value, $data)
    {
        return RegionService::getAddress($data['district_id']);
    }

    /**
     * @notes 完整地址
     * @param $value
     * @param $data
     * @return mixed|string
     * @author ljj
     * @date 2024/9/11 下午4:23
     */
    public function getCompleteAddressAttr($value, $data)
    {
        return RegionService::getAddress(
            [
                $data['province_id'] ?? '',
                $data['city_id'] ?? '',
                $data['district_id'] ?? ''
            ],
            $data['address'] ?? '',
        );
    }
}