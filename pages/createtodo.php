<?php
/**
 * Todo Create Page
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// include the Elgg engine
include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php"; 

// Logged in users only
gatekeeper();

// if username or owner_guid was not set as input variable, we need to set page owner
// Get the current page's owner
if ($container = (int) get_input('container_guid')) {
	elgg_set_page_owner_guid($container);
}
$page_owner = elgg_get_page_owner_entity();
if (!$page_owner) {
	$page_owner_guid = elgg_get_logged_in_user_guid();
	if ($page_owner_guid)
		elgg_set_page_owner_guid($page_owner_guid);
}	

$title = elgg_echo('todo:title:create');

// create content for main column

// breadcrumbs
elgg_push_breadcrumb($title);

$content .= elgg_view('navigation/breadcrumbs');
$content .= elgg_view_title($title);
$content .= elgg_view("todo/forms/edittodo");

// layout the sidebar and main column using the default sidebar
$body = elgg_view_layout('one_column_with_sidebar', $content, '');

// create the complete html page and send to browser
echo elgg_view_page($title, $body);
