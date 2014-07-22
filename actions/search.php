<?php
/**
* Looks for a given keyword in the transactions details
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

$report = class_report::getInstance();
// build a category tree
foreach($report->categories as $id=>$name) {
  $categories[$id] = $report->getCategoryName($id);
  }
$keyWord = $context->keyWord;
$results = null;
$error = false;
// let's perform the search if the keyword is a number or if it has at least 3 characters
if($keyWord != '' && (is_numeric($keyWord) || strlen($keyWord) > 2)) {
  $results = $report->search($keyWord);
  }
else {
  $error = $keys->error_BadSearchTerms;
  }
$count = count($results);
?>