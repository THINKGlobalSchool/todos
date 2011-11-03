<?php
/**
 * Todo Hoverstats view
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

$incomplete = $vars['incomplete'];
$complete = $vars['complete'];
$unaccepted = $vars['unaccepted'];

$incomplete_label = elgg_echo('todo:label:statusincomplete');
$complete_label = elgg_echo('todo:label:complete');
$unaccepted_label = elgg_echo('todo:label:unaccepted');

$user = elgg_get_logged_in_user_entity();

$incomplete_url = elgg_get_site_url() . "todo/dashboard/{$user->username}?type=assigned&status=incomplete&u={$user->guid}";
$complete_url = elgg_get_site_url() . "todo/dashboard/{$user->username}?type=assigned&status=complete&u={$user->guid}";

$content = <<<HTML
	<span id='todo-hover-stats'>
		<table class='elgg-table'>
			<tbody>
				<tr>
					<td><a href='$incomplete_url'>$unaccepted_label</a></td>
					<td>$unaccepted</td>
				</tr>
				<tr>
					<td><a href='$incomplete_url'>$incomplete_label</a></td>
					<td>$incomplete</td>
				</tr>
				<tr>
					<td><a href='$complete_url'>$complete_label</a></td>
					<td>$complete</td>
				</tr>
			</tbody>
		</table>
	</span>
HTML;

echo $content;