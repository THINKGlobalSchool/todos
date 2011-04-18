<?php
/**
 * Todo To Do's assigned to me
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
group_gatekeeper();

global $CONFIG;
	
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

$status = get_input('status', 'incomplete');

if (!in_array($status, array('complete', 'incomplete'))) {
	$status = 'incomplete';
}
	
if ($page_owner->getGUID() != elgg_get_logged_in_user_guid()) {
	elgg_push_breadcrumb(sprintf(elgg_echo('todo:title:ownedtodos'), $page_owner->name), "todo/owned/" . $page_owner->username);
} else {
	elgg_push_breadcrumb(elgg_echo('todo:title:assignedtodos'), "todo/{$page_owner->username}");
}

$test_id = get_metastring_id('manual_complete');
$one_id = get_metastring_id(1);
$wheres = array();
			
$user_id = elgg_get_logged_in_user_guid();		
$relationship = COMPLETED_RELATIONSHIP;
	
// Build list based on status
if ($status == 'complete') {
	elgg_push_breadcrumb(elgg_echo('todo:label:complete'), "todo/{$page_owner->username}?status=complete");
				
	$wheres[] = "(EXISTS (
			SELECT 1 FROM {$CONFIG->dbprefix}entity_relationships r2 
			WHERE r2.guid_one = '$user_id'
			AND r2.relationship = '$relationship'
			AND r2.guid_two = e.guid) OR 
				EXISTS (
			SELECT 1 FROM {$CONFIG->dbprefix}metadata md
			WHERE md.entity_guid = e.guid
				AND md.name_id = $test_id
				AND md.value_id = $one_id))";

	
} else if ($status == 'incomplete') {	
	set_input('display_label', true);
	elgg_push_breadcrumb(elgg_echo('todo:label:incomplete'), "todo/{$page_owner->username}?status=incomplete");	
	
	// Non existant 'manual complete'
	$wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$CONFIG->dbprefix}metadata md
			WHERE md.entity_guid = e.guid
				AND md.name_id = $test_id
				AND md.value_id = $one_id)";
						
	$wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$CONFIG->dbprefix}entity_relationships r2 
			WHERE r2.guid_one = '$user_id'
			AND r2.relationship = '$relationship'
			AND r2.guid_two = e.guid)";
}

$list = elgg_list_entities_from_relationship(array(
	'type' => 'object',
	'subtype' => 'todo',
	'relationship' => TODO_ASSIGNEE_RELATIONSHIP, 
	'relationship_guid' => elgg_get_logged_in_user_guid(), 
	'inverse_relationship' => FALSE,
	'metadata_name' => 'status',
	'metadata_value' => TODO_STATUS_PUBLISHED,
	'order_by_metadata' => array('name' => 'due_date', 'as' => 'int', 'direction' => get_input('direction', 'ASC')),
	'full_view' => FALSE,
	'wheres' => $wheres,
));


// Start building content
$content .= elgg_view('navigation/breadcrumbs');

if ($page_owner instanceof ElggGroup || $page_owner->getGUID() != elgg_get_logged_in_user_guid()) {
	$title = sprintf(elgg_echo('todo:title:ownedtodos'), $page_owner->name);
	$tabs = array(
		'assigned' => array(
			'title' => 'Assigned to ' . $page_owner->name,
			'url' => elgg_get_site_url() . 'todo/' . $page_owner->username,
			'selected' => true,
		),
		'owned' => array(
			'title' => 'Assigned by ' . $page_owner->name,
			'url' => elgg_get_site_url() . 'todo/owned/' . $page_owner->username,
			'selected' => false,
		)
	);
					
	$content .= elgg_view('page_elements/content_header', array('tabs' => $tabs, 'type' => 'todo', 'new_link' => elgg_get_site_url() . $new_link));
} else {
	$title = elgg_echo('todo:title:assignedtodos');
	$content .= get_todo_content_header('assigned', $new_link = "todo/createtodo/");
}	
	

$content .= elgg_view('todo/nav_showbycomplete', array('return_url' => 'todo'));

if ($list) {
	$content .= $list;
} else {
	$content .= elgg_view('todo/noresults');
}

// layout the sidebar and main column using the default sidebar
$body = elgg_view_layout('one_column_with_sidebar', $content, '');

// create the complete html page and send to browser
echo elgg_view_page($title, $body);
