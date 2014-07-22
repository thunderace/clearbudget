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
// a flag telling if current password was bad
$badCurrentPassword = false;
// the current user
$userSettings = class_settings::getInstance();
$userSettings->getSettings();
// the user to be edited
$userId = $context->userId;
$usersFactory = class_userFactory::getInstance();
$user = $usersFactory->getUser($userId);
// change language flag
$doLanguage = false;

// default return values
$return['error'] = true;
$return['msg'] = $keys->error_ImproperCall;

// define the default value
$username = $context->username;
$password = $context->password;
$currentPassword = $context->currentPassword;
$language = $context->language;
$type = $context->type;

// user must provide its password before anything
if($userId > 0 && $userSettings->password != md5($currentPassword)) {
    $return['error'] = true;
    $return['msg'] = $keys->text_mustGivePasswordWarning;
    return;
    }

// check if the current logged in user can create/edit users
if($userSettings->secureAccess()=='1' && !$userSettings->isAdmin() && $userId != $userSettings->id) {
  $return['error'] = true;
  $return['msg'] = $keys->failure;
  class_debug::addMsg(__FILE__, "user #{$userSettings->id} is not admin and can't edit user #$userId", DEBUGERROR);
  return;
  }


if(strlen($username) < 6) {
  $return['error'] = true;
  $return['msg'] = $keys->error_InvalidUsername;
  return;
  }
 else $user->username = $username;

// if password is empty, check if there is an existing valid password
if($password == '') {
  // if existing password is invalid, echo error
   if(strlen($user->password) < 6) {
    $return['error'] = true;
    $return['msg'] = $keys->error_InvalidPassword;
    return;
   }
}
elseif(strlen($password) < 6) {
  $return['error'] = true;
  $return['msg'] = $keys->error_InvalidPassword;
  return;
  }
 else $user->password = $password;

$availableLanguages = $keys->getAvailableLanguages();
if(!in_array($language, $availableLanguages)) {
  $return['error'] = true;
  $return['msg'] = $keys->error_InvalidLanguage;
  return;
}
else {
  if($userId == $userSettings->id && $language != $userSettings->getLanguage()) $doLanguage = true;
  $user->userLanguage = $language;
  }

if($type == 'user' || $type == 'admin') {
  // need at least one admin
  if($userId == $userSettings->id && $type == 'user' && $userSettings->type == 'admin' && $usersFactory->adminUserCount()==1) {
    $return['error'] = true;
    $return['msg'] = $keys->error_needsOneAdmin;
    return;
    }
  $user->type = $type;
}

// save the user
if($user->save() != 1) {
  $return['error'] = true;
  $return['msg'] = $keys->error_UnknownError;
  }
else {
  // reset the cookie since username and/or password may have changed
  if($userId == $userSettings->id) class_userCookie::loginUser($userId);
  // echo success
  $return['id'] = $user->id;
  $return['error'] = false;
  $return['doLanguage'] = $doLanguage;
  $return['msg'] = $keys->error_UserSettingsSuccess;
}
?>