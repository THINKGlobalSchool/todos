<?php
/**
 * Submission Spot Content module
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

echo elgg_view('modules/ajaxmodule', array(
	'title' => elgg_echo('todo:label:addcontent'),
	'limit' => 5,
	'types' => array('object'),
	'subtypes' => array('blog', 'bookmarks', 'image', 'album', 'poll', 'file', 'shared_doc', 'forum_reply', 'forum_topic'),
	'container_guid' => elgg_get_logged_in_user_guid(),
	'listing_type' => 'simpleicon',
	'module_type' => 'info',
	'module_class' => 'submission-content-pane',
	'module_id' => 'submission-add-content-container',
));
