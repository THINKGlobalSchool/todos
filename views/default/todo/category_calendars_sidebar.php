<?php
/**
 * Todo Category Calendars Sidebar View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get admin defined categories
$categories = elgg_get_plugin_setting('calendar_categories', 'todo');

if ($categories) {
	// Get category colors
	$colors = elgg_get_plugin_setting('calendar_category_colors', 'todo');
	$colors = unserialize($colors);

	$categories = unserialize($categories);

	// Create sidebar inputs
	foreach ($categories as $key => $category) {
		$category = get_entity($category);
		if (elgg_instanceof($category, 'object', 'group_category')) {
			$guid = $category->guid;
			
			$checked = '';
			if ($key == 0) {
				$checked = "checked='checked'";
			}
			
			// Set foreground color (background covered in CSS)
			//$fg = $colors[$category->guid]['fg'];

			$input = "<input class='right todo-sidebar-calendar-toggler' id='todo-sidebar-calendar-{$guid}' type='radio' name='category_calendar_radio' {$checked} />";

			$text = "<label style='color: #$fg;'>$category->title$input</label>";
			
			elgg_register_menu_item('todo-sidebar-calendars', array(
				'name' => 'todo-sidebar-calendar-' . $guid,
				'text' => $text,
				'href' => false,
				'priority' => $key,
				'item_class' => 'pam mvm elgg-todocalendar-feed elgg-todocalendar-feed-' . $guid
			));
		}
	}
	
	$category_content = elgg_view_menu('todo-sidebar-calendars', array('sort_by' => 'priority'));

	if ($category_content) {
		$category_module = elgg_view_module('aside', elgg_echo('todo:label:groupcategories'), $category_content, array('id' => 'todo-sidebar-calendars'));
		
		$datepicker = elgg_view('input/text', array('id' => 'todo-calendar-date-picker'));
		
		$date_module = elgg_view_module('aside', elgg_echo('todo:label:jumptodate'), $datepicker);
		
		$content = <<<HTML
			<div id='todo-calendar-sidebar-content'>
				$category_module
				<div id='todo-calendar-sidebar-groups'></div>
				$date_module
			</div>
HTML;
		echo $content;
	}
}