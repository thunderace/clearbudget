<?php
/**
* Process the user login by checking username/password against the DB
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

$userFactory = class_userFactory::getInstance();
$userSettings = class_settings::getInstance();
$context = class_context::getInstance();

// by default, there is an error
$return['error'] = true;
$return['msg'] = $keys->error_UnknownError;
$return['nextAction'] = false;

// if site is not supposed to be secured, we echo success
if($userSettings->secureAccess() == false) {
  class_debug::addMsg(__FILE__, 'redirection to the home page', DEBUGDEBUG);
  $return['error'] = false;
  $return['msg'] = $keys->success;
  return;
  }

// some flag
$badUsername = false;
$badPassword = false;

// get the input values
$username = $context->username;
$password = $context->password;
$backAction = $context->backAction;
class_debug::addMsg(__FILE__, 'backAction is "'.$backAction.'"', DEBUGDEBUG);

// if the user is logged in, we log him out
if($context->isUserLoggedIn()) {
  class_userCookie::deleteCookie();
  $return['error'] = false;
  $return['msg'] = $keys->error_successLogout;
  $return['nextAction'] = false;
  return;
  }

// let's try to login
$userId = $userFactory->getUserId($username, $password);
if($userId == false) {
  $return['error'] = true;
  $return['msg'] = $keys->error_BadUsernameOrPassword;
  return;
  }
else {
  // log the user in
  class_userCookie::loginUser($userId);
  // clean up the next action
  if($backAction == '' || $backAction == 'login') $backAction = 'home';
  $return['error'] = false;
  $return['msg'] = $keys->error_successLogin;
  $return['nextAction'] = $backAction;  
  }
?>