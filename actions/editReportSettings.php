<?php
/**
* Edit or add a report settings
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
$reportSettings = new class_reportsettings();
$report = class_report::getInstance();

// small hack as JQuery does not handle the 'en' language as i18n...blank is 'en'!
$lang = $keys->getLang();
if($lang == 'en') $lang='';

// get if we are adding or editing
$add = $context->add;

// get all categories
$categories[] = '0';
foreach($report->categories as $id=>$name) {
  $categories[] = $id;
  }


if($add != "1") {
  $reportSettings->getReportSettings();
  if($reportSettings->categories!='') $reportCategories = explode(',', $reportSettings->categories);
  else $reportCategories[] = 0;
  }
else {
  $reportCategories[] = 0;
  }
?>