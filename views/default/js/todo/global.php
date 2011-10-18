<?php
/**
 * Todo Global JS library
 * - Registers global todo related JS
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
elgg.provide('elgg.todo.global');

elgg.todo.global.init = function() {			
	$(".todo-show-info").live('hover', elgg.todo.global.showEntityInfo);
	$(".todo-show-info").live('click', function(event) {
		event.preventDefault();
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
	})
	
	event.preventDefault();
}

elgg.register_hook_handler('init', 'system', elgg.todo.global.init);
//</script>