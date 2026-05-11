<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\shopapi\lists;

use app\common\model\User;
use app\common\model\UserTransfer;

/**
 * 转账记录列表
 * Class TransferLists
 * @package app\shopapi\lists
 */
class TransferLists extends BaseShopDataLists
{
    /**
     * @notes 设置搜索条件
     * @return void
     * @author lbzy
     * @datetime 2023-12-15 11:19:09
     */
    public function setSearch()
    {
        switch ($this->params['type'] ?? '') {
            // 转入
            case 'in':
                $this->searchWhere[] = [ 'ut.transfer_in', '=', $this->userId ];
                break;
            // 转出
            case 'out':
                $this->searchWhere[] = [ 'ut.transfer_out', '=', $this->userId ];
                break;
            // 转入 + 转出
            default:
                $this->searchWhere[] = [ 'ut.transfer_out|ut.transfer_in', '=', $this->userId ];
                break;
        }
        
        $this->searchWhere[] = [ 'u.user_delete', '=', 0 ];
    }

    /**
     * @notes 转账列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Tab
     * @date 2021/8/12 14:14
     */
    public function lists(): array
    {
        // 设置搜索
        $this->setSearch();

        $lists = User::alias('u')
            ->field('ut.id,ut.transfer_in,ut.transfer_out,ut.money,ut.money as money_desc,ut.create_time,u.nickname,u.avatar,u.sn')
            ->where($this->searchWhere)
            ->join('user_transfer ut', 'u.id=ut.transfer_out or u.id=ut.transfer_in')
            ->group('ut.id')
            ->limit($this->limitOffset, $this->limitLength)
            ->order('ut.id', 'desc')
            ->select()
            ->toArray();

        foreach($lists as &$item) {
            $this->format($item);
        }

        return $lists;
    }

    /**
     * @notes 转账记录数量
     * @return int
     * @author Tab
     * @date 2021/8/12 14:14
     */
    public function count(): int
    {
        // 设置搜索
        $this->setSearch();

        $count = User::alias('u')
            ->join('user_transfer ut', 'u.id=ut.transfer_out or u.id=ut.transfer_in')
            ->group('ut.id')
            ->where($this->searchWhere)
            ->count();

        return $count;
    }
    
    /**
     * @notes notes
     * @param $item
     * @return void
     * @author lbzy
     * @datetime 2023-12-15 11:14:12
     */
    public function format(&$item)
    {
        // 转入
        if($item['transfer_in'] == $this->userId) {
            $item['money_desc'] = '+' . $item['money'];
        }
        // 转出
        if($item['transfer_out'] == $this->userId) {
            $item['money_desc'] = '-' . $item['money'];
        }
        
        $item['nickname'] = '转账-' . $item['nickname'];
    }
}
