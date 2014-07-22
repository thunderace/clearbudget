<?php
/**
* File holding the user property key class
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
* Class to handles translation of Keys to proper language. The selected language is
* defined by the context. This class only 'transform' a given Key to the corresponding
* translation word from the translation files
* 
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_propertyKey {
  /**
	* @var object Hold the instance of the current class
	* @access private
	* @static	
	*/
  private static $_instance = null;
  /**
	* @var string the language to translate the keys to
	* @access private
	*/
  private $lang = null;
  /**
	* @var array holds all the keys and their translated values
	* @access private	
	*/
  private  $keys = null;
  /**
	* @var array holds all the list of available languages
	* @access private
	*/
  private static $availableLanguages = null;
  
  /**
	* return the current language 
	*		
	* @return string the language
	* @access public	
	*/
  public function getLang() {
    return $this->lang;
    }
  
  /**
	* Switch the current language 
	*	
	* @param string the requested language	
	* @return void
	* @access public	
	*/
  public function switchToLang($lang) {
    $this->lang = $lang;
    // load the corresponding language file
    $this->loadKeysFile();
    }

  public static function getAvailableLanguages() {
    if(self::$availableLanguages != null) return self::$availableLanguages;
    // scan the 'lang' folder to find the language files
    clearstatcache();
    if($handle = opendir('lang')) {
      while (false !== ($file = readdir($handle))) {
        if($file != '.' && $file != '..') {
          $fileParts = explode('.', $file);
          if(count($fileParts) == 2 && $fileParts[1] == 'txt') {
            $dash = explode('-', $fileParts[0]);
            if(count($dash) == 2) {
              self::$availableLanguages[] = $fileParts[0];
              class_debug::addMsg(__FILE__, $fileParts[0].' added as a valid language', DEBUGDEBUG);
              }
            }
          }
        }
      closedir($handle);
      }
    else {
      class_debug::addMsg(__FILE__, 'Cannot open language file folder - unable to use property keys', DEBUGERROR);
    }
  return self::$availableLanguages;
  }

  /**
	* Magic function to return any key from the internal array of keys 
	*	
	* This function listen to the DEBUGPROPERTYKEY as to return the key name instead of the translated value.
	* This is useful to see where the keys are used on the user interface.	
	* 	
	* @param string the key name	
	* @return string the translated key value or a warning message if key is not present
	* @access public	
	*/
  public function __get($name) {
    // set the return value to null by default
    $data = '('.$name.')';
    
    // check if the data is present and not null
    if(isset($this->keys[$name])) $data = $this->keys[$name];

    // if mode debug is enabled, we return the key name
    if(DEBUGPROPERTYKEY === true) $data = $name;
    
    return trim($data);
    }
    
  /**
	* Loads the translation file into the internal arrays of keys 
	*	
	* 		
	* @return void
	* @access private	
	*/
  private function loadKeysFile () {
    // load the key file, if unsuccessful, load the default language
    $keysFilePath = 'lang'.DIRECTORY_SEPARATOR.$this->lang.'.txt';
    // check if a file exists for the requested language, if not, default to english
    if(!file_exists($keysFilePath)) {
      class_debug::addMsg(__FILE__, 'Cannot find language file for '.$this->lang.', defaults to '.DEFAULTLANGUAGE, DEBUGWARNING);
      $this->lang = DEFAULTLANGUAGE;
      $keysFilePath = 'lang'.DIRECTORY_SEPARATOR.DEFAULTLANGUAGE.'.txt';
      }
    // load the file
    $lines = file($keysFilePath);
    foreach($lines as $line) {
      // check for the comments tag
      if(strpos($line, '//') === 0) continue;
      // detect line malformed (blank or not having the :: tag)
      if(strpos($line, '::') === false) continue;
      // explode the line at the :: tag
      $item = explode('::', $line);
      // load the keys and the translation in the array
      $this->keys[$item[0]] = $item[1];
      }
    class_debug::addMsg(__FILE__, count($this->keys).' properties keys loaded from '.$keysFilePath, DEBUGDEBUG);

    // set the locale according to the language
    switch($this->lang) {
      case 'fr-FR': $locale = 'french';
//      case 'fr-FR': $locale = 'fr_FR.UTF8';
      break;
      case 'es-ES': $locale = 'spanish';
      break;
      case 'tr-TR': $locale = 'turkish';
      break;
      case 'it-IT': $locale = 'italian';
      break;
      case 'pt-BR': $locale = 'portuguese';
      break;
      case 'gr-GR': $locale = 'greek';
      break;
      default:
      case 'en-US': $locale = 'american';
      break;
      }
    $setLocale = setlocale(LC_TIME, $locale);
//    $setLocale = setlocale(LC_ALL, $locale);
      class_debug::addMsg(__FILE__, 'locale set to '.$locale.' ('.$setLocale.')', DEBUGINFO);
    }
  
  /**
	* Provide an instance of the current class 
	*	
	* Implementation of the singleton pattern
	*   		
	* @return object An instance of this class
	* @access public
	* @static	
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
	* looks at the class_context to get the requested language.
	* access to this class is private as to implement the singleton pattern.
	*   	
	* @return void
	* @access private
	*/
  private function __construct() {
    global $context;
    if($context->userLanguage!=null) $this->lang=$context->userLanguage;
    else $this->lang = DEFAULTLANGUAGE;
    
    // load the corresponding language file
    $this->loadKeysFile();
    } // __construct()
  }
?>
