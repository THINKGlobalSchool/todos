<?php
/**
 * Todo Category Calendars Feed View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 * 
 */

$category_guid = get_input('category', FALSE);

// Get category flags
$basic_task = get_input(TODO_BASIC_TASK, FALSE);
$assessed_task = get_input(TODO_ASSESSED_TASK, FALSE);
$exam = get_input(TODO_EXAM, FALSE);

// Check for due only input
$due_only = get_input('due_only', FALSE);

$category = get_entity($category_guid);

$container_guids = ELGG_ENTITIES_ANY_VALUE;

// Array to hold container colors (per group colors)
$container_colors = array();

// Get plugin setting colors
$calendar_colors = elgg_get_plugin_setting('calendar_category_colors', 'todos');
$calendar_colors = unserialize($calendar_colors);

// Isolate palette for this category
$category_color = $calendar_colors[$category_guid];
$category_palette = $category_color['palette'];

if (elgg_instanceof($category, 'object', 'group_category')) {
	$groups = groupcategories_get_groups($category, 0);
} else if ($category_guid == 'student_groups') {
	$groups = elgg_get_logged_in_user_entity()->getGroups(array('limit' => 0));
}

// Add groups to feed
if ($groups) {
	$container_guids = array();
	foreach ($groups as $idx => $group) {
		// Add group guid to container
		$container_guids[] = $group->guid;

		// Pick out a color from palette based on the index
		$container_colors[$group->guid] = $category_palette[$idx];
	}
}

$options = array(
	'type' => 'object',
	'subtype' => 'todo',
	'container_guids' => $container_guids,
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

$todos = new ElggBatch('elgg_get_entities_from_metadata', $options);

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
		$description .= "<div class='todo-calendar-icon-padding todo-calender-tooltip-return-required'>" . elgg_echo('todo:label:submissionrequired') . "</div>";
		$todo_event_title .= "<span class='todo-calendar-icon-padding todo-calendar-event-subtitle-return-required'></span>";
	}
	
	if ($todo->category) {
		$css_category = preg_replace('/[^a-z0-9\-]/i', '-', $todo->category);
		$description .= "<div class='todo-calendar-icon-padding todo-calender-tooltip-{$css_category}'>" . elgg_echo("todo:label:{$todo->category}") . "</div>";
		$todo_event_title .= "<span class='todo-calendar-icon-padding todo-calendar-event-subtitle-{$css_category}'></span>";
	}
	
	$title_content = <<<HTML
	<div class='todo-calendar-event-title-container'>
		<span class='todo-calendar-event-title'>$container->name:</span>
		<span class='todo-calendar-event-subtitle'>$todo_event_title</span>
	</div>
HTML;
	
	// Check if we're using only the due date, or displaying the full duration
	if ($due_only) {
		$start_date = $todo->due_date;
	} else {
		// Use supplied start date, or default to time created
		$start_date = $todo->start_date ? $todo->start_date  : $todo->time_created;
	}

	$events[] = array(
		'title' => $title_content,
		'start' => $start_date,
		'end' => $todo->due_date,
		'url' => $todo->getURL(),
		'description' => $description,
		'color' => $container_colors[$container->guid],
		'className' => 'todo-calendar-event',
	);
}

$json = json_encode($events);

echo $json;