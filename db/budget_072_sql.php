<?php
$sql[] = "ALTER TABLE `t_imports` ADD COLUMN `importType` INTEGER;";

// new as of 0.8.1
$sql[] = "CREATE VIEW IF NOT EXISTS `v_suggestions_items` AS SELECT id, payee, memo,operationDate  FROM t_items WHERE deleteFlag=0 ORDER BY operationDate DESC;";

// update some view
$sql[] = "DROP VIEW IF EXISTS `v_distinct_month`";
$sql[] = "CREATE VIEW IF NOT EXISTS 'v_distinct_month' AS SELECT distinct strftime(\"%Y-%m-01\", operationDate) as month FROM t_items WHERE deleteFlag=0 ORDER BY operationDate DESC;";
$sql[] = "DROP VIEW IF EXISTS `v_items`";
$sql[] = "CREATE VIEW IF NOT EXISTS 'v_items' AS  SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category, t_categories.name as categoryName, comments, deleteFlag, investmentFlag FROM t_items JOIN t_categories on t_items.category = t_categories.id WHERE deleteFlag = 0 ORDER BY date(operationDate) DESC;";
$sql[] = "DROP VIEW IF EXISTS `v_items_unfiled`";
$sql[] = "CREATE VIEW IF NOT EXISTS 'v_items_unfiled' AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category, comments, t_categories.name as categoryName, comments FROM t_items JOIN t_categories on t_items.category = t_categories.id WHERE category = \"1\" AND deleteFlag = 0 ORDER BY date(operationDate) DESC;";
$sql[] = "DROP VIEW IF EXISTS `v_total_amount`";
$sql[] = "CREATE VIEW IF NOT EXISTS 'v_total_amount' AS  SELECT count(amount) as count, total(amount) as total, strftime(\"%m\", operationDate) as month, strftime(\"%Y\", operationDate) as year, debit, category, name, color, maxAmountPerMonth, strftime(\"%m-%Y\", operationDate) as limitMonth, operationDate FROM t_items JOIN t_categories ON t_categories.id=t_items.category WHERE deleteFlag=0 GROUP BY debit, category, strftime(\"%Y-%m\", operationDate) ORDER BY operationDate ASC;";

// update the version to 0.8.1
$sql[] = "UPDATE t_version SET `cb_version` = '0.8.1' WHERE 1=1 AND ID=1;";
?>