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
$db = class_db::getInstance();
$tasks = array();
// set the fields to retreive
$db->addFields('id');
$db->addFields('amount');
$db->addFields('memo');
$db->addFields('type');
$db->addFields('reminderDay');
$db->select('v_tasks_reminder');
$tasks = $db->fetchAllRows();

// get the current language - used for the calendar
$lang = $keys->getLang();

// get the requested task id
$id = $context->taskId;
// the transaction id
$trnId = $context->id;
// the back link
$backLink = $context->backLink;

// the email and sms flag
$smsFlag = "";
$emailFlag = "";
    
// load the task
if($id>0) $task = new class_task($id);
else $task = new class_task();

// load the transaction data if transaction id is given
if($trnId > 0) {
  $transactionFactory = class_transactionFactory::getInstance();
  $transaction = $transactionFactory->getTransaction($trnId);
  // overwrite the task with transaction data
  $task->memo = $transaction->payee;
  if($transaction->debit == "1") $task->amount = '-'.$transaction->amount;
  else $task->amount = '-'.$transaction->amount;
  $reminderDay = explode('-', $transaction->operationDate);
  $task->reminderDay = $reminderDay[2];
  }
?>