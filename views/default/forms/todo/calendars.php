<?php 
/**
 * Todo Calendars Configuration Form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 *
 */


$categories = elgg_get_plugin_setting('calendar_categories', 'todos');
$category_colors = elgg_get_plugin_setting('calendar_category_colors', 'todos');
$spread = elgg_get_plugin_setting('palette_spread', 'todos');

if (!$spread) {
	$spread = 50;
}

if ($categories) {
	$categories = unserialize($categories);
	$category_colors = unserialize($category_colors);

	if (!in_array("student_groups", $categories)) {
		$categories[] = "student_groups";
	}

	$background_label = elgg_echo('todo:label:calendarbackground');
	$foreground_label = elgg_echo('todo:label:calendarforeground');
	
	foreach ($categories as $category_guid) {
		$category = get_entity($category_guid);

		if (elgg_instanceof($category, 'object', 'group_category')) {
			$title = $category->title;
		} else {
			$title = elgg_echo('todo:label:studentfilter');
		}
		
		$bg = $category_colors[$category_guid]['bg'];
		
		$background_input = elgg_view('input/text', array(
			'name' => 'background[]',
			'value' => $bg,
			'class' => 'mvm',
		));
		
		$fg = $category_colors[$category_guid]['fg'];
		
		$foreground_input = elgg_view('input/text', array(
			'name' => 'foreground[]',
			'value' => $fg,
			'class' => 'mvm',
		));
	
		$category_color_content .= "<tr>
			<td><div class='elgg-todocalendar-feed elgg-todocalendar-feed-$category_guid pas mvm'>$title</div></td>
			<td>$background_input</td>
			<td>$foreground_input</td>
		</tr>";
	}
	
	$category_color_label = elgg_echo('todo:label:categorycolors');
	
	$color_content = <<<HTML
		<div>
			<label>$category_color_label</label><br /><br />
			<table class='elgg-table'>
				<thead>
					<tr>
						<th></th>
						<th>$background_label</th>
						<th>$foreground_label</th>
					</tr>
				</thead>
				<tbody>
					$category_color_content
				</tbody>
			</table>
		</div>
HTML;
}

$student_hidden_input = elgg_view('input/hidden', array(
	'name' => 'student_category',
	'value' => 'student_groups'
));

$categories_input = elgg_view('input/groupcategories', array(
	'label' => elgg_echo('todo:label:showcategorycalendar'),
	'value' => $categories,
));

$spread_label = elgg_echo('todo:label:palettespread');
$spread_input = elgg_view('input/text', array(
	'value' => $spread,
	'name' => 'palette_spread',
));

$submit_input = elgg_view('input/submit', array(
	'name' => 'submit', 
	'value' => elgg_echo('save')
));

$content = <<<HTML
	<div>
		$student_hidden_input
		$categories_input
	</div>
	$color_content
	<div>
		$submit_input
	</div>
	<div>
		<label>$spread_label</label>
		$spread_input
	</div>
HTML;

echo $content;