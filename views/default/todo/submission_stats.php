<?php
/**
 * User submissions stats view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

$user_guid = elgg_extract('user_guid', $vars);
$group_guid = elgg_extract('group_guid', $vars, NULL);

$complete_including_closed_count = count_complete_todos($user_guid, $group_guid);

$submissions_count = count_submissions($user_guid, $group_guid);

$ontime_submissions_count = count_submissions($user_guid, $group_guid, TRUE);

$assigned_todos_count = count_assigned_todos($user_guid, $group_guid);

// Calculate percentages
if ($assigned_todos_count > 0) {
	$complete_including_closed_percentage = $complete_including_closed_count / $assigned_todos_count;
	$complete_including_closed_percentage = number_format($complete_including_closed_percentage * 100, 1) . "%";
	
	$complete_percentage = $submissions_count / $assigned_todos_count;
	$complete_percentage = number_format($complete_percentage * 100, 1) . "%";
	
	$ontime_percentage = $ontime_submissions_count / $assigned_todos_count;
	$ontime_percentage = number_format($ontime_percentage * 100, 1) . "%";
	
} else { // No dividing by zero!
	$complete_including_closed_percentage = "N/A";
	$complete_percentage = "N/A";
	$ontime_including_closed_percentage = "N/A";
	$ontime_percentage = "N/A";
}

// Labels
$complete_label = elgg_echo('todo:label:complete');
$complete_including_closed_label = elgg_echo('todo:label:completeincludingclosed');
$ontime_label = elgg_echo('todo:label:ontime');
$ontime_including_closed_label = elgg_echo('todo:label:ontimeincludingclosed');

$content = <<<HTML
	<div class='submission-complete-info'>
		<span><strong>$complete_label:</strong> $complete_percentage</span>
		<span><strong>$complete_including_closed_label:</strong> $complete_including_closed_percentage</span>
		<span><strong>$ontime_label:</strong> $ontime_percentage</span>
	</div>
HTML;

echo $content;