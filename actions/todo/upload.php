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

// Manual action check
action_gatekeeper();
gatekeeper();

// must have a file if a new file upload
if (empty($_FILES['upload']['name'])) {
	echo json_encode(array(
		'status' => 0,
		'message' => elgg_echo('todo:error:nofile'),
	));
	return;
}

$file = new ElggFile();

$title = $_FILES['upload']['name'];

$file->title = $title;
$file->access_id = ACCESS_PRIVATE; // Set file access to private for now

// we have a file upload, so process it
if (isset($_FILES['upload']['name']) && !empty($_FILES['upload']['name'])) {
	
	$prefix = "file/";
	
	$filestorename = elgg_strtolower(time().$_FILES['upload']['name']);
	
	$file->setFilename($prefix.$filestorename);
	$file->setMimeType($_FILES['upload']['type']);
	$file->originalfilename = $_FILES['upload']['name'];
	$file->simpletype = "submission";
	$file->subtype = "todosubmissionfile";

	$file->open("write");
	$file->write(get_uploaded_file('upload'));
	$file->close();
	
	$guid = $file->save();
} 

// handle results differently for new files and file updates
if ($guid) {	
	echo json_encode(array(
		'status' => 1,
		'guid' => $file->getGUID(),
		'name' => $file->title,
		'url' => $file->getURL()
	));
	exit;
	return;
} else {
	echo json_encode(array(
		'status' => 0,
		'message' => elgg_echo('todo:error:fileupload'),
	));
	return;
}
