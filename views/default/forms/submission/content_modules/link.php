<?php
/**
 * Submission link module
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

$link_content = elgg_view('input/text', array(
	'id' => 'submission-link', 
	'name' => 'submission_link'
)) . "<br /><br />";

$link_content .= elgg_view('input/submit', array(
	'id' => 'submission-submit-link', 
	'name' => 'link_submit', 
	'value' => 'Submit'
));

$link_form = elgg_view('input/form', array(
	'id' => "submission-link-form",
	'body' => $link_content
));

echo elgg_view_module('info', elgg_echo('todo:label:addlink'), $link_form, array(
	'id' => 'submission-add-link-container', 
	'class' => 'submission-content-pane',
));