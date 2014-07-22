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
<h1><img src="style/icons/clock_play.png"/> <?php echo $keys->text_actionTransactionRanges; ?></h1>
<table width="100%" class="tableReport">
<tr>
  <th><?php
foreach($availableReportRanges as $range) {
  if($range['activeFlag'] == "1") {
    echo $range["name"];
    }
  else {
    echo '<a href="#" setReport="true" id="'.$range["id"].'">'.$range["name"].'</a>';
    }
  echo ' - ';
  }?>
  </th>
  <th>
  <?php
if(count($tasks) > 0) {
  echo '<ul style="float:right; width: 450px; margin: 0; padding 0; text-align: left;" class="reminders" id="reminders">';
  foreach($tasks as $when=>$task) {
    echo '<li><a href="#" action="taskScheduler" backLink="action=home" taskId="'.$task['id'].'">';
    if(date('j') == $task['reminderDay']) echo '<img src="style/icons/clock_red.png"/> '.$task['memo'].' - '.$currencySymbol.$task['amount']; 
    else echo '<img src="style/icons/clock_error.png"/> '.$when.' '.$keys->text_days.': '.$task['memo'].' - '.$currencySymbol.$task['amount'];
    echo '</a></li>';
    }
  echo '</ul>';
  echo '<script type="text/javascript">';
  echo '$("#reminders").newsticker();';
  echo '</script>';
  }
?>

  </th>
  <th style="text-align: right;">
    <a href="#" title="<?php echo $keys->linkText_editReportSetting;?>" editReportSettings="true" add="0"><img src="style/icons/table_edit.png"/></a>&nbsp;&nbsp;| 
    <a href="#" title="<?php echo $keys->linkText_addReportSetting;?>" editReportSettings="true" add="1"><img src="style/icons/table_add.png"/></a>&nbsp;&nbsp;|
    <a href="#" title="<?php echo $keys->linkText_deleteReportSetting;?>" deleteReportSettings="true"><img src="style/icons/bin_empty.png"/></a>&nbsp;&nbsp;|
    <a href="#" title="<?php echo $keys->linkText_taskReminderEditLink;?>" action="taskScheduler" backLink="home"><img src="style/icons/clock_edit.png"/></a>&nbsp;&nbsp;|
    <a href="#" title="<?php echo $keys->linkText_printReport;?>" onclick="window.print();return false;"><img src="style/icons/printer.png"/></a>
  </th>
</table>

<?php if($context->errorFlag) { ?>
<br/><br/><br/>
<div class="warning"><blockquote><?php echo $keys->error_GenericWarning; ?>: <?php echo $keys->$error;?></blockquote></div>
<div>
<ul>
  <?php if($error != 'error_noReportSelected') {?>
  <li><a href="#" editReportSettings="true" add="0""><?php echo $keys->text_EditReport;?>: <strong><?php echo $report->reportSettings->name;?></strong></a></li>
  <?php } ?>
  <li><?php echo $keys->text_ChooseReport;?></li>
  <ul><?php foreach($availableReportRanges as $range) {
    if($report->reportSettings->id != $range['id']) {
      echo '<li><a href="#" setReport="true" id="'.$range["id"].'">'.$range["name"].'</a></li>';
      }
    }?>
  </ul>
</ul>
</div>
<br/><br/><br/>
<?php 
  return;
  }
?>
<?php if($report->reportSettings->debit == '1') { ?>
<script type="text/javascript">
// JavaScript Document
function drawVisualization() {
    var data = new google.visualization.DataTable();
      data.addRows(6);
      data.addColumn('string', 'Categories');
      data.addColumn('date', 'Date');
      data.addColumn('number', 'Credit');
      data.addColumn('number', 'Debit');
      //data.addColumn('string', 'Type');
      
      data.setValue(0, 0, 'Appless');
      data.setValue(0, 1, new Date (1988,0,1));
      data.setValue(0, 2, 1000);
      data.setValue(0, 3, 300);
      //data.setValue(0, 4, 'East');
      
      data.setValue(1, 0, 'Oranges');
      data.setValue(1, 1, new Date (1988,0,1));
      data.setValue(1, 2, 950);
      data.setValue(1, 3, 200);
      //data.setValue(1, 4, 'West');
      
      data.setValue(2, 0, 'Bananas');
      data.setValue(2, 1, new Date (1988,0,1));
      data.setValue(2, 2, 300);
      data.setValue(2, 3, 250);
      //data.setValue(2, 4, 'West');
      
      data.setValue(3, 0, 'Appless');
      data.setValue(3, 1, new Date(1988,1,1));
      data.setValue(3, 2, 1200);
      data.setValue(3, 3, 400);
      //data.setValue(3, 4, "East");
      
      data.setValue(4, 0, 'Oranges');
      data.setValue(4, 1, new Date(1988,1,1));
      data.setValue(4, 2, 900);
      data.setValue(4, 3, 150);
      //data.setValue(4, 4, "West");
      
      data.setValue(5, 0, 'Bananas');
      data.setValue(5, 1, new Date(1988,1,1));
      data.setValue(5, 2, 788);
      data.setValue(5, 3, 617);
      //data.setValue(5, 4, "West");
    
      var motionchart = new google.visualization.MotionChart(
        document.getElementById('visualization'));
        motionchart.draw(data, {'width': 1250, 'height': 400});
        }
      drawVisualization();
  </script>
<div id="visualization" style="width: 800px; height: 400px;"></div>
<div id="reportGraph">
  <div class="carousel" > 
      <ul> 
        <li><img src="<?php echo $url3; ?>" alt="" width="550" height="200" /></li>
        <li><img src="<?php echo $url4; ?>" alt="" width="450" height="200" /></li>
        <li><img src="<?php echo $url2; ?>" alt="" width="450" height="200" /></li>
        <li><img src="<?php echo $url5; ?>" alt="" width="450" height="200" /></li> 
      </ul> 
    </div>
</div>

<?php }

