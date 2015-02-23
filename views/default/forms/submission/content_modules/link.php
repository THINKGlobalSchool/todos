<?php
/**
 * Submission link module
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
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
	'value' => elgg_echo('add')
));

$link_content .= elgg_view('input/submit', array(
	'class' => 'elgg-button elgg-button-cancel submission-cancel-add',
	'name' => 'cancel_add',
	'value' => elgg_echo('cancel')
));

$link_form = elgg_view('input/form', array(
	'id' => "submission-link-form",
	'body' => $link_content
));

echo elgg_view_module('featured', elgg_echo('todo:label:addlink'), $link_form, array(
	'id' => 'submission-add-link-container', 
	'class' => 'submission-content-pane',
));