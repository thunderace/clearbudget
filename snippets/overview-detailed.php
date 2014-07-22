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
  <td style="text-align:right"><a href="#overview-timeline" class="noprint"><?php echo $keys->text_overviewTimeline;?></a> | <strong><?php echo $keys->text_overviewDetailed;?></strong> | <a href="#overview-simple" class="noprint"><?php echo $keys->text_overviewSimple;?></a></td>
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
if($report->reportSettings->debit == '1') {
  echo "<h2 id=\"div_graphs\" class=\"print toggle\"><img src=\"style/icons/chart_curve.png\" /> {$keys->pageTitle_ReportMainGraph} <span class=\"openDiv\">&nbsp;</span></h2>";
  echo $this->includeAction('overview-graphs');
  }
?>
<h2 id="div_summary" class="print toggle"><img src="style/icons/coins.png"/><?php echo $keys->tableTitle_overview;?> <span class="openDiv">&nbsp;</span></h2>
<div id="summary">
<table class="tableReport print">
<tr>
<td rowspan="4" width="200"><img src="<?php echo $url5; ?>" border="1" alt="" width="200" height="90" /></td>
<th>&nbsp;</th>
<?php
foreach($dates as $date) {
  echo '<th class="nobg"><a href="#listMonthItems&monthSelected='.$date.'">'.utf8_encode(strftime('%b', strtotime('01-'.$date))).'</a></th>';
  }
?>
 <th ><?php echo $keys->text_Total; ?></th>
</tr>
<?php 
if($report->reportSettings->credit == "1") {
?>
<tr class="hoverHighlight">
<td class="category"><?php echo $keys->text_Credit ?></td>
<?php
    // start the row
  foreach($dates as $date) {
    if(isset($totalCreditsPerMonth[$date])) echo '<td class="categoryAmount"><a href="#listMonthItems&monthSelected='.$date.'&debit=0">'.$report->formatNumber($totalCreditsPerMonth[$date]).'</a></td>';
    else echo '<td class="categoryAmount"><a href="#listMonthItems&monthSelected='.$date.'&debit=0">'.$report->formatNumber(0).'</a></td>';
    }
  // print the total and average
  echo '<td class="total">'.$report->formatNumber(($totalCredits)).'</td>';
 ?>
</tr>
<?php
 } 
if($report->reportSettings->debit == "1") {
?>
<tr class="hoverHighlight"><td class="category"><?php echo $keys->text_Debit ?></td>
<?php
    // start the row
  foreach($dates as $date) {
    if(isset($totalDebitsPerMonth[$date])) echo '<td class="categoryAmount"><a href="#listMonthItems&monthSelected='.$date.'&debit=1">'.$report->formatNumber($totalDebitsPerMonth[$date]).'</a></td>';
    else echo '<td class="categoryAmount"><a href="#listMonthItems&monthSelected='.$date.'&debit=1">'.$report->formatNumber(0).'</a></td>';
    }
  // print the total and average
  echo '<td class="total">'.$report->formatNumber($totalDebits).'</td>';
 ?>
</tr>
<?php } ?>
<tr><th><?php echo $keys->text_Total; ?></th>
<?php
foreach($dates as $date) {
  if(isset($totalDebitsPerMonth[$date])) $debit = $totalDebitsPerMonth[$date];
  else $debit = 0;
  if(isset($totalCreditsPerMonth[$date])) $credit = $totalCreditsPerMonth[$date];
  else $credit = 0;
  echo '<th>'.$report->formatNumber($credit - $debit).'</th>';
  }
?>
 <th>&nbsp;</th>
