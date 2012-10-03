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
$categories = serialize($category_guids);

$backgrounds = get_input('background');
$foregrounds = get_input('foreground');

if ($backgrounds && $foregrounds) {
	$colors = array();
	for ($i = 0; $i < count($category_guids); $i++) {
		$colors[$category_guids[$i]] = array(
			'bg' => $backgrounds[$i],
			'fg' => $foregrounds[$i],
		);
	}
	elgg_set_plugin_setting('calendar_category_colors', serialize($colors), 'todo');
}


elgg_set_plugin_setting('calendar_categories', $categories, 'todo');

system_message(elgg_echo('todo:success:calendarsettings'));
forward(REFERER);