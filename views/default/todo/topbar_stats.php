<?php
/**
 * Todo Topbar Stats View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 * 
 * @uses $vars
 * 
 */
$user = elgg_get_logged_in_user_entity();

$today = strtotime(date("F j, Y",time() + todo_get_submission_timezone_offset()));
$next_week = strtotime("+7 days", $today);
		
$new = count_unaccepted_todos($user->guid);
$due_today = count_assigned_todos_by_due_date($user_guid, array('start' => $today, 'operand' => '='), 'incomplete');
$upcoming = count_assigned_todos_by_due_date($user_guid, array('start' => $today, 'operand' => '>'), 'incomplete');
$past_due = count_assigned_todos_by_due_date($user_guid, array('start' => $today, 'operand' => '<'), 'incomplete');
$due_this_week = count_assigned_todos_by_due_date($user_guid, array('start' => $today, 'end' => $next_week), 'incomplete');

$past_due_label = elgg_echo('todo:label:pastdue');
$upcoming_label = elgg_echo('todo:label:incomplete');
$new_label = elgg_echo('todo:label:new');
$today_label = elgg_echo('todo:label:today');
$this_week_label = elgg_echo('todo:label:nextweek');

$user = elgg_get_logged_in_user_entity();

$url = elgg_get_site_url() . "todo/dashboard/{$user->username}?context=assigned&status=incomplete";

$today_url = $url . "&priority=" . TODO_PRIORITY_TODAY;
$this_week_url = $url . "&priority=" . TODO_PRIORITY_MEDIUM;
$past_url = $url . "&priority=" . TODO_PRIORITY_HIGH;
$upcoming_url = $url . "&sort_order=DESC";

// Show iPlan if enabled
if (elgg_get_plugin_setting('enable_iplan', 'todos')) {
	$iplan_link = elgg_view('output/url', array(
		'text' => elgg_echo('todo:label:iplancalendar'),
		'href' => elgg_get_site_url() . 'todo/iplan',
		'class' => 'elgg-button elgg-button-submit',
	));

	$iplan_content = "<tr>
		<td colspan='2' class='todo-iplan-hover'>$iplan_link</td>
	</tr>";
}

$content = <<<HTML
	<table class='elgg-table todo-topbar-stats-table'>
		<tbody>
			<tr>
				<td><a href='$url'>$new_label</a></td>
				<td>$new</td>
			</tr>
			<tr>
				<td><a href='$today_url'>$today_label</a></td>
				<td>$due_today</td>
			</tr>
			<tr>
				<td><a href='$this_week_url'>$this_week_label</a></td>
				<td>$due_this_week</td>
			</tr>
			<tr>
				<td><a href='$upcoming_url'>$upcoming_label</a></td>
				<td>$upcoming</td>
			</tr>
			<tr>
				<td><a href='$past_url'>$past_due_label</a></td>
				<td>$past_due</td>
			</tr>
			$iplan_content
		</tbody>
	</table>
HTML;

echo $content;