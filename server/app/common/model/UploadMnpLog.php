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

use think\model\concern\SoftDelete;

class UploadMnpLog extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


    /**
     * @notes 操作人
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/4/9 下午4:19
     */
    public function getAdminNameAttr($value, $data)
    {
        $name = Admin::where(['id'=>$data['admin_id']])->value('name');
        return $name;
    }

    /**
     * @notes 状态
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2025/4/9 下午4:22
     */
    public function getStatusDescAttr($value, $data)
    {
        $status = ['上传中','上传成功','上传失败'];
        return $status[$data['status']];
    }
}