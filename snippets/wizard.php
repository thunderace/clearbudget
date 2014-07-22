<?php
/**
* Display the action screen
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
<h1><img src="style/icons/transmit.png"> <?php echo $keys->link_menuWizard; ?></h1>
<table width="100%" class="tableReport">
<tr>
  <th style="text-align: middle; height: 80px">
    <img src="style/btn1.png" align="middle" />
    <button onclick="wizard(1)" id="step1button"><img src="style/icons/transmit_go.png" id="step1">&nbsp;&nbsp; <?php echo $keys->link_WizardStep1; ?></button>
  </th>
  <th style="text-align:center; height: 80px">
    <img src="style/btn2.png" align="middle"/>
    <button <?php if(!$doTransaction) { ?>disabled<?php } ?> onclick="wizard(2)" id="step2button"><img src="style/icons/transmit.png"  id="step2"><?php echo $keys->link_WizardStep2; ?></button>
  </th>
  <th style="text-align:center; height: 80px">
    <img src="style/btn3.png" align="middle"/>
    <button <?php if(!$doKeyword) { ?>disabled<?php } ?> onclick="wizard(3)" id="step3button"><img src="style/icons/transmit.png" id="step3"><?php echo $keys->link_WizardStep3; ?></button>
  </th>
  <th style="text-align:center; height: 80px">
    <img src="style/btn4.png" align="middle"/>
    <button <?php if(!$doSort) { ?>disabled<?php } ?> onclick="wizard(4)" id="step4button"><img src="style/icons/transmit.png" id="step4" ><?php echo $keys->link_WizardStep4; ?></button>
  </th>
</tr>
</table>
<div id="wizard"></div>
<script language="javascript">
  // start the wizard
  wizard(<?php echo $wizardId; ?>);
</script>
