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

elgg.todo.fileUploadURL = elgg.get_site_url() + 'action/todo/upload';

elgg.todo.loadAssigneesURL = elgg.get_site_url() + 'todo/loadassignees';

elgg.todo.init = function() {
	// Create submission click handler
	$('.todo-submit-empty').live('click', elgg.todo.completeClick);
				
	// Set up submission dialog
	$(".todo-lightbox").fancybox({
		//'modal': true,
		'onComplete': function() {
			// Set up submission content form
			elgg.todo.submissionFormDefault();
			
			if (typeof(tinyMCE) !== 'undefined') {
				tinyMCE.EditorManager.execCommand('mceAddControl', false, 'submission-description');
			}
		},
		'onCleanup': function() {
			if (typeof(tinyMCE) !== 'undefined') {
	    		tinyMCE.EditorManager.execCommand('mceRemoveControl', false, 'submission-description');
			}
		}
	});
	
	// TODO FORM SETUP
	// Submission form submit handler
	$("form#todo-submission-form").live('submit', elgg.todo.submissionFormSubmit);
	
	// Hack modules to add an 'add' button	
	$("#submission-add-content-container").delegate('.elgg-item', 'mouseenter mouseleave', elgg.todo.submissionAddHover);
	
	// SUBMISSION CONTENT FORM SETUP
	
	// Make menu items clickable
	$(".submission-content-menu-item").live('click', elgg.todo.submissionContentMenuClick);
	
	// Make submit link button clickable
	$("#submission-submit-link").live('click', elgg.todo.submissionSubmitLink);
	
	// Back button click action
	$("#submission-content-back-button").live('click', elgg.todo.submissionFormDefault);
	
	// Register submit handler for submission file form
	$("#submission-file-form").submit(elgg.todo.submissionSubmitFile);
	
	$('.submission-content-input-add').live('click', elgg.todo.submissionSubmitContent)
	
	// OTHER
	
	// Remove assignee click handler
	$(".todo-remove-assignee").live('click', elgg.todo.removeAssignee);

	// Assign onchange for the assignee type select input
	$('#todo-assignee-type-select').change(elgg.todo.assigneeTypeSelectChange);
	
	// What is this rollover for submission tags
	$('#todo-suggested-what').click(function(e){e.preventDefault();});
	$('#todo-suggested-what').hover(function() {
		var options = {
			my: 'center top',
			at: 'center bottom',
			of: $(this),
			collision: 'fit fit'
		}
		$('.todo-help-popup').toggle().position(options);
	});
	
	// Todo dashboard nav items
	$('.todo-ajax-list').live('click', function(e){
		$('#todo-dashboard').html("<div class='elgg-ajax-loader'></div>");
		$('#todo-dashboard').load($(this).attr('href'));
		$('.todo-ajax-list-item').removeClass('elgg-state-selected');
		$(this).closest('.todo-ajax-list-item').addClass('elgg-state-selected');
		e.preventDefault();
	});
	
	// Todo dashboard filter nav items
	$('.todo-ajax-list-complete').live('click', function(e){
		$('#todo-dashboard-content').html("<div class='elgg-ajax-loader'></div>");
		$('#todo-dashboard').load($(this).attr('href'));
		e.preventDefault();
	});
	
	// Todo dashboard sort nav items
	$('.todo-ajax-sort').live('click', function(e){
		$('#todo-dashboard-content').html("<div class='elgg-ajax-loader'></div>");
		$('#todo-dashboard').load($(this).attr('href'));
		e.preventDefault();
	});

	// Special pagination helper for todo content
	$('#todo-dashboard .elgg-pagination a').live('click', function(e) {
		$('#todo-dashboard-content').html("<div class='elgg-ajax-loader'></div>");
		$('#todo-dashboard').load($(this).attr('href'));
		e.preventDefault();
	});
}

/**	
 * Click handler for creating an empty submission
 */