</tr>
</table><br/>
</div>
<?php 
if($report->reportSettings->debit == "1") {
?>
<h2 id="div_debits" class="print toggle"><img src="style/icons/medal_gold_1.png"/> <?php echo $keys->tableTitle_debit; ?> <span class="openDiv">&nbsp;</span></h2>
<div id="debits">
<?php
// build the output string
if($cloud!=false) {
  echo '<div class="cloudDiv">';
  echo '<ul id="cloud">';
  foreach($cloud as $name=>$tag) {
    echo '<li><a cloud="'.$tag['catId'].'" class="tag'.$tag['tag'].'">'.$name.'</a></li>';
    }
  echo '</ul>';
  echo '</div>';
  }

?>
<table class="tableReport print">
<tr><th><?php echo $keys->text_Debit; ?></th>
<?php
foreach($dates as $date) {
  //echo '<th class="nobg">'.utf8_encode(strftime('%b', strtotime('01-'.$date))).'</th>';
  echo '<th class="nobg"><a href="#listMonthItems&monthSelected='.$date.'&debit=1">'.utf8_encode(strftime('%b', strtotime('01-'.$date))).'</a></th>';
  }
?>
 <th><?php echo $keys->text_Total; ?></th>
 <th><?php echo $keys->text_Percent; ?></th>
 <th><?php echo $keys->text_Average; ?> (<?php echo $keys->text_PresetBudget; ?>)</th>
</tr>
<?php
$alt = '';
foreach($categories as $id=>$name) {
  // build the category link
  $categoryLink = '<a href="#listCategoryItems&categorySelected='.$id.'&debit=1" title="'.$keys->linkText_listCategoryItem.'">'.$report->getCategoryName($id).'</a>';
  // get the total for that category
  (isset($debitTotalPerCategory[$id]))? $total = $report->formatNumber($debitTotalPerCategory[$id]): $total = '0.00';
  // if total=0 we skip this category
  if($total == 0) continue;
  // start the row
  echo '<tr id="cloud-'.$id.'" class="hoverHighlight"><td class="category">'.$categoryLink.'</td>';
  // loop over the column
  $count = 0;
  foreach($dates as $date) {
    $class = 'categoryAmount';
    // change color if amount is greater than max amount per month
    if($report->categoryMaxAmountPerMonth[$id] != null && $report->categoryMaxAmountPerMonth[$id] != '' 
      && $report->categoryMaxAmountPerMonth[$id]!= 0 && isset($debitPerCategoryPerMonth[$id][$date])
      && $debitPerCategoryPerMonth[$id][$date] > 0 && $debitPerCategoryPerMonth[$id][$date] > $report->categoryMaxAmountPerMonth[$id]) {
      $class = 'categoryAmountOver';
      }
      
    // build the link or the n/a text
    if(isset($debitPerCategoryPerMonth[$id][$date])) {
      echo '<td class="'.$class.'"><a href="#listCategoryItems&monthSelected='.$date.'&categorySelected='.$id.'&debit=1" title="'.$keys->linkText_listCategoryMonthItem.'">'.$report->formatNumber($debitPerCategoryPerMonth[$id][$date]).'</a></td>';
      }
    else {
      echo'<td>0.00</td>';
      }
    $count ++;
    }
  
  // print the total and average
  echo '<td class="total">'.$total.'</td>';
  echo '<td>'.$debitsPctPerCategory[$id].'</td>';
  // detect if over budget on average
  if($report->categoryMaxAmountPerMonth[$id]>0 && (($total/$count)>$report->categoryMaxAmountPerMonth[$id])) $class = 'categoryAmountOver';
  else $class = 'categoryAmount';
  echo '<td class="'.$class.'"><a href="#editCategories&backLink=home" title="'.$keys->linkText_editCategorySettings.'">'.sprintf("%.2f",($total/$count)).'</a> ';
  echo (($report->categoryMaxAmountPerMonth[$id]>0)?"(".$report->categoryMaxAmountPerMonth[$id].")":"").'</td></tr>';
  }
 ?>
<tr><th><?php echo $keys->text_Total; ?></th>
<?php
foreach($dates as $date) {
  if(isset($totalDebitsPerMonth[$date])) echo '<th>'.$report->formatNumber($totalDebitsPerMonth[$date]).'</th>';
  else echo '<th>'.$report->formatNumber(0).'</th>';
  }
?>
 <th><?php echo $totalDebits; ?></th>
 <th colspan="2"></th>
</tr>
</table><br/>
</div>
<?php
  } // if debit is enable in report settings
