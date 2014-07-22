<?php
/**
* Display the homepage screen
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
?>

<h1 id="div_menus" class="toggle print"><img src="style/icons/chart_pie.png" /> <?php echo $keys->text_actionTransactionRanges.': '.ucwords($reportSettings->name); ?> <div class="openDiv">&nbsp;</div></h1>
<input id="confirmMessage" type="hidden" value="<?php echo $keys->text_deleteReportSettings; ?>"/>
<div id="menus">
<table width="100%" class="tableReport print">
<tr>
  <td><?php echo "$keys->text_OverallBalance: $currencySymbol $balance";?></td>
  <td><?php echo "$keys->text_TotalCredit: $currencySymbol $creditBalance";?></td>
  <td><?php echo "$keys->text_TotalDebit: $currencySymbol $debitBalance";?></td>
  <td><?php echo "$keys->text_TotalTransactions: $context->totalItemCount";?></td>
  <td style="text-align:right"><strong><?php echo $keys->text_overviewTimeline;?></strong> | <a href="#overview-detailed" class="noprint"><?php echo $keys->text_overviewDetailed;?></a> | <a href="#overview-simple" class="noprint"><?php echo $keys->text_overviewSimple;?></a></td>
</tr>
</table>
<table width="100%" class="tableReport">
<tr>
  <td><?php echo $keys->linkText_chooseReport; ?> <select id="reportSelector" onchange='setReport()'><?php
foreach($availableReportRanges as $range) {
  if($range['activeFlag'] == "1") {
    echo '<option value="'.$range["id"].'" selected>'.$range["name"].'</option>';
    }
  else {
    echo '<option value="'.$range["id"].'">'.$range["name"].'</option>';
    }
  }?>
  </select></td>
<?php
if(count($tasks) > 0) {
?>
  <td><a href="#taskScheduler&backLink=home"><?php echo $keys->totalMonthTasksAmount.': '.$currencySymbol.' '.$totalTasksAmount;?></a></td>
  <td style="width:600px">
    <ul id="reminders">
      <?php
      foreach($tasks as $task) {
        echo '<li>';
        if($task['when'] == 0) echo '<img src="style/icons/clock_red.png"/> <a href="#taskScheduler&backLink=home&taskId='.$task['id'].'">'.$task['memo'].'</a> '.$currencySymbol.' '.$task['amount'];
        else echo '<img src="style/icons/clock_error.png"/> '.$task['when'].' '.$keys->text_days.': <a href="#taskScheduler&backLink=home&taskId='.$task['id'].'">'.$task['memo'].'</a> '.$currencySymbol.' '.$task['amount'];
        echo '</li>';
        }
      ?>
    </ul>
  <script type="text/javascript">
  $("#reminders").newsticker();
  </script>
  </td>
<?php
  }
?>
  <td style="text-align: right;">
    <a href="#editReportSettings&add=0" title="<?php echo $keys->linkText_editReportSetting;?>"><img src="style/icons/table_edit.png"/></a>&nbsp;&nbsp;|
    <a href="#editReportSettings&add=1" title="<?php echo $keys->linkText_addReportSetting;?>"><img src="style/icons/table_add.png"/></a>&nbsp;&nbsp;|
    <a href="#home" onclick="deleteReportSettings()" title="<?php echo $keys->linkText_deleteReportSetting;?>"><img src="style/icons/bin_empty.png"/></a>&nbsp;&nbsp;|
    <a href="#taskScheduler&backLink=home" title="<?php echo $keys->linkText_taskReminderEditLink;?>"><img src="style/icons/clock_edit.png"/></a>&nbsp;&nbsp;|
    <a href="#print" title="<?php echo $keys->linkText_printReport;?>" onclick="window.print();return false;"><img src="style/icons/printer.png"/></a>
  </td>
</table>
<br/></div>
<?php if($context->errorFlag) { ?>
<br/><br/><br/>
<div class="warning"><blockquote><?php echo $keys->error_GenericWarning; ?>: <?php echo $keys->$error;?></blockquote></div>
<div>
<ul>
  <?php if($error != 'error_noReportSelected') {?>
  <li><a href="#editReportSettings&add=0"><?php echo $keys->text_EditReport;?>: <strong><?php echo $report->reportSettings->name;?></strong></a></li>
  <?php } ?>
  <li><?php echo $keys->text_ChooseReport;?></li>
  <li><ul><?php foreach($availableReportRanges as $range) {
    if($report->reportSettings->id != $range['id']) {
      echo '<li><a href="javascript:setReport(\''.$range["id"].'\')">'.$range["name"].'</a></li>';
      }
    }?>
  </ul></li>
</ul>
</div>
<br/><br/><br/>
<?php
  return;
  }
//echo "<h2 id=\"div_graphs\" class=\"print toggle\"><img src=\"style/icons/chart_curve.png\" /> {$keys->pageTitle_ReportMainGraph} <span class=\"openDiv\">&nbsp;</span></h2>";
echo $this->includeAction('overview-graphs');
?>
<div id="editTransactionManualy"></div>
<div id="transactionTimeline" style="height: 450px; border: 1px solid #aaa"></div>
