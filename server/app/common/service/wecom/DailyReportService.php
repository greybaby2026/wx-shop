<?php

namespace app\common\service\wecom;

use app\common\model\KefuAiConfig;

class DailyReportService
{
    protected $webhookUrl = '';

    public function __construct()
    {
        $this->webhookUrl = KefuAiConfig::getConfigValue('wecom_report_webhook', '');
    }

    public function sendDailyReport($date = null)
    {
        if (empty($this->webhookUrl)) {
            return ['success' => false, 'message' => '未配置Webhook地址'];
        }

        if ($date === null) {
            $date = date('Y-m-d', strtotime('-1 day'));
        }

        $data = $this->gatherData($date);
        $markdown = $this->buildMarkdown($data, $date);

        return $this->sendWebhook($markdown);
    }

    protected function gatherData($date)
    {
        $startTime = strtotime($date);
        $endTime = $startTime + 86400;

        $prevStartTime = $startTime - 86400;
        $prevEndTime = $startTime;

        $orderCount = \think\facade\Db::table('ls_order')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('delete_time', null)
            ->count();

        $orderAmount = \think\facade\Db::table('ls_order')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('delete_time', null)
            ->where('order_status', '<>', 4)
            ->sum('order_amount');

        $paidOrderCount = \think\facade\Db::table('ls_order')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('delete_time', null)
            ->where('order_status', '<>', 0)
            ->where('order_status', '<>', 4)
            ->count();

        $paidOrderAmount = \think\facade\Db::table('ls_order')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('delete_time', null)
            ->where('order_status', '<>', 0)
            ->where('order_status', '<>', 4)
            ->sum('order_amount');

        $newUserCount = \think\facade\Db::table('ls_user')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('delete_time', null)
            ->count();

        $prevOrderCount = \think\facade\Db::table('ls_order')
            ->where('create_time', 'between', [$prevStartTime, $prevEndTime])
            ->where('delete_time', null)
            ->count();

        $prevOrderAmount = \think\facade\Db::table('ls_order')
            ->where('create_time', 'between', [$prevStartTime, $prevEndTime])
            ->where('delete_time', null)
            ->where('order_status', '<>', 4)
            ->sum('order_amount');

        $prevNewUserCount = \think\facade\Db::table('ls_user')
            ->where('create_time', 'between', [$prevStartTime, $prevEndTime])
            ->where('delete_time', null)
            ->count();

        $kefuTotal = \think\facade\Db::table('ls_kefu_conversation')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->count();

        $kefuAiCount = \think\facade\Db::table('ls_kefu_conversation')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('answer_type', 'ai')
            ->count();

        $kefuGreetingCount = \think\facade\Db::table('ls_kefu_conversation')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('answer_type', 'greeting')
            ->count();

        $kefuTransferCount = \think\facade\Db::table('ls_kefu_conversation')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('answer_type', 'transfer_human')
            ->count();

        $uniqueVisitors = \think\facade\Db::table('ls_kefu_conversation')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->group('external_userid')
            ->count();

        $faqLearned = \think\facade\Db::table('ls_kefu_faq')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->count();

        $avgConfidence = \think\facade\Db::table('ls_kefu_conversation')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('answer_type', 'ai')
            ->avg('confidence_score');

        $visitorCount = \think\facade\Db::table('ls_index_visit')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->count();

        $prevVisitorCount = \think\facade\Db::table('ls_index_visit')
            ->where('create_time', 'between', [$prevStartTime, $prevEndTime])
            ->count();

        $pendingShipCount = \think\facade\Db::table('ls_order')
            ->where('order_status', 1)
            ->where('delete_time', null)
            ->count();

        $refundCount = \think\facade\Db::table('ls_after_sale')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('delete_time', null)
            ->count();

        $refundAmount = \think\facade\Db::table('ls_after_sale')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('delete_time', null)
            ->where('refund_status', 1)
            ->sum('refund_total_amount');

        $prevRefundCount = \think\facade\Db::table('ls_after_sale')
            ->where('create_time', 'between', [$prevStartTime, $prevEndTime])
            ->where('delete_time', null)
            ->count();

        $rechargeAmount = \think\facade\Db::table('ls_recharge_order')
            ->where('create_time', 'between', [$startTime, $endTime])
            ->where('pay_status', 1)
            ->where('delete_time', null)
            ->sum('order_amount');

        $prevRechargeAmount = \think\facade\Db::table('ls_recharge_order')
            ->where('create_time', 'between', [$prevStartTime, $prevEndTime])
            ->where('pay_status', 1)
            ->where('delete_time', null)
            ->sum('order_amount');

        return [
            'order_count' => $orderCount,
            'order_amount' => round($orderAmount, 2),
            'paid_order_count' => $paidOrderCount,
            'paid_order_amount' => round($paidOrderAmount, 2),
            'new_user_count' => $newUserCount,
            'prev_order_count' => $prevOrderCount,
            'prev_order_amount' => round($prevOrderAmount, 2),
            'prev_new_user_count' => $prevNewUserCount,
            'visitor_count' => $visitorCount,
            'prev_visitor_count' => $prevVisitorCount,
            'pending_ship_count' => $pendingShipCount,
            'refund_count' => $refundCount,
            'refund_amount' => round($refundAmount, 2),
            'prev_refund_count' => $prevRefundCount,
            'recharge_amount' => round($rechargeAmount, 2),
            'prev_recharge_amount' => round($prevRechargeAmount, 2),
            'kefu_total' => $kefuTotal,
            'kefu_ai_count' => $kefuAiCount,
            'kefu_greeting_count' => $kefuGreetingCount,
            'kefu_transfer_count' => $kefuTransferCount,
            'unique_visitors' => $uniqueVisitors,
            'faq_learned' => $faqLearned,
            'avg_confidence' => round($avgConfidence, 1),
        ];
    }

