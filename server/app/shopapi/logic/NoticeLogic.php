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

namespace app\shopapi\logic;

use app\common\enum\DistributionConfigEnum;
use app\common\enum\NoticeEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\Config;
use app\common\model\DistributionConfig;
use app\common\model\Notice;
use app\common\service\ConfigService;
use app\common\service\FileService;

/**
 * 通知逻辑
 * Class NoticeLogic
 * @package app\shopapi\logic
 */
class NoticeLogic extends BaseLogic
{
    /**
     * @notes 消息通知
     * @param $userId
     * @return array
     * @author Tab
     * @date 2021/8/25 10:26
     */
    public static function index($userId)
    {
        // 最新系统消息
        $system = Notice::where('scene_id', '<>', NoticeEnum::EARNINGS_NOTICE)
            ->where(['user_id' => $userId, 'send_type' => NoticeEnum::SYSTEM])
            ->order('id desc')
            ->findOrEmpty()->toArray();

        $system_notice_icon = ConfigService::get('default_image', 'system_notice_icon');
        $system_notice_icon = FileService::getFileUrl($system_notice_icon);
        $data['system'] = [
            'title' => '系统通知',
            'content' => $system['content'] ?? '暂无系统消息',
            'img' => $system_notice_icon,
            'type' => 'system',
        ];

        // 最新收益通知
        $dbConfig = DistributionConfig::column('value', 'key');
        $switch = $dbConfig['switch'] ?? DistributionConfigEnum::DEFAULT_SWITCH;
        if ($switch == 1) {
            $earnings = Notice::where('scene_id', '=',NoticeEnum::EARNINGS_NOTICE)
                ->where(['user_id' => $userId, 'send_type' => NoticeEnum::SYSTEM])
                ->order('id desc')
                ->findOrEmpty()->toArray();
        }

        if ($switch == 1) {
            $earnings_notice_icon = ConfigService::get('default_image', 'earnings_notice_icon');
            $earnings_notice_icon = FileService::getFileUrl($earnings_notice_icon);
            $data['earnings'] = [
                'title' => '收益通知',
                'content' => $earnings['content'] ?? '暂无收益消息',
                'img' => $earnings_notice_icon,
                'type' => 'earnings',
            ];
        }

        return array_values($data);
    }

    public static function unread($userId)
    {
        $lists = Notice::field('id,title,content,scene_id,extra,create_time')
            ->where([
                ['user_id', '=', $userId],
                ['send_type', '=', NoticeEnum::SYSTEM],
                ['read', '=', YesNoEnum::NO],
            ])
            ->order('id', 'desc')
            ->limit(20)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['extra'] = json_decode($item['extra'], true) ?: [];
            $item['scene_desc'] = NoticeEnum::getSceneDesc($item['scene_id']);
            $item['navigate'] = self::getNavigateByScene($item['scene_id'], $item['extra']);
        }

        $count = Notice::where([
            ['user_id', '=', $userId],
            ['send_type', '=', NoticeEnum::SYSTEM],
            ['read', '=', YesNoEnum::NO],
        ])->count();

        return [
            'lists' => $lists,
            'count' => $count,
        ];
    }

    public static function read($params, $userId)
    {
        try {
            if (isset($params['id']) && $params['id']) {
                $notice = Notice::where([
                    ['id', '=', $params['id']],
                    ['user_id', '=', $userId],
                ])->findOrEmpty();
                if ($notice->isEmpty()) {
                    throw new \Exception('通知不存在');
                }
                $notice->read = YesNoEnum::YES;
                $notice->save();
            } else {
                Notice::where([
                    ['user_id', '=', $userId],
                    ['send_type', '=', NoticeEnum::SYSTEM],
                    ['read', '=', YesNoEnum::NO],
                ])->update(['read' => YesNoEnum::YES]);
            }
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function getNavigateByScene($sceneId, $extra)
    {
        $navigates = [
            NoticeEnum::ORDER_PAY_NOTICE => ['page' => '/pages/order_detail/order_detail', 'key' => 'order_id'],
            NoticeEnum::ORDER_SHIP_NOTICE => ['page' => '/pages/order_detail/order_detail', 'key' => 'order_id'],
            NoticeEnum::REFUND_REFUSE_NOTICE => ['page' => '/bundle/pages/after_sale_detail/after_sale_detail', 'key' => 'after_sale_id'],
            NoticeEnum::REFUND_SUCCESS_NOTICE => ['page' => '/bundle/pages/after_sale_detail/after_sale_detail', 'key' => 'after_sale_id'],
            NoticeEnum::EARNINGS_NOTICE => ['page' => '/bundle/pages/commission_details/commission_details', 'key' => ''],
            NoticeEnum::RECEIPT_PAYMENT_NOTICE => ['page' => '/bundle/pages/withdraw_record/withdraw_record', 'key' => ''],
            NoticeEnum::INTEGRAL_NOTICE => ['page' => '/pages/order_detail/order_detail', 'key' => 'order_id'],
            NoticeEnum::WITHDRAW_CONFIRM_NOTICE => ['page' => '/bundle/pages/withdraw_success/withdraw_success', 'key' => 'withdraw_id', 'param' => 'id'],
        ];

        $nav = $navigates[$sceneId] ?? null;
        if (!$nav) {
            return '';
        }

        $url = $nav['page'];
        $paramName = $nav['param'] ?? $nav['key'];
        if ($nav['key'] && !empty($extra[$nav['key']])) {
            $url .= '?' . $paramName . '=' . $extra[$nav['key']];
        }
        return $url;
    }
}