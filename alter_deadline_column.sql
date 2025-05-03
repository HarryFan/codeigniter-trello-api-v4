-- 將 cards 表的 deadline 欄位從 date 修改為 datetime 類型
ALTER TABLE `cards` 
MODIFY COLUMN `deadline` datetime DEFAULT NULL;
