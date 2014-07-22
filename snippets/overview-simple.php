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
  <td style="text-align:right"><a href="#overview-timeline" class="noprint"><?php echo $keys->text_overviewTimeline;?></a> | <a href="#overview-detailed" class="noprint"><?php echo $keys->text_overviewDetailed;?></a> | <strong><?php echo $keys->text_overviewSimple;?></strong></td>
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
<?php return; } ?>
<?php
foreach($spendingSummary as $type=>$transactions) {
  echo "<h2 id=\"div_$type\" class=\"print toggle\"><img src=\"style/icons/chart_curve.png\"/> ".$keys->{'link_'.$type}." <span class=\"openDiv\">&nbsp;</span></h2>";
  echo "<div id=\"$type\">";
  // build the envelope for each category
  foreach($report->categories as $catId=>$catName) {
    //if($catId == 1) continue;
    // if no data, jump to next category
    if(!isset($transactions[$catId])) continue;
    // start the envelope div
    echo "<div class=\"print enveloppe\">";
    echo "<h2>";
    echo $catName;
    echo "<a href=\"#editCategories:subAction1&backLink=home\"><img src=\"style/icons/bullet_wrench.png\" class=\"noprint\" style=\"margin:5px; padding:0;\"/></a>";
    echo "</h2>";
    // build graph
    $chart = new GoogleChart('lc', '', 250, 100);
    // the main line color
    $chart->addColor('9797FF');
    // the trend line color (depends on Trend and credit/debit
    if($type=='debits') {
      if($categoryStats[$type][$catId]['b'] > 0) $chart->addColor('FF0000');
      else $chart->addColor('00FF00');
      }
    else {
      if($categoryStats[$type][$catId]['b'] >= 0) $chart->addColor('00FF00');
      else $chart->addColor('FF0000');
      }
    // the limit line color
    if($report->categoryMaxAmountPerMonth[$catId] > 0) {
      $chart->addColor('A9A9A9');
      }
    // thresholds
    $maxVal = 0;
    $minVal = 10000000000;
    // the x axis with the dates
    for($i=0; $i<count($transactions[$catId]); $i++) {
      $month = explode('-', $transactions[$catId][$i]['date']);
      $chart->addxAxis($month[0]);
      }
    // the transactions value
    for($i=0; $i<count($transactions[$catId]); $i++) {
      $total = sprintf("%.2f", $transactions[$catId][$i]['total']);
      $chart->addValue($total);
      if($total>$maxVal) $maxVal = $total;
      if($total<$minVal) $minVal = $total;
      }
    $chart->addLineStyle('2,2,2');
    $chart->addValueSerie();
    // the average line
    for($i=0; $i<count($transactions[$catId]); $i++) {
      $total = sprintf("%.2f", $categoryStats[$type][$catId]['a'] + ($categoryStats[$type][$catId]['b']*$i));
      $chart->addValue($total);
      if($total>$maxVal) $maxVal = $total;
      if($total<$minVal) $minVal = $total;
      }
    $chart->addLineStyle('1,15,5');
    $chart->addValueSerie();
    // the category limit if it is set
    if($report->categoryMaxAmountPerMonth[$catId] > 0) {
      $total = $report->categoryMaxAmountPerMonth[$catId];
      for($i=0; $i<count($transactions[$catId]); $i++) {
        $chart->addValue($total);
        }
      $chart->addLineStyle('1,1,0');
      $chart->addValueSerie();
      }

    //$chart->addTransparency();
    if($minVal<0) $minVal = 1.3*$minVal;
    else $minVal = 0.7*$minVal;
    $maxVal = 1.3*$maxVal;
    $chart->addScaling((int)$minVal, (int)$maxVal);
    // build the y axis
    $yAxis = (int)$minVal.'|'.(int)(($maxVal+$minVal)/2).'|'.(int)$maxVal;
    $chart->addyAxis($yAxis);
    $url = $chart->getURL();
    echo "<p>";
    echo "<img src=\"$url\" style=\"align:center;margin:0; padding:0;\"/>";
     // echo stats
    $lastMonthTotal = $categoryStats[$type][$catId]['lastMonthTotal'];
    if($type == 'debits') $debitsFlag = '1';
    else $debitsFlag = '0';
    echo "<table><tr><td>";
    if($type=='debits'){
        if($categoryStats[$type][$catId]['b'] > 0 ){ echo "<img src=\"style/icons/thumb_down.png\" align=\"center\">"; }
        else { echo "<img src=\"style/icons/thumb_up.png\" align=\"center\">"; }
      }
    else {
        if($categoryStats[$type][$catId]['b'] < 0 ){ echo "<img src=\"style/icons/thumb_down.png\" align=\"center\">"; }
        else { echo "<img src=\"style/icons/thumb_up.png\" align=\"center\">"; }
    }
    echo " <a href=\"#listCategoryItems&categorySelected={$catId}&debit=$debitsFlag\">$currencySymbol $lastMonthTotal</a></td>";
    echo "<td>";
    if($report->categoryMaxAmountPerMonth[$catId] > 0) {
      echo "<img src=\"style/icons/flag_red.png\" align=\"center\">";
      echo "$currencySymbol {$report->categoryMaxAmountPerMonth[$catId]}";
      }
    echo "</td>";
    echo "<td><img src=\"style/icons/sum.png\" align=\"center\">$currencySymbol ";
    if($type=='debits') {
      echo $debitTotalPerCategory[$catId]." ({$debitsPctPerCategory[$catId]}%)";
      }
    else {
      echo $creditTotalPerCategory[$catId]." ({$creditsPctPerCategory[$catId]}%)";
      }
    echo "</td>";
    echo "</tr></table>";
    echo "</p>";
    echo "</div>";
    }
  echo "<div style=\"clear: both;\"></div>";
  echo "</div>";
  }
?>