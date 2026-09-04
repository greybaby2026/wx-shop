<?php

namespace app\adminapi\logic\kefu;

use app\common\logic\BaseLogic;
use app\common\model\KefuAiConfig;
use app\common\model\KefuKnowledge;
use think\facade\Db;

class KefuAiBotLogic extends BaseLogic
{
    private static array $configKeys = [
        'deepseek_api_key',
        'deepseek_model',
        'deepseek_temperature',
        'deepseek_max_tokens',
        'wecom_corp_id',
        'wecom_kefu_secret',
        'wecom_callback_token',
        'wecom_encoding_aes_key',
        'wecom_open_kfid',
        'wecom_report_webhook',
    ];

    private static array $configDefaults = [
        'deepseek_api_key' => '',
        'deepseek_model' => 'deepseek-chat',
        'deepseek_temperature' => '0.7',
        'deepseek_max_tokens' => '2048',
        'wecom_corp_id' => '',
        'wecom_kefu_secret' => '',
        'wecom_callback_token' => '',
        'wecom_encoding_aes_key' => '',
        'wecom_open_kfid' => '',
            'wecom_report_webhook' => '',
    ];

    public static function getConfig()
    {
        $result = [];
        foreach (self::$configKeys as $key) {
            $result[$key] = KefuAiConfig::getConfigValue($key, self::$configDefaults[$key] ?? '');
        }
        return $result;
    }

    public static function setConfig($params)
    {
        Db::startTrans();
        try {
            foreach (self::$configKeys as $key) {
                if (isset($params[$key])) {
                    KefuAiConfig::setConfigValue($key, $params[$key]);
                }
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function getPrompt()
    {
        $prompt = KefuAiConfig::getConfigValue('system_prompt', '');
        return ['system_prompt' => $prompt];
    }

    public static function setPrompt($params)
    {
        Db::startTrans();
        try {
            if (!isset($params['system_prompt'])) {
                throw new \Exception('参数错误');
            }
            KefuAiConfig::setConfigValue('system_prompt', $params['system_prompt']);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function knowledgeDetail(int $id)
    {
        return KefuKnowledge::findOrEmpty($id)->toArray();
    }

    public static function knowledgeAdd($params)
    {
        Db::startTrans();
        try {
            $data = [
                'title' => $params['title'] ?? '',
                'content' => $params['content'] ?? '',
                'category' => $params['category'] ?? '',
                'status' => $params['status'] ?? 0,
                'quality_score' => $params['quality_score'] ?? 0,
                'source' => $params['source'] ?? '',
            ];
            KefuKnowledge::create($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function knowledgeEdit($params)
    {
        Db::startTrans();
        try {
            $knowledge = KefuKnowledge::findOrEmpty($params['id'] ?? 0);
            if ($knowledge->isEmpty()) {
                throw new \Exception('知识不存在');
            }
            $data = [];
            if (isset($params['title'])) $data['title'] = $params['title'];
            if (isset($params['content'])) $data['content'] = $params['content'];
            if (isset($params['category'])) $data['category'] = $params['category'];
            if (isset($params['status'])) $data['status'] = $params['status'];
            if (isset($params['quality_score'])) $data['quality_score'] = $params['quality_score'];
            if (isset($params['source'])) $data['source'] = $params['source'];
            $knowledge->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function knowledgeDel(int $id)
    {
        Db::startTrans();
        try {
            $knowledge = KefuKnowledge::findOrEmpty($id);
            if ($knowledge->isEmpty()) {
                throw new \Exception('知识不存在');
            }
            $knowledge->delete();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function knowledgeApprove(int $id)
    {
        Db::startTrans();
        try {
            $knowledge = KefuKnowledge::findOrEmpty($id);
            if ($knowledge->isEmpty()) {
                throw new \Exception('知识不存在');
            }
            $knowledge->status = 1;
            $knowledge->save();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function knowledgeReject(int $id)
    {
        Db::startTrans();
        try {
            $knowledge = KefuKnowledge::findOrEmpty($id);
            if ($knowledge->isEmpty()) {
                throw new \Exception('知识不存在');
            }
            $knowledge->delete();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function faqLists($params)
    {
        $page = intval($params['page_no'] ?? 1);
        $pageSize = intval($params['page_size'] ?? 15);
        $query = Db::table('ls_kefu_faq');

        if (!empty($params['category'])) {
            $query->where('category', $params['category']);
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', intval($params['status']));
        }
        if (!empty($params['keyword'])) {
            $query->where(function ($q) use ($params) {
                $q->whereOr('question', 'like', '%' . $params['keyword'] . '%')
                  ->whereOr('answer', 'like', '%' . $params['keyword'] . '%');
            });
        }

        $total = (clone $query)->count();
        $list = $query->order('id', 'desc')
            ->page($page, $pageSize)
            ->select()
            ->toArray();

        return [
            'lists' => $list,
            'count' => $total,
            'page_no' => $page,
            'page_size' => $pageSize,
        ];
    }

    public static function faqAdd($params)
    {
        Db::startTrans();
        try {
            Db::table('ls_kefu_faq')->insert([
                'question' => $params['question'] ?? '',
                'answer' => $params['answer'] ?? '',
                'category' => $params['category'] ?? 'general',
                'hit_count' => 0,
                'status' => intval($params['status'] ?? 1),
                'expire_time' => time() + 30 * 86400,
                'create_time' => time(),
                'update_time' => time(),
            ]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function faqEdit($params)
    {
        Db::startTrans();
        try {
            $id = intval($params['id'] ?? 0);
            if (empty($id)) throw new \Exception('参数错误');
            $data = [];
            if (isset($params['question'])) $data['question'] = $params['question'];
            if (isset($params['answer'])) $data['answer'] = $params['answer'];
            if (isset($params['category'])) $data['category'] = $params['category'];
            if (isset($params['status'])) $data['status'] = intval($params['status']);
            $data['update_time'] = time();
            Db::table('ls_kefu_faq')->where('id', $id)->update($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function faqDel(int $id)
    {
        Db::startTrans();
        try {
            Db::table('ls_kefu_faq')->where('id', $id)->delete();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public static function faqToggle(int $id, int $status)
    {
        Db::startTrans();
        try {
            Db::table('ls_kefu_faq')->where('id', $id)->update([
                'status' => $status,
                'update_time' => time(),
            ]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }
}
