<?php
/**
* Delete a specific transactions
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
$transactionFactory = class_transactionFactory::getInstance();

// output is json encoded
$this->json = true;

// preset the return message to its defaults
$return['error'] = true;
$return['msg'] = $keys->error_UnknownError;


$db = class_db::getInstance();

$id = $context->id;
if(!is_numeric($id)) {
  return;
  }

// get the deleteFlag for this transaction
// instantiate a transaction object
$transaction = & $transactionFactory->getTransaction($id);
$deleteFlag = $transaction->deleteFlag;

if($transaction->parentId != 0) {
  $return['error'] = true;
  $return['msg'] = $keys->error_CannotDeleteChildTransaction;
  return;
  }
 
// toggle the deleteFlag
if($deleteFlag == 1) {
  $return['restored'] = true;
  $return['removed'] = false;
  $deleteFlag='0';
  }
else {
  $return['restored'] = false;
  $return['removed'] = true;
  $deleteFlag='1';
  }

// update the flag
$transaction->deleteFlag = $deleteFlag;
$transaction->save();

$return['error'] = false;
$return['msg'] = $keys->success;
?>