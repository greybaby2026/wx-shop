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

namespace app\businessapi\controller;

use app\businessapi\lists\AddressLibraryLists;
use app\businessapi\logic\AddressLibraryLogic;
use app\businessapi\validate\AddressLibraryValidate;

class AddressLibraryController extends BaseBusinesseController
{
    /**
     * @notes 地址库列表
     * @return \think\response\Json
     * @author ljj
     * @date 2024/9/9 下午2:57
     */
    public function lists()
    {
        return $this->dataLists(new AddressLibraryLists());
    }

    /**
     * @notes 新增
     * @return \think\response\Json
     * @author ljj
     * @date 2024/9/9 下午3:18
     */
    public function add()
    {
        $params = (new AddressLibraryValidate())->post()->goCheck('add');
        (new AddressLibraryLogic())->add($params);
        return $this->success('添加成功',[],1,1);
    }

    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ljj
     * @date 2024/9/9 下午3:29
     */
    public function edit()
    {
        $params = (new AddressLibraryValidate())->post()->goCheck('edit');
        (new AddressLibraryLogic())->edit($params);
        return $this->success('修改成功',[],1,1);
    }

    /**
     * @notes 详情
     * @return \think\response\Json
     * @author ljj
     * @date 2024/9/9 下午3:33
     */
    public function detail()
    {
        $params = (new AddressLibraryValidate())->goCheck('detail');
        $result = (new AddressLibraryLogic())->detail($params);
        return $this->success('',$result);
    }

    /**
     * @notes 删除
     * @return \think\response\Json
     * @author ljj
     * @date 2024/9/9 下午3:40
     */
    public function del()
    {
        $params = (new AddressLibraryValidate())->post()->goCheck('del');
        (new AddressLibraryLogic())->del($params);
        return $this->success('删除成功',[],1,1);
    }

    /**
     * @notes 设置默认地址
     * @return \think\response\Json
     * @author ljj
     * @date 2024/9/9 下午3:53
     */
    public function default()
    {
        $params = (new AddressLibraryValidate())->post()->goCheck('default');
        (new AddressLibraryLogic())->default($params);
        return $this->success('操作成功',[],1,1);
    }
}