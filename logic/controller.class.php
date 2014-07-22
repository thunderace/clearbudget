<?php
/**
* File holding the controller class
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

/**
* Class managing the flow of event from a given request. It controls the actions
* that are requested and build the output message.
*
* this is the applicaton controller.
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_controller {
	/**
	* @var object Hold the instance of the current class
	* @access private
	* @static
	*/
  private static $_instance = null;
  /**
	* @var string The text to be output
	* @access private
	*/
  private $output = '';
 	/**
	* @var boolean A flag to tell if the output should be json encoded
	* @access private
	*/
  private $json = false;

  /**
	* @var array valid actions and the secure flag
	* @access private
	*/
  private $actions = array(
    'login'=>array(PAGE_ACCESS_LOGIN_NOT_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'actions'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'addTransactionsManualy'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'addTransactionsManualySubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'addTransactionsWizard'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'autoFileItem'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'blockUserSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ADMIN_ONLY),
    'breakTransaction'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'breakTransactionSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'credits'=>array(PAGE_ACCESS_LOGIN_NOT_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'deleteImport'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ADMIN_ONLY),
    'doBackup'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ADMIN_ONLY),
    'editCategories'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ADMIN_ONLY),
    'editCategoriesSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ADMIN_ONLY),
    'editKeywords'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'editKeywordsSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'editReportSettings'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'editReportSettingsSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'editTransactionSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'errorDisplay'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'deleteKeyword'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'deleteReportSettings'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'deleteTransaction'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'home'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'keywordSuggestor'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'listCategoryItems'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'listImportTransactions'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ADMIN_ONLY),
    'listMonthItems'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'loading'=>array(PAGE_ACCESS_LOGIN_NOT_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'login'=>array(PAGE_ACCESS_LOGIN_NOT_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'loginSubmit'=>array(PAGE_ACCESS_LOGIN_NOT_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'manageUpload'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ADMIN_ONLY),
    'overview-detailed'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'overview-graphs'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'overview-simple'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'overview-timeline'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'overview-timeline-data'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'search'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'setDateRange'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'setReport'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'settings'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'settingsSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ADMIN_ONLY),
    'setup'=>array(PAGE_ACCESS_LOGIN_NOT_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'setupSubmit'=>array(PAGE_ACCESS_LOGIN_NOT_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'share'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ADMIN_ONLY),
    'taskScheduler'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'taskSchedulerSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'uploadCSV'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'uploadCSVSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'uploadQfx'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'uploadQfxSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'uploadQif'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'uploadQifSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'userEditSubmit'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    'wizard'=>array(PAGE_ACCESS_LOGIN_REQUIRED, PAGE_ACCESS_ALL_USERS),
    );

  /**
	* Validate the action given to be sure it is part of the valid one
	*
	* @return boolean true if valid, false otherwise
	* @access private
	*/
  private function validateAction() {
    global $context;
    class_debug::addMsg(__FILE__, 'action to validate is '.$context->action, DEBUGDEBUG);
    if(array_key_exists($context->action, $this->actions)) {
      class_debug::addMsg(__FILE__, 'action "'.$context->action.'" is valid', DEBUGINFO);
      return true;
      }
    class_debug::addMsg(__FILE__, 'action "'.$context->action.'" is invalid', DEBUGINFO);
    return false;
    }

  /**
	* Check if the user should be logged in
	*
	* If the site require to be logged in, then it checks if the user is logged in.
	* If not, redirect him to the login page.
	*
	* @return void
	* @access private
	*/
  private function controlSiteAccess() {
    global $context;
    $userSettings = class_settings::getInstance();

    // if site should not be secured, let's forget about those checkes
    if($userSettings->secureAccess() !== '1') {
      class_debug::addMsg(__FILE__, 'Application is not secured', DEBUGINFO);
      return;
    }

    // check action access
    switch($this->actions[$context->action][0]) {
      case PAGE_ACCESS_LOGIN_NOT_REQUIRED:
        class_debug::addMsg(__FILE__, 'Action "'.$context->action.'" does not require to be logged in', DEBUGINFO);
        return;
      break;
      default:
      case PAGE_ACCESS_LOGIN_REQUIRED:
        // check if the user is logged in
        if(!$context->isUserLoggedIn()) {
          class_debug::addMsg(__FILE__, 'User is not logged, redirecting to login page', DEBUGINFO);
          // save the requested page
          if($context->backAction == null) $context->setBackAction($context->action);
          // set the next action to be the login page
          $context->redirect('login');
          }
      else {
        class_debug::addMsg(__FILE__, 'User is logged in', DEBUGINFO);
        // if page is for admin only, check if user is an admin
        if($this->actions[$context->action][1] == PAGE_ACCESS_ADMIN_ONLY && !$userSettings->isAdmin()) {
          class_debug::addMsg(__FILE__, 'User is not admin and page is only for admin', DEBUGINFO);
          $context->error = 'error_actionIsForAdminOnly';
          $context->redirect('errorDisplay');
          }
        }
      break;
      }
    }

	/**
	* Performs the requested action from the context object
	*
	* Multiple action file might be loaded in a loop. One action might set a next action to be called in the context.
	*
	* @return void
	* @access public
	*/
  public function doAction() {
    global $context;

    // validate the request action
    if($this->validateAction()==false) {
      $context->redirect('home');
      }

    // we always check if the application should be password protected
    $this->controlSiteAccess();

    // load the property key object to perform translation
    $keys = class_propertyKey::getInstance();
    class_debug::addMsg(__FILE__, 'Property keys loaded', DEBUGINFO);

    // load the action(s) needed
    // this is a loop since an action can require a next action to be loaded
    do {
      class_debug::addMsg(__FILE__, 'Start action: '.$context->action, DEBUGINFO);
      // preset the next action to be false - prevents looping for ever :-)
      $context->nextAction = false;
      // prepare the path to be included
      $actionPath = 'actions'.DIRECTORY_SEPARATOR.$context->action.'.php';
      // if file does not exist, we simply continue
      if(file_exists($actionPath)) {
        // includes the files (i.e.: execute it)
        include($actionPath);
        class_debug::addMsg(__FILE__, 'Done loading "'.$actionPath.'"', DEBUGINFO);
        }
      else {
        class_debug::addMsg(__FILE__, 'loading of "'.$actionPath.'" failed', DEBUGINFO);
        break;
        }
      // if next action is set, we override the context action
      if($context->nextAction !== false) $context->redirect($context->nextAction);
      }
    while($context->nextAction !== false);

    // now load the snippet output
    // build up the path to the snippet
    $snippetPath = 'snippets'.DIRECTORY_SEPARATOR.$context->snippet.'.php';
    // check snippet existence and load it
    if(file_exists($snippetPath)) {
      ob_start();
      // load the content
      include($snippetPath);
      // get the content from the output buffer and escape it for security
      $this->output = ob_get_contents();
      // clean the buffer
      ob_end_clean();
      class_debug::addMsg(__FILE__, 'Done loading "'.$snippetPath.'"', DEBUGINFO);
      }
    else {
      class_debug::addMsg(__FILE__, 'loading of "'.$snippetPath.'" failed', DEBUGINFO);
      }
    }

/**
	* Performs the requested action and return the output
	*
	* @return void
	* @access public
	*/
  public function includeAction($action) {
    $output = "";
    
    // load the property key object to perform translation
    $keys = class_propertyKey::getInstance();
    class_debug::addMsg(__FILE__, 'Property keys loaded', DEBUGINFO);


    class_debug::addMsg(__FILE__, 'Start action: '.$action, DEBUGINFO);
    // prepare the path to be included
    $actionPath = 'actions'.DIRECTORY_SEPARATOR.$action.'.php';
    // if file does not exist, we simply continue
    if(file_exists($actionPath)) {
      // includes the files (i.e.: execute it)
      include($actionPath);
      class_debug::addMsg(__FILE__, 'Done loading "'.$actionPath.'"', DEBUGINFO);
      }
    else {
      class_debug::addMsg(__FILE__, 'loading of "'.$actionPath.'" failed', DEBUGINFO);
      }

    // build up the path to the snippet
    $snippetPath = 'snippets'.DIRECTORY_SEPARATOR.$action.'.php';
    // check snippet existence and load it
    if(file_exists($snippetPath)) {
      ob_start();
      // load the content
      include($snippetPath);
      // get the content from the output buffer and escape it for security
      $output = ob_get_contents();
      // clean the buffer
      ob_end_clean();
      class_debug::addMsg(__FILE__, 'Done loading "'.$snippetPath.'"', DEBUGINFO);
      }
    else {
      class_debug::addMsg(__FILE__, 'loading of "'.$snippetPath.'" failed', DEBUGINFO);
      }
    return $output;
    }

  /**
	* Render the output when in AJAX mode
	*
	* Ajax mode is dictated from the context as it is a parameter in the query.
	*
	* @return void
	* @access private
	*/
  private function renderAjax() {
    // echo the output
    echo $this->output;
    }

  /**
	* Render the output
	*
	* Renders the main template page.
	*
	* @return void
	* @access private
	*/
  private function renderGeneric() {
    // load the property key object to perform translation
    $keys = class_propertyKey::getInstance();
    ob_start();
    // include the main template file
    include("style/index.php");
    // get the content
    $output =  ob_get_contents();
    // stop buffering
    ob_end_clean();
    // echo the ouput
    echo $output;
    }

  /**
	* Master function to render the output
	*
	* Switch the rendering between Ajax and non Ajax calls known from the context
	*
	* @return void
	* @access public
	*/
  public function render() {
    global $context;
    // always tell the browser to not cache
    $this->sendNoCacheHeader();

    // if in AJAX mode, echo of the raw action ouput
    // if not, load the default template page
    if($context->ajax) {
      class_debug::addMsg(__FILE__, 'Rendering in Ajax mode', DEBUGINFO);
      $this->renderAjax();
      }
    else {
      class_debug::addMsg(__FILE__, 'Rendering in full mode', DEBUGINFO);
      $this->renderGeneric();
      }
    class_debug::addMsg(__FILE__, 'Rendering done', DEBUGINFO);
    class_debug::displayMsg($this->json);
    }

  /**
	* Sends the HTTP headers to force the user agent to not cache
	*
	* @return void
	* @access public
	*/
  public function sendNoCacheHeader() {
    // cache control: no cache
    header("Cache-Control: no-cache");
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 1 Jan 2000 01:00:00 GMT"); // Date in the past
    }

  /**
	* Sends the HTTP headers to force the user agent to redirect
	*
	* @param string URL the URL to redirect to (can be relative or absolute)
	* @return void
	* @access public
	*/
  public function sendRedirectTo($URL = null) {
    // prevent redirecting to nothing
    if($URL === null) return;
    // send header redirect
    header("Location: ".$URL);
    die();
    }

  /**
	* Provide an instance of the current class
	*
	* Implementation of the singleton pattern
	*
	* @return object An instance of this class
	* @access public
	*/
  public static function getInstance() {
    if(is_null(self::$_instance)) {
      self::$_instance = new self();
      }
    return self::$_instance;
    } // getInstance()

  /**
	* class constructor
	*
	* access to this class is private as to implement the singleton pattern
	*
	* @return void
	* @access private
	*/
  private function __construct() {
    } // __construct()
  }
?>