<?php
/**
 * Todo due label (displays general information wether a todo is past due/due next week/due future)
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['entity']
 */

if (get_input('display_label', false) && get_input('page_context') != 'widgets') {
	$today = strtotime(date("F j, Y",time() + todo_get_submission_timezone_offset()));
	$tomorrow = strtotime("+1 days", $today);
	$next_week = strtotime("+7 days", $today);

	if ($vars['entity']->due_date < $today) {
		$label = elgg_echo("todo:label:pastdue");
		$priority = TODO_PRIORITY_HIGH;
	} else if ($vars['entity']->due_date == $today) {
		$label = elgg_echo("todo:label:today");
		$priority = TODO_PRIORITY_TODAY;
	} else if ($vars['entity']->due_date == $tomorrow) {
		$label = elgg_echo("todo:label:tomorrow");
		$priority = TODO_PRIORITY_TOMORROW;
	} else if ($vars['entity']->due_date > $today && $vars['entity']->due_date <= $next_week) {	
		$label = elgg_echo("todo:label:nextweek");
		$priority = TODO_PRIORITY_MEDIUM;
	} else if ($vars['entity']->due_date > $next_week) {
		$label = elgg_echo("todo:label:future");
		$priority = TODO_PRIORITY_LOW;
	}

	echo "<div class='todo-priority-label todo-priority-{$priority}'><span class='label-text'>{$label}</span></div>";
}