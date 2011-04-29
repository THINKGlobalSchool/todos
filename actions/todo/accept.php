<?php
/**
 * Todo Accept todo action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
	
$user = elgg_get_logged_in_user_entity();

$todo_guid = get_input('guid');
$todo = get_entity($todo_guid);
	
if ($user && elgg_instanceof($todo, 'object', 'todo') && user_accept_todo($user->getGUID(), $todo_guid)) {
	// Success message
	system_message(elgg_echo("todo:success:accepted"));
	forward(REFERER);
}

register_error(elgg_echo("todo:error:accepted"));		
forward(REFERER);
