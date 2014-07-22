<?php
/**
* Save the modification to an existing keyword or create a new one
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
$return['msg'] = $keys->error_UnknownError;

$id = $context->id;
$keyword = $context->keyword;
$category = $context->category;

//echo $id.'-'.$categoryName.'-'.$categoryColor.'-'.$categoryMaxAmountPerMonth;
if($keyword == null || $keyword == '') {
  // no further action
  $action = false;
  $return['error'] = true;
  $return['msg'] = $keys->error_BadKeyword;
  return;
  }

if($id == null || $id == '') {
  // no further action
  $action = false;
  $return['error'] = true;
  $return['msg'] = $keys->error_GenericError;
  return;
  }

if($category == null || $category == '') {
  // no further action
  $action = false;
  $return['error'] = true;
  $return['msg'] = $keys->error_GenericError;
  return;
  }

$db = class_db::getInstance();
$db->addFields('keyword', $keyword);
$db->addFields('category', $category);
if($id > 0) {
  $db->addWhere('id', '=', $id);
  $result = $db->update('t_keywords');
  }
else {
  $result = $db->insert('t_keywords');
  $return['id'] = $result;
  }
if($result!==false) {
  $return['error'] = false;
  $return['msg'] = $keys->success;
    }
else {
  $return['error'] = true;
  $return['msg'] = $keys->error_SaveKeywordFailure;
  }
?>