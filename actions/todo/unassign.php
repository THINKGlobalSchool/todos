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

// Start engine as this action is triggered via ajax
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/engine/start.php');

// Logged in check
gatekeeper();

// must have security token 
action_gatekeeper();

$assignee_guid = get_input('assignee_guid');
$assignee = get_entity($assignee_guid);

$todo_guid = get_input('todo_guid');
$todo = get_entity($todo_guid);

if (elgg_instanceof($todo, 'object', 'todo')) {
	$success = $assignee->removeRelationship($todo_guid, TODO_ASSIGNEE_RELATIONSHIP);
	$success &= $assignee->removeRelationship($todo_guid, TODO_ASSIGNEE_ACCEPTED);
	$success &= elgg_trigger_event('unassign', 'object', array('todo' => $todo, 'user' => $assignee));
	
	if ($success) {
		system_message(elgg_echo('todo:success:assigneeremoved'));
		forward(REFERER);
	}
	
}
register_error(elgg_echo('todo:error:assigneeremoved'));
forward(REFERER);