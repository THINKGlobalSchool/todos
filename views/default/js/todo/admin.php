<?php
/**
 * Todo Admin JS library
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
elgg.provide('elgg.todo.admin');

elgg.todo.admin.init = function() {			
	// Click handler for admin copy
	$(document).delegate('.todo-admin-move-button', 'click', elgg.todo.admin.moveClick);
}

// Click handler for admin import
elgg.todo.admin.moveClick = function(event) {	
	$('#todo-admin-output').html("<div class='elgg-ajax-loader'></div>");
	
	var todo_guid = $('input[name=todo_guid]').val();
	var group_guid = $('input[name=group_guid]').val();
	
	// Fire remote copy action
	elgg.action('todo/move', {
		data: {
			todo_guid: todo_guid,
			group_guid: group_guid,
		},
		success: function(data) {
			if (data.status != -1) {
				var content = '';
				if (data.output) {
					content = data.output;
				} else {
					content = elgg.echo('todo:error:nodata');
				}
			} else {
				content = data.system_messages.error;
			}
			$('#todo-admin-output').html(content);
		}
	});

	event.preventDefault();
}


elgg.register_hook_handler('init', 'system', elgg.todo.admin.init);