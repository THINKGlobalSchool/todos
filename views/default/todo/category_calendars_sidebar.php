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


$categories = elgg_get_plugin_setting('calendar_categories', 'todo');

if ($categories) {
	$colors = elgg_get_plugin_setting('calendar_category_colors', 'todo');
	$colors = unserialize($colors);

	$categories = unserialize($categories);

	foreach ($categories as $category) {
		$category = get_entity($category);
		if (elgg_instanceof($category, 'object', 'group_category')) {
			$guid = $category->guid;
			$input = elgg_view('input/checkbox', array(
				'id' => 'todo-sidebar-calendar-' . $guid,
				'class' => 'right todo-sidebar-calendar-toggler',
				'checked' => 'checked'
			));
			
			$bg = $colors[$category->guid]['bg'];
			$fg = $colors[$category->guid]['fg'];
			
			
			$text = "<label style='background: #$gb; color: #$fg;'>$category->title$input</label>";
			
			elgg_register_menu_item('todo-sidebar-calendars', array(
				'name' => 'todo-sidebar-calendar-' . $guid,
				'text' => $text,
				'href' => false,
				'item_class' => 'pam mvm elgg-todocalendar-feed elgg-todocalendar-feed-' . $guid // @TODO STYLES?
			));
		}
	}
	
	$content = elgg_view_menu('todo-sidebar-calendars');

	if ($content) {
		$category_module = elgg_view_module('aside', elgg_echo('todo:label:groupcategories'), $content, array('id' => 'todo-sidebar-calendars'));
		
		$datepicker = elgg_view('input/text', array('id' => 'todo-calendar-date-picker'));
		
		$date_module = elgg_view_module('aside', elgg_echo('todo:label:jumptodate'), $datepicker);
		
		$content = <<<HTML
			<div id='todo-calendar-sidebar-content'>
				$category_module
				$date_module
			</div>
HTML;
		echo $content;
	}
}

