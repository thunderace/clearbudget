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
$title = $keys->tableTitle_MonthlyTransactions.' - '.$date.': '.$count.'  '.$keys->text_SearchResultCountString.' - <a href="#home">'.$keys->link_back.'</a>';
if(!$toDiv) {
  echo '<h1><img src="style/icons/table.png"/>'.$title.'</h1>';
  $toDiv = '';
  }
else {
  $toDiv = ':'.$toDiv;
  }
?>
<h2><img src="style/icons/table.png"/> <?php echo $keys->tableTitle_MonthlAvailable; ?></h2>
<?php
if(count($availableMonthYear) == 0) {
  echo '<div class="warning"><blockquote>'.$keys->error_NoTransactionData.'</blockquote></div>';
  echo '<br/><br/><br/><br/><br/><br/><br/>';
  return;
  }
?>
<table class="tableReport">
<?php
echo '<tr>';
$yearTD = 0;
foreach($availableMonthYear as $monthYear) {
  $split = explode('-', $monthYear['month']);
  $year=$split[0];
  $month=$split[1];
  $formatedDate = $month.'-'.$year;
  if($yearTD != $year && $yearTD == 0) {
    echo '<tr><td>'.$year.'</td>';
    $yearTD = $year;
    }
  elseif($yearTD != $year) {
    echo '</td></tr>';
    echo '<tr><td>'.$year.'</td>';
    $yearTD = $year;
    }
  if($monthSelected == $year.'-'.$month) $style="background: green;";
  else $style = '';
  echo '<td style="'.$style.'"><a href="#listMonthItems'.$toDiv.'&monthSelected='.$formatedDate.'">'.utf8_encode(strftime('%b', strtotime('01-'.$formatedDate))).'</a></td>';
  }
echo '</tr>';
  ?>
</table>
<br/>
<?php
// include the display
include('listItems.php');
?>