<?php
/**
* credits to all free software used in clearbudget
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
<h1>Share your statistics</h1>
<p class="text5">We welcome you to share the below information with us in order to build large scale statistics on finance and budget management.<br/>
  Please share this with us on a regular basis as to keep our master database up to date!
  <ul><li>There are no personal data included and no ways to link this back to you: your privacy is important to us!.</li>
  <li>The data will be kept by us and not shared in any ways with third parties.</li>
  <li>The data will be aggregated in our main database in order to build large scale statistics.</li>
  <li>The statistic results will be freely available on the clearBudget.net website.</li>
</ul></p>
<table class="tableReport">
  <thead>
    <tr>
      <th>Data label</th>
      <th>Data Value</th>
    </tr>
  </thead>
  <tbody>
    <tr><td>Browser Version</td><td><?php echo $userAgent; ?></td></tr>
    <tr><td>clearBudget Version</td><td><?php echo CB_VERSION; ?></td></tr>
    <tr><td>Number of categories</td><td><?php echo $numOfCategories; ?></td></tr>
    <tr><td>Number of keywords</td><td><?php echo $numOfKeywords; ?></td></tr>
    <tr><td>Main language</td><td><?php echo $language; ?></td></tr>
    <tr><td>Currency</td><td><?php echo $currency; ?></td></tr>
    <tr><td>Total number of Credit transactions</td><td><?php echo $numCredits; ?></td></tr>
    <tr><td>Total Number of Debit transactions</td><td><?php echo $numDebits; ?></td></tr>
    <tr><td>Credit values per Month</td><td><?php foreach($creditPerMonth as $date=>$amount) {echo $date.': '.$amount.', '; } ?></td></tr>
    <tr><td>Debit values per Month</td><td><?php foreach($debitPerMonth as $date=>$amount) {echo $date.': '.$amount.', '; } ?></td></tr>
  </tbody>
</table>
<div  style="text-align: center">
<p class="text5">
<form action="http://www.clearbudget.net/dataStore.html" method="POST">
  <input type="hidden" name="uid" value="<?php echo $uid; ?>"/>
  <input type="hidden" name="data" value="<?php echo $data; ?>"/>
  <input type="submit" name="submit" value="Click here to share your data"/>
</form>
</p>
</div>