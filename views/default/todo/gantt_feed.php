<?php
/**
 * Todo Category Gantt Feed View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

$category_guid = get_input('category', FALSE);

// Get category flags
$basic_task = get_input(TODO_BASIC_TASK, FALSE);
$assessed_task = get_input(TODO_ASSESSED_TASK, FALSE);
$exam = get_input(TODO_EXAM, FALSE);

$category = get_entity($category_guid);

$container_guids = ELGG_ENTITIES_ANY_VALUE;

if (elgg_instanceof($category, 'object', 'group_category')) {
	// Array to hold container colors (per group colors)
	$container_colors = array();

	// Get plugin setting colors
	$calendar_colors = elgg_get_plugin_setting('calendar_category_colors', 'todo');
	$calendar_colors = unserialize($calendar_colors);
	
	// Isolate palette for this category
	$category_color = $calendar_colors[$category_guid];
	$category_palette = $category_color['palette'];

	$groups = groupcategories_get_groups($category, 0);
	if ($groups) {
		$container_guids = array();
		foreach ($groups as $idx => $group) {
			// Add group guid to container
			$container_guids[] = $group->guid;

			// Pick out a color from palette based on the index
			$container_colors[$group->guid] = $category_palette[$idx];
		}
	}
}

// @TODO START DATE

$options = array(
		'type' => 'object',
		'subtype' => 'todo',
		'limit' => 0,
);

$metadata_values = array();

if ($basic_task) {
	$metadata_values[] = TODO_BASIC_TASK;
}

if ($assessed_task) {
	$metadata_values[] = TODO_ASSESSED_TASK;
}

if ($exam) {
	$metadata_values[] = TODO_EXAM;
}

if (count($metadata_values) >= 1) {
	$options['metadata_name'] = 'category';
	$options['metadata_values'] = $metadata_values;
}

$todos_source = array();

foreach ($container_guids as $container_guid) {
	$group = get_entity($container_guid);

	$options['container_guid'] = $container_guid;
	$todos = new ElggBatch('elgg_get_entities_from_metadata', $options);

	$group_name = $group->name;
	$group_set = false;

	foreach ($todos as $todo) {
		$start_date = $todo->start_date ? $todo->start_date  : $todo->time_created;

		$start_date = (int)$start_date * 1000;
		$end_date = (int)$todo->due_date * 1000;

		if ($group_set) {
			$group_name = " ";
		} else {
			$group_set = true;
		}

		$color = $container_colors[$container_guid];

		$todos_source[] = array(
			'name' => $group_name,
			'desc' => $todo->title,
			'values' => array(array(
				'label' => $todo->title,
				'from' => "/Date($start_date)/",
				'to' => "/Date($end_date)/",
				'dataObj' => "$color",
				'customClass' => 'todo-gantt-item'
			))
		);
	}
}

$json = json_encode($todos_source);

echo $json;