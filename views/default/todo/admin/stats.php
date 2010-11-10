<?php 
	/**
	 * Todo Assignee view, includes a control to remove assignee from a todo
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 *
	 */
	
	// Just in case.. I want to capture everything
	$ia = elgg_get_ignore_access();
	elgg_set_ignore_access(true);
	
	// Grab all site todos
	$todos = elgg_get_entities(array('type' => 'object', 'subtype' => 'todo', 'limit' => 999999)); 
	
	elgg_set_ignore_access($ia);
	
	// Empty vars
	$complete_todos = array();
	$manually_completed_todos = array();
	$assigned_to_users = 0;
	$total_completion = 0; // for counting average
	
	// Loop over for some various stats
	foreach ($todos as $todo) {
		// If all assignees have completed, add to total complete
		if (have_assignees_completed_todo($todo->getGUID())) {
			$complete_todos[] = $todo;
		}
		
		// If manually completed, add to manual complete count
		if ($todo->manual_complete) {
			$manually_completed_todos[] = $todo;
		}
		
		// Get assigned users
		$assigned_users = get_todo_assignees($todo->getGUID());		
		
		// For counting submissions
		$submissions_count = 0;
		foreach ($assigned_users as $user) {
			if (get_user_submission($user->getGUID(), $todo->getGUID())) {
				$submissions_count++;
			}
		}
		
		// For counting average
		$total_completion += ($submissions_count / count($assigned_users)) * 100;

		// Counting total assigned
		$assigned_to_users += count($assigned_users);
	}
		
	// Todo count
	$todo_count_label = elgg_echo('todo:label:admin:totaltodos');
	$todo_count = count($todos);

	// Submission count
	$submission_count_label = elgg_echo('todo:label:admin:totalsubmissions');
	$submission_count = elgg_get_entities(array('type' => 'object', 'subtype' => 'todosubmission', 'count' => true)); 
	
	// Assigned users count 
	$assigned_user_count_label = elgg_echo('todo:label:admin:assignedusers');
	
	// Assigned groups count
	$assigned_group_count_label = elgg_echo('todo:label:admin:assignedgroups');
	
	// Complete total count 
	$todo_complete_count_label = elgg_echo('todo:label:admin:totalcomplete');
	$todo_complete_count = count($complete_todos);
	
	// Incomplete total count 
	$todo_incomplete_count_label = elgg_echo('todo:label:admin:totalincomplete');
	$todo_incomplete_count = $todo_count - count($complete_todos);
	
	// Percentage complete 
	$todo_complete_percentage_label = elgg_echo('todo:label:admin:completepercentage');
	$todo_complete_percentage = number_format(($todo_complete_count / $todo_count) * 100, 1);
	
	// Manual complete count 
	$manual_complete_count_label = elgg_echo('todo:label:admin:manualcomplete');
	$manual_complete_count = count($manually_completed_todos);
	
	// Manual complete percentage 
	$manual_complete_percentage_label = elgg_echo('todo:label:admin:manualpercentage');
	$manual_complete_percentage = number_format(($manual_complete_count / $todo_complete_count) * 100, 1);
	
	// Assigned/Subissions
	$assigned_submitted_label = elgg_echo('todo:label:admin:assignedsubmitted');
	$assigned_submitted_content = $submission_count . '/'  . $assigned_to_users . ' (' . number_format(($submission_count / $assigned_to_users) * 100, 1) . '%)';
	
	// Completion average
	$completion_average_label = elgg_echo('todo:label:admin:completionaverage');
	$completion_average = number_format($total_completion / count($todos), 1) . '%';

	
echo <<<EOT

<div class='todoadmin'>
	<table class='todostats totals'>
		<caption>Totals</caption>
		<tr>
			<td class='label'>$todo_count_label</td>
			<td class='content'>$todo_count</td>
		</tr>
		<tr>
			<td class='label'>$submission_count_label</td>
			<td class='content'>$submission_count</td>
		</tr>
		<tr>
			<td class='label'>$assigned_user_count_label</td>
			<td class='content'>$assigned_to_users</td>
		</tr>
	</table>
	
	<table class='todostats complete'>
		<caption>Assignee Completion</caption>
		<tr>
			<td class='label'>$assigned_submitted_label</td>
			<td class='content'>$assigned_submitted_content</td>
		</tr>
		<tr>
			<td class='label'>$completion_average_label</td>
			<td class='content'>$completion_average</td>
		</tr>
	</table>
	
	<table class='todostats complete'>
		<caption>General Completion</caption>
		<tr>
			<td class='label'>$todo_complete_count_label</td>
			<td class='content'>$todo_complete_count</td>
		</tr>
		<tr>
			<td class='label'>$todo_incomplete_count_label</td>
			<td class='content'>$todo_incomplete_count</td>
		</tr>
		<tr>
			<td class='label'>$todo_complete_percentage_label</td>
			<td class='content'>$todo_complete_percentage%</td>
		</tr>
		<tr>
			<td class='label'>$manual_complete_count_label</td>
			<td class='content'>$manual_complete_count</td>
		</tr>
		<tr>
			<td class='label'>$manual_complete_percentage_label</td>
			<td class='content'>$manual_complete_percentage%</td>
		</tr>
	</table>
</div>
EOT;

?>

