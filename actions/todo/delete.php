<?php
/**
 * Todo Delete Action (actually just disables the todo)
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$guid = get_input('guid');
$todo = get_entity($guid);

if (elgg_instanceof($todo, 'object', 'todo') && $todo->canEdit()) {
	$container = get_entity($todo->container_guid);
	if ($todo->disable()) {
		// Remove from river
		elgg_delete_river(array(
			'object_guid' => $guid,
			'action_type' => 'create',
		));
		system_message(elgg_echo('todo:success:delete'));
	} else {
		register_error(elgg_echo('todo:error:delete'));
	}
} else {
	register_error(elgg_echo('todo:error:invalid'));
}

forward('todo');
