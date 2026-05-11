<?php

namespace app\adminapi\controller\order;

use app\adminapi\logic\order\OrderDeliveryBatchLogic;
use app\adminapi\validate\order\DeliveryBatchImport;
use app\adminapi\controller\BaseAdminController;
use app\common\cache\ExportCache;
use app\common\service\excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\response\Json;

class DeliveryBatchController extends BaseAdminController
{
    
    
    /**
     * @notes 批量发货首页
     * @return Json
     * @author lbzy
     * @datetime 2024-04-11 16:46:54
     */
    function index()
    {
        return $this->success('成功', OrderDeliveryBatchLogic::lists(input()));
    }
    
    /**
     * @notes 批量发货详情
     * @return mixed
     * @author lbzy
     * @datetime 2024-04-12 16:05:38
     */
    function detail()
    {
        return $this->success('成功', OrderDeliveryBatchLogic::detail(input('id/d')));
    }
    
    /**
     * @notes 立即导入
     * @return Json
     * @author lbzy
     * @datetime 2024-04-12 09:45:06
     */
    function import()
    {
        $lists  = IOFactory::load($_FILES['file']['tmp_name'])->getActiveSheet()->toArray(null, true, true, true);
        
        $validate = $this->validate([ 'lists' => $lists ], DeliveryBatchImport::class);
        
        if ($validate !== true) {
            return $this->fail($validate);
        }
        
        $result = OrderDeliveryBatchLogic::importLists($lists, $_FILES['file']['name']);
        
        if (! is_array($result)) {
            return $this->fail($result);
        }
    
        return $this->success('导入成功', $result);
    }
    
    /**
     * @notes 执行发货页面
     * @return Json|void
     * @author lbzy
     * @datetime 2024-04-12 14:33:52
     */
    function delivery()
    {
        $detail = OrderDeliveryBatchLogic::detail(input('id/d'));
        
        if ($this->request->isPost()) {
            OrderDeliveryBatchLogic::delivery($detail);
            return $this->success('发货完成');
        }
    }
    
    /**
     * @notes 下载模版文件等
     * @return string|\think\response\File|Json
     * @author lbzy
     * @datetime 2024-04-11 17:15:00
     */
    function down()
    {
        $filename       = pathinfo(input('file/s'), PATHINFO_FILENAME);
        $exportCache    = new ExportCache();
        $src            = $exportCache->getSrc();
        
        if (!file_exists($src)) {
            mkdir($src, 0775, true);
        }
    
        $path = app()->getRootPath() . 'public/common/excel/' . $filename . '.xls';
        
        switch ($filename) {
            case 'delivery_batch_template':
                $filename2 = '批量发货模版.xls';
                break;
            case 'delivery_batch_express_company':
                $filename2 = '快递公司列表.xls';
                break;
            default:
                $filename2 = '模版.xls';
                break;
        }
        
        copy($path, $src . $filename2);
        
        return $this->success('', [
            'url'   => (string) (url('index/download/export', ['file' => $exportCache->setFile($filename2)], true, true))
        ], 2, 1);
    }
    
    /**
     * @notes 下载导入失败的
     * @return Json
     * @author lbzy
     * @datetime 2024-04-12 17:37:41
     */
    function down2()
    {
        $exportCache    = new ExportCache();
        $src            = $exportCache->getSrc();
        if (!file_exists($src)) {
            mkdir($src, 0775, true);
        }
        
        $lists = OrderDeliveryBatchLogic::getFailInfoLists(input('id/d'));
        
        Excel::out($lists, [
            [ 'field' => 'sn', 'title' => '订单编号', 'excel_data_type' => DataType::TYPE_STRING ],
            [ 'field' => 'express_name', 'title' => '快递公司名称' ],
            [ 'field' => 'express_no', 'title' => '快递单号' ],
            [ 'field' => 'fail_content', 'title' => '失败原因' ],
        ],'', '', '导入失败', $src);
    
        return $this->success('', [
            'url'   => (string) (url('index/download/export', ['file' => $exportCache->setFile('导入失败.xlsx')], true, true))
        ], 2, 1);
    }
}