# 大团长充值分销功能 - 产品需求文档

## Overview
- **Summary**: 在现有将军世家电商系统中新增"大团长充值分销"功能。用户充值后即时给上级发放佣金到可提现余额，充值资金通过余额支付购物时不产生购物分销佣金，防止双重佣金。采用方案B：余额支付订单不产生购物分销佣金，无需新增 recharge_balance 字段。
- **Purpose**: 支持大团长加入需预充值5000元的门槛计划，激励上级推广充值，同时防止资金漏洞造成亏损。
- **Target Users**: 大团长（充值用户）、上级分销商（获得佣金）、平台管理员（配置佣金规则）

## Goals
- 充值成功后即时给上级发放佣金到可提现余额（user_earnings）
- 支持一级和二级分销佣金，比例后台可配置
- 余额支付的订单不产生购物分销佣金
- 充值不可退款，消除退款套利风险
- 所有资金流转有完整的流水记录

## Non-Goals (Out of Scope)
- 不创建 recharge_balance 新字段（方案B不需要）
- 不修改现有余额支付逻辑（BalancePayService）
- 不修改现有退款逻辑
- 不修改现有转账逻辑
- 不实现充值退款功能
- 不在小程序端新增充值分销相关页面

## Background & Context
- 现有系统基于 likeshop 开源电商系统，ThinkPHP 6.0 框架
- 现有分销系统支持二级分销，佣金在订单确认收货后经结算周期才到账
- 充值功能已有，充值金额进入 user_money（不可提现余额）
- 余额支付只扣减 user_money，不涉及 user_earnings
- 系统不支持混合支付（余额+第三方组合）
- 转账只操作 user_money，不涉及 user_earnings
- 退款退回 user_money 或原路退回

## Functional Requirements
- **FR-1**: 充值支付成功后，即时计算并发放佣金给上级分销商
- **FR-2**: 一级佣金比例后台可配置，默认20%
- **FR-3**: 二级佣金比例后台可配置，默认5%
- **FR-4**: 充值佣金即时到账到上级的 user_earnings（可提现余额），无需等待结算周期
- **FR-5**: 余额支付（pay_way=BALANCE_PAY）的购物订单不创建分销订单记录，不产生购物分销佣金
- **FR-6**: 微信/支付宝等外部支付的购物订单正常产生购物分销佣金（现有逻辑不变）
- **FR-7**: 充值佣金记录独立存储在 ls_recharge_commission 表中
- **FR-8**: 充值佣金发放时记录账户流水（AccountLog）
- **FR-9**: 后台可配置充值分销开关（全局开关）
- **FR-10**: 后台可配置单笔佣金上限（防止异常大额）
- **FR-11**: 用户无上级时不发放佣金
- **FR-12**: 上级不是分销商时仍可发放充值佣金（充值佣金不要求上级是分销商）

## Non-Functional Requirements
- **NFR-1**: 充值佣金即时结算，响应时间不超过2秒
- **NFR-2**: 佣金计算精确到分，使用 bcmath 函数避免浮点误差
- **NFR-3**: 所有资金操作使用数据库事务，保证原子性
- **NFR-4**: 佣金发放需记录完整流水，便于对账和审计

## Constraints
- **Technical**: PHP 7.1+，ThinkPHP 6.0，MySQL
- **Business**: 充值不可退款（现有系统不支持充值退款）
- **Dependencies**: 依赖现有 User、Distribution、RechargeOrder 等模型

## Assumptions
- 充值不可退款，因此充值佣金即时结算无风险
- 余额支付的订单全部不产生购物分销佣金，包括非充值来源的余额
- 自购充值不返佣（充值是消费行为，不是推广行为）
- 佣金提现到余额后再购物，仍不产生购物分销佣金（余额支付统一规则）

## Acceptance Criteria

### AC-1: 充值成功即时发放佣金
- **Given**: 用户A有上级B（一级）和上上级C（二级），充值分销功能已开启
- **When**: 用户A充值5000元并支付成功
- **Then**: 上级B的 user_earnings 即时增加 5000×20%=1000元，上上级C的 user_earnings 即时增加 5000×5%=250元，ls_recharge_commission 表有2条记录
- **Verification**: `programmatic`

### AC-2: 无上级时不发放佣金
- **Given**: 用户A没有上级（first_leader=0）
- **When**: 用户A充值5000元并支付成功
- **Then**: 不产生任何佣金记录，user_earnings 无变化
- **Verification**: `programmatic`

### AC-3: 余额支付不产生购物分销佣金
- **Given**: 用户A用余额支付购物1000元
- **When**: 订单支付成功
- **Then**: 不创建 DistributionOrderGoods 记录，不产生购物分销佣金
- **Verification**: `programmatic`

### AC-4: 外部支付正常产生购物分销佣金
- **Given**: 用户A用微信支付购物1000元
- **When**: 订单支付成功
- **Then**: 正常创建 DistributionOrderGoods 记录，产生购物分销佣金（与现有逻辑一致）
- **Verification**: `programmatic`

### AC-5: 充值佣金有完整流水记录
- **Given**: 充值分销功能已开启
- **When**: 用户A充值成功并触发佣金发放
- **Then**: AccountLog 表中有对应的 BW_INC_RECHARGE_COMMISSION 流水记录
- **Verification**: `programmatic`

### AC-6: 后台可配置佣金比例
- **Given**: 管理员登录后台
- **When**: 修改一级佣金比例为15%，二级佣金比例为3%
- **Then**: 后续充值按新比例计算佣金
- **Verification**: `programmatic`

### AC-7: 充值分销开关可关闭
- **Given**: 管理员关闭充值分销开关
- **When**: 用户充值成功
- **Then**: 不产生任何充值佣金
- **Verification**: `programmatic`

### AC-8: 单笔佣金上限生效
- **Given**: 单笔佣金上限配置为500元，一级佣金比例20%
- **When**: 用户充值5000元，一级佣金计算为1000元
- **Then**: 实际发放佣金为500元（受上限限制）
- **Verification**: `programmatic`

### AC-9: 充值佣金记录可查询
- **Given**: 存在充值佣金记录
- **When**: 管理员在后台查看充值佣金列表
- **Then**: 能看到佣金明细（充值人、获得人、金额、比例、层级、时间等）
- **Verification**: `human-judgment`

## Open Questions
- [x] 充值退款场景如何处理？→ 不支持充值退款
- [x] 转账是否允许充值余额参与？→ 方案B不涉及，余额支付统一不产佣金
- [x] 佣金提现到余额后再购物是否产佣金？→ 不产，余额支付统一规则
