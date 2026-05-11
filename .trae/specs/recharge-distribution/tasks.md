# 大团长充值分销功能 - 实施计划

## [x] Task 1: 创建充值佣金数据库表和模型
- **Priority**: P0
- **Depends On**: None
- **Description**: 
  - 创建 `ls_recharge_commission` 数据库表
  - 创建 `RechargeCommission` 模型
  - 字段：id, sn, recharge_order_id, recharge_order_sn, user_id, from_user_id, level, ratio, recharge_amount, earnings, status, settle_time, create_time
- **Acceptance Criteria Addressed**: AC-1, AC-5
- **Test Requirements**:
  - `programmatic` TR-1.1: 数据库表 ls_recharge_commission 存在且字段完整
  - `programmatic` TR-1.2: RechargeCommission 模型可正常创建和查询记录
- **Notes**: status 字段默认为1（已结算），因为充值佣金即时结算

## [x] Task 2: 新增 AccountLogEnum 枚举和配置项
- **Priority**: P0
- **Depends On**: None
- **Description**: 
  - 在 AccountLogEnum 中新增 BW_INC_RECHARGE_COMMISSION = 405
  - 将新枚举加入 BW_INC 常量数组
  - 在 getChangeTypeDesc 中添加描述
  - 新增充值分销配置项：recharge_distribution_open, recharge_distribution_first_ratio, recharge_distribution_second_ratio, recharge_distribution_max_commission
- **Acceptance Criteria Addressed**: AC-5, AC-6, AC-7, AC-8
- **Test Requirements**:
  - `programmatic` TR-2.1: AccountLogEnum::BW_INC_RECHARGE_COMMISSION 值为 405
  - `programmatic` TR-2.2: 405 在 BW_INC 数组中
  - `programmatic` TR-2.3: getChangeTypeDesc(405) 返回 '充值佣金增加可提现余额'
  - `programmatic` TR-2.4: ConfigService::get('recharge_distribution', 'open') 可读取配置
- **Notes**: 配置项存入 ls_config 表，使用 ConfigService 管理

## [x] Task 3: 创建充值佣金业务逻辑
- **Priority**: P0
- **Depends On**: Task 1, Task 2
- **Description**: 
  - 创建 RechargeCommissionLogic 类
  - 实现 settle($rechargeOrder) 方法：
    1. 检查充值分销开关是否开启
    2. 获取充值用户的上级信息（first_leader, second_leader）
    3. 计算一级佣金和二级佣金
    4. 检查单笔佣金上限
    5. 使用事务：增加上级 user_earnings + 记录 AccountLog + 创建 RechargeCommission 记录
    6. 无上级时直接返回
- **Acceptance Criteria Addressed**: AC-1, AC-2, AC-7, AC-8
- **Test Requirements**:
  - `programmatic` TR-3.1: 有上级用户充值后，上级 user_earnings 增加
  - `programmatic` TR-3.2: 无上级用户充值后，不产生佣金记录
  - `programmatic` TR-3.3: 开关关闭时充值不产生佣金
  - `programmatic` TR-3.4: 佣金超过上限时按上限发放
  - `programmatic` TR-3.5: 佣金计算使用 bcmath，精度正确
- **Notes**: 使用 Db::startTrans() 保证事务原子性

## [ ] Task 4: 修改充值成功回调，接入佣金逻辑
- **Priority**: P0
- **Depends On**: Task 3
- **Description**: 
  - 修改 PayNotifyLogic::recharge() 方法
  - 在现有充值成功逻辑后，调用 RechargeCommissionLogic::settle($order)
  - 佣金发放应在充值金额入账之后执行
- **Acceptance Criteria Addressed**: AC-1
- **Test Requirements**:
  - `programmatic` TR-4.1: 充值支付成功后，PayNotifyLogic::recharge() 被调用
  - `programmatic` TR-4.2: 充值成功后上级 user_earnings 增加
  - `programmatic` TR-4.3: 充值成功后 ls_recharge_commission 有记录
  - `programmatic` TR-4.4: 充值成功后 AccountLog 有 BW_INC_RECHARGE_COMMISSION 流水
- **Notes**: 佣金逻辑放在事务内，与充值入账保持原子性

## [ ] Task 5: 修改购物分销逻辑，排除余额支付订单
- **Priority**: P0
- **Depends On**: None
- **Description**: 
  - 修改 DistributionOrderGoodsLogic::add($orderId) 方法
  - 在方法开头检查订单支付方式
  - 如果 pay_way == PayEnum::BALANCE_PAY，直接 return 不创建分销订单
  - 其他支付方式正常走现有逻辑
- **Acceptance Criteria Addressed**: AC-3, AC-4
- **Test Requirements**:
  - `programmatic` TR-5.1: 余额支付订单不创建 DistributionOrderGoods 记录
  - `programmatic` TR-5.2: 微信支付订单正常创建 DistributionOrderGoods 记录
  - `programmatic` TR-5.3: 支付宝支付订单正常创建 DistributionOrderGoods 记录
- **Notes**: 只需在 add 方法开头加一个判断，改动最小

## [ ] Task 6: 后台充值分销配置管理
- **Priority**: P1
- **Depends On**: Task 2
- **Description**: 
  - 在后台充值设置页面新增充值分销配置区域
  - 配置项：开关、一级比例、二级比例、单笔上限
  - 复用现有 ConfigService 存取配置
  - 在 adminapi/RechargeLogic 中新增 getConfig/setConfig 方法
  - 在 adminapi/RechargeController 中新增对应接口
- **Acceptance Criteria Addressed**: AC-6, AC-7
- **Test Requirements**:
  - `programmatic` TR-6.1: 后台可读取充值分销配置
  - `programmatic` TR-6.2: 后台可修改充值分销配置
  - `programmatic` TR-6.3: 修改配置后立即生效
- **Notes**: 配置存储在 ls_config 表，group = 'recharge_distribution'

## [ ] Task 7: 后台充值佣金记录查看
- **Priority**: P1
- **Depends On**: Task 1
- **Description**: 
  - 创建 RechargeCommissionLists 列表类
  - 在 adminapi 中新增 RechargeCommissionController
  - 支持按时间、用户、层级筛选
  - 支持查看佣金统计汇总
- **Acceptance Criteria Addressed**: AC-9
- **Test Requirements**:
  - `programmatic` TR-7.1: 后台可查询充值佣金列表
  - `programmatic` TR-7.2: 列表支持分页
  - `human-judgement` TR-7.3: 列表展示信息完整（充值人、获得人、金额、比例、层级、时间）
- **Notes**: 参考现有 DistributionOrderGoodsLists 的实现模式

## [ ] Task 8: OpenAPI 新增充值佣金查询接口
- **Priority**: P2
- **Depends On**: Task 1, Task 3
- **Description**: 
  - 在 OpenApiLogic 中新增 getRechargeCommission 方法
  - 在 UserController 中新增 rechargeCommission 接口
  - 支持按用户ID查询充值佣金统计和明细
  - 更新 openapi_doc.html 文档
- **Acceptance Criteria Addressed**: AC-9
- **Test Requirements**:
  - `programmatic` TR-8.1: API 可查询用户充值佣金统计
  - `programmatic` TR-8.2: API 可查询用户充值佣金明细列表
  - `programmatic` TR-8.3: API 需要签名认证
- **Notes**: 参考现有 commission 接口的实现模式
