<?php
/**
* Save the changes to a given transaction
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

// instantiate necessary object
$transactionFactory = class_transactionFactory::getInstance();

// JSON flag to true as this always returns JSON data
$this->json = true;

// get the input fields
$id = $context->id;
$comment = $context->comment;
$category = $context->category;

// preset the return message to its defaults
$return['error'] = true;
$return['msg'] = $keys->error_UnknownError;

// update the record if necessary (we have an ID, a Category and/or a Comment)
if($id != null) {
  // instantiate a transaction object
  $transaction = & $transactionFactory->getTransaction($id);
  if($category!=null && $category!='' && strtolower($category)!='null') $transaction->category = $category;
  if($comment!==null) $transaction->comments = $comment;
  
  // save the transaction
  $result = $transaction->save();
  if($result != 1) {
    $return['error'] = true;
    $return['msg'] = $keys->failure;
    }
  else {
    $return['error'] = false;
    $return['msg'] = $keys->success;
    }
  }
?>