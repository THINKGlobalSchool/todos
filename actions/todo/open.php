<?php
/**
 * Todo manual open todo action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$todo_guid = get_input('guid');
$todo = get_entity($todo_guid);
	
if (elgg_instanceof($todo, 'object', 'todo')) {
	
	$todo->manual_complete = false;
	if ($todo->save()) {
		
		// Success message
		system_message(elgg_echo("todo:success:flagopen"));
		forward(REFERER);
	}
}

register_error(elgg_echo("todo:error:flagopen"));		
forward(REFERER);
