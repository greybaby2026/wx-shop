<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class WithdrawOperationLog extends BaseModel
{
    protected $name = 'withdraw_operation_log';

    protected $append = ['action_desc'];

    public function getActionDescAttr($value, $data)
    {
        $desc = [
            'pass' => '审核通过',
            'refuse' => '审核拒绝',
            'transfer_success' => '转账成功',
            'transfer_fail' => '转账失败',
            'search' => '查询转账结果',
        ];
        return $desc[$data['action']] ?? '';
    }

    public static function addLog($withdrawId, $sn, $action, $content = '')
    {
        $adminId = session('admin_id') ?? 0;
        $adminName = session('admin_name') ?? '系统';

        return self::create([
            'withdraw_id' => $withdrawId,
            'sn' => $sn,
            'operator_id' => $adminId,
            'operator_name' => $adminName,
            'action' => $action,
            'content' => $content,
            'create_time' => time(),
        ]);
    }
}
