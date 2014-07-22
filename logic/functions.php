<?php
// define the default time zone
date_default_timezone_set('America/Los_Angeles');

/**
	* Gets the default language from the user-agent 
	*	
	* Reads the HTTP headers from the request for the HTTP_ACCEPT_LANGUAGE tag.
	* If found, it return the first language.
	*     		
	* @return mixed the language as a string is any OR boolean false
	*/
function getUserAgentLanguage() {
  // if the user agent did not send anything, return false
  if (!isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) return false;
  // get all languages
  $langs=explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
  // get the first language in the list
  $lang=strtolower(substr($langs[0],0,2));
  return $lang;
  }
  
/**
	* lookup the data directory to find the DB 
	*  	
	* @return void
	*/	
function findDatabase() {
  $dbFile = false;
  if ($handle = opendir(SQLITEDBROOTFOLDER)) {
    while (false !== ($file = readdir($handle))) {
      if($file != "." && $file != "..") {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if($ext == 'sqlite') {
          $dbFile = $file;
          }
        }
      }
    closedir($handle);
    }
  // if no DB file was found, a random name is generated
  if($dbFile==false) {
    $dbFile = substr(base64_encode(rand(1000000000,9999999999)),0,10).'.sqlite';
    }
  // the 'SQLITEDB' constant is set with the DB name and path
  DEFINE('SQLITEDB', SQLITEDBROOTFOLDER.DIRECTORY_SEPARATOR.$dbFile);
  }
// call the function immediately as to set the DB constant
if(!defined('SQLITEDB')) findDatabase();

/**
	* clean a string from any extra white spaces 
	*  	
	* @param string the string
	* @return string the clean string
	*/	
function clean($string) {
  if(!is_string($string)) return;
  $string = strtolower(trim($string));
  while(1) {
    $tmp = str_replace('  ', ' ', $string);
    if($tmp != $string) $string = $tmp;
    else break;
    }
  return $string; 
  }
  
/**
	* clean the amount as to always have a valid float 
	*  	
	* @param string the amount
	* @return float the clean amount
	*/		
function cleanAmount($amount){
  $amount = trim($amount);
  if(preg_match("~^([0-9]+|(?:(?:[0-9]{1,3}([.,' ]))+[0-9]{3})+)(([.,])[0-9]{1,2})?$~", $amount, $r)){
    if(!empty($r['2'])){
      $pre = preg_replace("~[".$r['2']."]~", "", $r['1']);
      }
    else {
      $pre = $r['1'];
      }
    if(!empty($r['4'])){
      $post = ".".preg_replace("~[".$r['4']."]~", "", $r['3']);
      }
    else {
      $post = '.00';
      }
    $amount = $pre.$post;
    return $amount;
    }
  return false;
  }

/**
 * This function format the date as our DB expects it
 * We expect YYYY-MM-DD as the correct format 
 */
function formatManualDate($string, &$invalidDateFormat) {
  $defaultDate = date("Y-m-d");
  $returnDate = $defaultDate;
  // if no dates is given, then we use the default
  if($string == '') return $defaultDate;
  
  // Manual format should already be YYYYMMDD
  $year = substr($string, 0, 4);
  $month = substr($string, 5, 2);
  $day = substr($string, 8, 2);
  if(!is_numeric($year) || !is_numeric($month) || !is_numeric($day) || !checkdate($month, $day, $year)) $invalidDateFormat = true;
  if($month > 12) $invalidDateFormat = true;
  $returnDate =  $year.'-'.$month.'-'.$day;
  return $returnDate;
  }
  
/**
 * This function 'tries' to format the date as our DB expects it
 * We expect YYYY-MM-DD as the correct format 
 */
function formatQFXDate($string, &$invalidDateFormat) {
  $defaultDate = date("Y-m-d");
  $returnDate = $defaultDate;
  // if no dates is given, then we use the default
  if($string == '') return $defaultDate;
  
  // let's try to reformat the date as to get YYYY-MM-DD
  // QFX format is YYYYMMDD
  $year = substr($string, 0, 4);
  $month = substr($string, 4, 2);
  $day = substr($string, 6, 2);
  if(!checkdate($month, $day, $year)) $invalidDateFormat = true;
  if($month > 12) $invalidDateFormat = true;
  $returnDate =  $year.'-'.$month.'-'.$day;
  return $returnDate;
  }

/**
 * This function 'tries' to format the date as our DB expects it
 * 
 * This is a bit of a nightmare as QIF format is very loosy on that side
 * Date can be DD/MM/YY, MM/DD/YY, DD/MM/YYYY, MM/DD/YYYY, or with - instead of /
 * Dates in US QIF files are usually in the format MM/DD/YY, although
 * four-digit years are not uncommon.  Dates sometimes occur without the
 * slash separator, or using other separators in place of the slash,
 * commonly '-' and '.'.  US Quicken seems to be using the ' to indicate
 * post-2000 two-digit years (such as 01/01'00 for Jan 1 2000).  Some
 * banks appear to be using a completely undifferentiated numeric string
 * formated YYYYMMDD in downloaded QIF files.
 * 	
 * European QIF files may have dates in the DD/MM/YY format.
 * check these for more info:
 *  http://svn.gnucash.org/trac/browser/gnucash/branches/1.8/src/import-export/qif-import/file-format.txt
 *  http://web.intuit.com/support/quicken/docs/d_qif.html   
 */
function formatQIFDate($string, &$invalidDateFormat) {
  global $context;
  $defaultDate = date("Y-m-d");
  $returnDate = $defaultDate;
  // if no dates is given, then we use the default
  if($string == '') return $defaultDate;
  
  // let's try to reformat the date as to get YYYY-MM-DD
  
  // split it with /
  $date = explode('/', $string);
  
  // if date aren't splitted by /, we try with -
  if($date[0] == $string) $date = explode('-', $string);
  
  // test if we successfully splitted the date
  if($date[0] == $string || count($date) != 3) {
    // ok, we really cannot parse this stuff so we use the default date 
    return $defaultDate;
    }
  
  // if year is on 2 digit we fake it to a year 2000 something
  if(strlen($date[2]) == 2) $date[2] = '20'.$date[2];
  
  // format the date depending on the QIF Locale flag
  switch($context->QIFLocaleType) {
    case 'eu':
      // here we expect the month to be in place #1 :: DD/MM/YYYY
      if($date[1] > 12) $invalidDateFormat = true;
      if(!checkdate($date[1], $date[0], $date[2])) $invalidDateFormat = true;
      $returnDate =  $date[2].'-'.$date[1].'-'.$date[0];
    break;
    case 'us':
    //default:
      // here we expect the month to be in place #0 :: MM/DD/YYYY
      if($date[0] > 12) $invalidDateFormat = true;
      if(!checkdate($date[0], $date[1], $date[2])) $invalidDateFormat = true;
      $returnDate =  $date[2].'-'.$date[0].'-'.$date[1];
    break;
    }
  return $returnDate;
  }
?>
