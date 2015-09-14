<?php
/**
 * Todo Category Calendars Sidebar View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 * 
 */

// Get admin defined categories
$categories = elgg_get_plugin_setting('calendar_categories', 'todos');

if ($categories) {
	// Get category colors
	$colors = elgg_get_plugin_setting('calendar_category_colors', 'todos');
	$colors = unserialize($colors);

	$categories = unserialize($categories);

	array_pop($categories);

	// If the user is a member of the 'student' role add a filter for a psuedo category containing the users groups
	if (roles_is_member(elgg_get_plugin_setting('studentrole', 'todos'), elgg_get_logged_in_user_guid())) {
		
		$last = array_pop($colors);
		$colors = array('student_groups' => $last) + $colors;
		array_unshift($categories, "student_groups");
	}

	// Create sidebar inputs
	foreach ($categories as $key => $value) {
		$checked = '';
		if ($key == 0) {
			$checked = "checked='checked'";
		}

		// Set foreground color (background covered in CSS)
		$fg = $colors[$value]['fg'];
		$bg = $colors[$value]['bg'];

		if (elgg_instanceof($category = get_entity($value), 'object', 'group_category')) {
			$guid = $category->guid;

			$input = "<input class='right todo-sidebar-calendar-toggler' id='todo-sidebar-calendar-{$guid}' type='radio' name='category_calendar_radio' {$checked} />";

			$text = "<label>$category->title$input</label>";
			
			elgg_register_menu_item('todo-filter-calendars', array(
				'name' => 'todo-sidebar-calendar-' . $guid,
				'text' => $text,
				'href' => false,
				'priority' => $key,
				'item_class' => 'pas mrs elgg-todocalendar-feed elgg-todocalendar-feed-' . $guid
			));
		} else if ($value == 'student_groups') {

			// Create the psuedo category filter
			$input = "<input class='right todo-sidebar-calendar-toggler' id='todo-sidebar-calendar-student_groups' type='radio' name='category_calendar_radio' {$checked} />";

			$student_filter_label = elgg_echo('todo:label:studentfilter');

			$text = "<label>$student_filter_label$input</label>";
			
			elgg_register_menu_item('todo-filter-calendars', array(
				'name' => 'todo-sidebar-calendar-student',
				'text' => $text,
				'href' => false,
				'priority' => $key,
				'item_class' => 'pas mrs elgg-todocalendar-feed elgg-todocalendar-feed-student_groups'
			));
		}
	}
	
	$category_content = elgg_view_menu('todo-filter-calendars', array('sort_by' => 'priority'));

	if ($category_content) {
		// Group categories
		$group_category_module = elgg_view_module('aside', elgg_echo('todo:label:groupcategories'), $category_content, array('id' => 'filter-calendars'));
		
		// Todo categories
		$todo_category_input = elgg_view('input/checkboxes', array(
			'name' => 'todo_category',
			'class' => 'todo-sidebar-todo-category-checkbox',
			'value' => array(TODO_ASSESSED_TASK, TODO_EXAM),
			'options' => todo_get_categories_dropdown(TRUE),
		));

		$todo_category_module = elgg_view_module('aside', elgg_echo('todo:label:todocategories'), $todo_category_input, array('id' => 'filter-todo-categories'));

		// Due date/start day switcher
		$due_switch_label = elgg_echo('todo:label:dueswitch');

		$due_switch_check = elgg_view('input/checkboxes', array(
			'name' => 'due_switch',
			'class' => 'todo-sidebar-todo-due-checkbox mtm',
			'options' => array(0 => $due_switch_label)
		));

		// Date content
		$datepicker = elgg_view('input/text', array('id' => 'todo-calendar-date-picker'));		
		$date_module = elgg_view_module('aside', elgg_echo('todo:label:jumptodate'), $datepicker . $due_switch_check, array('id' => 'filter-date'));
		
		$content = <<<HTML
			<div id='todo-calendar-filters-content'>
				$group_category_module
				$todo_category_module
				$date_module
			</div>
HTML;
		echo $content;
	}
}