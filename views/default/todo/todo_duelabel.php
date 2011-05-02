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



if (get_input('display_label', false)) {
	$today = strtotime(date("F j, Y"));
	$next_week = strtotime("+7 days", $today);

	if ($vars['entity']->due_date <= $today) {
		$label = elgg_echo("todo:label:pastdue");
		$priority = TODO_PRIORITY_HIGH;
	} else if ($vars['entity']->due_date > $today && $vars['entity']->due_date <= $next_week) {	
		$label = elgg_echo("todo:label:nextweek");
		$priority = TODO_PRIORITY_MEDIUM;
	} else if ($vars['entity']->due_date > $next_week) {
		$label = elgg_echo("todo:label:future");
		$priority = TODO_PRIORITY_LOW;
	}

	echo "<span class='todo-priority-label todo-priority-{$priority}'><span class='label-text'>{$label}</span></span>";
}