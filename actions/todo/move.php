<?php
/**
 * Todo move action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$todo_guid = get_input('todo_guid');
$group_guid = get_input('group_guid');

$todo = get_entity($todo_guid);
$group = get_entity($group_guid);

$valid = TRUE;

echo "<pre>";

if (!elgg_instanceof($todo, 'object', 'todo')) {
	echo "Invalid Todo!\r\n";
	$valid = FALSE;
}

if (!elgg_instanceof($group, 'group')) {
	echo "Invalid Group!\r\n";
	$valid = FALSE;
}

if ($valid) {
	$todo->container_guid = $group->guid;
	$todo->save();
	echo "Moved: $todo_guid\r\n";
	echo "To: $group_guid";
}

echo "</pre>";

forward(REFERER);