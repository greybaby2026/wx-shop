<?php

namespace app\common\service\ai;

use app\common\model\KefuConversation;
use app\common\model\User;
use app\common\model\Order;
use app\common\enum\OrderEnum;

class AiChatService
{
    protected $deepSeek;
    protected $knowledgeService;
    protected $promptBuilder;

    public function __construct()
    {
        $this->deepSeek = new DeepSeekService();
        $this->knowledgeService = new KnowledgeService();
        $this->promptBuilder = new PromptBuilderService();
    }

    public function handleMessage($userQuestion, $externalUserid = '', $openKfid = '', $userId = 0)
    {
        if ($this->isTransferToHuman($userQuestion)) {
            return $this->handleTransferToHuman($userQuestion, $externalUserid, $openKfid, $userId);
        }

        if ($this->isGreeting($userQuestion)) {
            return $this->handleGreeting($externalUserid, $openKfid, $userId);
        }

        if ($this->isOrderQuery($userQuestion)) {
            $orderReply = $this->handleOrderQuery($externalUserid, $openKfid, $userId);
            if ($orderReply) {
                return $orderReply;
            }
        }

        $sessionId = md5($openKfid . $externalUserid . date('Ymd'));

        $category = $this->classifyQuestion($userQuestion);

        $knowledgeList = $this->knowledgeService->search($userQuestion, 5, $category);
        $knowledgeIds = array_column($knowledgeList, 'id');

        $userContext = $this->getUserContext($externalUserid, $userId);

        $chatHistory = $this->getChatHistory($externalUserid, $openKfid);

        $messages = $this->promptBuilder->buildMessages($userQuestion, $knowledgeList, $userContext, $chatHistory);

        $aiResult = $this->deepSeek->chat($messages);

        if (!$aiResult['success']) {
            $fallbackReply = "亲，非常抱歉，我暂时无法处理您的问题。请稍后再试，或输入「转人工」联系人工客服为您服务~\n\n常见问题您可以先查看：\n1. 如何下单？\n2. 发货时间是多久？\n3. 如何申请售后？";

            KefuConversation::logConversation([
                'user_id' => $userId,
                'open_kfid' => $openKfid,
                'external_userid' => $externalUserid,
                'user_question' => $userQuestion,
                'ai_answer' => $fallbackReply,
                'answer_type' => 'ai',
                'confidence_score' => 0,
                'knowledge_sources' => $knowledgeIds,
                'session_id' => $sessionId,
            ]);

            return [
                'success' => true,
                'reply' => $fallbackReply,
                'confidence' => 0,
                'knowledge_ids' => $knowledgeIds,
            ];
        }

        $aiReply = $aiResult['content'];
        $confidence = $this->calculateConfidence($knowledgeList, $aiReply, $userQuestion);

        if (!empty($knowledgeIds)) {
            foreach ($knowledgeIds as $kid) {
                \think\facade\Db::table('ls_kefu_knowledge')
                    ->where('id', $kid)
                    ->inc('use_count')
                    ->update();
            }
        }

        if ($confidence < 4.0 && !$this->isBrandQuestion($userQuestion)) {
            $aiReply .= "\n\n💡 如果以上回答未能解决您的问题，可以点击「转人工客服」联系我们的客服人员哦~";
        }

        KefuConversation::logConversation([
            'user_id' => $userId,
            'open_kfid' => $openKfid,
            'external_userid' => $externalUserid,
            'user_question' => $userQuestion,
            'ai_answer' => $aiReply,
            'answer_type' => 'ai',
            'confidence_score' => $confidence,
            'knowledge_sources' => $knowledgeIds,
            'session_id' => $sessionId,
        ]);

        $this->appendChatHistory($externalUserid, $userQuestion, $aiReply);

        return [
            'success' => true,
            'reply' => $aiReply,
            'confidence' => $confidence,
            'knowledge_ids' => $knowledgeIds,
        ];
    }

