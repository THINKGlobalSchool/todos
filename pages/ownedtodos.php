<?php
/**
 * Todo Owner/Created To Do's
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
$page_owner = elgg_get_page_owner_entity();
if (!$page_owner) {
	$page_owner_guid = elgg_get_logged_in_user_guid();
	if ($page_owner_guid) {
		elgg_set_page_owner_guid($page_owner_guid);
		$page_owner = elgg_get_page_owner_entity();
	}
}	

$limit = get_input("limit", 10);
$offset = get_input("offset", 0);

if ($page_owner instanceof ElggGroup || $page_owner->getGUID() != elgg_get_logged_in_user_guid()) {
	$title = sprintf(elgg_echo('todo:title:ownedtodos'), $page_owner->name);
} else {
	$title = elgg_echo('todo:title:yourtodos');
}


$options = array(
				'types' => 'object', 
				'subtypes' => 'todo', 
				'limit' => $limit, 
				'offset' => $offset, 
				'full_view' => FALSE
				);

// Check page owner for other user, loggedinuser or group
if ($page_owner != elgg_get_logged_in_user_entity()) { 
	if ($page_owner instanceof ElggGroup) {
		// breadcrumbs
		elgg_pop_breadcrumb();
		elgg_push_breadcrumb(elgg_echo('Groups'), "groups/world");
		elgg_push_breadcrumb($page_owner->name, "groups/" . $page_owner->getGUID() ."/");
		// If we're a group, use regular header, with proper new link
		$header = get_todo_content_header('groups', 'todo/createtodo/?container_guid=' . $page_owner->getGUID());
		$options['container_guid'] = $page_owner->getGUID();
	} else {
		$tabs = array(
			'assigned' => array(
				'title' => 'Assigned to ' . $page_owner->name,
				'url' => elgg_get_site_url() . 'todo/' . $page_owner->username,
				'selected' => false,
			),
			'owned' => array(
				'title' => 'Assigned by ' . $page_owner->name,
				'url' => elgg_get_site_url() . 'todo/owned/' . $page_owner->username,
				'selected' => true,
			)
		);

			$header .= elgg_view('page_elements/content_header', array('tabs' => $tabs, 'type' => 'todo', 'new_link' => elgg_get_site_url() . $new_link));
		$options['owner_guid'] = $page_owner->getGUID();
	}
	elgg_push_breadcrumb(sprintf(elgg_echo('todo:title:ownedtodos'), $page_owner->name), "todo/owned/" . $page_owner->username);
} else {
	$header = get_todo_content_header('owned');
	elgg_push_breadcrumb(elgg_echo('todo:title:yourtodos'), "todo/owned");
	// Setting owner_guid will show all users owned todo's, including todo's created on a groups behalf
	$options['owner_guid'] = $page_owner->getGUID();
}

$content .= elgg_view('navigation/breadcrumbs');
$content .= $header;
	
$list .= elgg_list_entities($options);
	
if ($list) {
	$content .= $list;
} else {
	$content .= elgg_view('todo/noresults');
}

// layout the sidebar and main column using the default sidebar
$body = elgg_view_layout('one_column_with_sidebar', $content, '');

// create the complete html page and send to browser
echo elgg_view_page($title, $body);
