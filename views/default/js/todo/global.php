<?php
/**
 * Todo Global JS library
 * - Registers global todo related JS
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 * 
 */
?>
//<script>
elgg.provide('elgg.todo.global');

elgg.todo.global.init = function() {			
	$(document).on('hover', ".todo-show-info", elgg.todo.global.showEntityInfo);
	$(document).on('click', ".todo-show-info", function(event) {
		event.preventDefault();
	});
	
	// Ajaxify todo accept button
	$(document).on('click', ".todo-accept-ajax", elgg.todo.global.acceptTodo);
	
	// Hide multi-todo's when clicking outside box
	$(document).on('click', 'body', function(event) {
		if (!$(event.target).hasClass('todo-show-info') && event.target.className !== "todo-entity-info") {
			$(".todo-entity-info").fadeOut();
		}
	});
}

// Function to handle click events for the todo show info link
elgg.todo.global.showEntityInfo = function(event) {
	$id = $($(this).attr('href'));

	$id.fadeIn();

	$id.appendTo('body').position({
		my: "right top",
		at: "right top",
		of: $(this),
		offset: "0 25",
	});

	event.preventDefault();
}

// Accept a todo
elgg.todo.global.acceptTodo = function(event) {
	var todo_guid = $(this).attr('name');
	$_this = $(this);

	elgg.action('todo/accept', {
		data: {
			guid: todo_guid,
		},
		success: function(data) {
			if (data.status != -1) {
				// Create accepted list item
				var $accepted_li = $(document.createElement('li'));
				$accepted_li.html("<span class='accepted'>✓ Accepted</span>");
				
				// Replace the accept button
				$_this.fadeOut('fast', function() {
					if ($(this).closest('.elgg-menu-todo-actions').length) {
						// Place the accepted item in the entity menu (full view)
						$(this).closest('.todo').find('.elgg-menu-entity').prepend($accepted_li);
					} else {
						// Straight up replace (listing view)
						$(this).replaceWith($accepted_li).fadeIn('fast');
					}
				});
			}
		}
	});
	event.preventDefault();
}

/**
 * Chosen setup handler for todo dashboard inputs
 */
elgg.todo.global.setupMenuInputs = function (hook, type, params, options) {
	// Disable search for these inputs
	var disable_search_ids = new Array(
		'todo-context-filter',
		'todo-due-filter',
		'todo-status-filter',
		'todo-view-filter',
		'todo-submission-filter',
		'todo-submission-return-filter',
		'todo-submission-ontime-filter'
	);

	// Disable search for above inputs
	if ($.inArray(params.id, disable_search_ids) != -1) {
		options.disable_search = true;
	}

	// Allow deselect for these ids
	var allow_deselect_ids = new Array(
		'todo-submission-filter',
		'todo-group-filter',
		'todo-group-categories-filter',
		'todo-category-filter'
	);

	// Set deselect for dashboard inputs
	if ($.inArray(params.id, allow_deselect_ids) != -1) {
		options.width = "135px";
		options.allow_single_deselect = true;
	}

	if (params.id == 'todo-submission-filter') {
		options.width = "60px";
	}

	return options;
}

// Trim HTTP or HTTPS from a url string
elgg.todo.global.trimProtocol = function(str) {
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

if (typeof String.prototype.startsWith != 'function') {
  // see below for better implementation!
  String.prototype.startsWith = function (str){
    return this.indexOf(str) == 0;
  };
}

// Hook to customize some todo dashboard inputs
elgg.register_hook_handler('getOptions', 'chosen.js', elgg.todo.global.setupMenuInputs);
elgg.register_hook_handler('init', 'system', elgg.todo.global.init);