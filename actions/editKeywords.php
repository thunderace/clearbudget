<?php
/**
* Fectch all available keywords to be edited
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

$addRow=false;
if($context->numRow !== null) $addRow=true;
if($context->addKeyWord !== null) $addKeyWord=$context->addKeyWord;
else $addKeyWord = false;

// select all categories
$db = class_db::getInstance();
$db->addFields('id');
$db->addFields('name');
$db->addWhere('id', '!=', 1);
$numCategory = $db->select('t_categories', true);
$categories = $db->fetchAllRows();
class_debug::addMsg(__FILE__, 'got '.$numCategory.' categories from DB', DEBUGINFO);

$keywords = array();
// select all keywords
$db = class_db::getInstance();
$db->addFields('id');
$db->addFields('keyword');
$db->addFields('category');
$numRow = $db->select('t_keywords', true);
$keywords = $db->fetchAllRows();
class_debug::addMsg(__FILE__, 'got '.$numRow.' keywords from DB', DEBUGINFO);
if($numRow == 0) $addRow=true;
?>