if($report->reportSettings->credit == "1") {
?>
<h2 id="div_credits" class="print toggle"><img src="style/icons/medal_gold_2.png"/> <?php echo $keys->tableTitle_credit; ?> <span class="openDiv">&nbsp;</span></h2>
<div id="credits">
<table class="tableReport print">
<tr><th><?php echo $keys->text_Credit; ?></th>
<?php
foreach($dates as $date) {
  //echo '<th class="nobg">'.utf8_encode(strftime('%b', strtotime('01-'.$date))).'</th>';
  echo '<th class="nobg"><a href="#listMonthItems&monthSelected='.$date.'&debit=0">'.utf8_encode(strftime('%b', strtotime('01-'.$date))).'</a></th>';
  }
?>
 <th><?php echo $keys->text_Total; ?></th>
 <th><?php echo $keys->text_Percent; ?></th>
 <th><?php echo $keys->text_Average; ?> (<?php echo $keys->text_PresetBudget; ?>)</th>
</tr>
<?php
foreach($categories as $id=>$name) {
  // build the category link
  $link = '<a href="#listCategoryItems&categorySelected='.$id.'&debit=0" title="'.$keys->linkText_listCategoryItem.'">'.$report->getCategoryName($id).'</a>';
  // get the total for that category
  (isset($creditTotalPerCategory[$id]))? $total = $report->formatNumber($creditTotalPerCategory[$id]): $total = '0.00';
  // if total=0 we skip this category
  if($total == 0) continue;
  // start the row
  echo '<tr class="hoverHighlight"><td class="category">'.$link.'</td>';
  // loop over the column
  $count = 0;
  foreach($dates as $date) {
    // get the total for that category and date
    (isset($creditPerCategoryPerMonth[$id][$date]))? $amount=$report->formatNumber($creditPerCategoryPerMonth[$id][$date]):$amount='0.00';
    // print the column
    if($amount > 0) {
      echo '<td class="categoryAmount"><a href="#listCategoryItems&monthSelected='.$date.'&categorySelected='.$id.'&debit=0" title="'.$keys->linkText_listCategoryMonthItem.'">'.$amount.'</a></td>';
      }
    else {
      echo '<td class="categoryAmount">'.$amount.'</td>';
      }
    
    $count ++;
    }
  // print the total and average
  echo '<td class="total">'.$total.'</td>';
  echo '<td>'.$creditsPctPerCategory[$id].'</td>';
  // detect if over budget on average
  if($report->categoryMaxAmountPerMonth[$id]>0 && (($total/$count)>$report->categoryMaxAmountPerMonth[$id])) $class = 'categoryAmountOver';
  else $class = 'categoryAmount';
  echo '<td class="'.$class.'"><a href="#editCategories" title="'.$keys->linkText_editCategorySettings.'">'.sprintf("%.2f",($total/$count)).'</a> ';
  echo (($report->categoryMaxAmountPerMonth[$id]>0)?"(".$report->categoryMaxAmountPerMonth[$id].")":"").'</td></tr>';
  }
 ?>
<tr class="hoverHighlight"><th><?php echo $keys->text_Total; ?></th>
<?php
foreach($dates as $date) {
  if(isset($totalCreditsPerMonth[$date])) echo '<th>'.$report->formatNumber($totalCreditsPerMonth[$date]).'</th>';
  else echo '<th>'.$report->formatNumber(0).'</th>';
  }
?>
 <th><?php echo $report->totalCredits; ?></th>
 <th colspan="2"></th>
</tr>
<?php
  } // if credit is enable in report settings
?>
</table><br/>
</div>
</div>
