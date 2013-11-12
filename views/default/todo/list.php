<?php
/**
 * Todo ajax list view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

$context = get_input('context', 'assigned');
$filter_priority = get_input('priority', 0);
$status = get_input('status', 'incomplete');
$sort_order = get_input('sort_order', 'ASC');
$container_guid = get_input('container_guid', elgg_get_logged_in_user_guid()); 

// List todos
// if ($status != 'submissions') {
	
$options = array(
	'context' => $context,
	'status' => $status,
	'sort_order' => $sort_order,
	'container_guid' => $container_guid,
	'list' => TRUE,
	'limit' => 15,
);

$today = strtotime(date("F j, Y"));
$next_week = strtotime("+7 days", $today);

switch ($filter_priority) {
	case TODO_PRIORITY_HIGH;
		$options['due_date'] = $today;
		$options['due_operand'] = '<';
		break;
	case TODO_PRIORITY_TODAY;
		$options['due_date'] = $today;
		$options['due_operand'] = '=';
		break;
	case TODO_PRIORITY_MEDIUM;
		$options['due_start'] = $today;
		$options['due_end'] = $next_week;
		break;
	case TODO_PRIORITY_LOW;
		$options['due_date'] = $next_week;
		$options['due_operand'] = '>';
		break;
	default:
		$options['due_date'] = FALSE;
		break;
}	

echo "<div id='todo-dashboard-content'>";
echo get_todos($options);
// } else {
// 	echo "<div id='todo-dashboard-content'>";
// 	if (submissions_gatekeeper($container_guid)) {
// 		echo "<div class='todo-user-submissions-content'>";
// 		echo elgg_view('todo/user_submissions', array(
// 			'user_guid' => $container_guid,
// 		));
// 		echo "</div>";
// 	} else {
// 		echo elgg_echo('todo:error:access');
// 	}

// }

echo "</div>";	
