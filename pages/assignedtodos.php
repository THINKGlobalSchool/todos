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
	
	if (!$status == 'complete' || !$status == 'incomplete') {
		$status = 'incomplete';
	}
	

	$title = elgg_echo('todo:title:assignedtodos');
	
	// create content for main column
	$content = elgg_view_title($title);
	
	$content .= elgg_view('todo/assignednav');
	
	$context = get_context();
	set_context('search');
	
	/*
		This is... weird. But it works and makes sense. 
		Set ignore access, this will return all entities (ignoring access level) with which 
		this user has been assigned. Which is fine, because we can ignore the access level 
		safely because we'll only get entities where there is a relationship to this user. 
		Make sense? Sure it does!
		
		TODO: Find a better way.. it makes sense, but its gross. 
	*/
	$ia = elgg_set_ignore_access(TRUE);
	$assigned_entities = get_users_todos(get_loggedin_userid());
	elgg_set_ignore_access($ia);
	
	if ($status == 'complete') {
		foreach ($assigned_entities as $entity) {
			if (has_user_submitted(get_loggedin_userid(), $entity->getGUID())) {
				$entities[] = $entity;
			}
		}
	} else if ($status == 'incomplete') {
		foreach ($assigned_entities as $entity) {
			if (!has_user_submitted(get_loggedin_userid(), $entity->getGUID())) {
				$entities[] = $entity;
			}
		}
	}
	
	$list .= elgg_view_entity_list($entities, count($entities), $offset, $limit, false, false, true);
	//$list .= list_entities_from_relationship(TODO_ASSIGNEE_RELATIONSHIP, $page_owner_guid, false, 'object', 'todo', 0, $limit, false, false, true);
	
	set_context($context);
	
	if ($list) {
		$content .= $list;
	} else {
		$content .= elgg_view('todo/noresults');
	}
	
	// layout the sidebar and main column using the default sidebar
	$body = elgg_view_layout('two_column_left_sidebar', '', $content);

	// create the complete html page and send to browser
	page_draw($title, $body);
?>