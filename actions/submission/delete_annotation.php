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
	// Get the value array
	$annotation_value = unserialize($comment->value);
	
	// Make sure it is an actual array
	if (is_array($annotation_value)) {
		// Get entity
		$entity = get_entity($annotation_value['attachment_guid']);
		
		// Make sure entity is a submission annotation attachment
		if (elgg_instanceof($entity, 'object', 'submissionannotationfile')) {

			// If we have an image, process the thumbnails
			if ($entity->simpletype == "image") {
				// Grab thumbnails
				$thumbnail = $entity->thumbnail;
				$smallthumb = $entity->smallthumb;
				$largethumb = $entity->largethumb;

				if ($thumbnail) { //delete standard thumbnail image
					$delfile = new ElggFile();
					$delfile->owner_guid = $entity->getOwnerGUID();
					$delfile->setFilename($thumbnail);
					$delfile->delete();
				}
				if ($smallthumb) { //delete small thumbnail image
					$delfile = new ElggFile();
					$delfile->owner_guid = $entity->getOwnerGUID();
					$delfile->setFilename($smallthumb);
					$delfile->delete();
				}
				if ($largethumb) { //delete large thumbnail image
					$delfile = new ElggFile();
					$delfile->owner_guid = $entity->getOwnerGUID();
					$delfile->setFilename($largethumb);
					$delfile->delete();
				}
			}

			// Delete entity
			if (!$entity->delete()) {
				register_error('todo:error:deletefile');
				forward(REFERER);
			}
		}
	}

	$entity = get_entity($comment->entity_guid);

	if ($comment->canEdit()) {
		$comment->delete();
		system_message(elgg_echo("submission_annotation:deleted"));
		forward($entity->getURL());
	}

} else {
	$url = "";
}

register_error(elgg_echo("submission_annotation:notdeleted"));
forward(REFERER);