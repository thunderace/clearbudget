<?php
/**
* Display the graphs according to the report settings
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
global $context;
// get the currency
$currencySymbol = class_currency::getCurrencySymbol();

if($report->reportSettings->debit == '1') {
  echo '<div id="graphs">';
  echo '<table class="tableReport print">';
  $rowspan = 0;
  $cols = array();
  foreach($categories as $id=>$name) {
    if(isset($categoryColor[$id])) {
      if($id!=1) $cols[] = '<td class="noprint" style="color: #000000;background: #'.$categoryColor[$id].';"><a href="#editCategories&backLink=home" title="'.$keys->linkText_editCategorySettings.'">'.$name.'</a></td>';
      else  $cols[] = '<td class="noprint" style="color: #000000;background: #'.$categoryColor[$id].';">'.$name.'</td>';
      }
    }
  $rowspan = count($cols) + 1;
  foreach($cols as $key=>$col) {
    if($key == 0) {
      echo '<tr class="noprint">';
      echo $col;
      echo '<td rowspan="'.$rowspan.'"><img src="'.$url3.'" alt="" width="400" height="180" /></td>';
      echo '<td rowspan="'.$rowspan.'"><img src="'.$url4.'" alt="" width="350" height="160" /></td>';
      echo '<td rowspan="'.$rowspan.'"><img src="'.$url2.'" alt="" width="300" height="160" /></td>';
      echo '</tr>';
      }
  else   {
      echo '<tr>';
      echo $col;
      echo '</tr>';
      }
    }
  // add extra for printing
  echo '<tr class="printonly">';
  echo '<td><img src="'.$url3.'" alt="" width="400" height="180" /></td>';
  echo '<td><img src="'.$url4.'" alt="" width="350" height="160" /></td>';
  echo '</tr>';
  echo '<tr class="printonly">';
  echo '<td colspan="2" style="text-align: center"><img src="'.$url2.'" alt="" width="300" height="160" /></td>';
  echo '</tr>';
  echo '</table>';
  echo '<br/>';
  echo '</div>';
  }
?>