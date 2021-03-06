<?php
/**
 * Todo Download Submission Files
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get the guid
$todo_guid = get_input("guid");

// Get the todo
$todo = get_entity($todo_guid);

// Check for valid todo and check permissions
if (!$todo || !$todo->canEdit()) {
	register_error(elgg_echo("todo:error:invalid"));
	forward();
}

// Get submission batch
$submission_batch = get_todo_submissions_batch($todo_guid, 0);

$files = array();

// Find submission content
foreach ($submission_batch as $submission) {
	// Need to unserialize from metadata
	$contents = unserialize($submission->content);

	if ($contents) {
		// Check each content item
		foreach ($contents as $content) {
			// Try to grab entity
			$entity = get_entity($content);

			// Check if we have a downloadable entity (file, todosubmission file)
			if (elgg_instanceof($entity, 'object', 'file') || elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
				// Add to files array (guid => {'username' => username, 'filename' => file_location})
				$files[$entity->guid]['filename'] = $entity->getFilenameOnFilestore();
				$files[$entity->guid]['username'] = $entity->getOwnerEntity()->username;
			}
		}
	}
}

// Create a hash for the files array (to check for changes)
$zip_hash = md5(serialize($files));

// If we have files, proceed with zip
if (count($files) > 0) {
	$dataroot = elgg_get_config('dataroot');

	// File friendly todo title
	$todo_title = str_replace("-", "_", elgg_get_friendly_title($todo->title));

	// Try to create todo export directory
	$todo_export_dir = "{$dataroot}todo_export";
	if (!file_exists($todo_export_dir)) {
		mkdir($todo_export_dir);
	}

	// Set zip location/name
	$zip_location = "{$todo_export_dir}/{$todo_title}.zip";
	
	// Only create new zips if the file doesn't exist, or the hash has changed
	if (!file_exists($zip_location) || $zip_hash != $todo->zip_hash) {
		// Create a new zip
		$zip = new ZipArchive;

		// Try opening
		if ($zip->open($zip_location, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
			register_error(elgg_echo('todo:error:zipcreate'));
		}

		// Add files to zip
		foreach ($files as $guid => $file) {
			// Double-check that file exists
			if (file_exists($file['filename'])) {

				// Get file info
				$file_info = pathinfo($file['filename']);
				$file_extension = $file_info['extension'];

				// Set a friendlier file output name
				$file_out = "{$todo_title}_{$file['username']}_{$guid}.{$file_extension}";

				// Add to zip
				$zip->addFile($file['filename'], $file_out);

				// Check for errors
				if (!$zip->status == ZIPARCHIVE::ER_OK) {
					register_error(elgg_echo('todo:error:zipfileerror', array($file['filename'])));
				}
			}
		}

		// Close zip
		$zip->close();

		// Set hash
		$todo->zip_hash = $zip_hash;
	}

	$zip_base = basename($zip_location);

	todo_download_send_headers($zip_base);

	header("Content-Length: ".filesize($zip_location));

	ob_clean();
	flush();
	readfile($zip_location);
	exit;
} else {
	register_error(elgg_echo('todo:error:nofiles'));
	forward(REFERER);
}
