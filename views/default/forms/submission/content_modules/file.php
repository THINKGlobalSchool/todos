<?php
/**
 * Submission file module
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

$form_body .= elgg_view("input/file",array(
	'name' => 'upload', 
	'id' => 'upload'
)) . "<br /><br />";

$form_body .= elgg_view('input/submit', array(
	'id' => 'submission-submit-file', 
	'name' => 'file_submit', 
	'value' => elgg_echo('todo:label:upload'),
));

$file_form = elgg_view('input/form', array(
	'id' => 'submission-file-form',
	'body' => $form_body,
	'enctype' => 'multipart/form-data',
));

echo elgg_view_module('info', elgg_echo('todo:label:addfile'), $file_form, array(
	'id' => 'submission-add-file-container', 
	'class' => 'submission-content-pane',
));