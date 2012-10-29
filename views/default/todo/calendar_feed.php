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
	
	// Truncated title and group name
	$todo_event_title = elgg_get_excerpt($todo->title, 75);
	
	if ($todo->return_required) {
		$description .= "<br /><span class='todo-calender-tooltip-return-required'>" . elgg_echo('todo:label:submissionrequired') . "</span>";
		$todo_event_title .= "<span class='todo-calendar-event-subtitle-return-required'></span>";
	}
	
	if ($todo->category) {
		$css_category = preg_replace('/[^a-z0-9\-]/i', '-', $todo->category);
		$description .= "<br /><span class='todo-calender-tooltip-{$css_category}'>" . elgg_echo("todo:label:{$todo->category}") . "</span>";
		$todo_event_title .= "<span class='todo-calendar-event-subtitle-{$css_category}'></span>";
	}
	
	$title_content = <<<HTML
	<div class='todo-calendar-event-title-container'>
		<span class='todo-calendar-event-title'>
			$container->name:
		</span>
		<span class='todo-calendar-event-subtitle'>
			$todo_event_title
		</span>
	</div>
HTML;
	
	$events[] = array(
		'title' => $title_content,
		'start' => $todo->time_created,
		'end' => $todo->due_date,
		'url' => $todo->getURL(),
		'description' => $description,
		'color' => $container_colors[$container->guid],
		'className' => 'todo-calendar-event',
	);
}

$json = json_encode($events);

echo $json;