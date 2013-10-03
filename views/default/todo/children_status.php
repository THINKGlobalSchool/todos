<?php
/**
 * Todo Children Status View, displays status of children todo submissions
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$parent = elgg_extract('parent', $vars, elgg_get_logged_in_user_entity());
$todo = elgg_extract('todo', $vars);

if (!elgg_instanceof($todo, 'object', 'todo')) {
	return FALSE;
}

$children = parentportal_get_parents_children($parent->guid);

if (count($children)) {
	// Ignore access here..
	$ia = elgg_get_ignore_access();
	elgg_set_ignore_access(TRUE);

	// Loop over parents children
	foreach ($children as $child) {
		// Make sure they are an assignee
		if (is_todo_assignee($todo->guid, $child->guid)) {
			$child_content .= "<tr>";
			$child_content .= "<td><strong>{$child->name}</strong></td>";

			$child_grade = elgg_echo('todo:label:notyetgraded');

			// Display wether or not child has submitted
			if (has_user_submitted($child->guid, $todo->guid)) {
				$child_content .= "<td><span class='complete'>" . elgg_echo('todo:label:complete') . "</span></td>";
				
				$submission = get_user_submission($child->guid, $todo->guid);

				if ($submission->grade !== NULL) {
					$child_grade = $submission->grade . "/" . $todo->grade_total;
				}

				// Check if theres a submission, may have been manually completed
				if ($submission = get_user_submission($child->guid, $todo->guid)) {
					$date = date("F j, Y", $submission->time_created);
					$ajax_url = elgg_get_site_url() . 'ajax/view/todo/ajax_submission?guid=' . $submission->guid;
					$submission_info = "<a onclick='javascript:return false;' rel='todo-submission-lightboxen' class='todo-submission-lightbox' href='{$ajax_url}'>View</a>";
				} else {
					$submission_info = "<span class='todo-status-dash'>-</span>";
				}

				$child_content .= "<td>{$submission_info}</td>";
			} else {
				$child_content .= "<td><span class='incomplete'>" . elgg_echo('todo:label:statusincomplete') . "</span></td><td>-</td>";
			}
			$child_content .= "<td><strong>{$child_grade}</strong></td>";
			
			$child_content .= "</tr>";
		}
	}
	elgg_set_ignore_access($ia);
}

if ($child_content) {
	if (elgg_is_active_plugin('roles')) {
		 if (roles_is_member(elgg_get_plugin_setting('view_students_role', 'parentportal'), elgg_get_logged_in_user_guid())) {
		 	$user_label = elgg_echo('todo:label:student');
		 } else if (roles_is_member(elgg_get_plugin_setting('todofacultyrole', 'todo'), elgg_get_logged_in_user_guid())) {
		 	$user_label = elgg_echo('todo:label:advisee');
		 } else if (elgg_is_active_plugin('parentportal') && parentportal_is_user_parent(elgg_get_logged_in_user_entity())) {
			$user_label = elgg_echo('parentportal:title:childinfo');
		}
	}

	if (!$user_label) {
		$user_label = elgg_echo('todo:label:child');
	}

	$content = "<table class='elgg-table'>";
	$content .= "<thead>
							<tr>
								<th><strong>" . $user_label . "</strong></th>
								<th><strong>" . elgg_echo('todo:label:status') . "</strong></th>
								<th><strong>" . elgg_echo('todo:label:submission') . "</strong></th>
								<th><strong>" . elgg_echo('todo:label:grade') . "</strong></th>
							</tr>
						</thead>
						<tbody>";
	$content .= $child_content;
	$content .= "</tbody></table>";
	
	echo $content;
}