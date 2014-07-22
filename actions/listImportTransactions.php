<?php
/**
* List all transaction for a given imported file
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      actions
* @access       public
*/

/***********************************************************************

  Copyright (C) 2008  Fabrice Douteaud (admin@clearbudget.net)

    This file is part of ClearBudget.

    ClearBudget is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    ClearBudget is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with ClearBudget.  If not, see <http://www.gnu.org/licenses/>.


************************************************************************/
//prevent direct access
if(!defined('ENGINEON')) die('Direct access forbidden');
$db = class_db::getInstance();
$transactionFactory = class_transactionFactory::getInstance();

// get the import id
$importId = $context->id;
// get info on that import
$db->addFields('originalFileName');
$db->addFields('importDate');
$db->addFields('importType');
$db->addWhere('id', '=', $importId);
$db->select('t_imports');
$importInfo = $db->fetchRow();

// translate the import type
switch($importInfo['importType']) {
  case IMPORTQFXFILE:
    $importTypeKey = $keys->text_importTypeQFX;
  break;
  case IMPORTMANUAL:
    $importTypeKey = $keys->text_importTypeManual;
    $importInfo['originalFileName'] = 'n/a';
  break;
  default:
  case IMPORTQIFFILE:
    $importTypeKey = $keys->text_importTypeQIF;
  break;
  case IMPORTCSVFILE:
    $importTypeKey = $keys->text_importTypeCSV;
  break;
  }

// get the listOnly flag
$listOnly = $context->listOnly;
// get all transactions for the given id
$transactions = $transactionFactory->getImportedTransactions($importId);
?>