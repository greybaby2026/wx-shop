<?php

namespace app\adminapi\lists\kefu;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\KefuKnowledge;

class KefuKnowledgeLists extends BaseAdminDataLists
{
    public function lists(): array
    {
        $where = [];
        $category = $this->request->get('category');
        if ($category !== null && $category !== '') {
            $where[] = ['category', '=', $category];
        }
        $status = $this->request->get('status');
        if ($status !== null && $status !== '') {
            $where[] = ['status', '=', intval($status)];
        }
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $where[] = function ($query) use ($keyword) {
                $query->whereOr('title', 'like', "%{$keyword}%")
                    ->whereOr('content', 'like', "%{$keyword}%");
            };
        }

        $lists = KefuKnowledge::field('id,title,content,category,status,quality_score,source,create_time,update_time')
            ->where($where)
            ->order('id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    public function count(): int
    {
        $where = [];
        $category = $this->request->get('category');
        if ($category !== null && $category !== '') {
            $where[] = ['category', '=', $category];
        }
        $status = $this->request->get('status');
        if ($status !== null && $status !== '') {
            $where[] = ['status', '=', intval($status)];
        }
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $where[] = function ($query) use ($keyword) {
                $query->whereOr('title', 'like', "%{$keyword}%")
                    ->whereOr('content', 'like', "%{$keyword}%");
            };
        }

        return KefuKnowledge::where($where)->count();
    }
}
