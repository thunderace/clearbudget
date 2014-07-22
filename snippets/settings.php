<?php
/**
* Display the user preferences
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
$userSettings = class_settings::getInstance();
$userSettings->getSettings();
// by default, the main form is disabled
$disableMainForm = 'disabled';
if($userSettings->secureAccess()) {
  $isAdmin = $userSettings->isAdmin();
  if($isAdmin) $disableMainForm = '';
}
else {
  $isAdmin = true;
  $disableMainForm = '';
}

function makeUserTableRow($id, $user, $keys, $isAdmin, $userSettings) {
  $formId = 'userForm-'.$id;
  $loadingImage = 'userLoading-'.$id;
  echo '<form id="'.$formId.'" onSubmit="userEditForm(\''.$formId.'\', \''.$loadingImage.'\'); return false">';
  echo '<input type="hidden" id="'.$formId.'_userId" name="userId" value="'.$id.'"/>';
  echo '<table class="tableReport">';
  echo '<tr>';
  echo '<td style="width:250px">'.$keys->text_login.'<br/><input type="text" size="25" value="'.$user->username.'" name="username" id="username"/></td>';
  echo '<td style="width:250px">'.$keys->text_password.'<br/><input type="password" size="15" value="" name="password" id="password"/></td>';
  echo '<td style="width:250px">'.$keys->text_language.'<br/><select name="language" id="language">';
  $languages = $keys->getAvailableLanguages();
  foreach($languages as $lang) {
    $key = 'link_SetLanguage_'.$lang;
    echo '<option value="'.$lang.'" ';
    if($user->userLanguage == $lang) echo 'selected';
    echo '>'.$keys->$key.'</option>';
    }
  echo '</select></td>';
  echo '<td style="width:150px">'.$keys->text_userType.'<br/><select name="type" id="type"';
  if(!$isAdmin) echo 'disabled>';
  else echo '>';
  echo '<option value="user" ';
  if($user->type == 'user') echo ' selected';
  echo '>User</option>';
  echo '<option value="admin"';
  if($user->type == 'admin') echo ' selected';
  echo '>Admin</option>';
  echo '</select></td>';
  echo '<td  style="width:180px"><span id="currentPassword'.$id.'"';
  if($id<=0) echo ' style="display: none;"';
  echo '>';
  if($isAdmin) echo $keys->text_admin_password;
  else echo $keys->text_current_password;
  echo '<br/> <input size="8" type="password" value="" name="currentPassword"/></span></td>';
  echo '<td width="30"><img class="hidden" id="'.$loadingImage.'" src="style/icons/icon_Loading.gif"/></td>';
  echo '<td width="30"><input type="image" src="style/icons/disk.png" value="'.$keys->link_save.'"/></td>';
  echo '<td width="30">';
  if($id>0 && $isAdmin && $id!=$userSettings->id) {
    echo '<a href="javascript:blockUser('.$id.')">';
    if($user->enabled) echo '<img src="style/icons/user_green.png" id="blockImg'.$id.'"/>';
    else echo '<img src="style/icons/user_red.png" id="blockImg'.$id.'"/>';
    echo '</a>';
  }
  echo '</td>';
  echo '</tr></table></form>';
  }
 ?>
<h1><img src="style/icons/user_edit.png"/> <?php echo $keys->pageTitle_accountSettings; ?></h1>
<h2><img src="style/icons/application_edit.png"/> <?php echo $keys->pageTitle_defaultAccountSettings; ?></h2>
<?php if($isAdmin) {?><form id="applicationSettings" onSubmit="applicationSettings(); return false"><?php } ?>
<table class="tableReport">
<tr>
  <th><?php echo $keys->text_settingName; ?></th>
  <th><?php echo $keys->text_settingInput; ?></th>
</tr>
<tr><td><?php echo $keys->text_secureAccess; ?></td>
<td>
<input type="radio" name="secureAccess" id="secureAccessOff" <?php echo $disableMainForm; ?> value='0' <?php if($secureAccess === '0') echo 'checked';?>><?php echo $keys->text_no; ?>
<input type="radio" name="secureAccess" id="secureAccessOn" <?php echo $disableMainForm; ?> value='1' <?php if($secureAccess == '1') echo 'checked';?>><?php echo $keys->text_yes; ?>
</td></tr>
<tr><td><?php echo $keys->text_language; ?>*</td>
<td><select name="language" id="language" <?php echo $disableMainForm; ?> >
<?php
$languages = $keys->getAvailableLanguages();
foreach($languages as $lang) {
  $key = 'link_SetLanguage_'.$lang;
  echo '<option value="'.$lang.'" ';
  if($defaultLanguage == $lang) echo 'selected';
  echo '>'.$keys->$key.'</option>';
  }
?>
</select></td></tr>
<tr><td><?php echo $keys->text_currency; ?>*</td>
<td><select name="currency" id="currency" <?php echo $disableMainForm; ?>>
<?php
$currencies = class_currency::getCurrencyList();
foreach($currencies as $code=>$name) {
  echo '<option value="'.$code.'" ';
  if($currency == $code) echo 'selected';
  echo '>'.$name.'</option>';
  }
?>
</select></td></tr>
<tr><td><?php echo $keys->text_initialBalance; ?></td>
<td><input type="text" value="<?php echo $initialBalance; ?>" name="initialBalance" id="initialBalance" <?php echo $disableMainForm; ?>></td></tr>
<?php if($isAdmin) { ?>
<tr><td colspan="2"  style="text-align:center" >
  <span class="hidden" id="loadingSpan"><img  src="style/icons/icon_Loading.gif"/></span>
  <span class="hidden" id="resultDiv"></span>
  <!--
  <?php if($userSettings->secureAccess()) { ?><img src="style/icons/error.png"/> <?php echo $keys->text_mustGivePasswordWarning; ?>: <input type="password" value="" name="currentPassword" id="currentPassword"><?php } ?>
  -->
  <span id="submitSpan"><input type="image" src="style/icons/disk.png" tabindex="7" value="<?php echo $keys->link_save; ?>"></span>
</td></tr>
<?php } ?>
</table>
</form>
<br/>
<div id="usersListing" style="<?php if(!$userSettings->secureAccess()) echo "display: none"; ?>">
<h2><img src="style/icons/user_female.png"/> <?php echo $keys->pageTitle_userAccountSettings; ?></h2>
<span id="passwordWarning" style="display: none;"><?php echo $keys->text_mustGivePasswordWarning; ?></span>
<?php
  $userCounter = count($users);
  foreach($users as $user) {
    makeUserTableRow($user->id, $user, $keys, $isAdmin, $userSettings);
    }
  if($nullUser) {
    for($i=0,$j=-1; $i<(MAXNUMUSERS-$userCounter); $i++, $j--) {
      makeUserTableRow($j, $nullUser, $keys, $isAdmin, $userSettings);
    }
  }
  ?>
</div>