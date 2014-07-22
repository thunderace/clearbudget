<?php
// modify the t_task_reminder to support different types
$sql[] = "ALTER TABLE t_tasks_reminder ADD COLUMN `type` VARCHAR DEFAULT 'monthly';";

// modify the table views to add the extra fields
$sql[] = "DROP VIEW IF EXISTS `v_items`";
$sql[] = "CREATE VIEW `v_items` AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category,isParent, parentId, t_categories.name as categoryName, comments, deleteFlag, investmentFlag, importType FROM t_items JOIN t_categories on t_items.category = t_categories.id JOIN t_imports on t_items.importId = t_imports.id WHERE deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC";
$sql[] = "DROP VIEW IF EXISTS `v_items_unfiled`";
$sql[] = "CREATE VIEW `v_items_unfiled` AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category, comments, isParent, parentId, t_categories.name as categoryName, comments, importType FROM t_items JOIN t_categories on t_items.category = t_categories.id JOIN t_imports on t_items.importId = t_imports.id WHERE category = \"1\" AND deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC";
$sql[] = "DROP VIEW IF EXISTS `v_tasks_reminder`";
$sql[] = "CREATE VIEW `v_tasks_reminder` AS select id, memo, amount, reminderDay, type, createDate from t_tasks_reminder;";
// new table

// new view

// update the version to 0.9.6
$sql[] = "UPDATE t_version SET `cb_version` = '0.9.6' WHERE ID=1;";
?>