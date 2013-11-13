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

// Global vars
elgg.todo.fileUploadURL = elgg.get_site_url() + 'action/todo/upload';
elgg.todo.loadAssigneesURL = elgg.get_site_url() + 'todo/loadassignees';
elgg.todo.ajaxListURL = elgg.get_site_url() + 'ajax/view/todo/list';
elgg.todo.defaultDashboard = $.param({
	'context': 'assigned',
	'priority': 0,
	'status': 'incomplete',
	'sort_order': 'DESC'
});

/**
 * Main init
 */
elgg.todo.init = function() {
	// Create submission click handler
	$('.todo-submit-empty').live('click', elgg.todo.completeClick);
				
	// Set up submission dialog
	$(".todo-lightbox").fancybox({
		//'modal': true,
		'onStart' : function() {
			$('.tgstheme-entity-menu-actions').fadeOut();
		},
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

	// INIT DASHBOARD NAV
	elgg.todo.initDashboardNavigation();
	
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
	
	// Register click handler for submission content submit
	$('.submission-content-input-add').live('click', elgg.todo.submissionSubmitContent);

	// Change handler for todo grade required checkbox
	$(document).delegate('#todo-grade-required-input', 'change', function(){
		if ($(this).is(":checked")) {
			$('#todo-grade-total-container').show();
		} else {
			$('#todo-grade-total-container').hide();
		}
	});
	
	// Change handler for student submission required checkbox
	$(document).delegate('#todo_return_required', 'change', function(){
		$('#todo-suggested-tags-container').toggle();
	});

	// Verify todo submit form
	$(document).delegate('#todo-edit', 'submit', elgg.todo.todoSaveSubmit);
	
	// OTHER
	
	// Remove assignee click handler
	$(".todo-remove-assignee").live('click', elgg.todo.removeAssignee);

	// Assign onchange for the assignee type select input
	$('#todo-assignee-type-select').change(elgg.todo.assigneeTypeSelectChange);

	$('#todo-rubric-select').change(elgg.todo.rubricSelectChange);
	
	// 'What is this' rollover for submission tags
	$('#todo-suggested-what').click(function(e){e.preventDefault();});
	$('#todo-suggested-what').hover(function() {
		var options = {
			my: 'center top',
			at: 'center bottom',
			of: $(this),
			collision: 'fit fit'
		}
		$('#suggesedtags-info').toggle().position(options);
	});
	
	// 'What is this' rollover for todo start date
	$('#todo-startdate-what').click(function(e){e.preventDefault();});
	$('#todo-startdate-what').hover(function() {
		var options = {
			my: 'center top',
			at: 'center bottom',
			of: $(this),
			collision: 'fit fit'
		}
		$('#startdate-info').toggle().position(options);
	});

	// GROUP/USER SUBMISSIONS

	// Group member click handler
	$(document).delegate('a.todo-group-member', 'click', elgg.todo.groupMemberClick);
	
	// Sort order click handler
	$(document).delegate('.todo-user-submissions-sort', 'click', elgg.todo.userSubmissionsSortClick);
	
	// Return filter change handler
	$(document).delegate('.todo-user-submission-return-dropdown', 'change', elgg.todo.returnFilterChange);

	// On time filter change handler
	$(document).delegate('.todo-user-submission-ontime-dropdown', 'change', elgg.todo.ontimeFilterChange);

	// CALENDER EVENTS
	$(document).delegate('.todo-sidebar-calendar-toggler', 'click', elgg.todo.toggleCalendar);

	$(document).delegate('.todo-sidebar-todo-category-checkbox input[name="todo_category[]"]', 'click', elgg.todo.toggleCalendarTodoCategory);
}

/**	
 * Click handler for creating an empty submission
 */
elgg.todo.completeClick = function(event) {
	// Replace with spinner
	var $button = $(this).clone(); // Store original button
	$(this).replaceWith("<div id='submit-empty-loader' class='elgg-ajax-loader'></div>");

	var todo_guid = $('#todo-guid').val();

	// Create empty submission
	if (!elgg.todo.createSubmission(todo_guid, '', '')) {
		// Display button again (retry)
		$('#submit-empty-loader').replaceWith($button);
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
		$('#submit-create-submission').attr('disabled', 'disabled');
		// Create submission
		if (!elgg.todo.createSubmission(todo_guid, content, comment)) {
			// Re-enable button (try again)
			$('#submit-create-submission').removeAttr('disabled');
		}
	} else {
		// error
		$("#submission-error-message").show().html("** Content is required");
	}
	
	event.preventDefault();
}

/**
 * Create submission action
 */
elgg.todo.createSubmission = function(todo_guid, content, comment) {	
	// Replace submit button with spinner	
	var $button = $('#submit-create-submission').clone(); // Store original button
	$('#submit-create-submission').replaceWith("<div id='submit-create-loader' class='elgg-ajax-loader'></div>");

	elgg.action('submission/save', {
		data: {
			submission_description: comment,
			todo_guid: todo_guid, 
			submission_content: content,
		},
		error: function(e) {
			// Display error (will probably look gross)
			$("#submission-error-message").show().html(e);
			elgg.register_error(e);
			$('#submit-create-loader').replaceWith($button);
			return false;
		},
		success: function(json) {
			// Check for bad status 
			if (json.status == -1) {
				$("#submission-error-message").show().html(json.output);
				$('#submit-create-loader').replaceWith($button);
				$button.removeAttr('disabled');
				return false;
			} else {
				// Remove tinymce
				if (typeof(tinyMCE) !== 'undefined') {
		    		tinyMCE.execCommand('mceRemoveControl', false, 'submission-description');
				}
				
				// Close dialog
				$.fancybox.close();
				
				// Reload
				setTimeout('window.location.reload()', 1000);
				
				return true;
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

/**
 * Handler to add an 'add' button to the modules content listing to allow
 * adding spot content to a todo submission
 */
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

/**
 * Load todo assignees into container
 */
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
 * Validate the todo save form
 */
elgg.todo.todoSaveSubmit = function(event) {
	var valid = true;

	if ($('select[name=status]').val() == 1) {
		if (!$('input[name=due_date]').val()) {
			elgg.register_error(elgg.echo('todo:error:requireddate'));
			valid = false;
		}

		if (!$('input[name=title]').val()) {
			elgg.register_error(elgg.echo('todo:error:requiredtitle'));
			valid = false;	
		}

		if ($('input[name=grade_required]').is(':checked') && !$('input[name=grade_total]').val()) {
			elgg.register_error(elgg.echo('todo:error:requiredgradetotal'));
			valid = false;
		}

		if ($('select[name=category]').val() == 0) {
			elgg.register_error(elgg.echo('todo:error:requiredcategory'));
			valid = false;
		}
	}
	
	if (!valid) {
		event.preventDefault();
	}
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
	if ($(this).val() == 0) { // Individual
		$('#todo-assign-individual-container').show();
		$('#todo-assign-group-container').hide();
		$("#todo-assignee-userpicker").removeAttr("disabled");
		$("#todo-group-assignee-select").attr("disabled","disabled");
		$("#todo-current-group-select").attr("disabled","disabled");
	} else if ($(this).val() == 1) { // Groups (other groups)
		$('#todo-assign-individual-container').hide();
		$('#todo-assign-group-container').show(1, function() {
			
			var options = {
				'placeholder_text_multiple': elgg.echo('todo:label:selectgroupsmulti'),
				'width' : '50%'
			};

			$("#todo-group-assignee-select").chosen(options);	
		});
		
		$("#todo-assignee-userpicker").attr("disabled","disabled");
		$("#todo-group-assignee-select").removeAttr("disabled");
		$("#todo-current-group-select").attr("disabled","disabled");
	} else if ($(this).val() == 2) { // Current group
		$('#todo-assign-group-container').hide();
		$('#todo-assign-individual-container').hide();
		$("#todo-current-group-select").removeAttr("disabled");
		$("#todo-group-assignee-select").attr("disabled","disabled");
		$("#todo-assignee-userpicker").attr("disabled","disabled");
	}
	event.preventDefault();
}

/**
 * On change handler for rubric select
 */
elgg.todo.rubricSelectChange = function() {
	if ($(this).val() == 1) {
		$('#todo-rubric-guid').removeAttr('disabled');
		$('#todo-rubric-select-container').show(1, function() {
			
			var options = {
				'width' : '50%'
			};

			$("#todo-rubric-guid").chosen(options);	
		});
	} else {
		$('#todo-rubric-select-container').hide();
		$('#todo-rubric-guid').attr('disabled', 'DISABLED');
	}
}

/**
 *  Click handler for todo group member click
 */
elgg.todo.groupMemberClick = function(event) {
	// Main container
	var $container = $(this).closest('div.todo-group-user-submissions-container');

	// Submissions content container
	var $submissions_content = $container.find('.todo-user-submissions-content');

	// Show spinner
	$submissions_content.html("<div class='elgg-ajax-loader'></div>");

	// Load content
	$submissions_content.load($(this).attr('href'));
	
	// Show user name in submissions module
	var name = $(this).html();
	
	$('.todo-user-submissions-header').html(name + "'s " + elgg.echo('todo:label:submissions'));

	event.preventDefault();
}

/**
 * Click handler for user submissions sort click
 */
elgg.todo.userSubmissionsSortClick = function(event) {
	// Get order
	var order = $(this).attr('href').substr(1);

	// Get container
	var $container = $(this).closest('.todo-user-submissions-content').find('.genericmodule-container');

	// Add option and change label based on sort order
	if (order == 'ASC') {
		$(this).html(elgg.echo('todo:label:sortdesc'));
		$(this).attr('href', '#' + 'DESC');
		elgg.modules.addOption($container, 'sort_order', 'ASC');
	} else {
		$(this).html(elgg.echo('todo:label:sortasc'));
		$(this).attr('href', '#' + 'ASC');
		elgg.modules.addOption($container, 'sort_order', 'DESC');
	}

	// Re-init module
	elgg.modules.genericmodule.destroy();
	elgg.modules.genericmodule.init();
	
	event.preventDefault();
}

/**
 * Change handler for return filter change
 */
elgg.todo.returnFilterChange = function(event) {
	var value = $(this).val();
	// Get container
	var $container = $(this).closest('.todo-user-submissions-content').find('.genericmodule-container');

	// Add option to container based on value
	if (value == 'all') {
		elgg.modules.removeOption($container, 'filter_return');
	} else if (value == 0) {
		elgg.modules.addOption($container, 'filter_return', 0);
	} else if (value == 1) {
		elgg.modules.addOption($container, 'filter_return', 1);
	}
	
	// Re-init module
	elgg.modules.genericmodule.destroy();
	elgg.modules.genericmodule.init();

	event.preventDefault();
}

/**
 * Change handler for on time filter change
 */
elgg.todo.ontimeFilterChange = function(event) {
	var value = $(this).val();
	// Get container
	var $container = $(this).closest('.todo-user-submissions-content').find('.genericmodule-container');

	// Add option to container based on value
	if (value == 'all') {
		elgg.modules.removeOption($container, 'filter_ontime');
	} else if (value == 0) {
		elgg.modules.addOption($container, 'filter_ontime', 0);
	} else if (value == 1) {
		elgg.modules.addOption($container, 'filter_ontime', 1);
	}
	
	// Re-init module
	elgg.modules.genericmodule.destroy();
	elgg.modules.genericmodule.init();

	event.preventDefault();
}

/**
 * Init submissions filter daterangepicker
 */
elgg.todo.initDateRangePicker = function(selector) {
	var $input = $(selector);
	
	// Init daterange picker
	$input.daterangepicker({
		posX: "0",
		posY: "25",
		onOpen: function() {
			$('.ui-daterangepickercontain')
				.position({
					my: "right top",
					at: "right bottom",
					of: $('input.todo-user-submissions-date-input'),
					offset: "0 0",
				})
			.addClass('todo-user-submissions-datepicker');
		},
		onClose: function() {
			elgg.todo.dateRangePickerChange($input);
		},
	});
}

/** 
 * Handle daterangepicker change events (not an event)
 */
elgg.todo.dateRangePickerChange = function($input) {
	// Get the date values
	var value = $input.val();
	
	// Date value will always be like 3/1/2012 - 3/9/2012
	var dates = value.split(" - "); // Split on ' - '
	
	// Get module container
	var $container = $input.closest('.todo-user-submissions-content').find('.genericmodule-container');

	var upper, lower;

	// If only have one date
	if (dates.length == 1) {
		// Get lower date
		var lower_date = Date.parse(dates[0]);
		
		// Lower timestamp
		lower = lower_date.getTime() / 1000;

		// Add a day for upper date
		var upper_date = lower_date.add({hours:23, minutes:59, seconds:59});
		
		// Upper timestamp
		upper = upper_date.getTime() / 1000;

	} else if (dates.length == 2) { // Date range
		// Use datejs (included with daterangepicker) to get timestamps
		lower = Date.parse(dates[0]).getTime() / 1000;
		upper = Date.parse(dates[1]).getTime() / 1000;
	}
	
	elgg.modules.addOption($container, 'time_lower', lower);
	elgg.modules.addOption($container, 'time_upper', upper);

	// Re-init module
	elgg.modules.genericmodule.destroy();
	elgg.modules.genericmodule.init();
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

/**
 * Determine if given string is a valid url
 */
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
	if(/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url)) {
		  return true;
		} else {
		  return false;
		}
}

elgg.todo.initStandaloneCalendar = function() {

	elgg.todo.initCalendar();

	var $category_container = $('#todo-calendar-categories');
	$category_container.load(elgg.get_site_url() + 'ajax/view/todo/category_calendar_filters', function() {
		// init date picker
		$('#todo-calendar-date-picker').datepicker({
			dateFormat: 'yy-mm-dd',
			onSelect: function(dateText,dp){
				$('#todo-category-calendar').fullCalendar('gotoDate', new Date(Date.parse(dateText)));
			}
		});

		// Load selected calendar
		$selected_category = $(this).find('input[name=category_calendar_radio]:checked');
		$selected_category.trigger('click');
	});
}

/**
 * Manually init calendars
 */
elgg.todo.initCalendar = function() {
	// calendars are stored in elgg.todo.calendars.
	elgg.todo.buildCalendar(elgg.todo.getCalendars());
}

/**
 * Returns the calendars
 *
 * @return {Object}
 */
elgg.todo.getCalendars = function() {
	return elgg.todo.calendars;
}

/**
 * Builds the calendar from a JSON object
 */
elgg.todo.buildCalendar = function(calendars, date, view) {
	// Check for supplied view	
	if (!view) {
		//view = 'basicWeek'; // Default to week view
		view = 'month';
	} else {
		view = view.name;
	}
	
	var url = elgg.get_site_url() + 'ajax/view/todo/calendar_feed';
	$('#todo-category-calendar').fullCalendar({
		weekMode: 'liquid',
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,basicWeek'
		},
		//theme: true,
		eventSources: elgg.todo.buildSources(calendars),
		defaultView: view,
		eventClick: function(event) {
			if (event.url) {
				window.open(event.url);
				return false;
			}
		},
		eventRender: function(event, element) {
			elgg.todo.makeCalendarTip(event, element);
			element.find('span.fc-event-title').html(element.find('span.fc-event-title').text());
		},
		loading: function(isLoading, view) {
			if (isLoading) {
				$(".todo-calendar-lightbox").fancybox({
					overlayShow: true,
					hideOnOverlayClick: false,
					hideOnContentClick: false,
					enableEscapeButton: false,
					showCloseButton: false,
				}).trigger('click');
			} else {
				// Using a timeout here, sometimes loading too quickly 
				// will prevent the lightbox from closing
				setTimeout(function() {
					$.fancybox.close();
				}, 300)			
			}
		}
	});	
	
	// Check for supplied date (need to this outside of loading event for it to be smooth)
	if (date) {
		// Set calendar to date
		$('#todo-category-calendar').fullCalendar('gotoDate', 
			date.getFullYear(), 
			date.getMonth(), 
			date.getDate()
		);
	}
}

/**
 * Build array of Full Calendar sources with unique class names
 *
 * @param {Object} The calendars object
 * @return {Array}
 */
elgg.todo.buildSources = function(calendars) {
	var sources = [];
	var i = 0;
	$.each(calendars, function(k, v) {
		if (v.display) {
			sources[i] = {
				'url' : v.url, 
				'type': 'GET',
				//'className': 'elgg-todocalendar-feed-' + k
			};
			i++;
		}
	});
	return sources;
}

/*
 * Toggle calendar requested and rebuild display
 */
elgg.todo.toggleCalendar = function() {
	var guid = $(this).attr('id').split('-')[3];
	var calendars = elgg.todo.getCalendars();

	$.each(calendars, function(k, v) {
		calendars[k]['display'] = false;
	});

	calendars[guid]['display'] = $(this).is(':checked');

	var current_date = $('#todo-category-calendar').fullCalendar('getDate');
	var current_view = $('#todo-category-calendar').fullCalendar('getView');
	
	$('#todo-category-calendar').fullCalendar('destroy');

	elgg.todo.buildCalendar(calendars, current_date, current_view);
	
	// Trigger a hook for any post toggle tasks
	elgg.trigger_hook('category_toggled', 'todo_dashboard', {'guid' : guid}, null);
}

/*
 * Toggle calendar todo category and rebuild display
 */
elgg.todo.toggleCalendarTodoCategory = function(event) {
	var category = $(this).val();

	var calendars = elgg.todo.getCalendars();

	var base_url = elgg.get_site_url() + 'ajax/view/todo/calendar_feed';

	var url_enabled = '';

	var $checked_categories = $('input[name="todo_category[]"]:checked');

	if ($checked_categories.length < 1) {
		event.preventDefault();
		return false;
	}

	$checked_categories.each(function() {
		url_enabled += "&" + $(this).val() + '=1';
	});

	$.each(calendars, function(k, v) {
	 	calendars[k]['url'] = base_url + "?category=" + k + url_enabled;
	});

	var current_date = $('#todo-category-calendar').fullCalendar('getDate');
	var current_view = $('#todo-category-calendar').fullCalendar('getView');
	
	$('#todo-category-calendar').fullCalendar('destroy');

	elgg.todo.buildCalendar(calendars, current_date, current_view);
}

/**
 * Calendar menu item loaded hook handler
 */
elgg.todo.calendarTabLoaded = function(hook, type, params, options) {
	if (params['tab'].hasClass('todo-calendars-item')) {
		elgg.todo.initCalendar();
		
		if (params['tab'].hasClass('todo-calendars-item')) {
			var $category_container = $('#todo-calendar-categories');
			$category_container.load(elgg.get_site_url() + 'ajax/view/todo/category_calendar_filters', function() {
				// init date picker
				$('#todo-calendar-date-picker').datepicker({
					dateFormat: 'yy-mm-dd',
					onSelect: function(dateText,dp){
						$('#todo-category-calendar').fullCalendar('gotoDate', new Date(Date.parse(dateText)));
					}
				});

				// Load selected calendar
				$selected_category = $(this).find('input[name=category_calendar_radio]:checked');
				$selected_category.trigger('click');
			});
		} else {
			// Remove sidebar
			$('#todo-calendar-filters-content').remove();
		}
	}
}

/**
 * Make tooltip from event/element
 */
elgg.todo.makeCalendarTip = function(event, element) {
	element.qtip({
		content: event.description,
		position: {
			corner: {
				target: 'topLeft',
				tooltip: 'bottomLeft'
			}
		},
		style: { 
			'font-size' : '11px',
			tip: 'bottomMiddle',
			width: 'auto',
			name: 'dark',
			'default': false,
		},
		show: {
			delay: 0,
		}
	});
}

/**
 * Show the group category legend when a category is toggled
 */
elgg.todo.showCategoryLegend = function(hook, type, params, options) {
	var guid = params['guid'];
	var $legend_container = $('#todo-category-calendar-legend');
	$legend_container.load(elgg.get_site_url() + 'ajax/view/todo/category_calendar_group_legend?category_guid=' + guid, function() {
		//
	});
}

/**
 * Chosen init handler
 */
elgg.todo.chosenInterrupt = function(hook, type, params, options) {
	if (params.id == 'todo-group-assignee-select' || params.id == 'todo-rubric-guid') {
		// Do nothing
		return function(){};
	}
	return options;
}

/**
 * Chosen handler for dashboard inputs
 */
elgg.todo.setupMenuInputs = function (hook, type, params, options) {
	if (params.id == 'todo-context-filter' 
		|| params.id == 'todo-due-filter' 
		|| params.id == 'todo-status-filter') {
		options.disable_search = true;
	}

	if (params.id == 'todo-group-filter') {
		options.width = "135px";
	}

	return options;
}

/**
 * Init dashboard filter/nav
 */
elgg.todo.initDashboardNavigation = function() {
	// Handle advanced click
	$('.todo-dashboard-show-advanced').live('click', function(event) {
		$(this).toggleClass('advanced-off').toggleClass('advanced-on');

		// Get nav container
		$(this).closest('#todo-dashboard-menu-container').find('.todo-dashboard-menu-advanced').toggle();

		event.preventDefault();
	});

	// Handle sort order clicks
	$('.todo-dashboard-sort').live('click', function(event) {
		$(this).toggleClass('descending').toggleClass('ascending');

		if ($(this).hasClass('descending')) {
			$(this).html(elgg.echo('todo:label:sortdesc'));
			$(this).val('ASC');
		} else if ($(this).hasClass('ascending')) {
			$(this).html(elgg.echo('todo:label:sortasc'));
			$(this).val('DESC');
		}

		// Use the list handler
		elgg.todo.listHandler($(this), true);

		event.preventDefault();
	});

	// Init pagination
	$('#todo-dashboard-content .elgg-pagination a').live('click', function(event) {
		// Get link params
		var link_params = deParam($(this).attr('href').slice($(this).attr('href').indexOf('?') + 1));
		
		// Set data attribute and value of offset
		$(this).attr('data-param', 'offset');
		$(this).val(link_params['offset']);

		// Use the trusty list handler with this element
		elgg.todo.listHandler($(this), true);

		event.preventDefault();
	});

	// If the content container is empty (first load, populate it from params)
	if ($('#todo-dashboard-content-container').is(':empty')) {
		elgg.todo.listHandler(null, false);
	}

	// Add popstate event listener
	window.addEventListener("popstate", function(e) {
	    elgg.todo.listHandler(null, false)
	});
}

/**
 * Hook handler for chosen.js change event
 */
elgg.todo.handleDashboardChange = function(hook, type, params, handler) {
	// Check if we're dealing with a todo dashboard filter
	if (params.element.hasClass('todo-dashboard-filter')) {
		return function() {
			// Use the todo list handler
			elgg.todo.listHandler(params.element, true);
		}
	} else {
		return handler;
	}
}

/**
 * Todo list handler, responsible for populating the dashboard with content and pushing state
 *
 * @param  obj  $element   Element that triggered the event
 * @param  bool pushState  Wether or not to push a new state
 * @return void
 */
elgg.todo.listHandler = function ($element, pushState) {
	$('#todo-dashboard-content-container').html("<div class='elgg-ajax-loader'></div>");

	var query_index = window.location.href.indexOf('?');

	// Check for params
	if (query_index != -1) {
		var params = deParam(window.location.href.slice(query_index + 1));
		var base_url = window.location.href.slice(0, query_index);
	} else {
		// Use defaults
		var params = deParam(elgg.todo.defaultDashboard);
		var base_url = window.location.href;
	}

	// If we were passed an element, update it's data param
	if ($element) {
		var param = $element.data('param');
		params[param] = $element.val();
	}

	// Push that state!
	if (pushState) {
		history.pushState({}, elgg.echo('todo:title:dashboard'), base_url + "?" + $.param(params));
	}

	// Make sure the appropriate inputs are selected on state change
	$.each(params, function(idx, val) {
		// Get select for each available parameter
		var $select = $("#todo-dashboard-menu-container").find("[data-param='" + idx + "']");
		
		// Set new value
		$select.val(val);

		// Update chosen
		$select.trigger('chosen:updated');
	});

	// Include any hidden inputs (ie page owner)
	$('.todo-dashboard-hidden-filter').each(function(idx) {
		params[$(this).attr('name')] = $(this).val();
	});

	// Load data
	elgg.get(elgg.todo.ajaxListURL, {
		data: params,
		success: function(data) {
			// Load data
			$("#todo-dashboard-content-container").html(data);
		},
		error: function() {
			// Show error on failure
			$("#todo-dashboard-content-container").html(elgg.echo('todo:error:content'));
		}
	});
}

elgg.register_hook_handler('change', 'chosen.js', elgg.todo.handleDashboardChange);
elgg.register_hook_handler('category_toggled', 'todo_dashboard', elgg.todo.showCategoryLegend);
//elgg.register_hook_handler('tab_loaded', 'todo_dashboard', elgg.todo.calendarTabLoaded);
elgg.register_hook_handler('init', 'system', elgg.todo.init);
elgg.register_hook_handler('init', 'chosen.js', elgg.todo.chosenInterrupt);
elgg.register_hook_handler('getOptions', 'chosen.js', elgg.todo.setupMenuInputs);	

/** OTHER UTILITY FUNCTIONS **/

// Fix for goofy chrome 'empty' states, from: http://stackoverflow.com/a/18126524/1202510   
(function() {
    // There's nothing to do for older browsers ;)
    if (!window.addEventListener)
        return;
    var blockPopstateEvent = true;
    window.addEventListener("load", function() {
        // The timeout ensures that popstate-events will be unblocked right
        // after the load event occured, but not in the same event-loop cycle.
        setTimeout(function(){ blockPopstateEvent = false; }, 0);
    }, false);
    window.addEventListener("popstate", function(evt) {
        if (blockPopstateEvent && document.readyState=="complete") {
            evt.preventDefault();
            evt.stopImmediatePropagation();
        }
    }, false);
})();

// Check if string starts with str
String.prototype.startsWith = function(str){
    return (this.indexOf(str) === 0);
}

// Deparam function (from: https://gist.github.com/dancrew32/1163405)
function deParam (params, coerce) {
	var obj = {};
	var coerce_types = { 'true': !0, 'false': !1, 'null': null };
	var decode = decodeURIComponent;

	// Iterate over all name=value pairs.
	$.each(params.replace(/\+/g, ' ').split('&'), function(j, v){
			var param = v.split( '=' ),
			key = decode(param[0]),
			val,
			cur = obj,
			i = 0,

			// If key is more complex than 'foo', like 'a[]' or 'a[b][c]', split it
			// into its component parts.
			keys = key.split(']['),
			keys_last = keys.length - 1;

			// If the first keys part contains [ and the last ends with ], then []
			// are correctly balanced.
			if (/\[/.test(keys[0]) && /\]$/.test(keys[keys_last])) {
			// Remove the trailing ] from the last keys part.
			keys[keys_last] = keys[keys_last].replace(/\]$/, '');

			// Split first keys part into two parts on the [ and add them back onto
			// the beginning of the keys array.
			keys = keys.shift().split('[').concat(keys);

			keys_last = keys.length - 1;
			} else {
				// Basic 'foo' style key.
				keys_last = 0;
			}

			// Are we dealing with a name=value pair, or just a name?
			if (param.length === 2) {
				val = decode(param[1]);

				// Coerce values.
				if (coerce) {
					val = val && !isNaN(val)            ? +val              // number
						: val === 'undefined'             ? undefined         // undefined
						: coerce_types[val] !== undefined ? coerce_types[val] // true, false, null
						: val;                                                // string
				}

				if (keys_last) {
					// Complex key, build deep object structure based on a few rules:
					// * The 'cur' pointer starts at the object top-level.
					// * [] = array push (n is set to array length), [n] = array if n is 
					//   numeric, otherwise object.
					// * If at the last keys part, set the value.
					// * For each keys part, if the current level is undefined create an
					//   object or array based on the type of the next keys part.
					// * Move the 'cur' pointer to the next level.
					// * Rinse & repeat.
					for ( ; i <= keys_last; i++) {
						key = keys[i] === '' ? cur.length : keys[i];
						cur = cur[key] = i < keys_last
							? cur[key] || (keys[i+1] && isNaN( keys[i+1] ) ? {} : [])
							: val;
					}

				} else {
					// Simple key, even simpler rules, since only scalars and shallow
					// arrays are allowed.

					if ($.isArray(obj[key])) {
						// val is already an array, so push on the next value.
						obj[key].push(val);

					} else if (obj[key] !== undefined) {
						// val isn't an array, but since a second value has been specified,
						// convert val into an array.
						obj[key] = [obj[key], val];

					} else {
						// val is a scalar.
						obj[key] = val;
					}
				}

			} else if (key) {
				// No value was defined, so set something meaningful.
				obj[key] = coerce
					? undefined
					: '';
			}
	});

	return obj;
}