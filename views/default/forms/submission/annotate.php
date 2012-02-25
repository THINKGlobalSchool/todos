<?php
/**
 * Todo submission annotate form
 *
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 * @uses ElggEntity $vars['entity'] The entity to comment on
 */


if (isset($vars['entity']) && elgg_is_logged_in()) {
	$comment_label = elgg_echo("submission_annotations:add");

	$comment_input = elgg_view('input/longtext', array(
		'name' => 'comment_text'
	));
	
	$attach_label = elgg_echo("submission_annotations:attach");
	
	$attach_input = elgg_view('input/file', array(
		'name' => 'upload',
		'class' => 'todo-submission-attachment-upload',
	));

	$submit_input = elgg_view('input/submit', array(
		'value' => elgg_echo("submission_annotations:post")
	));
	
	$entity_hidden = elgg_view('input/hidden', array(
		'name' => 'entity_guid',
		'value' => $vars['entity']->getGUID()
	));

	$content = <<<HTML
		<div>
			<label>$comment_label</label>
			$comment_input
		</div>
		<div>
			<label>$attach_label</label>
			<div id='todo-submission-dropzone-div' class='todo-submission-drop-info todo-submission-dropzone todo-submission-dropzone-background'></div>
			$attach_input
		</div>
		<div class="elgg-foot">
			$submit_input
		</div>
		$entity_hidden
HTML;

	echo $content;
}