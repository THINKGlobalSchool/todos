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
	$due = get_input('due', 'past');
	
	if (!in_array($status, array('complete', 'incomplete'))) {
		$status = 'incomplete';
	}
	
	if (!in_array($due, array('past', 'nextweek', 'future'))) {
		$due = 'past';
	}

	$title = elgg_echo('todo:title:alltodos');
	
	// create content for main column
	$content = elgg_view_title($title);
	
	$content .= elgg_view('todo/nav_showbycomplete', array('return_url' => 'pg/todo/everyone'));
		
	// First get a list of all accessable entities 
	$accessable_entities = elgg_get_entities(array('types' => 'object', 'subtypes' => 'todo', 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE));
	
	// Next get a list of entities where I'm an assignee (these may not be available in the above list)
	// Needs to ignore access .. not the best way.. :(
	$ia = elgg_set_ignore_access(TRUE);
	$assigned_entities = get_users_todos(get_loggedin_userid());
	elgg_set_ignore_access($ia);
	
	if ($assigned_entites) {
		$all_entities = array_merge($accessable_entities, $assigned_entities);

		// Need to make objects unique to use array_unique
		foreach ($all_entities as $key => $value) {
			$all_entities[$key] = serialize($value);
		}

		$all_entities = array_unique($entities);

		// Unserialize back to objects
		foreach ($all_entities as $key => $value) {
			$all_entities[$key] = unserialize($value);
		}
	} else {
		$all_entities = $accessable_entities;
	}
	
	
	// Show based on status
	if ($status == 'complete') {
		$content .= "</div>";
		foreach ($all_entities as $entity) {
			if (have_assignees_completed_todo($entity->getGUID())) {
				$entities[] = $entity;
			}
		}
	} else if ($status == 'incomplete') {
		$content .= elgg_view('todo/nav_showbydue', array('return_url' => 'pg/todo/everyone'));
		$content .= "</div>";
		foreach ($all_entities as $entity) {
			if (!have_assignees_completed_todo($entity->getGUID())) {
				$entities[] = $entity;
			}
		}
		
		$today = strtotime(date("F j, Y"));
		$next_week = strtotime("+7 days", $today);
				
		switch ($due) {
			case "nextweek":
				$entities = get_todos_due_between($entities, $today, $next_week);
				break;
			case "future": 
				$entities = get_todos_due_after($entities, $next_week);
				break;
			case "past":
				$entities = get_todos_due_before($entities, $today);
				break;
		}
		
		sort_todos_by_due_date($entities);
	}
	
	
	$context = get_context();
	set_context('search');
	
	$list .= elgg_view_entity_list($entities, count($entities), $offset, $limit, false, false, true);
	
	set_context($context);
	
	
	// Old way.. no good.
	//$list .= elgg_list_entities(array('types' => 'object', 'subtypes' => 'todo', 'limit' => $limit, 'offset' => $offset, 'full_view' => FALSE));
		
	
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