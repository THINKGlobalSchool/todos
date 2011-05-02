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

echo <<<HTML
	<div class="todo-assignee-remove">
		<a href='#' onclick="javascript:unassignAssignee({$vars['entity']->guid});return false;"><span class="elgg-icon elgg-icon-delete"></span></a>
		<span>{$vars['entity']->name}</span>
	</div>
HTML;
?>

