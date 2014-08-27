<?php
/**
 * Todo Topbar Hover Stats View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars
 * 
 */

$upcoming = $vars['upcoming'];
$past_due = $vars['past_due'];
$today = $vars['today'];
$new = $vars['new'];
$this_week = $vars['this_week'];

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
	<span id='todo-topbar-hover'>
		<table class='elgg-table'>
			<tbody>
				<tr>
					<td><a href='$url'>$new_label</a></td>
					<td>$new</td>
				</tr>
				<tr>
					<td><a href='$today_url'>$today_label</a></td>
					<td>$today</td>
				</tr>
				<tr>
					<td><a href='$this_week_url'>$this_week_label</a></td>
					<td>$this_week</td>
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
	</span>
HTML;

echo $content;