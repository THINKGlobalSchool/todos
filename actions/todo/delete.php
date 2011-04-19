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
		system_message(elgg_echo('todo:success:delete'));
		if (elgg_instanceof($container, 'group')) {
			forward("todo/group/$container->guid/owner");
		} else {
			forward("todo/owner/$container->username");
		}
	} else {
		register_error(elgg_echo('todo:error:delete'));
	}
} else {
	register_error(elgg_echo('todo:error:invalid'));
}

forward(REFERER);
