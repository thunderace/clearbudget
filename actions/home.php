<?php
/**
* switch between the selected home page (cookie selection based between overview-simple & overview-detailed
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
switch($context->homePage) {
   default:
   case 'overview-detailed':
     $nextAction = 'overview-detailed';
   break;
   case 'overview-simple':
     $nextAction = 'overview-simple';
   break;
   case 'overview-timeline':
     $nextAction = 'overview-timeline';
   break;
}
$context->nextAction = $nextAction;
class_debug::addMsg(__FILE__, "redirecting to the $nextAction page", DEBUGINFO);
?>