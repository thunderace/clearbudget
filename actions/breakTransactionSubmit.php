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

// by default, there is an error
$return['error'] = true;
$return['msg'] = '1'.$keys->error_ImproperCall;
$return['sum'] = 0;

// instantiate the transaction factory
$transactionFactory = class_transactionFactory::getInstance();

// some variables
$sum = 0; // the sum of all amounts
$transactions = array(); // array of child transactions
$insertedTransactions = array(); // array of new inserted child transactions - used for rollback

// get the form data
$parentTransactionId = $context->parentTransactionId;
for($i=0; $i<5;$i++) {
  $trn['id'] = $context->{'trnid'.$i};
  $trn['payee'] = $context->{'trnpayee'.$i};
  $trn['memo'] = $context->{'trnmemo'.$i};
  $trn['amount'] = $context->{'trnamount'.$i};
  $trn['category'] = $context->{'trncategory'.$i};
  // validate for amount and payee
  if(strlen($trn['payee']) > 0 && is_numeric($trn['amount']) && $trn['amount'] > 0) {
    // add this to the transactions array
    $transactions[] = $trn;
    // compute the sum of child amount
    $sum += $trn['amount'];
    }
  else {
    if(strlen($trn['payee']) > 0 && (!is_numeric($trn['amount']) || $trn['amount'] <= 0)) {
      $return['error'] = true;
      $return['msg'] = sprintf($keys->error_WrongTransactionChildDetails, $i+1);
      return;
      }
    if(is_numeric($trn['amount']) && $trn['amount'] > 0 && ($trn['payee'] == null || $trn['payee'] == '')) {
      $return['error'] = true;
      $return['msg'] = sprintf($keys->error_WrongTransactionChildDetails, $i+1);
      return;
      }
    }
  }

// validate the parent id
if(!isset($parentTransactionId)) {
  $return['error'] = true;
  $return['msg'] = $keys->error_GenericError;
  return;
  }

// load the parent transaction 
$parent = $transactionFactory->getTransaction($parentTransactionId);

// if transaction is deleted, do not show it but an error message
if($parent->deleteFlag != 0) {
  $return['error'] = true;
  $return['msg'] = $keys->error_transactionIsDeleted;
  return;
  }

// if at least 2 children, validate the total amount
if(count($transactions) > 1) {
  // validate the amount
  if($sum != $parent->amount) {
    $return['error'] = true;
    $return['msg'] = $keys->error_BadTransactionChildrenSum;
    $return['sum'] = sprintf("%.2f", $sum);
    return;
    }
  }
// if only transaction = error as 2 are need at least
elseif(count($transactions) == 1) {
  $return['error'] = true;
  $return['msg'] = $keys->error_NoTransactionChildren;
  return;
  }

// load all existing children
$oldChildren = $transactionFactory->getChildTransactions($parentTransactionId);

// delete the old children from the DB but keep the objects to rollback the delete
foreach($oldChildren as $oldChild) {
  $transactionFactory->deleteTransaction($oldChild->id);
  $oldChild->id = 0;
  }

// save the new children
foreach($transactions as $transaction) {
  // get an empty object
  $child = $transactionFactory->getTransaction();
  
  // data from the form
  $child->amount = $transaction['amount'];
  $child->payee = $transaction['payee'];
  $child->memo = $transaction['memo'];
  $child->category = $transaction['category'];
  // data from the parent
  $child->operationDate = $parent->operationDate;
  $child->parentId = $parent->id;
  $child->debit = $parent->debit;
  $child->importId = $parent->importId;
  // save the child
  $result = $child->save();
  // if some errors happens, echo the error
  // and rollback all changes
  if($result != 1) {
    // remove all new entries to roll back the changes
    if(count($insertedTransactions) > 0) {
      foreach($insertedTransactions as $insertedTransaction) {
        $transactionFactory->deleteTransaction($insertedTransaction);
        }
      }
    // return as something abnormal happened
    $return['error'] = true;
    $return['msg'] = $keys->text_duplicatedRecords;
    return;
    }
  else {
    // add the inserted transaction id for roll-back purpose
    $insertedTransactions[] = $child->id;
    }
  }

// remove previous children
foreach($oldChildren as $oldChild) {
  $oldChild->save();
  }

// load all existing children
$children = $transactionFactory->getChildTransactions($parentTransactionId);
// if there are children, make the transaction a parent
// if not, make sure it is not set as a parent
if(count($children) > 0) {
  // make the parent a real parent
  $parent->isParent = "1";
  $parent->save();
  // display the sum
  $return['sum'] = sprintf("%.2f", $sum);
  }
else {
  // it is not a parent anymore
  $parent->isParent = "0";
  $parent->save();
  $return['sum'] = sprintf("%.2f", $parent->amount);
  }

// echo success
$return['error'] = false;
$return['msg'] = $keys->success;
?>