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
	$("#multi-todo-toggle").live('click', elgg.todo.global.multiToggleClick);
	
	// Hide multi-todo's when clicking outside box
	$('body').live('click', function(event) {
		if (event.target.id !== "multi-todo-toggle" && event.target.className !== "multi-todo") {
			$("#multi-todos").fadeToggle();
		}
	});
}

// Function to handle click events for the multi todo toggler
elgg.todo.global.multiToggleClick = function(event) {
	$div = $(".elgg-menu-item-submitted-for-multiple-todos").find("#multi-todos");
		
	if ($div) {
		$div.appendTo('body')
			.position({
				my: "left top",
				at: "left bottom",
				of: $(this)
			});
	}
	
	$("#multi-todos").fadeToggle();
	
	event.preventDefault();
}

elgg.register_hook_handler('init', 'system', elgg.todo.global.init);
//</script>