<?php
/**
 * Todo remove assignee from todo relationship
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$current_user = elgg_get_logged_in_user_guid();

$assignee_guid = get_input('assignee_guid');
$assignee = get_entity($assignee_guid);

$todo_guid = get_input('todo_guid');
$todo = get_entity($todo_guid);

if (elgg_instanceof($todo, 'object', 'todo')) {
	// Check if user is trying to unassign themself from the todo
	if ($todo->canEdit() || (($current_user == $assignee_guid) && is_todo_assignee($todo_guid, $current_user))) {	
		$assignee->removeRelationship($todo_guid, TODO_ASSIGNEE_RELATIONSHIP);
		$assignee->removeRelationship($todo_guid, TODO_ASSIGNEE_ACCEPTED);
		
		if (elgg_trigger_event('unassign', 'object', array('todo' => $todo, 'user' => $assignee))) {
			system_message(elgg_echo('todo:success:assigneeremoved'));
			forward(REFERER);
		}
	}
}
register_error(elgg_echo('todo:error:assigneeremoved'));
forward(REFERER);