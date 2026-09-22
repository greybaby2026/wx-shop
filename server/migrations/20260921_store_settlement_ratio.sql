-- +----------------------------------------------------------------------
-- | Q3：结算基数改为「实际成交价 × 比例」—— 表结构变更
-- +----------------------------------------------------------------------
-- | 日期   ：2026-09-21
-- | 背景   ：经营决策 —— 结算基数以"小程序实际成交价"为准，按比例结算，
-- |          且支持「全局比例 + 按门店覆盖」两种粒度。
-- | 说明   ：原实现按 "协议供货价 → 成本价 → 0" 三级取价，但实测
-- |          supply_price 全库 0/1331 有值、cost_price 仅 1/1331 有值，
-- |          且后台无维护入口 → 金额恒为 0，无法出账。
-- | 影响面 ：ls_selffetch_shop 加 1 列；ls_store_settlement_order 加 2 列；
-- |          均有默认值，不改动任何现有数据，前端无需改动。
-- +----------------------------------------------------------------------

-- 【执行前检查】以下三条应返回「0 行」
-- SHOW COLUMNS FROM `ls_selffetch_shop` LIKE 'settlement_ratio';
-- SHOW COLUMNS FROM `ls_store_settlement_order` LIKE 'base_price';
-- SHOW COLUMNS FROM `ls_store_settlement_order` LIKE 'ratio';

-- 1) 门店结算比例（0 = 跟随全局配置）
ALTER TABLE `ls_selffetch_shop`
  ADD COLUMN `settlement_ratio` decimal(5,2) NOT NULL DEFAULT 0 COMMENT '结算比例(%)，0=跟随全局' AFTER `remark`;

-- 2) 结算明细：结算基数与当时比例（便于审计；比例日后被修改也能还原当时口径）
ALTER TABLE `ls_store_settlement_order`
  ADD COLUMN `base_price` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '结算基数(成交价总额)' AFTER `amount`,
  ADD COLUMN `ratio` decimal(5,2) NOT NULL DEFAULT 0 COMMENT '结算比例(%)' AFTER `base_price`;

-- 【执行后验证】期望：settlement_ratio / base_price / ratio 各返回 1 行
-- SHOW COLUMNS FROM `ls_selffetch_shop` LIKE 'settlement_ratio';
-- SHOW COLUMNS FROM `ls_store_settlement_order` LIKE '%price';
-- SHOW COLUMNS FROM `ls_store_settlement_order` LIKE 'ratio';


-- +----------------------------------------------------------------------
-- | 回滚脚本（如需撤销，取消注释后执行）
-- +----------------------------------------------------------------------
-- ALTER TABLE `ls_selffetch_shop`          DROP COLUMN `settlement_ratio`;
-- ALTER TABLE `ls_store_settlement_order`  DROP COLUMN `base_price`, DROP COLUMN `ratio`;
