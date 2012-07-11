<?php
/**
 * Submission Grade Action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$submission_guid = (int) get_input('submission_guid');
$grade = get_input('submission_grade', NULL);

$submission = get_entity($submission_guid);

if (!elgg_instanceof($submission, 'object', 'todosubmission')) {
	register_error(elgg_echo('todo:error:invalid'));
	forward(REFERER);
}

if (is_int($grade)) {
	register_error(elgg_echo('todo:error:invalidgrade'));
	forward(REFERER);
}

if ($submission->canEdit()) {
	$submission->grade = $grade;
	echo $submission->getOwnerGUID();
	system_message(elgg_echo('todo:success:grade'));
} else {
	register_error('todo:error:access');
}
forward(REFERER);