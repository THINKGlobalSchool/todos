<?php
	/**
	 * Todo Index
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
	$page_owner = page_owner_entity();
	if (!$page_owner) {
		$page_owner_guid = get_loggedin_userid();
		if ($page_owner_guid)
			set_page_owner($page_owner_guid);
	}	
	
	$limit = get_input("limit", 10);
	$offset = get_input("offset", 0);

	$title = elgg_echo('todo:title:yourtodos');
	
	// create content for main column
	$content = elgg_view_title($title);
	
	$context = get_context();
	set_context('search');
	
	$content .= elgg_list_entities(array('types' => 'object', 'subtypes' => 'todo', 'container_guid' => page_owner(), 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE));
	
	set_context($context);
	
	// layout the sidebar and main column using the default sidebar
	$body = elgg_view_layout('two_column_left_sidebar', '', $content);

	// create the complete html page and send to browser
	page_draw($title, $body);
?>