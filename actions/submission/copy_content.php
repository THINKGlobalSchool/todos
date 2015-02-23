<?php
/**
 * Todo copy submission content to profile (files or bookmarks)
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get inputs
$type = get_input('type', FALSE);
$todo_guid = get_input('todo_guid');

// Get todo
$todo = get_entity($todo_guid);

// Check valid todo
if (!elgg_instanceof($todo, 'object', 'todo')) {
	register_error(elgg_echo('todo:error:invalidtodo'));
	forward(REFERER);
}

// If we're dealing with a todo submission file
if ($type == 'file') {
	// Get entity input
	$entity_guid = get_input('entity_guid');

	// Get entity
	$entity = get_entity($entity_guid);

	// Check valid entity (todosubmissionfile)
	if (!elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
		register_error(elgg_echo('todo:error:invalid'));
		forward(REFERER);
	}

	// New file entity
	$file = new FilePluginFile();
	$file->subtype = "file";
	$file->title = $entity->originalfilename;
	$file->access_id = ACCESS_LOGGED_IN;

	todo_set_content_tags($file, $todo); // Set suggested tags

	$prefix = "file/";

	$filestorename = elgg_strtolower(time() . $entity->originalfilename);

	// Set filename
	$file->setFilename($prefix . $filestorename);

	// Set mimetype from original entity
	$file->setMimeType($entity->getMimeType());

	// Set original filename
	$file->originalfilename = $entity->originalfilename;

	// Set simpletype
	$file->simpletype = file_get_simple_type($entity->getMimeType());

	// Open the file to guarantee the directory exists
	$file->open("write");
	$file->close();

	// Copy the old file to the new one
	copy($entity->getFilenameOnFilestore(), $file->getFilenameOnFilestore());

	// Try to save
	$guid = $file->save();

	// If we have an image, we need to create thumbnails (this should be moved into a function)
	if ($guid && $file->simpletype == "image") {
		$file->icontime = time();
		
		$thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 60, 60, true);
		if ($thumbnail) {
			$thumb = new ElggFile();
			$thumb->setMimeType($_FILES['upload']['type']);

			$thumb->setFilename($prefix."thumb".$filestorename);
			$thumb->open("write");
			$thumb->write($thumbnail);
			$thumb->close();

			$file->thumbnail = $prefix."thumb".$filestorename;
			unset($thumbnail);
		}

		$thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 153, 153, true);
		if ($thumbsmall) {
			$thumb->setFilename($prefix."smallthumb".$filestorename);
			$thumb->open("write");
			$thumb->write($thumbsmall);
			$thumb->close();
			$file->smallthumb = $prefix."smallthumb".$filestorename;
			unset($thumbsmall);
		}

		$thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 600, 600, false);
		if ($thumblarge) {
			$thumb->setFilename($prefix."largethumb".$filestorename);
			$thumb->open("write");
			$thumb->write($thumblarge);
			$thumb->close();
			$file->largethumb = $prefix."largethumb".$filestorename;
			unset($thumblarge);
		}
	}

	// Check guid
	if ($guid) {
		// Good to go, add to river
		elgg_create_river_item(array(
			'view' => 'river/object/file/create',
			'action_type' => 'create',
			'subject_guid' => elgg_get_logged_in_user_guid(),
			'object_guid' => $file->guid
		));


		// Forward to edit
		system_message(elgg_echo("file:saved"));
		forward('file/edit/' . $file->guid);
	} else {
		// Failed
		register_error(elgg_echo("file:uploadfailed"));
		forward(REFERER);
	}

} else if ($type == 'bookmark') { // We're dealing with a bookmark
	// Get url input
	$url = get_input('url', FALSE);

	if ($url && !preg_match("#^((ht|f)tps?:)?//#i", $url)) {
		$url = "http://$url";
	}

	// Validate (borrowed from bookmarks plugin)
	$php_5_2_13_and_below = version_compare(PHP_VERSION, '5.2.14', '<');
	$php_5_3_0_to_5_3_2 = version_compare(PHP_VERSION, '5.3.0', '>=') &&
			version_compare(PHP_VERSION, '5.3.3', '<');

	$validated = false;
	if ($php_5_2_13_and_below || $php_5_3_0_to_5_3_2) {
		$tmp_address = str_replace("-", "", $url);
		$validated = filter_var($tmp_address, FILTER_VALIDATE_URL);
	} else {
		$validated = filter_var($url, FILTER_VALIDATE_URL);
	}

	// Make sure we have a valid url
	if (!$url || empty($url) || !$validated) {
		register_error(elgg_echo('todo:error:invalidurl'));
		forward(REFERER);
	}

	// Good so far
	$bookmark = new ElggObject;
	$bookmark->subtype = "bookmarks";

	$bookmark->title = $url;
	$bookmark->address = $url;
	$bookmark->access_id = ACCESS_LOGGED_IN;
	todo_set_content_tags($bookmark, $todo); // Set suggested tags

	// Save bookmark
	if ($bookmark->save()) {
		// Good!
		system_message(elgg_echo('bookmarks:save:success'));

		// Add river
		elgg_create_river_item(array(
			'view' => 'river/object/bookmarks/create',
			'action_type' => 'create',
			'subject_guid' => elgg_get_logged_in_user_guid(),
			'object_guid' => $bookmark->guid
		));


		// Forward to edit
		forward(elgg_normalize_url('bookmarks/edit/' . $bookmark->getGUID()));
	} else {
		register_error(elgg_echo('bookmarks:save:failed'));
		forward(REFERER);
	}
} else { // Bad type..
	register_error(elgg_echo('todo:error:invalidtype'));
	forward(REFERER);
}