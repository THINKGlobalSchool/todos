<?php
/**
 * Todo Dynamic Calendar CSS (will not cache)
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 */
header('Content-type: text/css', true);

$calendar_colors = elgg_get_plugin_setting('calendar_category_colors', 'todos');
$calendar_colors = unserialize($calendar_colors);

foreach ($calendar_colors as $guid => $calendar) {
	$bg = $calendar['bg'];
	$fg = $calendar['fg'];
	
echo <<<___CSS
	.elgg-todocalendar-feed-$guid a,
	.elgg-todocalendar-feed-$guid,
	.elgg-todocalendar-feed-$guid .fc-event-skin {
		background-color: $bg;
		border-color: $bg;
		color: $fg;
	}
	
	.elgg-todocalendar-feed-$guid label {
		color: $fg;
	}
___CSS;
}
?>