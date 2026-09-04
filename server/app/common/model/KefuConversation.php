<?php

namespace app\common\model;

use think\Model;

class KefuConversation extends Model
{
    protected $name = 'kefu_conversation';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function getKnowledgeSourcesAttr($value)
    {
        if (empty($value)) return [];
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function setKnowledgeSourcesAttr($value)
    {
        if (empty($value)) return '';
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public static function logConversation($data)
    {
        return self::create([
            'user_id' => $data['user_id'] ?? 0,
            'open_kfid' => $data['open_kfid'] ?? '',
            'external_userid' => $data['external_userid'] ?? '',
            'user_question' => $data['user_question'] ?? '',
            'ai_answer' => $data['ai_answer'] ?? '',
            'answer_type' => $data['answer_type'] ?? 'ai',
            'confidence_score' => $data['confidence_score'] ?? 0,
            'knowledge_sources' => $data['knowledge_sources'] ?? [],
            'session_id' => $data['session_id'] ?? '',
            'status' => 1,
        ]);
    }

    public static function updateHumanAnswer($id, $modifiedAnswer)
    {
        return self::where('id', $id)->update([
            'human_modified_answer' => $modifiedAnswer,
            'answer_type' => 'ai_confirmed',
        ]);
    }
}