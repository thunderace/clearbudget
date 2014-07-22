<?php
/**
* save a transaction details
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
$db = class_db::getInstance();
$transactionFactory = class_transactionFactory::getInstance();

// by default, there is an error
$return['error'] = true;
$return['msg'] = $keys->error_ImproperCall;

// a flag to warn us if date is wrong
$invalidDateFormat = false;
 
// get today's date
$today = date("Y-m-d");

// get the user input and do some checking
$trnCategory = $context->trnCategory;
// if category is unknown, we default to 1 as this is uncategorized
if(!$trnCategory || $trnCategory == null || strtolower($trnCategory)=='null' ) $trnCategory = 1;
$trnId = clean($context->id);
$trnDate = formatManualDate($context->trnDate, $invalidDateFormat);
$trnAmount = cleanAmount($context->trnAmount);
$trnPayee = clean($context->trnPayee);
$trnComments = clean($context->trnComments);
$trnMemo = clean($context->trnMemo);
$trnDebit = clean($context->trnDebit);
//$magic = md5($trnDate.$trnAmount.$trnComment);

// instantiate a transaction object
if($trnId > 0) {
  $transaction = & $transactionFactory->getTransaction($trnId);
  $importId = $transaction->importId;
  }
else {
  $transaction = & $transactionFactory->getTransaction();
}

// if transaction is editable, all fields must be valid
// else values are defaulted from existing values (no matter what the user entered
if($transaction->editable) {
  // value credit/debit flag
  if($context->trnDebit != '0' && $context->trnDebit != '1') {
    $return['error'] = true;
    $return['msg'] = $keys->failure;
    return;
    }

  // check if date was given
  if($context->trnDate == false || $context->trnDate == 'null') {
    $return['error'] = true;
    $return['msg'] = $keys->error_ImproperDate;
    return;
    }
  
  // check for invalid date
  if($invalidDateFormat) {
    $return['error'] = true;
    $return['msg'] = $keys->error_ImproperDate.' ('.$trnDate.')';
    return;
    }

  // check for an amount
  if($trnAmount == false ) {
    $return['error'] = true;
    $return['msg'] = $keys->error_ImproperAmount;
    return;
    }

  // check for a valid payee
  if($trnPayee == false ) {
    $return['error'] = true;
    $return['msg'] = $keys->error_ImproperPayee;
    return;
    }
  }
 else {
  $trnDate = $transaction->operationDate;
  $trnAmount = $transaction->amount;
  $trnPayee =$transaction->payee;
  $trnMemo =$transaction->memo;
  $trnDebit = $transaction->debit;
  }

if($trnId > 0) {
  $newImport = false;
  $edit = true;
  }
else {
  $edit = false;
  // get the import ID if there is one already for today
  $db->addFields('id');
  $db->addFields('importCount');
  $db->addWhere('importDate', '=', $today);
  $db->addWhere('importType', '=', IMPORTMANUAL);
  $db->select('t_imports');
  $importData = $db->fetchRow();
  if(is_array($importData)) {
    $importId = $importData['id'];
    $insertCounter = $importData['importCount'];
    class_debug::addMsg(__FILE__, 'importId for '.$today.' is '.$importId, DEBUGDEBUG);
    $newImport = false;
    }
  else {
    $insertCounter = 0;
    // create a new one
    class_debug::addMsg(__FILE__, 'Creating new import for '.$today, DEBUGDEBUG);
    $db->addFields('originalFileName', '');
    $db->addFields('importDate', $today);
    $db->addFields('importType', IMPORTMANUAL);
    $importId = $db->insert('t_imports');
    $newImport = true;
    class_debug::addMsg(__FILE__, 'new importId is '.$importId, DEBUGDEBUG);
    }
  }
 
// set the object with all the values
$transaction->category = $trnCategory;
$transaction->operationDate = $trnDate;
$transaction->amount = $trnAmount;
$transaction->payee = $trnPayee;
$transaction->memo = $trnMemo;
$transaction->comments = $trnComments;
$transaction->debit = $trnDebit;
$transaction->importId = $importId;
if(!$edit) $transaction->createDate = $today;
$transaction->cleared = '*';
// try to save the object on the DB
$result = $transaction->save();
// check the result (maybe duplicate or not enough data)
if($result == 1) {
  // build the return message
  $return['msg'] = $keys->text_importedTransaction;
  //$return['msg'] .= ' ('.class_currency::getCurrencySymbol().' '.$trnAmount.' - '.$trnComment.' - '.$trnDate.')';
  $return['error'] = false;
  class_debug::addMsg(__FILE__, 'inserted new transaction', DEBUGDEBUG);
  if(!$edit) {
    // increment the counter
    $insertCounter++;
    // update the import counter
    $db->addFields('importCount', $insertCounter);
    $db->addFields('importDuplicate', '0');
    $db->addWhere('id', '=', $importId);
    $db->update('t_imports');
    class_debug::addMsg(__FILE__, 'imports table update with one more inserts ('.$insertCounter.')', DEBUGDEBUG);
    }
  }
else {
   // build the return message
   $return['msg'] = $keys->text_duplicatedRecords;
   $return['error'] = true;
   class_debug::addMsg(__FILE__, 'Transaction already exists', DEBUGDEBUG);
   // we need to delete the import ID if that was a newly created one
   if($newImport) {
    $db->addWhere('id', '=', $importId);
    $db->delete('t_imports');
    class_debug::addMsg(__FILE__, 'deleting import '.$importId, DEBUGDEBUG);
    }
  }
?>