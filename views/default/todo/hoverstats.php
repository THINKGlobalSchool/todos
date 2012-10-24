<?php
/**
 * Todo Topbar Hover Stats View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars
 * 
 */

$upcoming = $vars['upcoming'];
$past_due = $vars['past_due'];
$today = $vars['today'];
$new = $vars['new'];

$past_due_label = elgg_echo('todo:label:pastdue');
$upcoming_label = elgg_echo('todo:label:incomplete');
$new_label = elgg_echo('todo:label:new');
$today_label = elgg_echo('todo:label:today');

$user = elgg_get_logged_in_user_entity();

$url = elgg_get_site_url() . "todo/dashboard/{$user->username}?type=assigned&status=incomplete&u={$user->guid}";

$today_url = $url . "&filter_priority=" . TODO_PRIORITY_TODAY;
$past_url = $url . "&filter_priority=" . TODO_PRIORITY_HIGH;

// Get faculty role
$faculty_role = elgg_get_plugin_setting('todofacultyrole', 'todo');


$iplan_link = elgg_view('output/url', array(
	'text' => elgg_echo('todo:label:iplancalendar'),
	'href' => elgg_get_site_url() . 'todo/dashboard?tab=iplan',
	'class' => 'elgg-button elgg-button-submit',
));

$iplan_content = "<tr>
	<td colspan='2' class='todo-iplan-hover'>$iplan_link</td>
</tr>";


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
					<td><a href='$url'>$upcoming_label</a></td>
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