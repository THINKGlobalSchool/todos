<?php
/**
 * Todo Assignee view, includes a control to remove assignee from a todo
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['entity'] - user
 * 
 */

echo <<<EOT
	<div class="todo_assignee">
		<div class="todo_assingee_info">
		<a href='#' onclick="javascript:unassignAssignee({$vars['entity']->guid});return false;">[Remove]</a>
		{$vars['entity']->name}
		</div>
	</div>
EOT;
?>

