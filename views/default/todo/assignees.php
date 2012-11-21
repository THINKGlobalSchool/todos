<?php
/**
 * Todo Assignee list view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['assignees'] - Array of assignees
 * 
 */

$assignees = elgg_extract('assignees', $vars);
$todo_guid = elgg_extract('todo_guid', $vars);

if ($assignees) { 
	$member_list .= "<div class='todo-assignees' id='$todo_guid'>";
	foreach ($assignees as $assignee) {
		$member_list .= "<div style='float: left; width: 200px;'>"  . elgg_view('todo/assignee', array('entity' => $assignee)) . "</div>";	
	}
	$member_list .= "<div style='clear: both;'></div></div>";
} else {
	$member_list = 'None';
}

echo $member_list;