echo "<h2><img src=\"style/icons/coins.png\"/> $keys->tableTitle_overview: $currencySymbol$balance ($keys->text_TotalCredit: $currencySymbol$creditBalance, $keys->text_TotalDebit: $currencySymbol$debitBalance, $keys->text_TotalTransactions: $context->totalItemCount)</h2>";
?>
<table class="tableReport print">
<tr><th>&nbsp;</th>
<?php
foreach($dates as $date) {
  echo '<th class="nobg">'.utf8_encode(strftime('%b', strtotime('01-'.$date))).'</th>';
  }
?>
 <th ><?php echo $keys->text_Total; ?></th>
</tr>
<?php 
if($report->reportSettings->credit == "1") {
?>
<tr class="hoverHighlight"><td class="category"><?php echo $keys->text_Credit ?></td>
<?php
    // start the row
  foreach($dates as $date) {
    if(isset($totalCreditsPerMonth[$date])) echo '<td>'.$report->formatNumber($totalCreditsPerMonth[$date]).'</td>';
    else echo '<td>'.$report->formatNumber(0).'</td>';
    }
  // print the total and average
  echo '<td class="total">'.$report->formatNumber(($totalCredits)).'</td>';
 ?>
</tr>
<?php
 }
?>
<?php 
if($report->reportSettings->debit == "1") {
?>
<tr class="hoverHighlight"><td class="category"><?php echo $keys->text_Debit ?></td>
<?php
    // start the row
  foreach($dates as $date) {
    if(isset($totalDebitsPerMonth[$date])) echo '<td>'.$report->formatNumber($totalDebitsPerMonth[$date]).'</td>';
    else echo '<td>'.$report->formatNumber(0).'</td>';
    }
  // print the total and average
  echo '<td class="total">'.$report->formatNumber($totalDebits).'</td>';
 ?>
</tr>
<?php
 }
?>
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
<tr><td colspan="<?php echo $colCount; ?>"><br/></td></tr>
</table>
<?php 
if($report->reportSettings->debit == "1") {
?>
<h2><img src="style/icons/medal_gold_1.png"/> <?php echo $keys->tableTitle_debit; ?></h2>
<table class="tableReport print">
<tr><th class="nobg" colspan="<?php echo $colCount; ?>"><?php
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

?></th></tr>
<tr><th><?php echo $keys->text_Debit; ?></th>
<?php
foreach($dates as $date) {
  echo '<th class="nobg">'.utf8_encode(strftime('%b', strtotime('01-'.$date))).'</th>';
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
  $categoryLink = '<a href="#" title="'.$keys->linkText_listCategoryItem.'" action="listCategoryItems_categorySelected='.$id.'_debit=1">'.$report->getCategoryName($id).'</a>';
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
      echo '<td class="'.$class.'"><a href="#" title="'.$keys->linkText_listCategoryMonthItem.'" action="listCategoryItems_monthSelected='.$date.'_categorySelected='.$id.'_debit=1">'.$report->formatNumber($debitPerCategoryPerMonth[$id][$date]).'</a></td>';
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
  echo '<td class="'.$class.'"><a href="#" title="'.$keys->linkText_editCategorySettings.'" action="editCategories">'.sprintf("%.2f",($total/$count)).'</a> ';
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
<tr><td colspan="<?php echo $colCount; ?>"><br/></td></tr>
</table>
<?php
  } // if debit is enable in report settings
if($report->reportSettings->credit == "1") {
?>
<h2><img src="style/icons/medal_gold_2.png"/> <?php echo $keys->tableTitle_credit; ?></h2>
<table class="tableReport print">
<tr><th><?php echo $keys->text_Credit; ?></th>
<?php
foreach($dates as $date) {
  echo '<th class="nobg">'.utf8_encode(strftime('%b', strtotime('01-'.$date))).'</th>';
  }
?>
 <th><?php echo $keys->text_Total; ?></th>
 <th><?php echo $keys->text_Percent; ?></th>
 <th><?php echo $keys->text_Average; ?> (<?php echo $keys->text_PresetBudget; ?>)</th>
</tr>
<?php
foreach($categories as $id=>$name) {
  // build the category link
  $link = '<a href="#" title="'.$keys->linkText_listCategoryItem.'" action="listCategoryItems_categorySelected='.$id.'_debit=0">'.$report->getCategoryName($id).'</a>';
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
      echo '<td class="categoryAmount"><a href="#" title="'.$keys->linkText_listCategoryMonthItem.'" action="listCategoryItems_monthSelected='.$date.'_categorySelected='.$id.'_debit=0">'.$amount.'</a></td>';
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
  echo '<td class="'.$class.'"><a href="#" title="'.$keys->linkText_editCategorySettings.'" action="editCategories">'.sprintf("%.2f",($total/$count)).'</a> ';
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


<tr><td colspan="<?php echo $colCount; ?>"><br/></td></tr>
</table>

<input id="confirmMessage" type="hidden" value="<?php echo $keys->text_deleteReportSettings; ?>"/>
<script type="text/javascript">
$(function() {
    $(".carousel").jCarouselLite({
        btnNext: ".next",
        btnPrev: ".prev",
        speed: 1500,
        auto: 1500
    });
});
</script>