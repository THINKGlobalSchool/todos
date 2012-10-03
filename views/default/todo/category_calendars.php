<?php
/**
 * Todo Category Calendars View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$content = <<<HTML
	<div id='todo-category-calendar'>
	</div>
HTML;

$categories = elgg_get_plugin_setting('calendar_categories', 'todo');

$calendars = array();

if ($categories) {
	$categories = unserialize($categories);

	foreach ($categories as $category) {
		$category = get_entity($category);
		if (elgg_instanceof($category, 'object', 'group_category')) {
			$calendars[$category->guid] = array(
				'display' => TRUE,
				'url' => elgg_get_site_url() . 'ajax/view/todo/calendar_feed?category=' . $category->guid,
			);
		}
	}
}

$json = json_encode($calendars);

$content .= <<<JAVASCRIPT
	<script type='text/javascript'>
			elgg.todo.calendars = $json;
	</script>
JAVASCRIPT;

echo $content;