<?php
/**
 * Todo ajax list view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 */

// Get page owner (passed via input due to no page owner in ajax views)
$page_owner = get_input('page_owner'); // Can be empty! :D:D

$filter_priority = get_input('priority', 0);
$status = get_input('status', 'incomplete');
$sort_order = get_input('sort_order', 'DESC');
$container_guid = get_input('container_guid', null);
$submission = get_input('submission', null);

// May be passed usernames in assignee/assigner params
$assignee = get_user_by_username(get_input('assignee', false));
$assigner = get_user_by_username(get_input('assigner', false));

// Get assignee/assigner guids
$assignee_guid = $assignee ? $assignee->guid : get_input('assignee_guid', $page_owner);
$assigner_guid = $assigner ? $assigner->guid : get_input('assigner_guid', $page_owner);

if ($assigner && !$assignee) {
	$context = "owned";
} else if (!$assigner || $assignee && $assigner) {
	$context = "assigned";
}



$context = get_input('context', $context); // Use input, or use computed context

// Get todos options	
$options = array(
	'context' => $context,
	'status' => $status,
	'sort_order' => $sort_order,
	'container_guid' => $container_guid,
	'assigner_guid' => $assigner_guid,
	'assignee_guid' => $assignee_guid,
	'submission' => $submission,
	'list' => TRUE,
	'limit' => 15,
);

// Determine dates for priority filtering
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

echo "<div id='todo-dashboard-content'>" . get_todos($options) . "</div>";
