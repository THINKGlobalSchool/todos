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
	
	$user = get_loggedin_user();
	
	// Determine how we are going to view this todo
	$is_owner = $vars['entity']->canEdit();
	$is_assignee = is_todo_assignee($vars['entity']->getGUID(), $user->getGUID());
	
	$url = $vars['entity']->getURL();
	$owner = $vars['entity']->getOwnerEntity();
	$title = $vars['entity']->title;
	
	
	// Start putting content together
	$description_label = elgg_echo("todo:label:description");
	$description_content = elgg_view('output/longtext', array('value' => $vars['entity']->description));
	
	$duedate_label = elgg_echo("todo:label:duedate");
	$duedate_content = elgg_view('output/longtext', array('value' => $vars['entity']->due_date));
	
	$assignees_label = elgg_echo("todo:label:assignees");
	$assignees_content = elgg_view('todo/assigneelist', array('assignees' => get_todo_assignees($vars['entity']->getGUID())));
	
	$status_label = elgg_echo("todo:label:status");
	
	$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
				
	$strapline = sprintf(elgg_echo("todo:strapline"), date("F j, Y",$vars['entity']->time_created));
	$strapline .= " " . elgg_echo('by') . " <a href='{$vars['url']}pg/todo/{$owner->username}'>{$owner->name}</a> ";
	$strapline .= sprintf(elgg_echo("comments")) . " (" . elgg_count_comments($vars['entity']) . ")";

	$submission_form = elgg_view('todo/forms/submission', $vars);
	
	// Optional functionality
	if (TODO_RUBRIC_ENABLED && $rubric = get_entity($vars['entity']->rubric_guid)) {
		$controls .= "<a href='{$rubric->getURL()}'>" . elgg_echo('todo:label:viewrubric') . "</a>";
	}
	
	// Assignee only content
	if ($is_assignee) {
		$controls .= "&nbsp;&nbsp;&nbsp;<a id='create_submission' href='#'>" . elgg_echo("todo:label:completetodo") . "</a>";
		
		if (has_user_submitted($user->getGUID(), $vars['entity']->getGUID())) {
			$status_content .= "<span class='complete'>" . elgg_echo('todo:label:complete') . "</span>";
		} else {
			$status_content .= "<span class='incomplete'>" . elgg_echo('todo:label:incomplete') . "</span>";
		}
		
	} 
	
	// Owner only Content
	if ($is_owner) {
			$status_content .= elgg_view('todo/todostatus', $vars);
			$controls .= "&nbsp;&nbsp;&nbsp;<a href={$vars['url']}pg/todo/edittodo/{$vars['entity']->getGUID()}>" . elgg_echo("edit") . "</a>";
			$controls .= "&nbsp;&nbsp;&nbsp;" . elgg_view("output/confirmlink", 
									array(
										'href' => $vars['url'] . "action/todo/deletetodo?todo_guid=" . $vars['entity']->getGUID(),
										'text' => elgg_echo('delete'),
										'confirm' => elgg_echo('deleteconfirm'),
									));
	}

	if ($tags) {
		$tags = "<p class='fulltags'>
					" . $tags . "
				</p>";
	} else {
		$tags = '<p></p>';
	}
	
	// AJAX Endpoint for submissions
	$submission_url = elgg_add_action_tokens_to_url($CONFIG->wwwroot . 'mod/todo/actions/createsubmission.php');
	
	// JS
	$script = <<<EOT
	<script type='text/javascript'>
	$(function() {
		/** SET UP DIALOG POPUP **/
		$('#submission_dialog').dialog({
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
		
		/** SUBMISSION CLICK HANDLER **/
		$("a#create_submission").click(
			function() {
				$("#submission_dialog").dialog("open");
				return false;
			}
		);
		
		$("form#todo_submission_form").submit(
			function() {
				/** May not be tinyMCE **/
				if (tinyMCE) 
					var comment = tinyMCE.get('submission_description').getContent();
				else 
					var comment = $("textarea#submission_description").val();
				if (comment) {
					sendSubmission({$vars['entity']->guid}, comment);
					$("#submission_dialog").dialog("close");
				} else {
					// error
				}
				return false;
				
			}
		);
		
		function sendSubmission(entity_guid, comment) {
			$.ajax({
				url: "$submission_url",
				type: "POST",
				data: "todo_guid=" + entity_guid + "&description=" + comment,
				cache: false, 
				dataType: "html", 
				error: function() {
					alert('There was an error');	
				},
				success: function(data) {
				}
				
			});
		}			
	});
	</script>
	
EOT;
	
	// Put content together
	$comments = elgg_view_comments($vars['entity']);
	$info = <<<EOT
			<div class='contentWrapper singleview'>
				<div class='todo'>
					<div class='todo_header'>
						<div class='todo_header_title'><h2><a href='$url'>$title</a></h2></div>
						<div class='todo_header_controls'>
							$controls
						</div>
						<div style='clear:both;'></div>
					</div>
					<div class='strapline'>
						$strapline
					</div>
					$tags
					<div class='clearfloat'></div>
					<div class='description'>
						<label>$description_label</label><br />
						$description_content
					</div>
					<div>
						<label>$duedate_label</label><br />
						$duedate_content
					</div>
					<div>
						<label>$assignees_label</label><br />
						$assignees_content
					</div><br />
					<div>
						<label>$status_label</label><br />
						$status_content
					</div><br />
				</div>
			</div>
			$comments
			<div id="submission_dialog" style="display: none;" >$submission_form</div>
EOT;
	
	// Echo content
	echo $script . $info;
?>