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

$options = array(
	'type' => 'object',
	'subtype' => 'todo',
	'container_guids' => $container_guids,
	'limit' => 0,
);

$todos = new ElggBatch('elgg_get_entities', $options);

$events = array();


foreach ($todos as $todo) {
	$owner = $todo->getOwnerEntity();
	$container = $todo->getContainerEntity();

	$description = "Created by: {$owner->name}<br />";
	
	if ($owner->guid != $container->guid) {
		$description .= "In group: {$container->name}";
	}
	
	if ($todo->return_required) {
		$title_class = 'todo-calendar-event-title-info';
		$description .= "<br />" . elgg_echo('todo:label:submissionrequired');
	} else {
		$title_class = 'todo-calendar-event-title';
	}
	
	// Truncated title and group name
	$todo_truncated = elgg_get_excerpt($todo->title, 75);
	$title_content = <<<HTML
		<span class='$title_class'>
			$container->name:
		</span>
		<span class='todo-calendar-event-subtitle'>
			$todo_truncated
		</span>
HTML;
	
	$events[] = array(
		'title' => $title_content,
		'start' => $todo->time_created,
		'end' => $todo->due_date,
		'url' => $todo->getURL(),
		'description' => $description,
		'color' => $container_colors[$container->guid],
	);
}

$json = json_encode($events);

echo $json;