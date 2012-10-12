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
$category_colors = elgg_get_plugin_setting('calendar_category_colors', 'todo');
$spread = elgg_get_plugin_setting('palette_spread', 'todo');

if (!$spread) {
	$spread = 50;
}

if ($categories) {
	$categories = unserialize($categories);
	$category_colors = unserialize($category_colors);

	$background_label = elgg_echo('todo:label:calendarbackground');
	$foreground_label = elgg_echo('todo:label:calendarforeground');
	
	foreach ($categories as $category) {
		$category = get_entity($category);
		
		$bg = $category_colors[$category->guid]['bg'];
		
		$background_input = elgg_view('input/text', array(
			'name' => 'background[]',
			'value' => $bg,
			'class' => 'mvm',
		));
		
		$fg = $category_colors[$category->guid]['fg'];
		
		$foreground_input = elgg_view('input/text', array(
			'name' => 'foreground[]',
			'value' => $fg,
			'class' => 'mvm',
		));
	
		$category_color_content .= "<tr>
			<td><div class='elgg-todocalendar-feed elgg-todocalendar-feed-$category->guid pas mvm'>$category->title</div></td>
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