-- +----------------------------------------------------------------------
-- | 充值记录展示「折扣升级」—— 表结构变更（U9）
-- +----------------------------------------------------------------------
-- | 日期   ：2026-09-24
-- | 背景   ：充值成功后按档位授予永久折扣（只升不降）。用户需要在「充值记录」里
-- |          看到「哪一笔充值带来了折扣升级」，而不是只看到一笔金额。
-- | 方案   ：在充值订单上记录该笔充值「前 / 后」的折扣率，由服务端在授予折扣时写入。
-- |          discount_upgrade 由查询时推导（after>0 且 由无到有 或 折扣数值变小），
-- |          不额外冗余存储，避免两处口径不一致。
-- | 说明   ：全部为「新增列」，均有默认值，不改动任何现有数据，可安全灰度上线。
-- |          历史充值记录保持 0/0（不显示升级标记，属预期）。
-- | 影响面 ：ls_recharge_order 加 2 列。
-- | 前置   ：无（列均不存在时可直接执行）
-- +----------------------------------------------------------------------

-- 【执行前检查】以下两条应返回「0 行」（表示列尚不存在）
-- SHOW COLUMNS FROM `ls_recharge_order` LIKE 'discount_before';
-- SHOW COLUMNS FROM `ls_recharge_order` LIKE 'discount_after';

-- 1) 充值前 / 充值后的折扣率
--    0 = 无折扣；10 = 不打折（可配置但无意义）；9.5 = 95 折（数值越小折扣越优）
ALTER TABLE `ls_recharge_order`
  ADD COLUMN `discount_before` decimal(5,2) NOT NULL DEFAULT 0 COMMENT '本笔充值前折扣率(0=无折扣)',
  ADD COLUMN `discount_after` decimal(5,2) NOT NULL DEFAULT 0 COMMENT '本笔充值后折扣率(与充值前相同表示未升级)';

-- 【执行后验证】期望：第一条 1 行；第二条 1 行
-- SHOW COLUMNS FROM `ls_recharge_order` LIKE 'discount_before';
-- SHOW COLUMNS FROM `ls_recharge_order` LIKE 'discount_after';


-- +----------------------------------------------------------------------
-- | 回滚脚本（如需撤销本次变更，取消注释后执行）
-- +----------------------------------------------------------------------
-- ALTER TABLE `ls_recharge_order` DROP COLUMN `discount_before`, DROP COLUMN `discount_after`;
