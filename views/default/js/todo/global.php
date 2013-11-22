<?php
/**
 * Todo Global JS library
 * - Registers global todo related JS
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>
//<script>
elgg.provide('elgg.todo.global');

elgg.todo.global.init = function() {			
	$(".todo-show-info").live('hover', elgg.todo.global.showEntityInfo);
	$(".todo-show-info").live('click', function(event) {
		event.preventDefault();
	});
	
	// Ajaxify todo accept button
	$(".todo-accept-ajax").live('click', elgg.todo.global.acceptTodo);
	
	// Todo hover menu item
	$(".todo-topbar-item").mouseenter(function(event) {
		$('#todo-topbar-hover').appendTo($(this)).position({
			my: "left top",
			at: "left bottom",
			of: $(this),
			offset: "0 -8",
		})
	});

	// Hide multi-todo's when clicking outside box
	$('body').live('click', function(event) {
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
				// Find entity anchor
				var $entity_anchor = $(document).find('#entity-anchor-' + todo_guid);
				var $elgg_body = $entity_anchor.closest('.elgg-body');
				
				// Create accepted list item
				var $accepted_li = $(document.createElement('li'));
				$accepted_li.html("<span class='accepted'>✓ Accepted</span>");
				
				// Add accepted list item to the info menu
				$elgg_body.find('.elgg-menu-entity-info > li.elgg-menu-item-access').before($accepted_li).fadeIn();
				
				// Remove the accept button
				$_this.closest('.elgg-menu-item-todo-accept').fadeOut();
			}
		}
	});
	event.preventDefault();
}

elgg.register_hook_handler('init', 'system', elgg.todo.global.init);