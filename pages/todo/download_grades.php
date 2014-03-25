<?php
/**
 * Todo Download Group Grades
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

elgg_load_library('elgg:todo');

// Get the group guid
$group_guid = get_input("guid");

$group = get_entity($group_guid);

if (elgg_instanceof($group, 'group') && $group->canEdit()) {
	
	// Get gradeable todos
	$options = array(
		'type' => 'object',
		'subtype' => 'todo',
		'container_guid' => $group_guid,
		'metadata_name' => 'grade_required',
		'metadata_value' => '1',
		'limit' => 0,
	);

	$todos = elgg_get_entities_from_metadata($options);

	$members = $group->getMembers(0);
	
	$a_label = elgg_echo('todo:label:assignees');

	$output = array($a_label);


	foreach ($todos as $todo) {
		$output[] = $todo->title . ' (' . elgg_echo('todo:label:gradedoutof', array($todo->grade_total)) . ')';
	}

	ob_start();

	echo '"' . implode('","', $output) . '"';
	echo "\r\n";

	foreach ($members as $member) {
		// Skip group owner
		if ($member->guid == $group->owner_guid) {
			continue;
		}

		$output = array($member->name);

		foreach ($todos as $todo) {	
				
			if (has_user_submitted($member->guid, $todo->guid)) {
				// Check if theres a submission, may have been manually completed
				if ($submission = get_user_submission($member->guid, $todo->guid)) {
					$grade = $submission->grade;
				
					if ($grade !== NULL) {
						$output[] = "{$grade}";
					} else {
						$output[] = "Ungraded";
					}
				}
			} else if (!is_todo_assignee($todo->guid, $member->guid)) {
				$output[] = "Not assigned";
			} else {
				$output[] = "Incomplete";
			}
		}
		echo '"' . implode('","', $output) . '"';
		echo "\r\n";
	}

	$filename = elgg_get_friendly_title($group->name) . '_grades.csv';
	todo_download_send_headers($filename);

	$content = ob_get_clean();
	echo $content;

} else {
	register_error(elgg_echo('todo:error:access'));
	forward(REFERER);
}