    protected function getUserContext($externalUserid, $userId = 0)
    {
        $userContext = [];

        if ($userId <= 0 && !empty($externalUserid)) {
            $userId = $this->resolveUserId($externalUserid);
        }

        if ($userId > 0) {
            $user = User::find($userId);
            if ($user) {
                $userContext['昵称'] = $user->nickname ?? '未知';
                $userContext['会员等级'] = $user->level_name ?? '普通会员';
                $userContext['手机号'] = $user->mobile ?? '未绑定';
            }
        }

        return $userContext;
    }

    protected function resolveUserId($externalUserid)
    {
        $cached = cache('kefu_user_map_' . $externalUserid);
        if ($cached) {
            return $cached;
        }

        $mapping = \think\facade\Db::table('ls_kefu_user_mapping')
            ->where('external_userid', $externalUserid)
            ->find();

        if ($mapping) {
            cache('kefu_user_map_' . $externalUserid, $mapping['user_id'], 3600);
            return $mapping['user_id'];
        }

        $kefuMsg = new \app\common\service\wecom\KefuMessageService();
        $unionid = $kefuMsg->getCustomerUnionId($externalUserid);

        if ($unionid) {
            $userAuth = \think\facade\Db::table('ls_user_auth')
                ->where('unionid', $unionid)
                ->find();

            if ($userAuth) {
                $userId = $userAuth['user_id'];

                \think\facade\Db::table('ls_kefu_user_mapping')->insert([
                    'external_userid' => $externalUserid,
                    'user_id' => $userId,
                    'create_time' => time(),
                ]);

                cache('kefu_user_map_' . $externalUserid, $userId, 3600);
                return $userId;
            }
        }

        return 0;
    }

