<?php
/**
 * Todo Logs
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

$dropped_todos_title = elgg_echo('todo:label:admin:drops');
$dropped_info_title = elgg_echo('todo:label:admin:dropstats');

$options = array(
	'type' => 'object',
	'subtype' => 'todo',
	'annotation_name' => 'todo_dropped',
	'limit' => 0,
	'count' => TRUE,
);

// Total drop count
$total_drop_count = elgg_get_annotations($options);

$options['count'] = FALSE;

// Individual drops
$drops = elgg_get_annotations($options);

$drop_content = '';

// Build drops content
foreach ($drops as $drop) {
	elgg_set_ignore_access(true);
	$todo = $drop->getEntity();
	$user = get_entity($drop->owner_guid);
	$user_link = elgg_view('output/url', array(
		'text' => $user->name,
		'href' => $user->getURL(),
	));

	$todo_link = elgg_view('output/url', array(
		'text' => $todo->title,
		'href' => $todo->getURL()
	));
	
	$drop_content .= "<tr>
						<td>
							{$user_link}
						</td>
						<td>
							{$todo_link}
						</td>
					</tr>";

	elgg_set_ignore_access(false);
}

if (!$drop_content) {
	$drop_content = elgg_echo('todo:label:admin:nodrops');
} else {
	$drop_content = "<table class='elgg-table'>
						<tr>
							<th width='30%'>
								" . elgg_echo('todo:label:assignee') . "
							</th>
							<th>
								" . elgg_echo('todo:todo') . "
							</th>
						</tr>
						{$drop_content}
					</table>";
}

// View info content
$total_label = elgg_echo('todo:label:admin:totaldrops');

$drop_info_content = <<<HTML
	<div>
		<label>$total_label:</label>&nbsp;$total_drop_count
	</div>
HTML;

echo elgg_view_module('inline', $dropped_info_title, $drop_info_content);
echo elgg_view_module('inline', $dropped_todos_title, $drop_content);
