<?php
/**
* Display the transactions in a table fashion
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
// get the currency
$currencySymbol = class_currency::getCurrencySymbol();
$disable="disabled";
$backTag = '';
if(strlen($backLink) > 0) {
  $backTag = ' - <a href="#'.$backLink.'" >'.$keys->link_back.'</a>';
  }
?>
<div id="taskSchedulerDiv">
<h2><img src="style/icons/clock_add.png"/> <?php echo $keys->text_taskScheduler; ?> <?php echo $backTag;?></h2>

<form id="taskScheduler" onSubmit="taskSchedulerSubmit(); return false;">
<input type="hidden" name="taskId" value="<?php echo $task->id; ?>">
<input type="hidden" name="backLink" value="<?php echo $backLink; ?>">
<table class="tableReport">
<tr>
  <th><u><?php echo $keys->text_memo; ?></u></th>
  <th><u><?php echo $keys->text_amountLong; ?></u></th>
  <th><u><?php echo $keys->text_reminderDay; ?></u></th>
  <th><u><?php echo $keys->text_reminderAlertType; ?></u></td>
</tr>
<tr><td colspan="6" style="display:none;" id="resultDiv"></td></tr>
<tr>
    <td><input tabindex="1" type="text" size="80" id="memo" name="memo" value="<?php echo $task->memo; ?>"></td>
    <td><?php echo $currencySymbol;?> <input tabindex="2" type="text" name="amount" size="5" value="<?php echo $task->amount; ?>"></td>
    <td><?php echo $keys->text_reminderDayHint; ?><br/>
    <input tabindex="3" type="text" size="10" id="reminderDay" name="reminderDay" value="<?php echo $task->reminderDay; ?>"></td>
    <td></td>
</tr>
<tr>
    <td colspan="6" style="text-align:center"> <input  tabindex="7" type="submit" value="<?php echo $keys->link_save;?>"> </td>
</tr>
</table>
</form>
<?php
if(count($tasks) > 0) {
?>
<br/>
<h2><img src="style/icons/clock.png"/> <?php echo $keys->tableTitle_taskSchedulerEdit; ?></h2>
<table class="tableReport">
<tr>
  <th><u><?php echo $keys->text_memo; ?></u></th>
  <th><u><?php echo $keys->text_amountLong; ?></u></th>
  <th><u><?php echo $keys->text_reminderDay; ?></u></th>
  <th colspan="2"></th>
</tr>
<?php
$alt="";
foreach($tasks as $task) {
  if($alt == "") $alt = 'class="alt"';
  else $alt = "";
  echo '<tr>';
  echo '<td '.$alt.'><input type="text" disabled size="80" value="'.$task['memo'].'"></td>';
  echo '<td '.$alt.'>'.$currencySymbol.' <input disabled type="text" size="5" value="'.$task['amount'].'"></td>';
  echo '<td '.$alt.'><input type="text" size="10" disabled value="'.$task['reminderDay'].'"></td>';
  echo '<td '.$alt.'><a href="#taskScheduler:taskSchedulerDiv&backLink='.$backLink.'&taskId='.$task['id'].'"><img src="style/icons/clock_edit.png"></a></td>';
  echo '<td '.$alt.'><a href="#taskScheduler:taskSchedulerDiv" onclick=\'taskSchedulerDelete("'.$task['id'].'")\'><img src="style/icons/bin_empty.png"></a></td>';
  echo '</tr>';
  }
?>
</table>
<?php
  } // if tasks are defined
?>
</div>