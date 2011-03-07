<?php
/**
 * Todo List All Site To Do's
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
$page_owner = page_owner_entity();
if (!$page_owner) {
	$page_owner_guid = get_loggedin_userid();
	if ($page_owner_guid)
		set_page_owner($page_owner_guid);
}	

$limit = get_input("limit", 10);
$offset = get_input("offset", 0);

$status = get_input('status', 'incomplete');

if (!in_array($status, array('complete', 'incomplete'))) {
	$status = 'incomplete';
}

$title = elgg_echo('todo:title:alltodos');

// create content for main column

// breadcrumbs
elgg_push_breadcrumb(elgg_echo('todo:label:' . $status), "{$CONFIG->site->url}pg/todo/?status=" . $status);

$content .= elgg_view('navigation/breadcrumbs');	
$content .= get_todo_content_header('all');

$content .= elgg_view('todo/nav_showbycomplete', array('return_url' => 'pg/todo/everyone'));

// Show based on status
if ($status == 'complete') {
	$list .= elgg_list_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'todo',
		'metadata_name_value_pairs' => array(array(
												'name' => 'complete',
												'value' => 1, 
												'operand' => '='),
											array(
												'name' => 'status',
												'value' => TODO_STATUS_PUBLISHED,
												'operand' => '=',
											)),
		'order_by_metadata' => array('name' => 'due_date', 'as' => 'int', 'direction' => get_input('direction', 'ASC')),
		'full_view' => FALSE,
	));	
} else if ($status == 'incomplete') {
	set_input('display_label', true);
	// Creating some magic SQL to grab todos without complete metadata
	$test_id = get_metastring_id('complete');
	$one_id = get_metastring_id(1);
	$wheres = array();
	$wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$CONFIG->dbprefix}metadata md
			WHERE md.entity_guid = e.guid
				AND md.name_id = $test_id
				AND md.value_id = $one_id)";

	
	$list = elgg_list_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'todo',
		'metadata_name' => 'status',
		'metadata_value' => TODO_STATUS_PUBLISHED,
		'order_by_metadata' => array('name' => 'due_date', 'as' => 'int', 'direction' => get_input('direction', 'ASC')),
		'full_view' => FALSE,
		'wheres' => $wheres,
	));	
}

if ($list) {
	$content .= $list;
} else {
	$content .= elgg_view('todo/noresults');
}

// layout the sidebar and main column using the default sidebar
$body = elgg_view_layout('one_column_with_sidebar', $content, '');

// create the complete html page and send to browser
echo elgg_view_page($title, $body);
