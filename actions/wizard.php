<?php
/**
* Compute the initial wizard state
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

// check if we have some categories defined
$db->addFields('count(id)');
$db->addWhere('id', '!=', 1);
$db->select('t_categories');
$cat = $db->fetchRow();
// if there are some categories, then we can add transactions
if($cat['count(id)'] > 0) $doTransaction = true;
// if not, then we cannot add transactions
else $doTransaction = false;

// check if we have some transactions
$db->addFields('count(id)');
$db->select('v_items');
$trn = $db->fetchRow();
// if there are some items and we do have some categories, then we can add keywords
if($trn['count(id)'] > 0 && $doTransaction) $doKeyword = true;
// else we can't add keywords
else $doKeyword = false;

// check if we have some keywords
$db->addFields('count(id)');
$db->select('t_keywords');
$key = $db->fetchRow();
// if there are some keywords and some categories and some transations, then we can do auto sorting
if($key['count(id)'] > 0 && $cat['count(id)'] > 0 && $doKeyword != false ) $doSort = true;
// else we can't auto-sort
else $doSort = false;

// if we can add transaction, let's make it the default step
if($doTransaction) $wizardId = 2;
// else we go to step 1 (add categories)
else $wizardId = 1;
?>