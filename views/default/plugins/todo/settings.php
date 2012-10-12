<?php 
/**
 * Todo settings form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$delete = $vars['entity']->zipdelete;

if (!$delete) {
	$delete = 'daily';
}

$submissions_role_label = elgg_echo('todo:label:submissionsadmin');
$submissions_role_input = elgg_view('input/roledropdown', array(
	'name' => 'params[todosubmissionsrole]',
	'value' => $vars['entity']->todosubmissionsrole,
	'show_hidden' => TRUE,
));

$faculty_role_label = elgg_echo('todo:label:facultyrole');
$faculty_role_input = elgg_view('input/roledropdown', array(
	'name' => 'params[todofacultyrole]',
	'value' => $vars['entity']->todofacultyrole,
	'show_hidden' => TRUE,
));

$calendar_salt_label = elgg_echo('todo:label:calendarsalt');
$calendar_salt_input = elgg_view('input/text', array(
	'name' => 'params[calsalt]',
	'value' => $vars['entity']->calsalt
));

$zip_delete_label = elgg_echo('todo:label:zipdelete');
$zip_delete_input = elgg_view('input/dropdown', array(
		'name' => 'params[zipdelete]',
		'options_values' => array(
			'minute' => 'minute',
			'fiveminute' => 'fiveminute',
			'fifteenmin' => 'fifteenmin',
			'halfhour' => 'halfhour',
			'hourly' => 'hourly',
			'daily' => 'daily',
			'weekly' => 'weekly',
			'monthly' => 'monthly',
			'yearly' => 'yearly',
	),
	'value' => $delete,
));

$content = <<<HTML
	<div>
		<label>$calendar_salt_label</label>
		$calendar_salt_input
	</div>
	<div>
		<label>$zip_delete_label</label>
		$zip_delete_input
	</div>
	<div>
		<label>$submissions_role_label</label> 
		$submissions_role_input
	</div>
	<div>
		<label>$faculty_role_label</label> 
		$faculty_role_input
	</div>
HTML;

echo $content;