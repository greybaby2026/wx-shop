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

use app\common\enum\LuckyDrawEnum;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

class LuckyDraw extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    /**
     * @notes 获取状态描述
     * @param $value
     * @param $data
     * @return string|string[]
     * @author Tab
     * @date 2021/11/24 14:16
     */
    public function getStatusDescAttr($value, $data)
    {
        return LuckyDrawEnum::getStatusDesc($data['status']);
    }

    /**
     * @notes 获取参与人次
     * @param $value
     * @param $data
     * @return int
     * @author Tab
     * @date 2021/11/24 15:42
     */
    public function getJoinNumAttr($value, $data)
    {
       return LuckyDrawRecord::where('activity_id', $data['id'])->count();
    }

    /**
     * @notes 获取中奖人次
     * @param $value
     * @param $data
     * @return int
     * @author Tab
     * @date 2021/11/24 15:43
     */
    public function getWinNumAttr($value, $data)
    {
        return LuckyDrawRecord::where([
            ['activity_id', '=' , $data['id']],
            ['prize_type', '<>' , LuckyDrawEnum::NOT_WIN],
        ])->count();
    }

    /**
     * @notes 获取开始时间描述
     * @param $value
     * @param $data
     * @author Tab
     * @date 2021/11/24 15:43
     */
    public function getStartTimeDescAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['start_time']);
    }

    /**
     * @notes 获取结束时间描述
     * @param $value
     * @param $data
     * @author Tab
     * @date 2021/11/24 15:43
     */
    public function getEndTimeDescAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['end_time']);
    }

    /**
     * @notes 设置顶部图片
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function setTopImageAttr($value, $data)
    {
        return FileService::setFileUrl($value);
    }

    /**
     * @notes 获取顶部图片
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function getTopImageAttr($value, $data)
    {
        return FileService::getFileUrl($value);
    }

    /**
     * @notes 设置开始按钮图片
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function setStartButtonImageAttr($value, $data)
    {
        return FileService::setFileUrl($value);
    }

    /**
     * @notes 获取开始按钮图片
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function getStartButtonImageAttr($value, $data)
    {
        return FileService::getFileUrl($value);
    }

    /**
     * @notes 设置奖品底图
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function setPrizeBaseImageAttr($value, $data)
    {
        return FileService::setFileUrl($value);
    }

    /**
     * @notes 获取奖品底图
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function getPrizeBaseImageAttr($value, $data)
    {
        return FileService::getFileUrl($value);
    }

    /**
     * @notes 设置容器图片
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function setContainerImageAttr($value, $data)
    {
        return FileService::setFileUrl($value);
    }

    /**
     * @notes 获取容器图片
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function getContainerImageAttr($value, $data)
    {
        return FileService::getFileUrl($value);
    }

    /**
     * @notes 设置背景图片
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function setBackgroundImageAttr($value, $data)
    {
        return FileService::setFileUrl($value);
    }

    /**
     * @notes 获取背景图片
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function getBackgroundImageAttr($value, $data)
    {
        return FileService::getFileUrl($value);
    }

    /**
     * @notes 设置分享封面
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function setShareImageAttr($value, $data)
    {
        if (empty($value)) {
            return $value;
        }
        return FileService::setFileUrl($value);
    }

    /**
     * @notes 获取分享封面
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2025/7/24 上午11:20
     */
    public function getShareImageAttr($value, $data)
    {
        if (empty($value)) {
            return $value;
        }
        return FileService::getFileUrl($value);
    }
}