elgg.todo.completeClick = function(event) {
	// Replace with spinner
	$(this).replaceWith("<div class='elgg-ajax-loader'></div>");

	var todo_guid = $('#todo-guid').val();

	// Create empty submission
	elgg.todo.createSubmission(todo_guid, '', '');

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
		$('#submit-create-submission').attr('disabled', 'disabled');
		// Create submission
		elgg.todo.createSubmission(todo_guid, content, comment);
	} else {
		// error
		$("#submission-error-message").show().html("** Content is required");
	}
	
	event.preventDefault();
}

elgg.todo.createSubmission = function(todo_guid, content, comment) {
	
	// Replace submit button with spinner
	$('#submit-create-submission').replaceWith("<div class='elgg-ajax-loader'></div>");
	
	
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
				$.fancybox.close();
				
				// Reload
				setTimeout('window.location.reload()', 1000);
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
	
	if (link) {
		
		// Check for valid link
		if (!elgg.todo.isValidURL(link)) {
			elgg.register_error(elgg.echo('todo:error:invalidurl'));
			return false;
		}

		// Get a protocol trimmed version of the link, and site url
		var trimmed_link = elgg.todo.trimProtocol(link);
		var trimmed_site = elgg.todo.trimProtocol(elgg.get_site_url());
	
		// Check is given url comes from this site
		if (trimmed_link.indexOf(trimmed_site) !== -1) {
			// This url did come from this site, parse out the first number we come across
			var regex = ".*?(\\d+)";
			var p = new RegExp(regex,["i"]);
			var m = p.exec(trimmed_link);
		
			// If we have a match, try to find the elgg object 
			if (m != null) {
				var guid = m[1];
							
				elgg.action('todo/checkcontent', {
					data: {
						guid: guid,
						show_error: 0,
					},
					success: function(data) {
						if (data.status == -1) {
							elgg.todo.confirmLocalLink(link);
						} else {
							$('#submission-content-select').append(
								$('<option></option>').attr('selected', 'selected').val(data.output.entity_guid).html(data.output.entity_title)
							);
							elgg.todo.submissionFormDefault();	
						}
					}
				});
			} else {
				elgg.todo.confirmLocalLink(link);
			} 
		} else {
			$('#submission-content-select').append(
				$('<option></option>').attr('selected', 'selected').val(link).html(link)
			);
			elgg.todo.submissionFormDefault();
			$('#submission-link').val('');
		}
	}
	event.preventDefault();
}

/**
 * Confirm that the user wants to submit a local link
 */
