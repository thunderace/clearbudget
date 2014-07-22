<?php
/**
* Contains constant used over the applications
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
// the database file to use
DEFINE('OLDSQLITEDB', 'db'.DIRECTORY_SEPARATOR.'budget.sqlite');
if(!defined('SQLITEDBROOTFOLDER')) DEFINE('SQLITEDBROOTFOLDER', 'data');

// minimum PHP version needed
DEFINE('MIN_PHP_VERSION', '5.1.2');
 
// clearbudget version, release name and runtime mode
DEFINE('CB_VERSION', '0.9.8');
DEFINE('CB_RELEASE', 'LilyParlotte');

// general debug level
DEFINE('DEBUGDISPLAYMSG', false);
DEFINE('DEBUGDEBUG', 1);
DEFINE('DEBUGINFO', 2);
DEFINE('DEBUGWARNING', 3);
DEFINE('DEBUGERROR', 4);
DEFINE('DEBUGFATALERROR', 5);
DEFINE('DEBUGNONE', 6);
DEFINE('DEBUGTIMEPRECISION', '0.3');
if(!defined('DEBUGLEVEL')) DEFINE('DEBUGLEVEL', DEBUGINFO);

// access constant
DEFINE('PAGE_ACCESS_LOGIN_REQUIRED', 1);
DEFINE('PAGE_ACCESS_LOGIN_NOT_REQUIRED', 2);
DEFINE('PAGE_ACCESS_ADMIN_ONLY', 3);
DEFINE('PAGE_ACCESS_ALL_USERS', 4);

// the maximum number of allowed users
DEFINE('MAXNUMUSERS', 5);

// specific flag to debug the property keys (to see where they are...very useful!)
DEFINE('DEBUGPROPERTYKEY', false);

// define the maximum login cookie lifetime - 900 = 15mns (do not forget that it is also hardcoded in process.js)
DEFINE('LOGINCOOKIELIFETIME', 900);

// define the possible pass keys to encrypt the user cookie
if(!defined('LOGINCOOKIESALT')) DEFINE('LOGINCOOKIESALT', 'Salt is always good');
if(!defined('LOGINCOOKIEPEPPER')) DEFINE('LOGINCOOKIEPEPPER', 'Pepper like in ThaiPepper');

// the default language
DEFINE('DEFAULTLANGUAGE', 'en-US');

// definition of the available colors (used for categories mainly)
$colors[] = "000000,Black,Black";
$colors[] = "808080,Gray,Gray";
$colors[] = "A9A9A9,DarkGray,DarkGray";
$colors[] = "D3D3D3,LightGrey,LightGray";
$colors[] = "FFFFFF,White,White";
$colors[] = "7FFFD4,Aquamarine,Aquamarine";
$colors[] = "0000FF,Blue,Blue";
$colors[] = "000080,Navy,Navy";
$colors[] = "800080,Purple,Purple";
$colors[] = "FF1493,DeepPink,DeepPink";
$colors[] = "EE82EE,Violet,Violet";
$colors[] = "FFC0CB,Pink,Pink";
$colors[] = "006400,DarkGreen,DarkGreen";
$colors[] = "008000,Green,Green";
$colors[] = "9ACD32,YellowGreen,YellowGreen";
$colors[] = "FFFF00,Yellow,Yellow";
$colors[] = "FFA500,Orange,Orange";
$colors[] = "FF0000,Red,Red";
$colors[] = "A52A2A,Brown,Brown";
$colors[] = "DEB887,BurlyWood,BurlyWood";
$colors[] = "F5F5DC,Beige,Beige";

// since we can't have an array as a constant, so we set the serialize version of the colors array as constant
// not very nice but efficient
DEFINE('COLORS', serialize($colors));

// data import types
DEFINE('IMPORTQIFFILE', 0);
DEFINE('IMPORTQFXFILE', 1);
DEFINE('IMPORTMANUAL', 2);
DEFINE('IMPORTCSVFILE', 3);

// define the CSV line format
DEFINE('CSVLINEFORMAT', 'payee,operationDate,memo,amount');

// the threshold of % of transactions a word should appear to be shown as a suggestion - 15% now
define('KEYWORDSUGGESTIONTHRESHOLD', 5);
// number of transactions to look for suggestions (to avoid loading all the database)
// 1000 sounds a good compromise between speed and meaningful suggestions
// it runs is 200ms average on my small laptops!
define('KEYWORDSUGGESTIONNUMTRANSACTION', 2000);

// number of days for which to show reminders - between 1 and 31 days from today's date
define('REMINDERSLIMIT', 5);

// a flag to let know all other files that we passed here
DEFINE('ENGINEON', true);
?>