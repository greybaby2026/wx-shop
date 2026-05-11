# 提现流程优化 Spec

## Why
当前提现流程存在多个体验和技术问题：商家转账V3确认收款流程复杂、定时任务未处理WAIT_USER_CONFIRM状态导致用户可能永久卡在"待收款"、金额计算存在浮点精度bug、V3签名随机数实现有误、管理员审核操作不够直观。这些问题直接影响用户提现体验和资金安全。

## What Changes
- 修复V3签名随机数bug（rand字符串参数→mt_rand整数参数）
- 修复金额计算浮点精度问题（统一使用bcmath）
- 定时任务增加WAIT_USER_CONFIRM状态处理和超时自动关闭机制
- 统一V3查询逻辑（消除search()和定时任务中的代码重复）
- receive方法增加事务保护防止竞态
- 管理端审核操作优化（通过/拒绝分离为独立按钮）
- 小程序端确认收款体验优化（进入详情页主动弹窗提示收款）
- 增加提现风控（单笔限额校验、每日总额限制）
- 增加提现操作日志记录

## Impact
- Affected specs: 提现申请、审核、转账、确认收款全流程
- Affected code: 
  - `server/app/adminapi/logic/withdraw/WithdrawLogic.php`
  - `server/app/adminapi/logic/withdraw/WechatMerchantTransferLogic.php`
  - `server/app/shopapi/logic/WithdrawLogic.php`
  - `server/app/common/command/WechatMerchantTransfer.php`
  - `web/src/components/finance/withdraw-pane.vue`
  - `uniapp/bundle/pages/withdraw_success/withdraw_success.vue`

## ADDED Requirements

### Requirement: V3签名随机数修复
系统在生成微信V3签名时 SHALL 使用 `mt_rand(10000, 99999)` 生成随机数，而非 `rand('10000','99999')`。

#### Scenario: V3签名生成
- **WHEN** 系统调用微信V3商家转账接口
- **THEN** 随机数字符串使用mt_rand生成，确保为整数参数

### Requirement: 金额计算精度修复
系统在计算提现手续费和实际到账金额时 SHALL 使用bcmath函数（bcmul/bcdiv/bcsub），避免浮点运算精度丢失。

#### Scenario: 手续费计算
- **WHEN** 用户申请提现100元，手续费比例10%
- **THEN** 手续费使用 `bcmul(bcdiv($money, 100, 4), $percentage, 2)` 计算

#### Scenario: 实际到账金额计算
- **WHEN** 提现金额100元，手续费10元
- **THEN** 实际到账使用 `bcsub($money, $handlingFee, 2)` 计算

### Requirement: WAIT_USER_CONFIRM状态处理
定时任务 SHALL 处理微信V3转账的WAIT_USER_CONFIRM状态，对于超过72小时未确认的记录自动关闭并回退金额。

#### Scenario: 用户长时间未确认收款
- **WHEN** 提现记录状态为ING，微信返回WAIT_USER_CONFIRM，且创建时间超过72小时
- **THEN** 系统自动将状态更新为FAIL，回退用户可提现余额，记录账户流水

#### Scenario: 用户短时间内未确认收款
- **WHEN** 提现记录状态为ING，微信返回WAIT_USER_CONFIRM，且创建时间未超过72小时
- **THEN** 系统保持状态不变，等待用户确认

### Requirement: 统一V3查询逻辑
系统 SHALL 将search()方法和定时任务中的V3查询逻辑抽取为公共方法，消除代码重复。

#### Scenario: 管理员手动查询
- **WHEN** 管理员点击"查询结果"按钮
- **THEN** 调用统一的V3查询公共方法

#### Scenario: 定时任务自动查询
- **WHEN** 定时任务扫描到提现中的记录
- **THEN** 调用同一公共方法查询微信状态

### Requirement: 确认收款事务保护
系统在用户确认收款时 SHALL 将查询微信状态和更新数据库操作包裹在数据库事务中，防止与定时任务竞态。

#### Scenario: 用户确认收款与定时任务并发
- **WHEN** 用户点击确认收款的同时定时任务也在更新状态
- **THEN** 事务保证只有一个操作成功更新状态，不会重复加余额

### Requirement: 管理端审核操作优化
管理端提现列表中 SHALL 将审核通过和审核拒绝分为两个独立按钮，避免误操作。

#### Scenario: 管理员审核通过
- **WHEN** 管理员点击"通过"按钮
- **THEN** 弹出确认框，确认后直接执行审核通过操作

#### Scenario: 管理员审核拒绝
- **WHEN** 管理员点击"拒绝"按钮
- **THEN** 弹出输入框填写拒绝原因，确认后执行审核拒绝操作

### Requirement: 小程序确认收款体验优化
小程序端 SHALL 在用户进入提现详情页时，如果检测到待收款状态，主动弹出收款确认弹窗。

#### Scenario: 用户进入待收款详情页
- **WHEN** 用户打开提现详情页，且该记录状态为"待收款"
- **THEN** 页面加载完成后自动弹出微信收款确认弹窗

### Requirement: 提现风控增强
系统 SHALL 增加单笔提现限额校验和每日提现总额限制。

#### Scenario: 单笔提现超额
- **WHEN** 用户申请提现金额超过后台配置的单笔最高限额
- **THEN** 系统拒绝申请并提示"单笔提现金额不能超过XXX元"

#### Scenario: 每日提现总额超限
- **WHEN** 用户当日已提现总额+本次申请金额超过后台配置的每日最高限额
- **THEN** 系统拒绝申请并提示"今日提现总额已达上限"

### Requirement: 提现操作日志
系统 SHALL 记录管理员的审核、转账等操作日志，包含操作人、操作时间、操作类型、操作内容。

#### Scenario: 管理员审核通过
- **WHEN** 管理员审核通过一笔提现
- **THEN** 系统记录操作日志：操作人ID、操作时间、操作类型"审核通过"、提现单号

## MODIFIED Requirements

### Requirement: 定时任务转账状态查询
定时任务 SHALL 同时处理V2和V3通道的转账状态查询，并增加WAIT_USER_CONFIRM状态处理和超时自动关闭逻辑。原逻辑仅处理SUCCESS和FAIL状态。
