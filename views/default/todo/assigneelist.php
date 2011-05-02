<?php
/**
 * Todo Assignee List, a nice formatted list assignees
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['assignees'] - users/groups entities
 * 
 */

$assignees = $vars['assignees'];

$content = "<div><table class='todo-assignee-table'>";

foreach ($assignees as $assignee) {
	$content .= "<tr><td>";
	$content .= $assignee->name;		
	$content .= '</td></tr>';
}

$content .= '</table></div>';

echo $content;
