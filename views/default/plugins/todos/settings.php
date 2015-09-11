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
$enable_iplan = $vars['entity']->enable_iplan;
$submission_tz = $vars['entity']->submission_tz;

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

$admin_role_label = elgg_echo('todo:label:adminrole');
$admin_role_input = elgg_view('input/roledropdown', array(
    'name' => 'params[todoadminrole]',
    'value' => $vars['entity']->todoadminrole,
    'show_hidden' => TRUE,
));

$dropout_exempt_role_label = elgg_echo('todo:label:dropoutexemptrole');
$dropout_exempt_role_input = elgg_view('input/roledropdown', array(
    'name' => 'params[dropoutexemptrole]',
    'value' => $vars['entity']->dropoutexemptrole,
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

$enable_iplan_label = elgg_echo('todo:label:enable_iplan');
$enable_iplan_input = elgg_view('input/dropdown', array(
		'name' => 'params[enable_iplan]',
		'options_values' => array(
			'1' => elgg_echo('option:yes'),
			'0' => elgg_echo('option:no')
		),
	'value' => $enable_iplan,
));



// Time zone offset select
$utc = new DateTimeZone('UTC');
$dt = new DateTime('now', $utc);

$tz_option_values = array(0 => 'Disabled');

foreach(DateTimeZone::listIdentifiers() as $tz) {
	$current_tz = new DateTimeZone($tz);
	$offset =  $current_tz->getOffset($dt);
	$transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
	$abbr = $transition[0]['abbr'];
	$formatted_offset = todo_format_tz_offet($offset);
	$option = "{$tz} [{$abbr} $formatted_offset]";
	$tz_option_values[$tz] = $option;
}

$submission_tz_label = elgg_echo('todo:label:submission_tz');
$submission_tz_input = elgg_view('input/dropdown', array(
		'name' => 'params[submission_tz]',
		'options_values' => $tz_option_values,
		'value' => $submission_tz,
));

if ($submission_tz) {
	$server_time = date('D, d M Y H:i:s',time());
	$offset_time = date('D, d M Y H:i:s',time() + todo_get_submission_timezone_offset());
	$time_offset_preview = "<pre>
Server Time: $server_time
Offset Time: $offset_time
</pre>";
}

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
		<label>$enable_iplan_label</label>
		$enable_iplan_input
	</div>
	<div>
		<label>$submissions_role_label</label> 
		$submissions_role_input
	</div>
	<div>
		<label>$faculty_role_label</label> 
		$faculty_role_input
	</div>
	<div>
        <label>$admin_role_label</label> 
        $admin_role_input
    </div>
    <div>
        <label>$dropout_exempt_role_label</label> 
        $dropout_exempt_role_input
    </div>
	<div>
		<label>$submission_tz_label</label> 
		$submission_tz_input
		$time_offset_preview
	</div>
HTML;

echo $content;