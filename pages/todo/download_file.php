<?php
/**
 * Todo Submission File Download Page
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 */

// Get the guid
$file_guid = get_input("guid");

// Get the file
$file = get_entity($file_guid);
if (!elgg_instanceof($file, 'object', 'todosubmissionfile') && !elgg_instanceof($file, 'object', 'submissionannotationfile')) {
	register_error(elgg_echo("file:downloadfailed"));
	forward();
}

$mime = $file->getMimeType();
if (!$mime) {
	$mime = "application/octet-stream";
}

$filename = $file->originalfilename;

// fix for IE https issue
header("Pragma: public");

header("Content-type: $mime");
if (strpos($mime, "image/") !== false || $mime == "application/pdf") {
	header("Content-Disposition: inline; filename=\"$filename\"");
} else {
	header("Content-Disposition: attachment; filename=\"$filename\"");
}
header("Content-Length: {$file->getSize()}");

while (ob_get_level()) {
    ob_end_clean();
}
flush();
readfile($file->getFilenameOnFilestore());
exit;
