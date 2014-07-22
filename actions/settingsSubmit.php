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
// some flags
$missingMandatoryUserSettings = false;
$badCurrentPassword = false;
// get a  setting object
$settings = class_settings::getInstance();
$usersFactory = class_userFactory::getInstance();

// define the default values
is_null($context->currentPassword)?$currentPassword = null:$currentPassword = $context->currentPassword;
is_null($context->secureAccess)?$secureAccess = $settings->secureAccess():$secureAccess = $context->secureAccess;
is_null($context->currency)?$currency = $settings->getCurrency():$currency = $context->currency;
is_null($context->language)?$language = $settings->getLanguage():$language = $context->language;
is_null($context->initialBalance)?$initialBalance = $settings->getInitialBalance():$initialBalance = $context->initialBalance;

// by default, there is an error
$return['error'] = true;
$return['msg'] = $keys->error_ImproperCall;
$return['doLanguage'] = false;

// a flag to tell if user language has changed
$doLanguage = false;

// is site secure
$isSiteSecure = $settings->secureAccess();

// if site is secured already, we validate the password
/* // Removed and process added to the user edit process
if($isSiteSecure) {
  if($settings->password != md5($currentPassword)) {
    $return['error'] = true;
    $return['msg'] = $keys->text_mustGivePasswordWarning;
    return;
    }
  }
*/
// if we have the request to secure the access to the site, we must have at least one admin user
if($secureAccess == '1') {
    $settings->secureAccess = '1';
    if($usersFactory->adminUserCount()<1) {
      $return['error'] = true;
      $return['msg'] = $keys->error_adminUserUndefined;
      return;
      }
    // if site was not secure but is now secure, set the $doLanguage to true to force a reload
    if(!$isSiteSecure) {
      //$doLanguage = true;
      // prepare a new div with "please login" message
      
      }
    }
else {
  // if secure access is not requested, logged out the existing user if any
  $settings->secureAccess = '0';
  class_userCookie::deleteCookie();
  }

// if a new language is required, update the flag
if($language != $settings->getLanguage()) $doLanguage = true;
$currency = $context->currency;
$initialBalance = $context->initialBalance;

// if everything is good, we update the user preferences
if($language!='NULL' && $currency!='NULL' && $language!='' && $currency!='') {
  // update the user settings
  $settings->currency = $currency;
  $settings->language = $language;
  $settings->initialBalance = $initialBalance;
  
  // update the user preference
  $settings->updateUserSettings();
  // build the return msg
  $return['error'] = false;
  $return['msg'] = $keys->error_UserSettingsSuccess;
  $return['doLanguage'] = $doLanguage;
  }
else {
  $missingMandatoryUserSettings = true;
  $return['error'] = true;
  $return['msg'] = $keys->error_MissingMandatoryUserSettings;
  }
?>