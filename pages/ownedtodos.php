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
	
	global $CONFIG;
	
	// if username or owner_guid was not set as input variable, we need to set page owner
	// Get the current page's owner
	$page_owner = page_owner_entity();
	if (!$page_owner) {
		$page_owner_guid = get_loggedin_userid();
		if ($page_owner_guid) {
			set_page_owner($page_owner_guid);
			$page_owner = page_owner_entity();
		}
	}	
	
	$limit = get_input("limit", 10);
	$offset = get_input("offset", 0);

	if ($page_owner instanceof ElggGroup || $page_owner->getGUID() != get_loggedin_userid()) {
		$title = sprintf(elgg_echo('todo:title:ownedtodos'), $page_owner->name);
	} else {
		$title = elgg_echo('todo:title:yourtodos');
	}
	
	// create content for main column
	
	// breadcrumbs
	elgg_push_breadcrumb(elgg_echo('todo:title'), "{$CONFIG->site->url}pg/todo/everyone");	
	
	
	if ($page_owner instanceof ElggGroup) { 
		$header = elgg_view_title($title);
		elgg_push_breadcrumb(elgg_echo('todo:menu:groupassignedtodos'), "{$CONFIG->site->url}pg/todo/owned/" . $page_owner->username);
	} else {
		$header = get_todo_content_header('owned');
		elgg_push_breadcrumb(elgg_echo('todo:title:yourtodos'), "{$CONFIG->site->url}pg/todo/owned");
	}
	
	$content .= elgg_view('navigation/breadcrumbs');
	$content .= $header;
		
	$list .= elgg_list_entities(array('types' => 'object', 'subtypes' => 'todo', 'container_guid' => page_owner(), 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE));
		
	if ($list) {
		$content .= $list;
	} else {
		$content .= elgg_view('todo/noresults');
	}
	
	// layout the sidebar and main column using the default sidebar
	$body = elgg_view_layout('one_column_with_sidebar', $content, '');

	// create the complete html page and send to browser
	page_draw($title, $body);
?>