    protected function isOrderQuery($question)
    {
        $keywords = ['查询订单', '我的订单', '订单查询', '查看订单', '订单状态', '查订单', '看订单', '订单到哪了', '到哪了', '物流查询', '快递到哪了'];
        foreach ($keywords as $kw) {
            if (mb_strpos($question, $kw) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function handleOrderQuery($externalUserid, $openKfid, $userId = 0)
    {
        if ($userId <= 0 && !empty($externalUserid)) {
            $userId = $this->resolveUserId($externalUserid);
        }

        if ($userId <= 0) {
            return [
                'success' => true,
                'reply' => "亲，我暂时无法查询到您的订单信息。可能是因为您的微信账号尚未与商城账号关联。\n\n您可以通过以下方式查看订单：\n1. 在小程序「我的」→「我的订单」中查看\n2. 输入「转人工」联系客服帮您查询",
                'confidence' => 3.0,
                'knowledge_ids' => [],
            ];
        }

        $statusMap = [
            OrderEnum::STATUS_WAIT_PAY => '待付款',
            OrderEnum::STATUS_WAIT_DELIVERY => '待发货',
            OrderEnum::STATUS_WAIT_RECEIVE => '待收货',
            OrderEnum::STATUS_FINISH => '已完成',
            OrderEnum::STATUS_CLOSE => '已关闭',
        ];

        $orders = Order::where('user_id', $userId)
            ->where('order_status', 'in', [0, 1, 2, 3])
            ->with(['orderGoods' => function ($query) {
                $query->field('order_id,goods_name,goods_price,goods_num');
            }])
            ->field('id,sn,order_status,order_amount,total_num,create_time')
            ->order('id desc')
            ->limit(5)
            ->select()
            ->toArray();

        if (empty($orders)) {
            return [
                'success' => true,
                'reply' => "亲，您目前暂无订单记录。快去挑选心仪的商品吧~ 🛍️",
                'confidence' => 8.0,
                'knowledge_ids' => [],
            ];
        }

        $reply = "📋 您最近的订单如下：\n\n";
        foreach ($orders as $i => $order) {
            $status = $statusMap[$order['order_status']] ?? '未知';
            $goodsNames = [];
            if (!empty($order['order_goods'])) {
                foreach ($order['order_goods'] as $og) {
                    $goodsNames[] = $og['goods_name'] . ' x' . $og['goods_num'];
                }
            }
            $goodsStr = implode('、', $goodsNames);
            if (mb_strlen($goodsStr) > 30) {
                $goodsStr = mb_substr($goodsStr, 0, 30) . '...';
            }

            $reply .= ($i + 1) . "️⃣ 订单号：{$order['sn']}\n";
            $reply .= "   状态：{$status}\n";
            $reply .= "   商品：{$goodsStr}\n";
            $reply .= "   金额：¥{$order['order_amount']}\n\n";
        }

        $reply .= "💡 如需查看更多详情，可在小程序「我的订单」中查看。有其他问题也可以直接问我~";

        return [
            'success' => true,
            'reply' => $reply,
            'confidence' => 9.0,
            'knowledge_ids' => [],
        ];
    }

    protected function classifyQuestion($question)
    {
        $keywords = [
            'product' => ['商品', '产品', '价格', '库存', '规格', '尺寸', '颜色', '材质', '面料'],
            'order' => ['订单', '下单', '购买', '支付', '付款', '购物车', '优惠券'],
            'after_sale' => ['退货', '退款', '换货', '售后', '维修', '投诉', '差评'],
            'distribution' => ['分销', '佣金', '提成', '推荐', '推广', '团长'],
            'delivery' => ['发货', '物流', '快递', '配送', '收货', '签收'],
            'policy' => ['政策', '规则', '协议', '条款', '隐私', '保障'],
        ];

        foreach ($keywords as $cat => $words) {
            foreach ($words as $word) {
                if (mb_strpos($question, $word) !== false) {
                    return $cat;
                }
            }
        }

        return 'general';
    }

    protected function calculateConfidence($knowledgeList, $aiReply, $userQuestion)
    {
        $knowledgeCount = count($knowledgeList);

        if ($knowledgeCount === 0) {
            return 3.0;
        }

        $isApology = (mb_strpos($aiReply, '抱歉') !== false || mb_strpos($aiReply, '不确定') !== false || mb_strpos($aiReply, '无法') !== false);

        $hasRelevantAnswer = (mb_strlen($aiReply) > 30 && !$isApology);

        $confidence = 3.0;

        if ($knowledgeCount >= 3 && $hasRelevantAnswer) {
            $confidence = 7.5;
        } elseif ($knowledgeCount >= 2 && $hasRelevantAnswer) {
            $confidence = 6.5;
        } elseif ($knowledgeCount >= 1 && $hasRelevantAnswer) {
            $confidence = 5.5;
        }

        foreach ($knowledgeList as $item) {
            $titleScore = $this->simpleTextMatch($userQuestion, $item['title']);
            if ($titleScore > 0.3) {
                $confidence += 1.0;
            }
        }

        if ($isApology) {
            $confidence = max($confidence - 3.0, 1.0);
        }

        return max(1.0, min(10.0, $confidence));
    }

    protected function simpleTextMatch($text1, $text2)
    {
        $chars1 = mb_str_split($text1);
        $chars2 = mb_str_split($text2);
        $common = array_intersect($chars1, $chars2);
        $total = array_unique(array_merge($chars1, $chars2));

        return count($total) > 0 ? count($common) / count($total) : 0;
    }

    protected function isTransferToHuman($question)
    {
        $keywords = ['转人工', '人工客服', '找人工', '人工服务', '转客服', '联系客服', '找客服'];
        foreach ($keywords as $kw) {
            if (mb_strpos($question, $kw) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function handleTransferToHuman($question, $externalUserid, $openKfid, $userId)
    {
        $reply = "亲，已为您转接人工客服，请稍等片刻，客服专员会尽快为您处理~ 😊\n\n等待期间，您也可以描述一下遇到的问题，方便客服快速帮您解决哦~";

        KefuConversation::logConversation([
            'user_id' => $userId,
            'open_kfid' => $openKfid,
            'external_userid' => $externalUserid,
            'user_question' => $question,
            'ai_answer' => $reply,
            'answer_type' => 'transfer_human',
            'confidence_score' => 10,
            'session_id' => md5($openKfid . $externalUserid . date('Ymd')),
        ]);

        return [
            'success' => true,
            'reply' => $reply,
            'confidence' => 10,
            'knowledge_ids' => [],
        ];
    }

    protected function isGreeting($question)
    {
        $question = trim($question);
        $greetings = ['你好', '您好', '在吗', '在不在', 'hi', 'hello', '嗨', '嘿', '哈喽', '早上好', '下午好', '晚上好', '早安', '午安', '晚安'];
        foreach ($greetings as $g) {
            if (mb_strtolower($question) === $g || mb_strtolower($question) === $g . '！' || mb_strtolower($question) === $g . '~') {
                return true;
            }
        }

        if (preg_match('/^\[.+\]$/', $question)) {
            return true;
        }

        if (mb_strlen($question) <= 2 && !preg_match('/[\d]/', $question)) {
            return true;
        }

        return false;
    }

    protected function handleGreeting($externalUserid, $openKfid, $userId)
    {
        $replies = [
            '亲，您好！欢迎来到将军世家~ 我是AI智能客服，有什么可以帮您的吗？😊',
            '亲，您好呀！将军世家为您服务~ 请问有什么需要帮忙的？',
            '亲，欢迎光临将军世家！我是您的专属客服助手，有任何问题都可以问我哦~',
        ];

        $reply = $replies[array_rand($replies)];

        KefuConversation::logConversation([
            'user_id' => $userId,
            'open_kfid' => $openKfid,
            'external_userid' => $externalUserid,
            'user_question' => 'greeting',
            'ai_answer' => $reply,
            'answer_type' => 'greeting',
            'confidence_score' => 10,
            'session_id' => md5($openKfid . $externalUserid . date('Ymd')),
        ]);

        return [
            'success' => true,
            'reply' => $reply,
            'confidence' => 10,
            'knowledge_ids' => [],
        ];
    }

    protected function isBrandQuestion($question)
    {
        $brandKeywords = ['将军世家', '品牌', '男装', '正装', '通勤', '张勇', '创始人', '老板', '性价比', '精工', '面料', '门店', '线下'];
        foreach ($brandKeywords as $kw) {
            if (mb_strpos($question, $kw) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function getChatHistory($externalUserid, $openKfid, $limit = 6)
    {
        $history = [];

        $cacheKey = 'kefu_chat_history_' . $externalUserid;
        $cached = cache($cacheKey);

        if ($cached && is_array($cached)) {
            return array_slice($cached, -$limit);
        }

        $conversations = \think\facade\Db::table('ls_kefu_conversation')
            ->where('external_userid', $externalUserid)
            ->where('answer_type', 'in', ['ai', 'greeting'])
            ->where('create_time', '>', time() - 1800)
            ->order('id', 'desc')
            ->limit($limit)
            ->field('user_question, ai_answer')
            ->select()
            ->toArray();

        if (!empty($conversations)) {
            $conversations = array_reverse($conversations);
            foreach ($conversations as $conv) {
                if ($conv['user_question'] !== 'greeting') {
                    $history[] = ['role' => 'user', 'content' => $conv['user_question']];
                }
                $history[] = ['role' => 'assistant', 'content' => $conv['ai_answer']];
            }
        }

        cache($cacheKey, $history, 1800);

        return array_slice($history, -$limit);
    }

    public function appendChatHistory($externalUserid, $userQuestion, $aiAnswer)
    {
        $cacheKey = 'kefu_chat_history_' . $externalUserid;
        $history = cache($cacheKey) ?: [];

        $history[] = ['role' => 'user', 'content' => $userQuestion];
        $history[] = ['role' => 'assistant', 'content' => $aiAnswer];

        if (count($history) > 12) {
            $history = array_slice($history, -12);
        }

        cache($cacheKey, $history, 1800);
    }
}
