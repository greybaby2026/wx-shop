# Tasks

- [ ] Task 1: 修复V3签名随机数bug和金额计算精度问题
  - [ ] 1.1: 修复 WechatMerchantTransferLogic.php 中 `rand('10000','99999')` 改为 `mt_rand(10000, 99999)`
  - [ ] 1.2: 修复 shopapi/WithdrawLogic.php 中手续费计算，改用 `bcmul(bcdiv($money, 100, 4), $percentage, 2)`
  - [ ] 1.3: 修复 shopapi/WithdrawLogic.php 中 left_money 计算，改用 `bcsub($money, $handlingFee, 2)`

- [ ] Task 2: 统一V3查询逻辑，抽取公共方法
  - [ ] 2.1: 在 WechatMerchantTransferLogic.php 中新增 `queryAndUpdateStatus()` 公共方法，封装V3查询+状态更新逻辑
  - [ ] 2.2: 重构 adminapi/WithdrawLogic.php 的 `search()` 方法，调用公共方法
  - [ ] 2.3: 重构 WechatMerchantTransfer.php 定时任务，调用公共方法

- [ ] Task 3: 定时任务增加WAIT_USER_CONFIRM处理和超时自动关闭
  - [ ] 3.1: 在公共查询方法中增加WAIT_USER_CONFIRM状态判断
  - [ ] 3.2: 新增超时自动关闭逻辑：创建时间超过72小时的WAIT_USER_CONFIRM记录自动标记FAIL并回退金额
  - [ ] 3.3: 在提现配置中增加超时时间配置项（默认72小时）

- [ ] Task 4: receive方法增加事务保护
  - [ ] 4.1: 在 shopapi/WithdrawLogic.php 的 `receive()` 方法中，将查询微信状态+更新数据库包裹在Db事务中
  - [ ] 4.2: 增加乐观锁或状态二次校验，防止并发重复确认

- [ ] Task 5: 管理端审核操作优化
  - [ ] 5.1: 修改 withdraw-pane.vue，将"审核"按钮拆分为"通过"和"拒绝"两个独立按钮
  - [ ] 5.2: "通过"按钮弹出简单确认框
  - [ ] 5.3: "拒绝"按钮弹出输入框填写拒绝原因
  - [ ] 5.4: 重新构建前端并部署

- [ ] Task 6: 小程序确认收款体验优化
  - [ ] 6.1: 修改 withdraw_success.vue，在页面加载时检测待收款状态自动弹出收款确认
  - [ ] 6.2: 增加自动重试逻辑，收款弹窗失败后允许用户手动重试

- [ ] Task 7: 提现风控增强
  - [ ] 7.1: 在提现配置中增加"单笔最高提现金额"和"每日最高提现总额"配置项
  - [ ] 7.2: 在 shopapi/WithdrawLogic.php 的 `apply()` 方法中增加单笔限额校验
  - [ ] 7.3: 增加每日提现总额校验（查询当日已申请提现总额）
  - [ ] 7.4: 前端提现配置页面增加对应配置项
  - [ ] 7.5: 重新构建前端并部署

- [ ] Task 8: 提现操作日志
  - [ ] 8.1: 创建 withdraw_operation_log 数据表迁移（操作人ID、操作时间、操作类型、提现单号、操作内容）
  - [ ] 8.2: 在审核通过、审核拒绝、转账成功、转账失败等操作中记录日志
  - [ ] 8.3: 管理端提现详情中展示操作日志

# Task Dependencies
- [Task 2] depends on [Task 1] (修复bug后再重构)
- [Task 3] depends on [Task 2] (公共方法抽取后再增加新逻辑)
- [Task 4] is independent
- [Task 5] is independent
- [Task 6] is independent
- [Task 7] is independent
- [Task 8] is independent
