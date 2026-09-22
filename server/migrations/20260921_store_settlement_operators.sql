-- +----------------------------------------------------------------------
-- | 门店结算中心：新增「确认操作人」「标记付款操作人」两列
-- +----------------------------------------------------------------------
-- | 日期   ：2026-09-21
-- | 背景   ：结算后台原仅记录 confirm_time / pay_time，无法追溯操作人。
-- |          经营模式确认为「线下付款 + 后台登记」，必须在系统内留痕
-- |          "谁确认的账单、谁标记的已付款"，否则出账无法追责。
-- | 影响面 ：ls_store_settlement 增加 2 列，均有默认值 0；
-- |          不改动任何现有行数据，前端无需改动。
-- | 前置   ：无（结算表当前 0 行，随时可执行）
-- +----------------------------------------------------------------------

-- 【执行前检查】以下两条应返回「0 行」（表示列尚不存在）
-- SHOW COLUMNS FROM `ls_store_settlement` LIKE 'confirm_admin_id';
-- SHOW COLUMNS FROM `ls_store_settlement` LIKE 'pay_admin_id';

ALTER TABLE `ls_store_settlement`
  ADD COLUMN `confirm_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '确认操作人ID(后台管理员)' AFTER `confirm_time`,
  ADD COLUMN `pay_admin_id`     int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '标记付款操作人ID(后台管理员)' AFTER `pay_time`;

-- 【执行后验证】期望返回 2 行
-- SHOW COLUMNS FROM `ls_store_settlement` LIKE '%admin_id';


-- +----------------------------------------------------------------------
-- | 回滚脚本（如需撤销本次变更，取消注释后执行）
-- +----------------------------------------------------------------------
-- ALTER TABLE `ls_store_settlement`
--   DROP COLUMN `confirm_admin_id`,
--   DROP COLUMN `pay_admin_id`;
