<?php
/**
 * Todo Entity View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$full = elgg_extract('full_view', $vars, FALSE);
$todo = elgg_extract('entity', $vars, FALSE);

if (!elgg_instanceof($todo, 'object', 'todo')) {
	return TRUE;
}

$owner = $todo->getOwnerEntity();
$container = $todo->getContainerEntity();
$categories = elgg_view('output/categories', $vars);

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array(
	'href' => "todo/owner/$owner->username",
	'text' => $owner->name,
));

$author_text = elgg_echo('todo:label:assignedby', array($owner_link));

$tags = elgg_view('output/tags', array('tags' => $todo->tags));

$comments_count = $todo->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $todo->getURL() . '#comments',
		'text' => $text,
	));
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $todo,
	'handler' => 'todo',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

// Add due date
if ($todo->due_date) {
	$date = is_int($todo->due_date) ? date("F j, Y", $todo->due_date) : $todo->due_date;
	$due_date = elgg_echo('todo:label:due', array($date));
}

$subtitle = "<strong>$due_date</strong><p>$author_text $comments_link</p>";
$subtitle .= $categories;

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}


if ($full) { // Full View
	// Determine how we are going to view this todo
	$is_owner = $todo->canEdit() ? true : is_todo_admin();
	$is_assignee = is_todo_assignee($todo->getGUID(), elgg_get_logged_in_user_guid());
	$is_parent = elgg_get_logged_in_user_entity()->is_parent;

	// Start putting content together
	$description_label = elgg_echo("todo:label:description");
	$description_content = elgg_view('output/longtext', array('value' => $vars['entity']->description));

	$duedate_label = elgg_echo("todo:label:duedate");
	$duedate_content = elgg_view('output/longtext', array('value' => $date));

	$return_label = elgg_echo("todo:label:returnrequired");
	$return_content = $todo->return_required ? 'Yes' : 'No';
	
	$grade_label = elgg_echo("todo:label:grade");

	if ($todo->grade_required) {
		$grade_content = elgg_echo("todo:label:gradedoutof", array($todo->grade_total));
	} else {
		$grade_content = elgg_echo("todo:label:notgraded");
	}

	$status_label = elgg_echo("todo:label:status");
	
	// Default status
	if (have_assignees_completed_todo($todo->getGUID())) {
		$status_content = "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";		
	} else {
		$status_content = "<span class='incomplete'>" . elgg_echo('todo:label:statusincomplete') . "</span>";
	}
	
	// Assignee
	if ($is_assignee) {
		if (has_user_submitted(elgg_get_logged_in_user_guid(), $todo->getGUID())) {
			$status_content = "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";

			$submission = get_user_submission($user->guid, $todo->guid);

			$status_content .= "<span class='todo-grade-status'>";

			if ($submission->grade !== NULL) {
				$status_content .= $submission->grade . "/" . $todo->grade_total;
			} else {
				$status_content .= "(" . elgg_echo('todo:label:notyetgraded') . ")";
			}

			$status_content .= "</span>";
		} else {
			$status_content = "<span class='incomplete'>" . elgg_echo('todo:label:statusincomplete') . "</span>";
		}
	} 
	
	// If we're viewing as a parent
	if (!$is_assignee && !$is_owner && elgg_is_active_plugin('parentportal')) {
		$child_content = elgg_view('todo/children_status', array(
			'todo' => $todo,
			'parent' => elgg_get_logged_in_user_entity(),
		));
		
		if ($child_content) {
			$status_content = $child_content;
		}
	}
	
	// Owner
	if ($is_owner) {
		$status_content .= elgg_view('todo/status', $vars);
	} 

	// Description Content
	$body = elgg_view_module('info', $description_label, $description_content);

	// Optional Start Date
	if ($todo->start_date) {
		$start = is_int($todo->start_date) ? date("F j, Y", $todo->start_date) : $todo->start_date;
		$startdate_label = elgg_echo("todo:label:startdate");
		$startdate_content = elgg_view('output/longtext', array('value' => $start));
		$body .= elgg_view_module('info', $startdate_label, $startdate_content);
	}

	// Due date content
	if ($todo->due_date) {
		$body .= elgg_view_module('info', $duedate_label, $duedate_content);
	}

	// Submission Required Content
	$body .= elgg_view_module('info', $return_label, $return_content);	

	// If supplied suggested tags, display them
	if ($todo->suggested_tags) {
		$suggested_tags_label = elgg_echo("todo:label:suggestedtags");
		$suggested_tags_content = elgg_view('output/tags', array('value' => $todo->suggested_tags));
		$body .= elgg_view_module('info', $suggested_tags_label, $suggested_tags_content);	
	}

	$body .= elgg_view_module('info', $grade_label, $grade_content);	
	
	// If we have a rubric guid, display its info
	if ((int)$todo->rubric_guid) {
		$rubric = get_entity($todo->rubric_guid);
		if (elgg_instanceof($rubric, 'object', 'rubric')) {
			$rubric_content = "<a href='{$rubric->getURL()}'>{$rubric->title}</a>";
			$body .= elgg_view_module('info', elgg_echo('todo:label:assessmentrubric'), $rubric_content);
		}
	}
	
	$body .= elgg_view_module('info', $status_label, $status_content);
	

	$header = elgg_view_title($todo->title);

	$params = array(
		'entity' => $todo,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
	);
	$list_body = elgg_view('object/elements/summary', $params);

	$todo_info = elgg_view_image_block($owner_icon, $list_body);
	
	// Submission form vars
	$submission_form_vars = array();
	$submission_form_vars['id'] = 'todo-submission-form';
	$submission_form_vars['name'] = 'todo_submission_form';
	
	// View submission form
	$submission_form = elgg_view_form('submission/save', $submission_form_vars, array('entity' => $todo));
	
	// For hash submissions
	$hash_todo = $todo->guid;

	echo <<<HTML
<div class='todo'>
	$header
	$todo_info<br />
	$body
	<div style='display: none;'>
		<div id="todo-submission-dialog">$submission_form</div>
	</div>
	<script type='text/javascript'>
		hash_check = function() {
			// Trigger hash check
			var todo_guid = "{$hash_todo}";
			elgg.todo.submission.processHash(todo_guid);
		}
		elgg.register_hook_handler('ready', 'system', hash_check);
	</script>
</div>
HTML;
} else { // listing view
	$params = array(
		'entity' => $todo,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
		'content' => '',
	);
	
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($owner_icon, $list_body);
}
