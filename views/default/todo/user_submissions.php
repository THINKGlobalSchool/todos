<?php
/**
 * User submissions view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 */

$user_guid = get_input('user_guid', elgg_extract('user_guid', $vars, NULL));
$group_guid = get_input('group_guid', elgg_extract('group_guid', $vars, NULL));

// Make sure we can view the list
if (!submissions_gatekeeper($user_guid, $group_guid)) {
	echo elgg_echo('todo:error:access');
	return;
}

// Submission stats
$submission_stats = elgg_view('todo/submission_stats', array(
	'user_guid' => $user_guid,
	'group_guid' => $group_guid
));

$user = get_entity($user_guid);

if (!elgg_instanceof($user, 'user')) {
	echo elgg_echo('todo:error:invaliduser');
	return;
}

$view_vars = array('user_guid' => $user_guid);

// Check for a group
if (elgg_instanceof(get_entity($group_guid), 'group')) {
	$view_vars['group_guid'] = $group_guid;
	$view_vars['limit'] = 15;
}

$view_vars['filter_return'] = 1; // Default to return

// Create submissions module				
$module = elgg_view('modules/genericmodule', array(
	'view' => 'todo/submissions',
	'module_id' => 'todo-user-submissions-module',
	'view_vars' => $view_vars, 
));

// Output menu
$filter_menu = elgg_view_menu('todo_submission_dashboard', array(
	'class' => 'elgg-menu-hz elgg-menu-submissions-sort',
	'sort_by' => 'priority',
));

$js = <<<JAVASCRIPT
	<script>
		// Destroy/reload modules
		elgg.modules.genericmodule.destroy();
		elgg.modules.genericmodule.init();
		
		// Init daterangepicker
		elgg.todo.initDateRangePicker('input.todo-user-submissions-date-input');
	</script>
JAVASCRIPT;

$content = <<<HTML
	$filter_menu
	$submission_stats
	$module
	$js
HTML;

echo $content;