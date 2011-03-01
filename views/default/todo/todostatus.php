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

$todo = $vars['entity'];

// Get assignees
$assignees = get_todo_assignees($todo->getGUID());

// Table Headers
$content = "<div class='todo'>
				<table class='status_table'>
					<tr>
						<th>" . elgg_echo('todo:label:assignee') . "</th>
						<th>" . elgg_echo('todo:label:accepted') . "</th>
						<th>" . elgg_echo('todo:label:status') . "</th>
						<th>" . elgg_echo('todo:label:datecompleted') . "</th>
						<th>" . elgg_echo('todo:label:submission') . "</th>
						<th>" . elgg_echo('todo:label:reminder') . "</th>
					</tr>";

// Array of assignee guids to bulk remind
$assignee_guids = array();

$count = 0;
foreach ($assignees as $assignee) {
	//Populate assignee_guids array for later
	$assignee_guids[] = $assignee->getGUID();
	
	// Zebra
	$class = '';
	if ($count % 2 == 0) {
		$class .= ' alt'; 
	}
	
	// Default values
	$status = '<span class="incomplete">' . elgg_echo('todo:label:statusincomplete') . '</span>';
	$date = '-';
	$url = '-';
	$reminder = elgg_view("output/confirmlink", 
									array(
									'href' => $vars['url'] . "action/todo/sendreminder?todo_guid=" . $todo->getGUID() . "&a=" . $assignee->getGUID(),
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
			$url = "<a href='{$submission->getURL()}'>View</a>";
		}
		
		$reminder = '<span style="color: #bbbbbb;">-</span>';
	}
	
	// Build rest of content
	$content .= '<tr>';
	$content .= 	"<td class='$class'>$assignee->name</td>";
	$content .= 	"<td class='$class'>$accepted</td>";
	$content .= 	"<td class='$class'>$status</td>";
	$content .= 	"<td class='$class'>$date</td>";
	$content .= 	"<td class='$class'>$url</td>";
	$content .= 	"<td class='$class'>$reminder</td>";
	$content .= '</tr>';
	$count++;
}

// Build querystring
foreach ($assignee_guids as $idx => $guid) {
	$qs .= "&a[]=" . $assignee_guids[$idx];
}

$remind_all = elgg_view("output/confirmlink", 
								array(
								'href' => $vars['url'] . "action/todo/sendreminder?todo_guid=" . $todo->getGUID() . $qs,
								'text' => elgg_echo('todo:label:remindall'),
								'confirm' => elgg_echo('todo:label:remindconfirm'),
							));

$class = '';
if ($count % 2 == 0) {
	$class .= ' alt'; 
}

$content .= "<td colspan=5 style='text-align: right;' class='$class'></td>";
$content .= "<td class='$class'>$remind_all</td>";

$content .= '</table></div>';

echo $content;
