<?php
/**
* Update the user preferences
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
//prevent direct access
if(!defined('ENGINEON')) die('Direct access forbidden');
global $context;
// the current user
$userSettings = class_settings::getInstance();
$userSettings->getSettings();
// the user to be edited
$userId = $context->userId;
$usersFactory = class_userFactory::getInstance();
$user = $usersFactory->getUser($userId);

// default return values
$return['error'] = true;
$return['msg'] = $keys->error_ImproperCall;

// check if the current logged in user can create/edit users
if($userSettings->secureAccess()=='1' && !$userSettings->isAdmin() && $userId != $userSettings->id) {
  $return['error'] = true;
  $return['msg'] = $keys->failure;
  class_debug::addMsg(__FILE__, "user #{$userSettings->id} is not admin and can't edit user #$userId", DEBUGERROR);
  return;
  }

// cannot disable himself
if($userId == $userSettings->id) {
    $return['error'] = true;
    $return['msg'] = $keys->error_UnknownError;
    return;
    }

// toggle the enabled flag
if($user->enabled == '1') $user->enabled = '0';
else $user->enabled = '1';

// save the user
if($user->save() != 1) {
  $return['error'] = true;
  $return['msg'] = $keys->error_UnknownError;
  }
else {
  $return['enabled'] = $user->enabled;
  $return['error'] = false;
  $return['msg'] = $keys->error_UserSettingsSuccess;
}
?>