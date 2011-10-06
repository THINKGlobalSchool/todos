<?php
/**
 * Todo check content action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$guid = get_input('guid');
$show_error = get_input('show_error', TRUE);

$entity = get_entity($guid);

// Check for valid entity (exists and user is the owner)
if (elgg_instanceof($entity, 'object') && $entity->getOwnerEntity() == elgg_get_logged_in_user_entity()) {
	// Get a title for the entity
	if ($entity->title) {
		$title = $entity->title;
	} else if ($entity->name) {
		$title = $entity->name;
	} else {
		$title = $entity->guid;
	}
	
	// Return entity details
	echo json_encode(array(
		'entity_title' => $title,
		'entity_guid' => $entity->guid
	));
	forward(REFERER);
} else {
	// Something is wrong, display error
	if ($show_error) {
		register_error(elgg_echo('todo:error:invalid'));
	} else {
		register_error();
	}
	forward(REFERER);
}