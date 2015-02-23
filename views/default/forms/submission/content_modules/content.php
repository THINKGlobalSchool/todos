<?php
/**
 * Submission Spot Content module
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.com/
 * 
 */

$content = elgg_view('modules/ajaxmodule', array(
	'limit' => 5,
	'types' => array('object'),
	'subtypes' => array('blog', 'bookmarks', 'image', 'album', 'poll', 'file', 'shared_doc', 'forum_reply', 'forum_topic', 'simplekalura_video'),
	'container_guid' => elgg_get_logged_in_user_guid(),
	'listing_type' => 'simpleicon',
	'module_type' => 'featured'
));

$content .= elgg_view('input/submit', array(
	'class' => 'elgg-button elgg-button-cancel submission-cancel-add',
	'name' => 'cancel_add',
	'value' => elgg_echo('cancel')
));

echo elgg_view_module('featured', elgg_echo('todo:label:addcontent'), $content, array(
	'class' => 'submission-content-pane',
	'id'=> 'submission-add-content-container'
));