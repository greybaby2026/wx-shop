<?php

namespace app\common\service\ai;

use app\common\model\KefuKnowledge;

class KnowledgeService
{
    protected $synonyms = [
        '男装' => ['衣服', '服装', '男装', '正装', '通勤装', '商务装'],
        '品牌' => ['牌子', '品牌', '商标'],
        '价格' => ['价格', '多少钱', '贵不贵', '价位', '收费', '费用'],
        '质量' => ['质量', '品质', '做工', '面料', '材质', '精工'],
        '退换货' => ['退货', '退款', '换货', '售后', '退换', '退换货'],
        '物流' => ['物流', '快递', '发货', '配送', '运输', '送货'],
        '分销' => ['分销', '团长', '佣金', '推广', '提成', '代理'],
        '门店' => ['门店', '实体店', '线下', '店铺', '专卖店'],
        '创始人' => ['创始人', '老板', '张勇', '创立', '创办'],
        '尺码' => ['尺码', '尺寸', '大小', '码数', '号'],
        '优惠' => ['优惠', '折扣', '打折', '活动', '促销', '满减', '便宜'],
        '下单' => ['下单', '购买', '买', '订购', '购物', '结算', '付款', '支付'],
        '订单' => ['订单', '订单号', '订单状态', '查订单'],
    ];

    public function search($query, $topK = 5, $category = null)
    {
        $allKnowledge = KefuKnowledge::where('status', 1)
            ->where('delete_time', null)
            ->when($category, function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->field('id, title, content, category, quality_score, use_count')
            ->select()
            ->toArray();

        if (empty($allKnowledge)) {
            if ($category) {
                $allKnowledge = KefuKnowledge::where('status', 1)
                    ->where('delete_time', null)
                    ->field('id, title, content, category, quality_score, use_count')
                    ->select()
                    ->toArray();
            }
            if (empty($allKnowledge)) {
                return [];
            }
        }

        $queryKeywords = $this->extractKeywords($query);

        $scoredList = [];
        foreach ($allKnowledge as $item) {
            $score = $this->calculateScore($queryKeywords, $query, $item);
            $scoredList[] = ['item' => $item, 'score' => $score];
        }

        usort($scoredList, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $result = [];
        foreach (array_slice($scoredList, 0, $topK) as $scored) {
            if ($scored['score'] > 0.15) {
                $result[] = $scored['item'];
            }
        }

        return $result;
    }

    protected function calculateScore($queryKeywords, $rawQuery, $item)
    {
        $title = $item['title'];
        $content = $item['content'];
        $fullText = $title . ' ' . $content;

        $score = 0;

        $titleMatchScore = $this->matchScore($rawQuery, $title);
        $score += $titleMatchScore * 0.5;

        $contentMatchScore = $this->matchScore($rawQuery, $content);
        $score += $contentMatchScore * 0.2;

        $keywordScore = 0;
        foreach ($queryKeywords as $kw) {
            $kwLen = mb_strlen($kw);
            if ($kwLen < 2) continue;

            if (mb_strpos($title, $kw) !== false) {
                $keywordScore += $kwLen * 2;
            }
            if (mb_strpos($content, $kw) !== false) {
                $keywordScore += $kwLen;
            }

            foreach ($this->synonyms as $concept => $syns) {
                $queryHasConcept = false;
                $itemHasConcept = false;

                foreach ($syns as $syn) {
                    if (mb_strpos($rawQuery, $syn) !== false) $queryHasConcept = true;
                    if (mb_strpos($fullText, $syn) !== false) $itemHasConcept = true;
                }

                if ($queryHasConcept && $itemHasConcept) {
                    $keywordScore += $kwLen * 1.5;
                }
            }
        }

        $score += min($keywordScore / 20, 1.0) * 0.3;

        if ($item['quality_score'] > 0) {
            $score += ($item['quality_score'] / 10) * 0.05;
        }

        if ($item['use_count'] > 0) {
            $score += min($item['use_count'] / 50, 1.0) * 0.05;
        }

        return $score;
    }

    protected function matchScore($query, $text)
    {
        $queryLen = mb_strlen($query);
        if ($queryLen == 0 || mb_strlen($text) == 0) return 0;

        $matchLen = 0;
        for ($len = min($queryLen, 10); $len >= 2; $len--) {
            for ($i = 0; $i <= $queryLen - $len; $i++) {
                $sub = mb_substr($query, $i, $len);
                if (mb_strpos($text, $sub) !== false) {
                    $matchLen = max($matchLen, $len);
                    break 2;
                }
            }
        }

        return $matchLen / $queryLen;
    }

    protected function extractKeywords($query)
    {
        $stopWords = ['怎么', '什么', '如何', '吗', '呢', '啊', '吧', '呀', '哈',
            '哦', '嗯', '的', '了', '在', '是', '有', '我', '你', '他', '她',
            '这个', '那个', '一下', '可以', '能', '会', '要', '想', '请问',
            '帮我', '给我', '告诉', '麻烦', '谢谢', '你好', '您好', '亲',
            '和', '与', '及', '或', '但', '而', '且', '则', '很', '都',
            '也', '还', '又', '再', '才', '就', '已', '所', '以', '为'];

        $cleaned = str_replace($stopWords, ' ', $query);
        $cleaned = preg_replace('/\s+/', ' ', trim($cleaned));

        $keywords = [];

        $words = explode(' ', $cleaned);
        foreach ($words as $word) {
            $word = trim($word);
            if (mb_strlen($word) >= 2) {
                $keywords[] = $word;
            }
        }

        $len = mb_strlen($query);
        for ($i = 0; $i < $len - 1; $i++) {
            for ($n = 4; $n >= 2; $n--) {
                if ($i + $n <= $len) {
                    $gram = mb_substr($query, $i, $n);
                    $hasStop = false;
                    foreach ($stopWords as $sw) {
                        if (mb_strpos($gram, $sw) !== false) {
                            $hasStop = true;
                            break;
                        }
                    }
                    if (!$hasStop && !in_array($gram, $keywords)) {
                        $keywords[] = $gram;
                    }
                }
            }
        }

        return $keywords;
    }

    public function addKnowledge($title, $content, $category = 'general', $source = 'manual', $sourceConversationId = 0)
    {
        return KefuKnowledge::create([
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'embedding' => '',
            'source' => $source,
            'source_conversation_id' => $sourceConversationId,
            'status' => 1,
        ]);
    }
}
