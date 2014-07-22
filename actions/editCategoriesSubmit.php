<?php
/**
* Save the modification to an existing category or create a new one
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

// output is json encoded
$this->json = true;

// preset the return message to its defaults
$return['error'] = true;
$return['msg'] = $keys->error_UnknownError.'test';

$id = (integer)$context->id;
$categoryName = $context->name;
$categoryColor = $context->color;
if($categoryColor == null) $categoryColor = '000000';

$categoryMaxAmountPerMonth = $context->maxAmountPerMonth;
if($categoryMaxAmountPerMonth == null) $categoryMaxAmountPerMonth = 0;

//echo $id.'-'.$categoryName.'-'.$categoryColor.'-'.$categoryMaxAmountPerMonth;
if($categoryName == null || $categoryName == '') {
  // no further action
  $action = false;
  $return['error'] = true;
  $return['msg'] = $keys->error_BadCategoryName;
  return;
  }

if($id == null || $id == '') {
  // no further action
  $action = false;
  $return['error'] = true;
  $return['msg'] = $keys->error_GenericError;
  return;
  }

if($categoryMaxAmountPerMonth != null && $categoryMaxAmountPerMonth != '') {
  if(!is_numeric($categoryMaxAmountPerMonth) || $categoryMaxAmountPerMonth<0) {
  // no further action
  $action = false;
  $return['error'] = true;
  $return['msg'] = $keys->error_BadCategoryMaxAmountPerMonth;
  return;
  }
  }

$db = class_db::getInstance();
$db->addFields('name', $categoryName);
$db->addFields('color', $categoryColor);
$db->addFields('maxAmountPerMonth', $categoryMaxAmountPerMonth);
if($id > 0) {
  $db->addWhere('id', '=', $id);
  $result = $db->update('t_categories');
  }
else {
  $id = $db->insert('t_categories');
  $return['id'] = $id;
  }
// get the new total of maxAmountPerMonth
$db->addFields('maxAmountPerMonth');
$db->select('t_categories');
$results = $db->fetchAllRows();
$totalMaxAmount = 0;
foreach($results as $result) {
  $totalMaxAmount += $result['maxAmountPerMonth'];
  }
// return data
$return['totalMaxAmount'] = $totalMaxAmount;
$return['error'] = false;
$return['msg'] = $keys->success;
?>