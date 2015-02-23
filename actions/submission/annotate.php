<?php
/**
 * Submission Annotate Action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$entity_guid = (int) get_input('entity_guid');
$comment_text = get_input('comment_text');

if (empty($comment_text)) {
	register_error(elgg_echo("submission_annotation:blank"));
	forward(REFERER);
}

// Let's see if we can get an entity with the specified GUID
$entity = get_entity($entity_guid);

if (!$entity) {
	register_error(elgg_echo("submission_annotation:notfound"));
	forward(REFERER);
}

$user = elgg_get_logged_in_user_entity();

// Store annotation content in an array (will be serialized)
$content = array(
	'comment' => $comment_text,
);

// Check for exactly one file
if (count($_FILES) === 1) {
	// Check for errors
	if (empty($_FILES['upload']['name']) && $_FILES['upload']['error'] != 0) {
		register_error(elgg_echo('todo:error:uploadfailed'));
		forward(REFERER);
	}
	
	if (!($file = todo_upload_file($_FILES['upload'], 'submissionannotationfile', $entity->access_id))) {
		register_error(elgg_echo('todo:error:fileupload'));
		forward(REFERER);
	}

	// Set attachment guid to uploaded file
	$content['attachment_guid'] = $file->guid;

	// Add relationship to submission for access controls
	add_entity_relationship($file->guid, SUBMISSION_ANNOTATION_FILE_RELATIONSHIP, $entity_guid);
}

elgg_push_context('create_submission_annotation');
$annotation = create_annotation($entity->guid,
								'submission_annotation',
								serialize($content),
								"",
								$user->guid,
								$entity->access_id);
elgg_pop_context();

// tell user annotation posted
if (!$annotation) {
	register_error(elgg_echo("submission_annotation:failure"));
	
	forward(REFERER);
}

// notify if poster wasn't owner
if ($entity->owner_guid != $user->guid) {

	notify_user($entity->owner_guid,
				$user->guid,
				elgg_echo('submission_annotation:email:subject'),
				elgg_echo('submission_annotation:email:body', array(
					$entity->title,
					$user->name,
					$content['comment'],
					$entity->getURL(),
					$user->name,
					$user->getURL()
				))
			);
}

system_message(elgg_echo("submission_annotation:posted"));

//add to river
elgg_create_river_item(array(
	'view' => 'river/annotation/submission_annotation/create',
	'action_type' => 'comment',
	'subject_guid' => $user->guid,
	'object_guid' => $entity_guid,
	'access_id' => $entity->access_id,
	'annotation_id' => $annotation,
));

if (elgg_is_xhr()) {
	$annotation_view = elgg_view_annotation(elgg_get_annotation_from_id($annotation));
	$annotation_content = <<<HTML
		<li id="item-annotation-$annotation" class='elgg-item'>
		 	$annotation_view
		</li>
HTML;

	echo json_encode(array("annotation_content" => $annotation_content));
}

// Forward to the page the action occurred on
forward(REFERER);
