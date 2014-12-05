<?php
/**
 * Todo Entity View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
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
	$duedate_content = elgg_view('output/text', array('value' => $date));

	$return_label = elgg_echo("todo:label:returnrequired");
	$return_content = $todo->return_required ? 'Yes' : 'No';
	
	$grade_label = elgg_echo("todo:label:grade");

	if ($todo->grade_required) {
		$grade_content = elgg_echo("todo:label:gradedoutof", array($todo->grade_total));
	} else {
		$grade_content = elgg_echo("todo:label:notgraded");
	}
	
	if (elgg_is_admin_logged_in() || $is_owner || $is_assignee) {
		$status_label = elgg_echo("todo:label:overallstatus");
		// Default status
		if (have_assignees_completed_todo($todo->getGUID())) {
			$status_content = "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";		
		} else {
			$status_content = "<span class='incomplete'>" . elgg_echo('todo:label:statusincomplete') . "</span>";
		}
	}
	
	// Assignee
	if ($is_assignee) {
		$status_label = elgg_echo("todo:label:status");
		if (has_user_submitted(elgg_get_logged_in_user_guid(), $todo->getGUID())) {
			$status_content = "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";

			$submission = get_user_submission($user->guid, $todo->guid);

			$status_content .= "<span class='todo-grade-status'>";

			if ($submission->grade !== NULL) {
				$status_content .= $submission->grade . "/" . $todo->grade_total;
			} else if ($todo->grade_required) {
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
		$status_canedit .= elgg_view('todo/status', $vars);
	}

	// Description Content
	$body = elgg_view_module('aside', $description_label, $description_content);

	$body_table = "<table class='elgg-table todo-info-table'>";

	// Optional Start Date
	if ($todo->start_date) {
		$start = is_int($todo->start_date) ? date("F j, Y", $todo->start_date) : $todo->start_date;
		$startdate_label = elgg_echo("todo:label:startdate");
		$startdate_content = elgg_view('output/text', array('value' => $start));
		$body_table .= "<tr><td>$startdate_label</td><td>$startdate_content</td></td>";
	}

	// Due date content
	if ($todo->due_date) {
		$body_table .= "<tr><td>$duedate_label</td><td>$duedate_content</td></td>";
	}

	// Submission Required Content
	$body_table .= "<tr><td>$return_label</td><td>$return_content</td></td>";

	// If supplied suggested tags, display them
	if ($todo->suggested_tags) {
		$suggested_tags_label = elgg_echo("todo:label:suggestedtags");
		$suggested_tags_content = elgg_view('output/tags', array('value' => $todo->suggested_tags));
		$body_table .= "<tr><td>$suggested_tags_label</td><td>$suggested_tags_content</td></td>";
	}

	$body_table .= "<tr><td>$grade_label</td><td>$grade_content</td></td>";
	
	// If we have a rubric guid, display its info
	if ((int)$todo->rubric_guid) {
		$rubric = get_entity($todo->rubric_guid);
		if (elgg_instanceof($rubric, 'object', 'rubric')) {
			$rubric_label = elgg_echo('todo:label:assessmentrubric');
			$rubric_content = "<a href='{$rubric->getURL()}'>{$rubric->title}</a>";
			$body_table .= "<tr><td>$rubric_label</td><td>$rubric_content</td></td>";
		}
	}
	
	if ($status_content) {
		$body_table .= "<tr><td>$status_label</td><td>$status_content</td></td>";
	}

	$body_table .= "</table>";

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
	
	// Output submission form
	$submission_form = elgg_view('forms/submission/save', array('entity' => $todo));
	
	// For hash submissions
	$hash_todo = $todo->guid;

	echo <<<HTML
<div class='todo'>
	$header
	$todo_info<br />
	$body
	$body_table
	$status_canedit
	<div style='display: none;'>
		<div id="todo-submission-dialog">$submission_form</div>
	</div>
	<script type='text/javascript'>
		var submissionCheck = function() {
			if (window.location.href.indexOf('?submission=') != -1) {
				var guid = window.location.href.substring(window.location.href.indexOf('?submission=') + 12);
				$('td > a.todo-submission-lightbox[href$=' + guid + ']').trigger('click');
			}
		}
		elgg.register_hook_handler('ready', 'system', submissionCheck);
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
