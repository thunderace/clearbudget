<?php
// modify the t_items table to support parenting
$sql[] = "ALTER TABLE t_items ADD COLUMN `parentId` INTEGER DEFAULT 0;";
$sql[] = "ALTER TABLE t_items ADD COLUMN `isParent` INTEGER DEFAULT 0;";

// modify the table views to add the extra fields
$sql[] = "DROP VIEW 'v_items';";
$sql[] = "CREATE  VIEW 'v_items' AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category,isParent, parentId, t_categories.name as categoryName, comments, deleteFlag, investmentFlag FROM t_items JOIN t_categories on t_items.category = t_categories.id WHERE deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC;";
$sql[] = "DROP VIEW 'v_items_unfiled';";
$sql[] = "CREATE  VIEW 'v_items_unfiled' AS  SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category, comments, isParent, parentId, t_categories.name as categoryName, comments FROM t_items JOIN t_categories on t_items.category = t_categories.id WHERE category = \"1\" AND deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC;";
$sql[] = "DROP VIEW 'v_suggestions_items';";
$sql[] = "CREATE  VIEW 'v_suggestions_items' AS  SELECT id, payee, memo,operationDate  FROM t_items WHERE deleteFlag=0 AND isParent=0 ORDER BY operationDate DESC;";
$sql[] = "DROP VIEW 'v_total_amount';";
$sql[] = "CREATE VIEW 'v_total_amount' AS SELECT count(amount) as count, total(amount) as total, strftime(\"%m\", operationDate) as month, strftime(\"%Y\", operationDate) as year, debit, category, isParent, parentId, name, color, maxAmountPerMonth, strftime(\"%m-%Y\", operationDate) as limitMonth, operationDate FROM t_items JOIN t_categories ON t_categories.id=t_items.category WHERE deleteFlag=0 and isParent=0 GROUP BY debit, category, strftime(\"%Y-%m\", operationDate) ORDER BY operationDate ASC;";
$sql[] = "DROP VIEW 'v_total_balance';";
$sql[] = "CREATE VIEW 'v_total_balance' AS SELECT total(amount) as total, debit FROM t_items WHERE deleteFlag=0 and isParent=0 GROUP BY debit;";
$sql[] = "DROP VIEW 'v_distinct_month';";
$sql[] = "CREATE VIEW 'v_distinct_month' AS SELECT distinct strftime(\"%Y-%m-01\", operationDate) as month FROM t_items WHERE deleteFlag=0 AND isParent=0 ORDER BY operationDate DESC;";

// new table
$sql[] = "CREATE TABLE 't_tasks_reminder' (`id` INTEGER PRIMARY KEY  NOT NULL , `memo` VARCHAR, `reminderDay` INTEGER, `amount` FLOAT, `createDate` DATETIME)";
// new view
$sql[] = "CREATE VIEW 'v_tasks_reminder' AS select id, memo, amount, reminderDay, createDate from t_tasks_reminder;";

// update the version to 0.9.5
$sql[] = "UPDATE t_version SET `cb_version` = '0.9.5' WHERE ID=1;";
?>