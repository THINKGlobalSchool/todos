<?php
/**
 * Todo JS library
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>
//<script>
elgg.provide('elgg.todo');

elgg.todo.fileUploadURL = elgg.get_site_url() + 'mod/todo/actions/todo/upload.php';

elgg.todo.init = function() {	
	$(function() {
		/* Remove river comments for submissions, need to fix this
		$('.todo_submission_river_item .river_comments').remove();
		$('.todo_submission_river_item .river_comments_tabs').remove();
		*/
	
		// Create submission dialog
		$('#todo-submission-dialog').dialog({
			autoOpen: false,
			width: 725,
			modal: true,
			zIndex: 7002,
			dialogClass: 'todo-dialog',
			open: function(event, ui) { 
				$(".ui-dialog-titlebar-close").hide(); 
				if (typeof(tinyMCE) !== 'undefined') {
					tinyMCE.execCommand('mceAddControl', false, 'submission-description');
				}
				
				// Set up submission content form
				elgg.todo.submissionFormDefault();
			},
			beforeclose: function(event, ui) {
				if (typeof(tinyMCE) !== 'undefined') {
		    		tinyMCE.execCommand('mceRemoveControl', false, 'submission-description');
				}
		    },
			buttons: {
				"X": function() { 
					$(this).dialog("close"); 
				} 
			}
		});
		
		// TODO FORM SETUP
		
		// Create submission click handler
		$('.todo-create-submission').live('click', elgg.todo.completeClick);
		
		// Submission form submit handler
		$("form#todo-submission-form").live('submit', elgg.todo.submissionFormSubmit);
		
		
		// SUBMISSION CONTENT FORM SETUP
		
		// Make menu items clickable
		$(".submission-content-menu-item").live('click', elgg.todo.submissionContentMenuClick);
		
		// Make submit link button clickable
		$("#submission-submit-link").live('click', elgg.todo.submissionSubmitLink);
		
		// Back button click action
		$("#submission-content-back-button").live('click', elgg.todo.submissionFormDefault);
		
		// Register submit handler for submission file form
		$("#submission-file-form").submit(elgg.todo.submissionSubmitFile);

	});
}

/**	
 * Click handler for the complete/create submission buttons
 */
elgg.todo.completeClick = function(event) {
	if ($(this).hasClass('empty')) {
		var todo_guid = $('#todo-guid').val();
		// Create empty submission
		elgg.todo.createSubmission(todo_guid, '', '');
	} else {
		$("#todo-submission-dialog").dialog("open");
	}
	event.preventDefault();
}

/**
 * Submit handler for submission form
 */
elgg.todo.submissionFormSubmit = function(event) {
	/** May not be tinyMCE **/
	if (typeof(tinyMCE) !== 'undefined') {
		var comment = tinyMCE.get('submission-description').getContent();
		$("textarea#submission-description").val(comment);
	} else {
		var comment = $("textarea#submission-description").val();
	}
	
	var content = $("#submission-content-select").val();
	var todo_guid = $('#todo-guid').val();
		
	// If we have content (content is required)
	if (content) {
		// Create submission
		elgg.todo.createSubmission(todo_guid, content, comment);
	} else {
		// error
		$("#submission-error-message").show().html("** Content is required");
	}
	
	event.preventDefault();
}

elgg.todo.createSubmission = function(todo_guid, content, comment) {
	elgg.action('submission/save', {
		data: {
			submission_description: comment,
			todo_guid: todo_guid, 
			submission_content: content,
		},
		error: function(e) {
			// Display error (will probably look gross)
			$("#submission-error-message").show().html(e);
		},
		success: function(json) {
			// Check for bad status 
			if (json.status == -1) {
				$("#submission-error-message").show().html(json.output());
			} else {
				// Remove tinymce
				if (typeof(tinyMCE) !== 'undefined') {
		    		tinyMCE.execCommand('mceRemoveControl', false, 'submission-description');
				}
				
				// Close dialog
				$("#todo-submission-dialog").dialog("close");
				
				// Reload
				setTimeout ('window.location.reload()', 1000);
			}
		}
	});
}

/** 
 * Displays the submission content add menu in its default state
 */
elgg.todo.submissionFormDefault = function() {
	$("div.submission-content-pane").hide();
	$("div#submission-content-list").show();
	$("div#submission-content-menu").show();
	$("div#submission-control-back").hide();
}

/**
 * Submit link click handler
 */
elgg.todo.submissionSubmitLink = function(event) {
	var link = $('#submission-link').val();
	$('#submission-content-select').append(
		$('<option></option>').attr('selected', 'selected').val(link).html(link)
	);
	elgg.todo.submissionFormDefault();
	$('#submission-link').val('');
	event.preventDefault();
}

/** 
 * Submit handler for submission file form
 */ 
elgg.todo.submissionSubmitFile = function(event) {
	var options = { 
			url: 			elgg.security.addToken(elgg.todo.fileUploadURL), 
			type: 			"POST", 
	        target: 		'#submission-output',   // target element(s) to be updated with server response 
			clearForm: 		true,
	        beforeSubmit:  	function(formData, jqForm, options) { // pre-submit
							    $("#submission-ajax-spinner").show();
							}, 
	        success:       	function(response, statusText, xhr, $form) {
								$("#submission-ajax-spinner").hide();
								var file = eval( "(" + response + ")" );
								$('#submission-content-select').append(
									$('<option></option>').attr('selected', 'selected').val(file.guid).html(file.name)
								);
								elgg.todo.submissionFormDefault();	
							},
	    };
	
	$(this).ajaxSubmit(options); 
	
	event.preventDefault();
}

/**
 * Submission content menu item click handler
 */
elgg.todo.submissionContentMenuClick = function(event) {
	$("div.submission-content-pane").hide();
	$("div#submission-content-menu").hide();
	$("div#submission-control-back").show();
	
	// The id to show is supplied as the items href
	$($(this).attr('href')).show();
	
	event.preventDefault();
}

elgg.register_hook_handler('init', 'system', elgg.todo.init);
//</script>