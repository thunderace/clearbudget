<?php
/**
* Display the transaction belonging to a category
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      snippets
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
$title = $keys->tableTitle_transactions.' - '.$keys->text_categoryNameLong.': '.$category.' ('.$count.'  '.$keys->text_SearchResultCountString.') - <a href="#home">'.$keys->link_back.'</a>';
?>
<h1><img src="style/icons/table.png"/> <?php echo $title; ?></h1>
<?php
// include the display
include('listItems.php');
?>