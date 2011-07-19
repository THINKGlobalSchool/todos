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

$entity = get_entity($guid);

// Check for valid entity
if (elgg_instanceof($entity, 'object')) {
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
	register_error(elgg_echo('todo:error:invalid'));
	forward(REFERER);
}