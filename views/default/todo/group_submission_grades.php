<?php
/**
 * Group submissions grades ajax view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

$group_guid = get_input('group', FALSE);

$group = get_entity($group_guid);

// Check for valid group
if (!elgg_instanceof($group, 'group')) {
	echo elgg_echo('todo:error:invalidgroup');
	return;
}

$options = array(
	'type' => 'object',
	'subtype' => 'todo',
	'container_guid' => $group_guid,
	'metadata_name' => 'grade_required',
	'metadata_value' => '1',
	'limit' => 0,
);

$todos = elgg_get_entities_from_metadata($options);

if (count($todos)) {
	$members = $group->getMembers(0);
	
	$a_label = elgg_echo('todo:label:assignees');

	$content = "<table id='todo-grade-table' class='elgg-table'><thead><tr><th>{$a_label}</th>";

	foreach ($todos as $todo) {
		
		$grade_total_label = elgg_echo('todo:label:gradedoutof', array($todo->grade_total));
		$tip = "<p>{$todo->title}</p><p>{$grade_total_label}</p>";
		
		$todo_link = elgg_view('output/url', array(
			'text' => elgg_get_excerpt($todo->title, 15),
			'tiptip' => $tip,
			'href' => $todo->getURL(),
			'target' => "_blank",
			'class' => "todo-grade-tooltip",
		));
		$content .= "<th class='todo-title-link'>{$todo_link}</th>";
	}

	$content .= "</tr></thead><tbody>";

	foreach ($members as $member) {
		// Skip group owner
		if ($member->guid == $group->owner_guid) {
			continue;
		}
		
		$content .= "<tr><td class='assignee-name'>";
		$content .= elgg_view('output/url', array(
			'text' => $member->name,
			'href' => $member->getURL(),
		));
	
		foreach ($todos as $todo) {	
			$grade_content = '';
			$grade_text = '';
				
			if (has_user_submitted($member->guid, $todo->guid)) {
				// Check if theres a submission, may have been manually completed
				if ($submission = get_user_submission($member->guid, $todo->guid)) {
					$ajax_url = elgg_get_site_url() . 'ajax/view/todo/ajax_submission?guid=' . $submission->guid;
					$grade = $submission->grade;
					$grade_total = $todo->grade_total;
				
					if ($grade !== NULL) {
						$grade_text .= "{$grade}/{$grade_total}";
					} else {
						$grade_text .= "Ungraded";
					}
				
					$grade_content = "<a id='submission-grade-{$todo->guid}-{$member->guid}' onclick='javascript:return false;' rel='grade-lightbox-{$member->guid}' class='todo-submission-lightbox' href='{$ajax_url}' style='font-weight: bold;'>$grade_text</a>";
				}
			} else if (!is_todo_assignee($todo->guid, $member->guid)) {
				$grade_content = "Not assigned";
			} else {
				$grade_content = "Incomplete";
			}
			
			$content .= "<td>{$grade_content}</td>";
		}
	
		$content .= "</tr>";
	}

	$content .= "</tbody></table>";

} else {
	$content = "<h3 class='center' style='border-top: 1px dotted #CCCCCC; padding-top: 4px; margin-top: 5px;'>" . elgg_echo('todo:label:noresults') . "</h3>"; 
}

echo $content;

echo <<<JAVASCRIPT
	<script type='text/javascript'>
		elgg.tinymce.init();
		elgg.todo.submission.destroy();
		elgg.todo.submission.init();
		
		$(document).ready(function() {
			var gradeDataTable = $('#todo-grade-table').dataTable({
				"sScrollX": "100%",
				"bScrollCollapse": true,
				"bPaginate": false,
				"bInfo": false,
			});
			
			new FixedColumns(gradeDataTable);
			
			$('.todo-grade-tooltip').tipTip({
				delay           : 0,
				defaultPosition : 'top',
				fadeIn          : 25,
				fadeOut         : 300,
				edgeOffset      : -5,
				attribute		: 'tiptip',
				//keepAlive		: true,
			});
		});
	</script>
JAVASCRIPT;
