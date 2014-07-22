<?php
/**
* Delete a specific report setting
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
$reportSettings = new class_reportsettings();
// output is json encoded
$this->json = true;

// preset the return message to its defaults
$return['error'] = true;
$return['msg'] = $keys->error_UnknownError;

if($reportSettings->deleteActiveReport()) {
  $return['error'] = false;
  $return['msg'] = $keys->success;
  }
else{
  $return['error'] = true;
  $return['msg'] = $keys->error_CannotDeleteReport;
  }
?>