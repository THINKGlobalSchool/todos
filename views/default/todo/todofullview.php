<?php
/**
 * Todo Full View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$user = elgg_get_logged_in_user_entity();

$page_owner = elgg_get_page_owner_entity();
	
// Determine how we are going to view this todo
$is_owner = $vars['entity']->canEdit();
$is_assignee = is_todo_assignee($vars['entity']->getGUID(), $user->getGUID());

$url = $vars['entity']->getURL();
$owner = $vars['entity']->getOwnerEntity();
$title = $vars['entity']->title;
$return_required = $vars['entity']->return_required;
$due_date = is_int($vars['entity']->due_date) ? date("F j, Y", $vars['entity']->due_date) : $vars['entity']->due_date;
	
// Start putting content together
$description_label = elgg_echo("todo:label:description");
$description_content = elgg_view('output/longtext', array('value' => $vars['entity']->description));

$duedate_label = elgg_echo("todo:label:duedate");
$duedate_content = elgg_view('output/longtext', array('value' => $due_date));

$return_label = elgg_echo("todo:label:returnrequired");
$return_content = ($return_required ? "Yes": 'No' );

$status_label = elgg_echo("todo:label:status");

$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
		
// If container is a group, show the group name as well as the author in the info	
$group_name = $page_owner instanceof ElggGroup ? " (<a href='{$page_owner->getURL()}'>$page_owner->name</a>)" : '';			
			
$strapline .= sprintf(elgg_echo('todo:label:assignedby') , "<a href='todo/{$owner->username}'>{$owner->name}</a>$group_name ");

$submission_form = elgg_view('todo/forms/submission', $vars);

// Optional functionality
if (TODO_RUBRIC_ENABLED && $rubric = get_entity($vars['entity']->rubric_guid)) {
	$controls .= "<span class='entity_edit'><a href='{$rubric->getURL()}'>" . elgg_echo('todo:label:viewrubric') . "</a></span>";
}

// Set status content for viewers (will be changed, updated depending on how this todo is viewed)
if (have_assignees_completed_todo($vars['entity']->getGUID())) {
	$status_content = "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";
} else {
	$status_content = "<span class='incomplete'>" . elgg_echo('todo:label:statusincomplete') . "</span>";
}

// Assignee only content
if ($is_assignee) {
	if (has_user_accepted_todo($user->getGUID(), $vars['entity']->getGUID())) {
		$controls .= "<span class='accepted'>✓ Accepted</span>";
	} else {
		$controls .= "<span class='unviewed'>";
		$controls .= elgg_view("output/confirmlink", 
										array(
										'href' => elgg_get_site_url() . "action/todo/accept?guid=" . $vars['entity']->getGUID(),
										'text' => elgg_echo('todo:label:accept'),
										'confirm' => elgg_echo('todo:label:acceptconfirm'),
										'class' => 'action_button'
									)) . "</span>"; 
	}
	if (has_user_submitted($user->getGUID(), $vars['entity']->getGUID())) {
		$status_content = "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";
		if ($submission = get_user_submission($user->getGUID(), $vars['entity']->getGUID())) {
			$controls .= "&nbsp;&nbsp;&nbsp;<span class='entity_edit'><a id='view_submission' href='" . $submission->getURL() . "'>" . elgg_echo("todo:label:viewsubmission") . "</a></span>";
		}	
	} else {
		$status_content = "<span class='incomplete'>" . elgg_echo('todo:label:statusincomplete') . "</span>";
		// Check if the todo has been manually completed
		if ($vars['entity']->manual_complete) {
			$controls .= "<span class='todo-meta-controls'><b>Closed</b></span>";
		} else {
			// If we need to return something for this todo, the complete link will point to the submission form
			if ($vars['entity']->return_required) {
				$controls .= "<span class='todo-meta-controls'><a class='action_button' id='create_submission' href='#'>" . elgg_echo("todo:label:completetodo") . "</a></span>";
			} else {
				// No return required, link to createsubmissionaction and create blank submission
				$controls .= "<span class='todo-meta-controls'><a class='action_button' id='create_blank_submission' href='#'>" . elgg_echo("todo:label:completetodo") . "</a></span>";
			}
		}
	}
	
} else {
	if ($vars['entity']->manual_complete != true) {
		$controls .= "<span class='unviewed'>";
		$controls .= elgg_view("output/confirmlink", 
										array(
										'href' => elgg_get_site_url() . "action/todo/assign?todo_guid=" . $vars['entity']->getGUID(),
										'text' => elgg_echo('todo:label:signup'),
										'confirm' => elgg_echo('todo:label:signupconfirm'),
										'class' => 'action_button'
									)) . "</span>";
	}

}

// Owner only Content
if ($is_owner) {
		$status_content .= elgg_view('todo/todostatus', $vars);
		$controls .= "<span class='todo-meta-controls'><a class='action_button' href='" . elgg_get_site_url() . "todo/edit/{$vars['entity']->getGUID()}'>" . elgg_echo("edit") . "</a></span>";
		
		if ($vars['entity']->manual_complete) {
			$controls .= "<span class='todo-meta-controls'><b>Closed</b></span>";
		} else {
			$controls .= "<span class='todo-meta-controls'>" . elgg_view("output/confirmlink", 
									array(
										'href' => elgg_get_site_url() . "action/todo/completetodo?todo_guid=" . $vars['entity']->getGUID(),
										'text' => elgg_echo('todo:label:flagcomplete'),
										'confirm' => elgg_echo('todo:label:flagcompleteconfirm'),
										'class' => 'action_button'
									)) . "</span>";
		}
							
		$controls .= "<span class='delete_button todo-meta-controls'>" . elgg_view("output/confirmlink", 
								array(
									'href' => elgg_get_site_url() . "action/todo/delete?guid=" . $vars['entity']->getGUID(),
									'text' => elgg_echo('delete'),
									'confirm' => elgg_echo('deleteconfirm'),
								)) . "</span>";
}



if ($vars['entity']->status == TODO_STATUS_DRAFT) {
	$todo_status = elgg_echo('todo:status:draft'); 
} else if ($vars['entity']->status == TODO_STATUS_PUBLISHED) {
	$todo_status = elgg_echo('todo:status:published');
}

$todo_status_content = elgg_echo('todo:label:status') . ': ' . $todo_status; 

// AJAX Endpoint for submissions
$submission_url = elgg_add_action_tokens_to_url(elgg_get_site_url() . 'mod/todo/actions/todo/createsubmission.php');

// JS
$script = <<<HTML
<script type='text/javascript'>
$(function() {
	/** SET UP DIALOG POPUP **/
	$('#submission-dialog').dialog({
						autoOpen: false,
						width: 725,
						modal: true,
						open: function(event, ui) { 
							$(".ui-dialog-titlebar-close").hide(); 
							if (typeof(tinyMCE) !== 'undefined') {
								tinyMCE.execCommand('mceAddControl', false, 'submission_description');
							}
						},
						beforeclose: function(event, ui) {
							if (typeof(tinyMCE) !== 'undefined') {
					    		tinyMCE.execCommand('mceRemoveControl', false, 'submission_description');
							}
					    },
						buttons: {
							"X": function() { 
								$(this).dialog("close"); 
							} 
						}
					});
	
	$("a#create_submission").click(
		function() {
			$("#submission-dialog").dialog("open");
			return false;
		}
	);
	
	$("a#create_blank_submission").click(
		function() {
			sendSubmission();
			setTimeout ('window.location.reload()', 800);
			return false;
		}
	);
	
	$("form#todo-submission-form").submit(
		function() {
			/** May not be tinyMCE **/
			if (typeof(tinyMCE) !== 'undefined') {
				var comment = tinyMCE.get('submission_description').getContent();
				$("textarea#submission_description").val(comment);
			}
			else {
				var comment = $("textarea#submission_description").val();
				
			}
			
			var content = $("#submission_content").val();
				
			if (content) {
				sendSubmission();
				if (typeof(tinyMCE) !== 'undefined') {
		    		tinyMCE.execCommand('mceRemoveControl', false, 'submission_description');
				}
				$("#submission-dialog").dialog("close");
				setTimeout ('window.location.reload()', 800);
		
			} else {
				// error
				$("#submission-error-message").show().html("** Content is required");
			}
			return false;
		}
	);
	
	function sendSubmission() {
		data = $("form#todo-submission-form").serializeArray();
		$.ajax({
			url: stripJunk("$submission_url"),
			type: "POST",
			data: data,
			cache: false, 
			dataType: "html", 
			error: function() {
				//alert('There was an error');	
			},
			success: function(data) {
			}
			
		});
	}			
});
</script>

HTML;

// Put content together
$info = <<<HTML
			<div class='todo' style='border-bottom:1px dotted #CCCCCC; margin-bottom: 4px;'>
				<div class='todo_owner_block'>
					$strapline 
				</div>
				<br />
				<div class='strapline'>
					<div class='entity_metadata' style='float: right;'>
						$todo_status_content $controls
					</div>
					<div style='clear: both;'></div>
				</div>
				<p class='tags'>$tags</p>
				<div class='clearfloat'></div>
				<div class='description '>
					<label>$description_label</label><br />
					$description_content
				</div><br />
				<div>
					<label>$duedate_label</label><br />
					$duedate_content
				</div><br />
				<!--<div>
					<label>$assignees_label</label><br />
					$assignees_content
				</div><br />-->
				<div>
					<label>$return_label</label><br />
					$return_content
				</div><br />
				<div>
					<label>$status_label</label><br />
					$status_content
				</div><br />
			</div>
		<div id="submission-dialog" style="display: none;" >$submission_form</div>
HTML;

// Echo content
echo $script . $info;
