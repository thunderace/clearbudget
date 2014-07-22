<?php
/**
* File holding the debugger class
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
* Class providing ways to debug and trace the application execution. It provides
* log messages, execution timing and well formed outputs.
* 
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      framework
* @access       public
*/
class class_debug {
  /**
	* @var array holds each line of debuging messages
	* @access private
	* @static	
	*/
  private static $msg = array();
  /**
	* @var integer the time in millisecond when the processing started
	* @access private
	* @static	
	*/
  private static $start = null;
  /**
	* @var integer the time in millisecond between 2 debuging messages
	* @access private
	* @static	
	*/
  private static $intermediateTime = null;
  /**
	* @var integer the time in millisecond when the processing stopped
	* @access private
	* @static	
	*/
  private static $stop = null;
  /**
	* @var array the valid level of debuging
	* @access private
	* @static	
	*/
  private static $levels = array('UNKNOWN', 'DEBUG', 'INFO', 'WARNING', 'ERROR', 'FATAL ERROR', 'NONE');
  /**
	* @var string the default line break to use
	* @access private
	* @static	
	*/
  private static $lineBreak = "\n"; 

  
  /**
	* Class constructor 
	*	
	*   		
	* @return void
	* @access public
	*/  
  public function __construct() {
    } // __construct()
    
  /**
	* initiate the logging engine with the current time 
	*	
	*   		
	* @return void
	* @access private
	* @static	
	*/  
  private static function start() {
    if(self::$start == null) {
      self::$start = microtime(true);
      $timestamp = date('d/M/Y:H:i:s O');
      self::$msg[] = $_SERVER['REMOTE_ADDR'].' - ['.$timestamp.']: Request: '.$_SERVER['REQUEST_URI'];
      return;
      }
    } // start()
  
  /**
	* Stopped the logging engine with the current time 
	*	
	*   		
	* @return void
	* @access private
	* @static	
	*/  
  private static function stop() {
    if(self::$start != null) self::$stop = microtime(true);
    } // stop()
  
  /**
	* Compute the time difference between Now and the previous computed time
	* 
	* This is useful to get the time between two logs
	*   		
	* @return integer the time in milliseconds
	* @access private
	* @static	
	*/  
  private static function getIntermediateTime() {
    if(self::$intermediateTime == null) self::$intermediateTime = self::$start;
    $current = microtime(true);
    $intermediateTime =  sprintf('%'.DEBUGTIMEPRECISION.'f', $current - self::$intermediateTime);
    self::$intermediateTime = $current;
    return $intermediateTime;
    } // getIntermediateTime()
  
  /**
	* Add a log message 
	*	
	* Adds a message only if the requested level is at least equals to the current logging level
	* 
	* @param string the file where the log message is sent from
	* @param string the message to log
	* @param integer the message log level          		
	* @return void
	* @access public
	* @static	
	*/  
  public static function addMsg($filePath='', $msg, $level=1) {
    self::start();
    if($level == DEBUGFATALERROR) {
      self::displayMsg();
      die(self::$levels[$level].' + '.basename($filePath).' + '.$msg.' + '.self::getIntermediateTime().'s');
      }
    if($level >= DEBUGLEVEL) {
      // do some formating so it is easier to read on vi/tail/more or whatever you use
      $pFile = sprintf("%-22s", basename($filePath));
      $pLevel = sprintf("%-7s", self::$levels[$level]);
      $timestamp = date('d/M/Y:H:i:s O');
      self::$msg[] = '['.$timestamp.'] '.$pFile.' > '.$pLevel.' > '.$msg.' ('.self::getIntermediateTime().'s)';
      }
    } // addMsg()
  

  /**
	* Build a valid string from the message array 
	*	
	* 
	* @return string the messages concatenated as a string
	* @access private
	* @static	
	*/
  private static function buildMsg() {
    $returnMsg = '';
    if(DEBUGDISPLAYMSG) self::$lineBreak = "<br/>";
    // stop the timer
    self::stop();
      
    // compute the total computing time
    $totalTime = self::$stop - self::$start;
    
    // display the messages
    foreach(self::$msg as $msg) {
      $returnMsg .= $msg . self::$lineBreak;
      } // foreach
    $returnMsg .= 'Total queries executed: '.class_db::$queryCounter.self::$lineBreak;
    
    
    if(function_exists('memory_get_peak_usage')) {
      $maxMem = null;
      @$maxMem = memory_get_peak_usage(true);
      if($maxMem != null) {
        $returnMsg .= 'Max memory used '.($maxMem / 1024).'KB'.self::$lineBreak;
        }
      }
    $returnMsg .= 'Total Processing time: '.sprintf('%'.DEBUGTIMEPRECISION.'f',$totalTime).'s'.self::$lineBreak;
    return $returnMsg;
    }
  
  /**
	* Display the message string either back to the browser or in the log file
	* depending on the DEBUGDISPLAYMSG flag
	*	
	* @return void
	* @access public
	* @static	
	*/
  public static function displayMsg($JSONMode = false) {
    if(DEBUGLEVEL < DEBUGNONE) {
      $msg = self::buildMsg();
      if(DEBUGDISPLAYMSG && !$JSONMode) {
        echo $msg;
        }
      else {
        @error_log($msg, 3, 'trace.log');
        }
      }
    } // displayMsg()
  }
?>