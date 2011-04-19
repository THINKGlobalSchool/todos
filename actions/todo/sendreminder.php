<?php
/**
 * Todo Send reminder action
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

$todo_guid = get_input('todo_guid');
$assignee_guid = get_input('a');

$todo = get_entity($todo_guid);
	
$success = true;

if ($assignee_guid && !is_array($assignee_guid)) {
	$assignees = array($assignee_guid);
} else if (is_array($assignee_guid) && !empty($assignee_guid)) {
	$assignees = $assignee_guid;
} else {
	$success = false;
} 
	
foreach ($assignees as $guid) {	
	$assignee = get_entity($guid);
	if ($assignee && $todo && $todo->getSubtype() == "todo" && !has_user_submitted($guid, $todo->getGUID())) {
		$owner = get_entity($todo->container_guid);
		$success &= notify_user($guid, 
								$todo->container_guid, 
								elgg_echo('todo:email:subjectreminder'), 
								sprintf(elgg_echo('todo:email:bodyreminder'), $owner->name, $todo->title, $todo->getURL())
								);
	}
}
	
if ($success) {
	// Success message
	system_message(elgg_echo("todo:success:reminder"));
	forward(REFERER);
} else {
	register_error(elgg_echo("todo:error:reminder"));		
	forward(REFERER);
}
