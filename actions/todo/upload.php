<?php
/**
 * Todo simple file upload
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// we have a file upload, so process it
if (count($_FILES) === 1 && !empty($_FILES['upload']['name']) && $_FILES['upload']['error'] == 0) {
	if (!($file = todo_upload_file($_FILES['upload'], 'todosubmissionfile', ACCESS_PRIVATE))) {
		echo json_encode(array(
			'status' => 0,
			'message' => elgg_echo('todo:error:fileupload'),
		));
		exit;
	}

	echo json_encode(array(
		'status' => 1,
		'guid' => $file->guid,
		'name' => $file->title,
		'url' => $file->getURL()
	));
	exit;

} else {
	echo json_encode(array(
		'status' => 0,
		'message' => elgg_echo('todo:error:nofile'),
	));
	exit;
}