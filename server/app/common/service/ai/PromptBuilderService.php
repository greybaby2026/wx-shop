<?php

namespace app\common\service\ai;

class PromptBuilderService
{
    protected $systemPrompt = '';

    public function __construct()
    {
        $dbPrompt = \app\common\model\KefuAiConfig::getConfigValue('system_prompt', '');
        if (!empty($dbPrompt)) {
            $this->systemPrompt = $dbPrompt;
        } else {
            $this->systemPrompt = $this->getDefaultSystemPrompt();
        }
    }

    protected function getDefaultSystemPrompt()
    {
        return <<<PROMPT
你是「将军世家」的AI智能客服助手，代表品牌形象与客户沟通。

【品牌核心信息——必须严格遵守，不可编造】
- 品牌名：将军世家
- 公司类型：自主品牌男装公司
- 公司全称：将军世家(山东)服饰有限公司
- 总部地址：山东省临沂市兰陵县
- 创始人：张勇先生（70后企业家）
- 创立初衷：深感男装行业痛点——合适的衣服难买、价格虚高、品质参差不齐
- 主营品类：正装通勤服装
- 目标人群：25-60岁男性
- 品牌定位：中端、性价比
- 价格区间：200-800元
- 核心卖点：精工细作、面料精挑细选、自主品牌、性价比高
- 销售渠道：线上小程序 + 线下门店同步发售，同款同价

【回答规则】
1. 始终使用亲切友好的语气，称呼用户为"亲"
2. 回答简洁准确，控制在150字以内
3. 涉及品牌/公司问题时，严格基于以上信息回答，绝对不可编造地址、电话、门店数量等未提供的信息
4. 涉及订单/物流查询时，引导用户点击菜单「查询订单」或「物流查询」
5. 不确定的问题诚实告知"这个我暂时不太确定"，建议联系人工客服
6. 寒暄类问题简短回应即可
7. 禁止使用"?"等乱码符号

【回答范例——学习以下风格】
用户：将军世家是什么牌子？
回答：亲，将军世家是自主品牌男装，主营正装通勤服装，由张勇先生创立。我们专注为25-60岁男士提供高性价比着装，价格200-800元，精工细作，面料精选，线上线下同步发售哦~

用户：你们衣服贵不贵？
回答：亲，将军世家主打性价比，价格在200-800元之间。同等品质的男装，我们的价格更亲民，真正做到精工细作不虚标~

用户：你们是哪里的？
回答：亲，将军世家总部在山东省临沂市兰陵县，公司全称是将军世家(山东)服饰有限公司。线下门店同步发售，线上下单同样品质哦~

用户：质量怎么样？
回答：亲，将军世家坚持精工细作、面料精挑细选，每件服装出厂前都经过质量检查。而且支持7天无理由退换货，放心购买~
PROMPT;
    }

    public function buildMessages($userQuestion, $knowledgeList = [], $userContext = [], $chatHistory = [])
    {
        $messages = [];

        $systemContent = $this->systemPrompt;

        if (!empty($knowledgeList)) {
            $systemContent .= "\n\n【参考知识库】\n";
            foreach ($knowledgeList as $i => $item) {
                $idx = $i + 1;
                $systemContent .= "[知识{$idx}] {$item['title']}\n{$item['content']}\n";
            }
            $systemContent .= "\n请优先参考知识库回答。知识库未涉及的内容，基于品牌核心信息和对话经验回答；都没有的，诚实告知。";
        }

        $faqList = $this->getActiveFaq();
        if (!empty($faqList)) {
            $systemContent .= "\n\n【对话经验——参考以下已验证的问答】\n";
            foreach ($faqList as $faq) {
                $systemContent .= "用户：{$faq['question']}\n回答：{$faq['answer']}\n";
            }
            $systemContent .= "\n遇到类似问题时，请参考以上经验回答，但要用自然语言重新组织，不要照搬。";
        }

        if (!empty($userContext)) {
            $systemContent .= "\n\n【当前用户信息】\n";
            foreach ($userContext as $key => $val) {
                $systemContent .= "- {$key}：{$val}\n";
            }
        }

        $messages[] = ['role' => 'system', 'content' => $systemContent];

        if (!empty($chatHistory)) {
            foreach ($chatHistory as $history) {
                $messages[] = $history;
            }
        }

        $messages[] = ['role' => 'user', 'content' => $userQuestion];

        return $messages;
    }

    protected function getActiveFaq($limit = 10)
    {
        $cacheKey = 'kefu_faq_active_list';
        $cached = cache($cacheKey);
        if ($cached !== false && $cached !== null) {
            return $cached;
        }

        $faqList = \think\facade\Db::table('ls_kefu_faq')
            ->where('status', 1)
            ->where('expire_time', '>', time())
            ->order('hit_count', 'desc')
            ->order('update_time', 'desc')
            ->limit($limit)
            ->field('question, answer')
            ->select()
            ->toArray();

        cache($cacheKey, $faqList, 300);

        return $faqList;
    }
}