    protected function buildMarkdown($data, $date)
    {
        $orderCountChange = $this->calcChange($data['order_count'], $data['prev_order_count']);
        $orderAmountChange = $this->calcChange($data['order_amount'], $data['prev_order_amount']);
        $newUserChange = $this->calcChange($data['new_user_count'], $data['prev_new_user_count']);
        $visitorChange = $this->calcChange($data['visitor_count'], $data['prev_visitor_count']);
        $refundChange = $this->calcChange($data['refund_count'], $data['prev_refund_count']);
        $rechargeChange = $this->calcChange($data['recharge_amount'], $data['prev_recharge_amount']);

        $aiRate = $data['kefu_total'] > 0
            ? round(($data['kefu_ai_count'] + $data['kefu_greeting_count']) / $data['kefu_total'] * 100, 1)
            : 0;
        $transferRate = $data['kefu_total'] > 0
            ? round($data['kefu_transfer_count'] / $data['kefu_total'] * 100, 1)
            : 0;

        $weekDay = ['日', '一', '二', '三', '四', '五', '六'][date('w', strtotime($date))];

        $md = "## 📊 将军世家日报 {$date} 周{$weekDay}\n\n";

        $md .= "### 🛒 经营数据\n";
        $md .= "> 访客数：**{$data['visitor_count']}** 人 {$visitorChange}\n";
        $md .= "> 下单数：**{$data['order_count']}** 笔 {$orderCountChange}\n";
        $md .= "> 下单金额：**¥{$data['order_amount']}** {$orderAmountChange}\n";
        $md .= "> 付款数：**{$data['paid_order_count']}** 笔\n";
        $md .= "> 付款金额：**¥{$data['paid_order_amount']}**\n";
        $md .= "> 新增用户：**{$data['new_user_count']}** 人 {$newUserChange}\n";
        $md .= "> 充值金额：**¥{$data['recharge_amount']}** {$rechargeChange}\n\n";

        $md .= "### 📦 订单提醒\n";
        $md .= "> 待发货订单：**{$data['pending_ship_count']}** 笔\n";
        $md .= "> 退款申请：**{$data['refund_count']}** 笔 {$refundChange}\n";
        if ($data['refund_amount'] > 0) {
            $md .= "> 退款金额：**¥{$data['refund_amount']}**\n";
        }
        $md .= "\n";

        $md .= "### 🤖 客服数据\n";
        $md .= "> 接待人数：**{$data['unique_visitors']}** 人\n";
        $md .= "> 对话总数：**{$data['kefu_total']}** 条\n";
        $md .= "> AI自动回复：**{$data['kefu_ai_count']}** 条（占比 {$aiRate}%）\n";
        $md .= "> 转人工：**{$data['kefu_transfer_count']}** 次（占比 {$transferRate}%）\n";
        $md .= "> AI置信度：**{$data['avg_confidence']}** / 10\n";
        if ($data['faq_learned'] > 0) {
            $md .= "> 📚 新学知识：**{$data['faq_learned']}** 条\n";
        }
        $md .= "\n> 数据由AI智能客服系统自动生成";

        return $md;
    }

    protected function calcChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? '🆕' : '';
        }
        $change = round(($current - $previous) / $previous * 100, 1);
        if ($change > 0) {
            return "📈 +{$change}%";
        } elseif ($change < 0) {
            return "📉 {$change}%";
        }
        return '➡️ 持平';
    }

    protected function sendWebhook($markdown)
    {
        $postData = json_encode([
            'msgtype' => 'markdown',
            'markdown' => [
                'content' => $markdown,
            ],
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($this->webhookUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $resp = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($resp, true);

        if (isset($result['errcode']) && $result['errcode'] === 0) {
            return ['success' => true, 'message' => '推送成功'];
        }

        return ['success' => false, 'message' => '推送失败: ' . ($resp ?: 'HTTP ' . $httpCode)];
    }
}