elgg.todo.confirmLocalLink = function(link) {
	// If the object doesn't check out, show a confirmation
	var response = confirm(elgg.echo('todo:label:linkspotcontent'));
	
	// If 'ok' is clicked, add the link anyway
	if (response) {
		$('#submission-content-select').append(
			$('<option></option>').attr('selected', 'selected').val(link).html(link)
		);
		elgg.todo.submissionFormDefault();
	} else {
		// Reset the form and click the contenet menu item
		elgg.todo.submissionFormDefault();
		$('#add-content').click();
	}	
	
	$('#submission-link').val('');
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
 * Submit handler for submission content form
 */ 
elgg.todo.submissionSubmitContent = function(event) {
	var id = $(this).closest('.elgg-item').attr('id');
	
	var guid = id.substring(id.lastIndexOf('-') + 1);
	
	elgg.action('todo/checkcontent', {
		data: {
			guid: guid
		},
		success: function(data) {
			if (data.status == -1) {
				$("#submission-error-message").show().html("** Invalid Content");
			} else {
				$('#submission-content-select').append(
					$('<option></option>').attr('selected', 'selected').val(data.output.entity_guid).html(data.output.entity_title)
				);
				elgg.todo.submissionFormDefault();	
				
				var $listitem = $('#elgg-object-' + guid);
				var $addmenu = $listitem.data('addmenu');
				
				// Get values from content select, we don't want to show the add button
				// for already added content
				var selected = $('#submission-content-select').val();
				
				// Check if we've already added this content
				if (selected && $.inArray(guid, selected) !== -1) {
					// Update menu data accordingly
					var added = "<span class='todo-content-added'>Added!</span>";
					$addmenu.find('input').replaceWith(added);
					$listitem.find('input').replaceWith(added);
				}
			}
		}
	});

	event.preventDefault();
}

// Handler to add an 'add' button to the modules content listing to allow
// adding spot content to a todo submission
elgg.todo.submissionAddHover = function(event) {
	// For some reason the height is only accurate at this point.. 
	var height = $(this).height();
	if (event.type == 'mouseenter') {
		var $addmenu = $(this).data('addmenu') || null;

		if (!$addmenu) {
			var $addmenu = $("<div class='add-menu'><input type='submit' value='Add'class='elgg-button elgg-button-action submission-content-input-add' /></div>");
			$(this).data('addmenu', $addmenu);
			$addmenu.appendTo($(this));
		}
		
		// Grab guid and check to make sure content is not already selected
		var id = $(this).closest('.elgg-item').attr('id');
		var guid = id.substring(id.lastIndexOf('-') + 1);
		var selected = $('#submission-content-select').val();
		
		if (selected && $.inArray(guid, selected) !== -1) {
			// Update menu data accordingly
			var added = "<span class='todo-content-added'>Added!</span>";
			$addmenu.find('input').replaceWith(added);
		}

		var margin = '-' + height + 'px';

		$addmenu
			.css("width", '90px')
			.css("height", height + 'px')
			.css("z-index", '100')
			.fadeIn('fast')
			.position({
				my: "right top",
				at: "right top",
				of: $(this)
			}).css("margin-bottom", margin);
	} else if (event.type == 'mouseleave') {
		var $addmenu = $(this).data('addmenu');
		$addmenu.fadeOut();
	}
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

// Load todo assignees into container
elgg.todo.loadAssignees = function(guid, container) {
	elgg.get(elgg.todo.loadAssigneesURL, {
		data: {
			guid: guid
		},
		success: function(data){
			$("#" + container).html(data);
		}
	});
}

/**
 * Remove assignee from todo action, uses the anchor's HREF for
 * the assignee guid
 */
elgg.todo.removeAssignee = function(event) {	
	var assignee_guid = $(this).attr('href');
	var todo_guid =  $(this).closest('.todo-assignees').attr('id');
	
	var assignee = $(this).closest('.todo-assignee-container');
		
	elgg.action('todo/unassign', {
		data: {
			assignee_guid: assignee_guid, 
			todo_guid: todo_guid,
		}, 
		success: function() {
			assignee.remove();
		}
	});
	
	event.preventDefault();
}

/**
 *  Onchange handler for the assignee type select input
 */
elgg.todo.assigneeTypeSelectChange = function(event) {
	if ($(this).val() == 0) {
		$('#todo-assign-individual-container').show();
		$('#todo-assign-group-container').hide();
		$("#todo-assignee-userpicker").removeAttr("disabled");
		$("#todo-group-assignee-select").attr("disabled","disabled");
	} else {
		$('#todo-assign-individual-container').hide();
		$('#todo-assign-group-container').show();
		$("#todo-assignee-userpicker").attr("disabled","disabled");
		$("#todo-group-assignee-select").removeAttr("disabled");
	}
	event.preventDefault();
}

// Trim HTTP or HTTPS from a url string
elgg.todo.trimProtocol = function(str) {
	if (str) {
		if (str.startsWith("http://"))
			return str.substr(7);
		else if (str.startsWith("https://"))
			return str.substr(8);
		else 
			return str;
	}
	return false;
}

elgg.todo.isValidURL = function(url) {
	
	if (url.length == 0) { 
		return false; 
	}
	
	// if user has not entered http:// https:// or ftp:// assume they mean http://
	if(!(/^(https?|ftp):\/\//i.test(url))) {
		url = 'http://' + url; // set both the value
	}
	
	// Here begins the most horrible regex ever
	// From: http://stackoverflow.com/questions/2723140/validating-url-with-jquery-without-the-validate-plugin
	if(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url)) {
		  return true;
		} else {
		  return false;
		}
}

elgg.register_hook_handler('init', 'system', elgg.todo.init);