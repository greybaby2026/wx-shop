-- +----------------------------------------------------------------------
-- | 充值会员制（充值金额 → 永久购物折扣）—— 表结构变更
-- +----------------------------------------------------------------------
-- | 日期   ：2026-09-24
-- | 背景   ：充值金额不同对应不同折扣比例；账户「单笔充值」达到设定金额后，
-- |          永久享受购物折扣（按余额支付时生效）。
-- | 说明   ：全部为「新增列」，均有默认值，不改动任何现有数据，可安全灰度上线。
-- | 影响面 ：ls_recharge_template 加 1 列；ls_user 加 3 列；ls_order 加 1 列。
-- | 前置   ：无（列均不存在时可直接执行）
-- +----------------------------------------------------------------------

-- 【执行前检查】以下三条应返回「0 行」（表示列尚不存在）
-- SHOW COLUMNS FROM `ls_recharge_template` LIKE 'discount';
-- SHOW COLUMNS FROM `ls_user` LIKE 'recharge_discount';
-- SHOW COLUMNS FROM `ls_order` LIKE 'recharge_discount_amount';

-- 1) 充值档位：该档位对应的永久折扣率
--    10 = 不打折（可配置但无意义）；9.5 = 95 折；0 = 该档不享折扣
ALTER TABLE `ls_recharge_template`
  ADD COLUMN `discount` decimal(5,2) NOT NULL DEFAULT 0 COMMENT '永久折扣率(10=不打折,9.5=95折,0=不享折扣)';

-- 2) 用户：当前享有的充值会员折扣
--    冗余存储，避免每次下单回查充值历史；只升不降（数值越小折扣越优）
ALTER TABLE `ls_user`
  ADD COLUMN `recharge_discount` decimal(5,2) NOT NULL DEFAULT 0 COMMENT '当前充值会员折扣率(0=无折扣)',
  ADD COLUMN `recharge_discount_level_id` int(11) NOT NULL DEFAULT 0 COMMENT '达标档位ID(充值规则ID)',
  ADD COLUMN `recharge_discount_time` int(11) NOT NULL DEFAULT 0 COMMENT '达标时间戳';

-- 3) 订单：本单享受到的充值会员折扣额
--    独立记账，不并入 discount_amount（避免与优惠券口径混淆）
ALTER TABLE `ls_order`
  ADD COLUMN `recharge_discount_amount` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '充值会员折扣额';

-- 【执行后验证】期望：第一条 1 行；第二条 3 行；第三条 1 行
-- SHOW COLUMNS FROM `ls_recharge_template` LIKE 'discount';
-- SHOW COLUMNS FROM `ls_user` LIKE 'recharge_discount%';
-- SHOW COLUMNS FROM `ls_order` LIKE 'recharge_discount_amount';


-- +----------------------------------------------------------------------
-- | 回滚脚本（如需撤销本次变更，取消注释后执行）
-- +----------------------------------------------------------------------
-- ALTER TABLE `ls_recharge_template` DROP COLUMN `discount`;
-- ALTER TABLE `ls_user` DROP COLUMN `recharge_discount`, DROP COLUMN `recharge_discount_level_id`, DROP COLUMN `recharge_discount_time`;
-- ALTER TABLE `ls_order` DROP COLUMN `recharge_discount_amount`;
