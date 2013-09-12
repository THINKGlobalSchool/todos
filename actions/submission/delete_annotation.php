<?php
/**
 * Submission delete annotation
 *
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * @package Elgg
 */

// Ensure we're logged in
if (!elgg_is_logged_in()) {
	forward();
}

// Make sure we can get the comment in question
$annotation_id = (int) get_input('annotation_id');

if ($comment = elgg_get_annotation_from_id($annotation_id)) {
	$entity = get_entity($comment->entity_guid);
	if (elgg_is_admin_logged_in() || $comment->getOwnerGUID() == elgg_get_logged_in_user_guid()) {
		if (!$comment->delete()) {
			register_error(elgg_echo("submission_annotation:notdeleted"));
			forward(REFERER);
		} else {
			system_message(elgg_echo("submission_annotation:deleted"));
			forward($entity->getURL());
		}
		
	}

} else {
	$url = "";
}

