<?php
/**
 * Todo Category Calendars Feed View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$category_guid = get_input('category', FALSE);

$category = get_entity($category_guid);

$container_guids = ELGG_ENTITIES_ANY_VALUE;

if (elgg_instanceof($category, 'object', 'group_category')) {
	$groups = groupcategories_get_groups($category, 0);
	if ($groups) {
		$container_guids = array();
		foreach ($groups as $group) {
			$container_guids[] = $group->guid;
		}
	}
}

$options = array(
	'type' => 'object',
	'subtype' => 'todo',
	'container_guids' => $container_guids,
	'limit' => 0,
);

$todos = new ElggBatch('elgg_get_entities', $options);

$events = array();

foreach ($todos as $todo) {
	$events[] = array(
		'title' => $todo->title,
		'start' => $todo->time_created,
		'end' => $todo->due_date,
		'url' => $todo->getURL()
	);
}

$json = json_encode($events);

echo $json;