<?php
/**
* Bootstrap file...where everything begins ;-)
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
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
include('logic/constants.php');
include('logic/functions.php');
include('logic/db.class.php');
include('logic/debug.class.php');
include('logic/user.class.php');
include('logic/settings.class.php');
include('logic/reportsettings.class.php');
include('logic/context.class.php');
include('logic/controller.class.php');
include('logic/report.class.php');
include('logic/currency.class.php');
include('logic/propertyKey.class.php');
include('logic/GoogleChart.class.php');
include('logic/userCookie.class.php');
include('logic/userFactory.class.php');
include('logic/transaction.class.php');
include('logic/transactionFactory.class.php');
include('logic/task.class.php');
include('logic/setup.php');

// initialize the context
$context = class_context::getInstance();
// get the controller
$controller = class_controller::getInstance();

// do the requested action
$controller->doAction();

// render the output of the action
$controller->render();
?>