<?php
// modify tables
//$sql[] = "ALTER TABLE t_categories ADD COLUMN `parentId` INTEGER DEFAULT 0;";

// modify views

// new table

// new view
//$sql[] = "DROP VIEW IF EXISTS `v_items`";
//$sql[] = "CREATE VIEW `v_items` AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category,isParent, t_items.parentId, t_categories.parentId as catParentId, t_categories.name as categoryName, comments, deleteFlag, investmentFlag, importType, importedBy, modifiedBy FROM t_items JOIN t_categories on t_items.category = t_categories.id JOIN t_imports on t_items.importId = t_imports.id WHERE deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC";
//$sql[] = "DROP VIEW IF EXISTS `v_items_unfiled`";
//$sql[] = "CREATE VIEW `v_items_unfiled` AS SELECT t_items.id as id, amount, debit, payee, memo, operationDate, category, comments, isParent, t_items.parentId, t_categories.parentId as catParentId, t_categories.name as categoryName, comments, importType, importedBy, modifiedBy FROM t_items JOIN t_categories on t_items.category = t_categories.id JOIN t_imports on t_items.importId = t_imports.id WHERE category = \"1\" AND deleteFlag = 0 AND isParent = 0 ORDER BY date(operationDate) DESC";
//$sql[] = "DROP VIEW IF EXISTS `v_total_amount`";
//$sql[] = "CREATE VIEW `v_total_amount` AS SELECT count(amount) as count, total(amount) as total, strftime(\"%m\", operationDate) as month, strftime(\"%Y\", operationDate) as year, debit, category, isParent, t_items.parentId, t_categories.parentId as catParentId, name, color, maxAmountPerMonth, strftime(\"%m-%Y\", operationDate) as limitMonth, operationDate FROM t_items JOIN t_categories ON t_categories.id=t_items.category WHERE deleteFlag=0 and isParent=0 GROUP BY debit, category, strftime(\"%Y-%m\", operationDate) ORDER BY operationDate ASC;";

// new indeces

// update the version to 0.9.8
$sql[] = "UPDATE t_version SET `cb_version` = '0.9.8' WHERE ID=1;";
?>