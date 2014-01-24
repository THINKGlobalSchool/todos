<?php
/**
 * Todo Status View, displays status of todo's submissions
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars
 * 
 */

// Get todo
$todo = $vars['entity'];

// Get assignees
$assignees = get_todo_assignees($todo->getGUID());

if ($todo->grade_required) {
	$grade_header = "<th><strong>" . elgg_echo('todo:label:grade') . "</strong></th>";
	$colspan = 6;
} else {
	$colspan = 5;
}

if (!is_todo_admin()) {
	$remind_header = "<th><strong>" . elgg_echo('todo:label:reminder') . "</strong></th>";
}

// Table Headers
$content = "<br /><br/><table class='elgg-table'>
				<thead>
					<tr>
						<th><strong>" . elgg_echo('todo:label:assignee') . "</strong></th>
						<th><strong>" . elgg_echo('todo:label:accepted') . "</strong></th>
						<th><strong>" . elgg_echo('todo:label:status') . "</strong></th>
						<th><strong>" . elgg_echo('todo:label:datecompleted') . "</strong></th>
						$grade_header
						<th><strong>" . elgg_echo('todo:label:submission') . "</strong></th>
						$remind_header
					</tr>
				</thead>
				</tbody>";

// Array of assignee guids to bulk remind
$assignee_guids = array();

$dash = "<span class='todo-status-dash'>-</span>";

foreach ($assignees as $assignee) {
	//Populate assignee_guids array for later
	$assignee_guids[] = $assignee->getGUID();
		
	// Default values
	$status = '<span class="incomplete">' . elgg_echo('todo:label:statusincomplete') . '</span>';
	
	$submission_info = $date = $grade = $dash;
	
	$reminder = elgg_view("output/confirmlink", array(
		'href' => elgg_get_site_url() . "action/todo/sendreminder?todo_guid=" . $todo->guid . "&a=" . $assignee->guid,
		'text' => elgg_echo('todo:label:sendreminder'),
		'confirm' => elgg_echo('todo:label:remindconfirm'),
	));
	
	// Accepted/Unaccepted
	if (has_user_accepted_todo($assignee->guid, $vars['entity']->getGUID())) {
		$accepted = '<span class="complete">' . elgg_echo('todo:label:yes') . '</span>';	
	} else {
		$accepted = '<span class="incomplete">' . elgg_echo('todo:label:no') . '</span>';
	}
	
	// Show completed submission data
	if (has_user_submitted($assignee->guid, $vars['entity']->getGUID())) {
		$status = '<span class="complete">Complete</span>';
		
		// Check if theres a submission, may have been manually completed
		if ($submission = get_user_submission($assignee->guid, $vars['entity']->getGUID())) {
			$date = date("F j, Y", $submission->time_created);
			$ajax_url = elgg_get_site_url() . 'ajax/view/todo/ajax_submission?guid=' . $submission->guid;
			$submission_info = "<a onclick='javascript:return false;' rel='todo-submission-lightboxen' class='todo-submission-lightbox' href='{$ajax_url}'>View</a>";
			
			if ($submission->grade !== NULL) {
					$grade = $submission->grade . '/' . $vars['entity']->grade_total; 
			}
		}

		$reminder = $dash;
	}

	if ($todo->grade_required) {
		$grade_content = "<td id='assignee-grade-$assignee->guid' style='font-weight: bold;'>$grade</td>";
	}

	if (!is_todo_admin()) {
		$remind_content = "<td>$reminder</td>";
	}
	
	// Build rest of content
	$content .= '<tr>';
	$content .= 	"<td>$assignee->name</td>";
	$content .= 	"<td>$accepted</td>";
	$content .= 	"<td>$status</td>";
	$content .= 	"<td>$date</td>";
	$content .= 	$grade_content;
	$content .= 	"<td>$submission_info</td>";
	$content .= 	$remind_content;
	$content .= '</tr>';
}

// Build querystring
foreach ($assignee_guids as $idx => $guid) {
	$qs .= "&a[]=" . $assignee_guids[$idx];
}

// If there are submissions, display extra options
if (get_todo_submissions_count($todo->guid)) {
	$download_files = elgg_view('output/url', array(
		'href' => 'todo/download/' . $todo->guid,
		'text' => elgg_echo('todo:label:downloadfiles'),
		//'class' => 'elgg-button elgg-button-action',
	));
	
	$colspan = ($colspan == 6) ? 5 : 4;
	$download_content = "<td>$download_files</td>";
}

if ($todo->canEdit()) {
	$remind_all = elgg_view("output/confirmlink", array(
		'href' => elgg_get_site_url() . "action/todo/sendreminder?todo_guid=" . $todo->getGUID() . $qs,
		'text' => elgg_echo('todo:label:remindall'),
		'confirm' => elgg_echo('todo:label:remindconfirm'),
	));

	$content .= "<tr><td colspan=$colspan style='text-align: right;'></td>";
	$content .= $download_content;
	$content .= "<td>" . $remind_all . "</td></tr>";
}


$content .= '</tbody></table>';

echo $content;
