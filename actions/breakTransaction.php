<?php
/**
* load a parent transaction to create sub-transactions
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
// instantiate the transaction factory
$transactionFactory = class_transactionFactory::getInstance();
// get a report object
$report = class_report::getInstance();
// Array of child transactions
$children = array();

// get the requested transaction id
$id = $context->id;
// load the transaction
$transaction = $transactionFactory->getTransaction($id);

// if the transaction is a parent retreive its childs
// if the transaction is a child, retreive its parent and brothers
if($transaction->isParent == 1) {
  // get the children
  $tmp = $transactionFactory->getChildTransactions($id);
  foreach($tmp as $tid=>$trn) {
    $children[] = $trn;
    }
  }
elseif($transaction->parentId > 0) {
  $parentId = $transaction->parentId;
  $transaction = $transactionFactory->getTransaction($parentId);
  // get the brothers
  $tmp = $transactionFactory->getChildTransactions($parentId);
  foreach($tmp as $tid=>$trn) {
    $children[] = $trn;
    }
  }

// fill up the $children array with empty transactions as needed
for($i=count($children);$i<5;$i++) {
  $children[$i] = $transactionFactory->getTransaction();
  }

// build a category tree
foreach($report->categories as $id=>$name) {
  $categories[$id] = $report->getCategoryName($id);
  }
?>