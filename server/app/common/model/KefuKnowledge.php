<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class KefuKnowledge extends BaseModel
{
    use SoftDelete;

    protected $name = 'kefu_knowledge';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';

    protected $json = ['embedding', 'knowledge_sources'];
    protected $jsonAssoc = true;

    public function getEmbeddingAttr($value)
    {
        if (empty($value)) return [];
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function setEmbeddingAttr($value)
    {
        if (empty($value)) return '';
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public static function searchByContent($query, $category = null, $limit = 10)
    {
        $keywords = self::extractKeywords($query);

        $dbQuery = self::where('status', 1);

        if ($category) {
            $dbQuery->where('category', $category);
        }

        if (!empty($keywords)) {
            $dbQuery->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->whereOr('title', 'like', "%{$kw}%");
                    $q->whereOr('content', 'like', "%{$kw}%");
                }
            });
        }

        $results = $dbQuery->order('quality_score', 'desc')
            ->order('use_count', 'desc')
            ->limit($limit)
            ->select()
            ->toArray();

        if (empty($results) && $category) {
            $results = self::where('status', 1)
                ->where(function ($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->whereOr('title', 'like', "%{$kw}%");
                        $q->whereOr('content', 'like', "%{$kw}%");
                    }
                })
                ->order('quality_score', 'desc')
                ->order('use_count', 'desc')
                ->limit($limit)
                ->select()
                ->toArray();
        }

        return $results;
    }

    protected static function extractKeywords($query)
    {
        $stopWords = ['怎么', '什么', '如何', '吗', '呢', '啊', '吧', '呀', '哈',
            '哦', '嗯', '的', '了', '在', '是', '有', '我', '你', '他', '她',
            '这个', '那个', '一下', '可以', '能', '会', '要', '想', '请问',
            '帮我', '给我', '告诉', '麻烦', '谢谢', '你好', '您好', '亲'];

        $query = str_replace($stopWords, ' ', $query);
        $query = preg_replace('/\s+/', ' ', trim($query));
        $words = explode(' ', $query);

        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            $len = mb_strlen($word);
            if ($len >= 2) {
                $keywords[] = $word;
            }
            for ($i = 0; $i < $len - 1; $i++) {
                $bigram = mb_substr($word, $i, 2);
                if (!in_array($bigram, $keywords)) {
                    $keywords[] = $bigram;
                }
            }
        }

        if (empty($keywords)) {
            return [mb_substr($query, 0, 4)];
        }

        return $keywords;
    }
}