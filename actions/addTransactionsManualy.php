<?php
/**
* Load statistic data about transaction Credits
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

global $context;
$report = class_report::getInstance();
$db = class_db::getInstance();
$transactionFactory = class_transactionFactory::getInstance();

// get the transaction id if it is given
$id = $context->id;
if($id == null || $id == '') {
  $id = false;
  $transaction = $transactionFactory->getTransaction();
  }
else {
  $transaction = $transactionFactory->getTransaction($id);
  }
  
if($context->toDiv == false || $context->toDiv == 'null') {
  $toDiv = 'subAction1';
  }
else {
  $toDiv = $context->toDiv;
  }
  
$lang = $keys->getLang();
// small hack as JQuery does not handle the 'en' language as i18n...blank is 'en'!
if($lang == 'en') $lang='';

// get the categories
$db->addFields('id');
//$db->addFields('name');
//$db->addWhere('id', '!=', '1');
// execute
$db->select('v_categories');
while(($item = $db->fetchRow()) !== false) {
  $categories[] = $item['id'];
  }
?>