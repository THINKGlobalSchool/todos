<?php 
/**
 * Todo Calendars Configuration Form
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
	$categories = unserialize($categories);
}

$categories_input = elgg_view('input/groupcategories', array(
	'label' => elgg_echo('todo:label:showcategorycalendar'),
	'value' => $categories,
));

$submit_input = elgg_view('input/submit', array(
	'name' => 'submit', 
	'value' => elgg_echo('save')
));

$content = <<<HTML
	<div>
		$categories_input
	</div>
	<div>
		$submit_input
	</div>
HTML;

echo $content;