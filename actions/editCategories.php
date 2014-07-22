<?php
/**
* Fectch all available categories to be edited
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

if($context->numCategory != null) $addCategory=true;
else $addCategory=false;

// select all categories
$db = class_db::getInstance();
$db->addFields('id');
$db->addFields('name');
$db->addFields('color');
$db->addFields('maxAmountPerMonth');
//$db->addFields('parentId');
$db->addWhere('id', '!=', 1);
//$db->addWhere('parentId', '=', 0);
//$numParentCategories = $db->select('v_categories', true);
//$parentCategories = $db->fetchAllRows();
$numCategories = $db->select('v_categories', true);
$categories = $db->fetchAllRows();
class_debug::addMsg(__FILE__, 'got '.$numCategories.' parent categories from DB', DEBUGINFO);
/*
foreach($parentCategories as $key=>$parentCategory) {
  $db->addFields('id');
  $db->addFields('name');
  $db->addFields('color');
  $db->addFields('maxAmountPerMonth');
  $db->addWhere('parentId', '=', $parentCategory['id']);
  $numCategories = $db->select('v_categories', true);
  $categories[$parentCategory['id']] = $db->fetchAllRows();
  class_debug::addMsg(__FILE__, 'got '.$numCategories.' categories for parent '.$numCategories.' from DB', DEBUGINFO);
}

if($numParentCategories == 0) $addParentCategory=true;
 */
?>