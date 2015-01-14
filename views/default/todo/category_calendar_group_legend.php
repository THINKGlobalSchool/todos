<?php
/**
 * Todo Category Calendars Group Legend View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$category_guid = get_input('category_guid');

$category = get_entity($category_guid);

if (elgg_instanceof($category, 'object', 'group_category')) {
	// Get category colors from backend
	$colors = elgg_get_plugin_setting('calendar_category_colors', 'todos');
	$colors = unserialize($colors);

	// Get category specific colors and palette
	$category_colors = $colors[$category_guid];
	$category_palette = $category_colors['palette'];
	
	// Get foreground color
	$fg = $category_colors['fg'];

	// Get category groups
	$groups = groupcategories_get_groups($category, 0);

	foreach ($groups as $idx => $group) {
		// Get this groups color from the available palette
		$bg = $category_palette[$idx];
		
		$url = $group->getURL();

		// Create content
		$text = <<<HTML
			<div style='background: $bg; color: $fg;' class='todo-category-calendars-group-legend'>
				<a href='$url'>$group->name</a>
			</div>
HTML;
		
		elgg_register_menu_item('todo-category-calendars-groups', array(
			'name' => 'todo-sidebar-calendar-group' . $group->guid,
			'text' => $text,
			'href' => false,
		));	
	}
	
	$group_content = elgg_view_menu('todo-category-calendars-groups');
		
	$group_module = elgg_view_module('aside', elgg_echo('todo:label:grouplegend'), $group_content);

	echo $group_module;
}