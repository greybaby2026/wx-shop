<?php

namespace app\adminapi\controller\kefu;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\kefu\KefuKnowledgeLists;
use app\adminapi\logic\kefu\KefuAiBotLogic;
use app\common\service\wecom\DailyReportService;

class KefuAiBotController extends BaseAdminController
{
    public function getConfig()
    {
        $result = KefuAiBotLogic::getConfig();
        return $this->data($result);
    }

    public function setConfig()
    {
        $params = $this->request->post();
        $result = KefuAiBotLogic::setConfig($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('设置成功', [], 1, 1);
    }

    public function getPrompt()
    {
        $result = KefuAiBotLogic::getPrompt();
        return $this->data($result);
    }

    public function setPrompt()
    {
        $params = $this->request->post();
        $result = KefuAiBotLogic::setPrompt($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('设置成功', [], 1, 1);
    }

    public function knowledgeLists()
    {
        return $this->dataLists(new KefuKnowledgeLists());
    }

    public function knowledgeDetail()
    {
        $id = $this->request->get('id');
        $result = KefuAiBotLogic::knowledgeDetail(intval($id));
        return $this->success('获取成功', $result);
    }

    public function knowledgeAdd()
    {
        $params = $this->request->post();
        $result = KefuAiBotLogic::knowledgeAdd($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('添加成功', [], 1, 1);
    }

    public function knowledgeEdit()
    {
        $params = $this->request->post();
        $result = KefuAiBotLogic::knowledgeEdit($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('编辑成功', [], 1, 1);
    }

    public function knowledgeDel()
    {
        $id = $this->request->post('id');
        $result = KefuAiBotLogic::knowledgeDel(intval($id));
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('删除成功', [], 1, 1);
    }

    public function knowledgeApprove()
    {
        $id = $this->request->post('id');
        $result = KefuAiBotLogic::knowledgeApprove(intval($id));
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('审核通过', [], 1, 1);
    }

    public function knowledgeReject()
    {
        $id = $this->request->post('id');
        $result = KefuAiBotLogic::knowledgeReject(intval($id));
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('已拒绝', [], 1, 1);
    }

    public function faqLists()
    {
        $params = $this->request->get();
        $result = KefuAiBotLogic::faqLists($params);
        return $this->data($result);
    }

    public function faqAdd()
    {
        $params = $this->request->post();
        $result = KefuAiBotLogic::faqAdd($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('添加成功', [], 1, 1);
    }

    public function faqEdit()
    {
        $params = $this->request->post();
        $result = KefuAiBotLogic::faqEdit($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('编辑成功', [], 1, 1);
    }

    public function faqDel()
    {
        $id = $this->request->post('id');
        $result = KefuAiBotLogic::faqDel(intval($id));
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('删除成功', [], 1, 1);
    }

    public function faqToggle()
    {
        $id = $this->request->post('id');
        $status = $this->request->post('status', 1);
        $result = KefuAiBotLogic::faqToggle(intval($id), intval($status));
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功', [], 1, 1);
    }

    public function sendReport()
    {
        $date = $this->request->post('date', date('Y-m-d', strtotime('-1 day')));
        $service = new DailyReportService();
        $result = $service->sendDailyReport($date);
        if ($result['success']) {
            return $this->success($result['message'], [], 1, 1);
        }
        return $this->fail($result['message']);
    }
}
