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
$userSettings = class_settings::getInstance();
$userSettings->getSettings();
$usersFactory = class_userFactory::getInstance();
 
// define the default values
$username = $userSettings->getUsername();
$secureAccess = $userSettings->secureAccess();
$currency = $userSettings->getCurrency();
$language = $userSettings->getLanguage();
$defaultLanguage = $userSettings->getDefaultLanguage();
$initialBalance = $userSettings->getInitialBalance();

// if user is admin (or site is not secure), load all other user, if not, load only himself
$users = array();
if($userSettings->secureAccess()=='0' || $userSettings->isAdmin()) {
  $userIDs = $usersFactory->getAllUsersId();
  foreach($userIDs as $userId) {
    $users[] = $usersFactory->getUser($userId);
    }
  // a empty user
  $nullUser = $usersFactory->getUser();
  }
 else {
   $users[] = $usersFactory->getUser($userSettings->id);
   $nullUser = false;
 }

?>