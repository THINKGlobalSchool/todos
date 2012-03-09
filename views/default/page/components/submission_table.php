<?php
/**
 * Simple list view: Doesn't output a UL or LI's, just the item as
 * built from the object view
 * 
 * Based on the page/components/list view
 * 
 * @package Achievements
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['items']       Array of ElggEntity or ElggAnnotation objects
 * @uses $vars['offset']      Index of the first list item in complete list
 * @uses $vars['limit']       Number of items per page
 * @uses $vars['count']       Number of items in the complete list
 * @uses $vars['pagination']  Show pagination? (default: true)
 * @uses $vars['position']    Position of the pagination: before, after, or both
 * @uses $vars['full_view']   Show the full view of the items (default: false)
 */

$items = $vars['items'];
$offset = elgg_extract('offset', $vars);
$limit = elgg_extract('limit', $vars);
$count = elgg_extract('count', $vars);
$base_url = elgg_extract('base_url', $vars, '');
$pagination = elgg_extract('pagination', $vars, true);
$offset_key = elgg_extract('offset_key', $vars, 'offset');
$position = elgg_extract('position', $vars, 'after');

$nav = "";

if ($pagination && $count) {
	$nav .= elgg_view('navigation/pagination', array(
		'baseurl' => $base_url,
		'offset' => $offset,
		'count' => $count,
		'limit' => $limit,
		'offset_key' => $offset_key,
	));
}

// Build header
$todo_label = elgg_echo('todo:todo');
$submission_label = elgg_echo('todo:label:submission');
$info_label = elgg_echo('todo:label:info');

$html = <<<HTML
	<div class='todo-submissions-header'>
		<table class='elgg-table todo-submissions-table'>
			<thead>
				<tr>
					<th>
						$todo_label
					</th>
					<th>
						$submission_label
					</th>
					<th>
						$info_label
					</th>
				</tr>
			</thead>
			<tbody>
HTML;

if (is_array($items) && count($items) > 0) {
	foreach ($items as $item) {
		$html .= elgg_view_list_item($item, $vars);
	}
} else {
	$html .= "<tr><td colspan='3'><h3 class='center'>" . elgg_echo('todo:label:noresults') . "</h3></td></tr>"; 
}

$html .= "</tbody></table>";

if ($position == 'before' || $position == 'both') {
	$html = $nav . $html;
}

if ($position == 'after' || $position == 'both') {
	$html .= $nav;
}

echo $html;
