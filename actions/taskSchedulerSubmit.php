<?php
/**
* save a task details
*
* @author       Fabrice Douteaud <admin@clearbudget.net>
* @package      actions
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

// by default, there is an error
$return['error'] = true;
$return['msg'] = $keys->failure;

// a flag to warn us if date is wrong
$invalidDateFormat = false;
 
// get today's date
$today = date("Y-m-d");

// get the user input and do some checking
$taskReminderDay = clean($context->reminderDay);
$taskAmount = $context->amount;
$taskMemo = clean($context->memo);
$taskSMSFlag = clean($context->smsFlag);
$tasksEmailFlag = clean($context->emailFlag);
$taskId = clean($context->taskId);
$delete = clean($context->delete);
$backLink = $context->backLink;
$taskType = 'monthly'; // default task type

if($delete == 'delete' && $taskId > 0) {
  class_debug::addMsg(__FILE__, 'deleting reminder id '.$taskId, DEBUGINFO);
  $db = class_db::getInstance();
  $db->addWhere('id', '=', $taskId);
  $db->delete('t_tasks_reminder');
  }
else {
  class_debug::addMsg(__FILE__, 'task reminder edition', DEBUGINFO);
  // check if something is given
  if($taskReminderDay == false || $taskReminderDay == 'null') {
    $return['error'] = true;
    $return['msg'] = $keys->error_ImproperDate;
    return;
    }
  // check the reminder day
  if(!is_numeric($taskReminderDay) || $taskReminderDay <= 0 || $taskReminderDay > 31) {
    $return['error'] = true;
    $return['msg'] = $keys->error_ImproperReminderDay;
    return;
    }

  // check for an amount
  if(!is_numeric($taskAmount)) {
    $return['error'] = true;
    $return['msg'] = $keys->error_ImproperAmount;
    return;
    }

  // check for a valid comment
  if(strlen($taskMemo) < 1 ) {
    $return['error'] = true;
    $return['msg'] = $keys->error_ImproperComment;
    return;
    }

  // load the task
  if($taskId>0) $task = new class_task($taskId);
  else $task = new class_task();

  // set the object with all the values
  $task->amount = $taskAmount;
  $task->memo = $taskMemo;
  $task->reminderDay = $taskReminderDay;
  $task->type = $taskType;
  // try to save the object on the DB
  $task->save();
  }

$return['error'] = false;
$return['msg'] = $keys->success;
$return['backLink'] = $backLink;
?>