<?php
/**
 * Todo JS library
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
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

// Default todo dashboard params
elgg.todo.defaultDashboard = $.param({
	'context': 'assigned',
	'priority': 0,
	'status': 'incomplete',
	'sort_order': 'DESC'
});

/**
 * Main init hook
 */
elgg.todo.init = function() {
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
	$(document).delegate('#todo-edit', 'submit', elgg.todo.saveSubmit);
	
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

	// GOOGLE CALENDAR CONNECT
	$(document).delegate('#todo-calendar-connect-input', 'click', function(event) {
		this.select();
		event.preventDefault();
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
elgg.todo.saveSubmit = function(event) {
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
		$(this).html(elgg.echo('todo:label:sortdescarrow'));
		$(this).attr('href', '#' + 'DESC');
		elgg.modules.addOption($container, 'sort_order', 'ASC');
	} else {
		$(this).html(elgg.echo('todo:label:sortascarrow'));
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

/**
 * Standalone Calendar Init 
 */
elgg.todo.initStandaloneCalendar = function() {
	// calendars are stored in elgg.todo.calendars.
	elgg.todo.buildCalendar(elgg.todo.getCalendars());

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
				$(".todo-calendar-lightbox").colorbox({
					inline: true,
					overlayClose: false,
					closeButton: false,
					escKey: false,
					initialHeight: 70,
					initialWidth: 200,
					opacity: 0
				}).trigger('click');
			} else {
				// Using a timeout here, sometimes loading too quickly 
				// will prevent the lightbox from closing
				setTimeout(function() {
					$.colorbox.close();
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
 * Hook for todo form chosen inputs, will set these up manually.
 */
elgg.todo.chosenInterrupt = function(hook, type, params, options) {
	if (params.id == 'todo-group-assignee-select' || params.id == 'todo-rubric-guid') {
		// Do nothing
		return function(){};
	}
	return options;
}


// Require fileupload before regisitering hooks
require(['jquery.iframe-transport', 'jquery.fileupload', 'jquery.form'], function() {
	// Main hook
	elgg.register_hook_handler('init', 'system', elgg.todo.init);

	// Hook for todo form chosen inputs
	elgg.register_hook_handler('init', 'chosen.js', elgg.todo.chosenInterrupt);

	// Other
	elgg.register_hook_handler('category_toggled', 'todo_dashboard', elgg.todo.showCategoryLegend);
});