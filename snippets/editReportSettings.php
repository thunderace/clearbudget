<?php
/**
* Edit or add a report settings
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
?>
<script type="text/javascript">
	$(function() {
	  $.datepicker.setDefaults($.extend({showMonthAfterYear: false}, $.datepicker.regional['<?php echo $lang;?>']));
		$("#datepicker1").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: 'style/icons/calendar.gif', buttonImageOnly: true});
		$("#datepicker2").datepicker({dateFormat: 'yy-mm-dd', showOn: 'button', buttonImage: 'style/icons/calendar.gif', buttonImageOnly: true});

	});
	</script>
<form id="editReportSettings" onSubmit="editReportSettings(); return false">
<input type="hidden" name="reportId" value="<?php echo $reportSettings->id;?>"/>
<h2><img src="style/icons/table_edit.png"/> <?php echo $keys->text_editReportSettings; ?> - <a href="#home"><?php echo $keys->link_back; ?></a></h2>
<table class="tableReport">
<tr><td><?php echo $keys->text_reportName;?></td><td colspan="3"><input type="text" tabindex="1" name="name" value="<?php echo $reportSettings->name; ?>" size="20"/></td></tr>
<tr><td><?php echo $keys->text_reportDebitCredit;?></td><td colspan="3">
  <?php echo $keys->text_Debit; ?> <input type="radio" tabindex="2" name="transactions" value="debit" <?php if($reportSettings->credit == '0' && $reportSettings->debit == '1') echo 'checked'; ?>/>&nbsp; - &nbsp;
  <?php echo $keys->text_Credit; ?> <input type="radio" tabindex="3" name="transactions" value="credit" <?php if($reportSettings->credit == '1' && $reportSettings->debit == '0') echo 'checked'; ?>/>&nbsp; - &nbsp;
  <?php echo $keys->text_All; ?> <input type="radio" tabindex="4" name="transactions" value="all" <?php if($reportSettings->credit == '1' && $reportSettings->debit == '1') echo 'checked'; ?>/>
  </td>
</tr>
<tr>
  <td rowspan="3"><?php echo $keys->text_reportRange;?></td>
</tr>
<tr>
  <td style="vertical-align: text-top"><?php echo $keys->text_MonthRange; ?> <input type="radio" tabindex="5" name="type" value="range" <?php if($reportSettings->type == 'range') echo 'checked'; ?>/></td>
  <td colspan="2"><?php echo $keys->text_DateRange; ?> <input type="radio" tabindex="6" name="type" value="custom" <?php if($reportSettings->type == 'custom') echo 'checked'; ?></td>
</tr>
<tr>
  <td style="vertical-align: text-top"><input type="text" name="range"  tabindex="7" value="<?php echo $reportSettings->range; ?>" size="2"/> <?php echo $keys->text_Month; ?></td>
  <td><?php echo $keys->text_StartDate; ?> <input type="text" tabindex="8" id="datepicker1" name="startDate" value="<?php echo $reportSettings->minDate; ?>"></td>
  <td><?php echo $keys->text_EndDate; ?> <input type="text" tabindex="9" id="datepicker2" name="endDate" value="<?php echo $reportSettings->maxDate; ?>"></td>
</tr>
<tr>
  <td><?php echo $keys->text_reportCategories;?></td>
  <td colspan="3"><select  tabindex="10" name="categories[]" multiple>
<?php  foreach($categories as $categoryId) {
    echo '<option value="'.$categoryId.'"';
    if(in_array($categoryId, $reportCategories)) echo 'selected';
    echo '>';
    if($categoryId != '0') echo $report->getCategoryName($categoryId);
    else echo 'All';
    echo '</option>';
    }
?></select>
  </td>
</tr>
<tr>
  <td colspan="2">
  <span class="hidden" id="resultDiv"></span>
  </td>
  <td colspan="2">
  <img src="style/icons/icon_Loading.gif" class="hidden" id="loadingSpan">
  <input type="image"  tabindex="11" src="style/icons/disk.png" tabindex="7" value="<?php echo $keys->link_save; ?>">
  </td>
</tr>
</table>
</form>
<br/>