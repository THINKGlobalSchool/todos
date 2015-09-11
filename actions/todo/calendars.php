<?php
/**
 * Todo Calendar Configuration Save Action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$category_guids = get_input('categories_list');
$student_category = get_input('student_category');

if (!in_array($student_category, $category_guids)) {
	$category_guids[] = $student_category;
}

$category_count = count($category_guids);

$categories = serialize($category_guids);

$backgrounds = get_input('background');
$foregrounds = get_input('foreground');

$color_count = count($backgrounds);

// Add/remove elements if there is a count discrepency 
if ($category_count > $color_count) {
	array_unshift($backgrounds, "");
	array_unshift($foregrounds, "");
} else if ($category_count < $color_count) {


	$current_colors = unserialize(elgg_get_plugin_setting('calendar_category_colors', 'todos'));
	
	$current_categories = array();

	foreach ($current_colors as $idx => $val) {
		$current_categories[] = $idx;
	}

	$diff = array_diff($current_categories, $category_guids);
	
	foreach ($diff as $idx => $val) {
		unset($backgrounds[$idx]);
		unset($foregrounds[$idx]);

		$backgrounds = array_values($backgrounds);
		$foregrounds = array_values($backgrounds);
	}
}

$spread = get_input('palette_spread');

elgg_set_plugin_setting('palette_spread', $spread, 'todos');

if ($backgrounds && $foregrounds) {
	$colors = array();
	
	for ($i = 0; $i < count($category_guids); $i++) {
		// Get RGB of background
		$rgb = html2rgb($backgrounds[$i]);
		
		// Create palette
		$palette = generate_html_palette($rgb[0],$rgb[1],$rgb[2], $spread);

		// Set colors
		$colors[$category_guids[$i]] = array(
			'bg' => $backgrounds[$i],
			'fg' => $foregrounds[$i],
			'palette' => $palette,
		);
	}

	elgg_set_plugin_setting('calendar_category_colors', serialize($colors), 'todos');
}


elgg_set_plugin_setting('calendar_categories', $categories, 'todos');

system_message(elgg_echo('todo:success:calendarsettings'));
forward(REFERER);