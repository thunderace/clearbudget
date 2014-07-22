<?php
/**
* Sets the date range in which to look for transactions, this is done to avoid loading ALL transactions at once.
* (The application currently support last 3 months, 9 or 12 months)
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

// JSON flag to true as this always returns JSON data
$this->json = true;

// preset the return message to its defaults
$return['error'] = true;
$return['msg'] = $keys->error_UnknownError;

// get the requested range
$range = $context->monthRange;
if($range === null) $range = DEFAULTMONTHRANGE;

// set the limit month for that range
$report = class_report::getInstance();
class_debug::addMsg(__FILE__, 'calling getLimitMonth()', DEBUGINFO);
$report->setLimitMonth($range, true);

$return['error'] = false;
$return['msg'] = $keys->success;
?>