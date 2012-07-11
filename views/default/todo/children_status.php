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
			$child_content .= "<td><strong>{$child->name}</strong></td><td>";

			$child_grade = elgg_echo('todo:label:notyetgraded');

			// Display wether or not child has submitted
			if (has_user_submitted($child->guid, $todo->guid)) {
				$child_content .= "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";
				
				$submission = get_user_submission($child->guid, $todo->guid);

				if ($submission->grade !== NULL) {
					$child_grade = $submission->grade . "/" . $todo->grade_total;
				}
			} else {
				$child_content .= "<span class='incomplete'>" . elgg_echo('todo:label:statusincomplete') . "</span>";
			}
			$child_content .= "<td><strong>{$child_grade}</strong></td>";
			
			$child_content .= "</td></tr>";
		}
	}
	elgg_set_ignore_access($ia);
}

if ($child_content) {
	$content = "<table class='elgg-table'>";
	$content .= "<thead>
							<tr>
								<th><strong>" . elgg_echo('todo:label:child') . "</strong></th>
								<th><strong>" . elgg_echo('todo:label:status') . "</strong></th>
								<th><strong>" . elgg_echo('todo:label:grade') . "</strong></th>
							</tr>
						</thead>
						<tbody>";
	$content .= $child_content;
	$content .= "</tbody></table>";
	
	echo $content;
}