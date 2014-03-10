<?php
/**
 * Todo Category Calendars View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

elgg_load_js('jquery.gantt');
elgg_load_css('jquery.gantt');
elgg_load_css('tgs.fullcalendar');
elgg_load_css('tgs.calendars_dynamic');

$loader_url = elgg_get_site_url() . "_graphics/ajax_loader_bw.gif";

$content = <<<HTML
	<div id='todo-calendar-categories'></div>
	<div id='todo-gantt-calendar'></div>
	<div id='todo-category-calendar-legend'></div>
HTML;

$categories = elgg_get_plugin_setting('calendar_categories', 'todo');

$calendars = array();

if ($categories) {
	$categories = unserialize($categories);

	foreach ($categories as $category) {
		$category = get_entity($category);
		if (elgg_instanceof($category, 'object', 'group_category')) {
			$url = elgg_get_site_url() . 'ajax/view/todo/gantt_feed?category=' . $category->guid;
			$url .= '&' . TODO_ASSESSED_TASK . '=1&' . TODO_EXAM . '=1';

			$calendars[$category->guid] = array(
				'display' => FALSE,
				'url' => $url,
			);
		}
	}
}

$json = json_encode($calendars);

$content .= <<<JAVASCRIPT
	<script type='text/javascript'>
		$(document).ready(function() {
			elgg.todo.initGantt();
		});		

		// $(function() {

		// 	"use strict";

		// 	$("#todo-gantt-calendar").gantt({
		// 		source: elgg.get_site_url() + 'ajax/view/todo/gantt_feed?category=38244',
		// 		navigate: "scroll",
		// 		maxScale: "days",
		// 		scale: "days",
		// 		itemsPerPage: 10,
		// 		onItemClick: function(data) {
		// 			//
		// 		},
		// 		onAddClick: function(dt, rowId) {
		// 			//
		// 		},
		// 		onRender: function() {
		// 			if (window.console && typeof console.log === "function") {
		// 				console.log("chart rendered");
		// 			}
		// 		}
		// 	});
		// });
  
	</script>
JAVASCRIPT;

echo $content;