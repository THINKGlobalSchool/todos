<?php
/**
 * Todo Start
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

/*********************** TODO: (Code related) ************************/
// - Cleaner way to handle different content attachments (views, callbacks.. yadda)
// - Prettier everything (Rubric select, view rubric modal popup, etc.. )
// - File permissions

// MIGRATION TODOS
// - Examine language file (remove unused strings, clean up)

elgg_register_event_handler('init', 'system', 'todo_init');

function todo_init() {	
	// Library
	elgg_register_library('elgg:todo', elgg_get_plugins_path() . 'todo/lib/todo.php');
	elgg_load_library('elgg:todo');

	// Assignment (todo) access levels
	define('TODO_ACCESS_LEVEL_LOGGED_IN', ACCESS_LOGGED_IN);
	define('TODO_ACCESS_LEVEL_ASSIGNEES_ONLY', -10);
	
	// Determine if optional plugins are enabled
	define('TODO_RUBRIC_ENABLED', elgg_is_active_plugin('rubricbuilder') ? true : false);
	define('TODO_CHANNELS_ENABLED', elgg_is_active_plugin('shared_access') ? true : false);
	
	// Relationship for assignees
	define('TODO_ASSIGNEE_RELATIONSHIP', 'assignedtodo');
	
	// Relationship for accepting todo's
	define('TODO_ASSIGNEE_ACCEPTED', 'acceptstodo');
	
	// Relationship for submissions 
	define('SUBMISSION_RELATIONSHIP', 'submittedto');
	
	// Relationship for complete todos
	define('COMPLETED_RELATIONSHIP', 'completedtodo');
	
	// View Modes
	define('TODO_MODE_ASSIGNER', 0);
	define('TODO_MODE_ASSIGNEE', 1);
	
	// Priorities (currently just used for a pretty display)
	define('TODO_PRIORITY_HIGH', 1);
	define('TODO_PRIORITY_MEDIUM', 2);
	define('TODO_PRIORITY_LOW', 3);
	
	// Todo status's 
	define('TODO_STATUS_DRAFT', 0);
	define('TODO_STATUS_PUBLISHED', 1);
	
	//get_todo_groups_array();
	
	// Extend CSS
	elgg_extend_view('css/elgg','css/todo/css');
	
	// Admin CSS
	elgg_extend_view('css/admin', 'css/todo/admin');
	
	// Extend Metatags (for js)
	elgg_extend_view('html_head/extend','todo/metatags');
	
	// Add groups menu
	elgg_extend_view('groups/menu/links', 'todo/menu'); 
	
	// Extend groups profile page
	if (elgg_is_active_plugin('group-extender')) {
		elgg_extend_view('group-extender/sidebar','todo/group_todos', 2);
	}
	
	// Extend profile_ownerblock
	elgg_extend_view('profile_ownerblock/extend', 'todo/profile_link');
	
	// Extend admin view to include some extra styles
	elgg_extend_view('layouts/administration', 'todo/admin/css');
	
	// add the group pages tool option     
	add_group_tool_option('todo',elgg_echo('groups:enabletodo'),true);

	// Page handler
	elgg_register_page_handler('todo','todo_page_handler');

	// Add submenus
	elgg_register_event_handler('pagesetup','system','todo_submenus');
			
	// Register a handler for creating todos
	elgg_register_event_handler('create', 'object', 'todo_create_event_listener');

	// Register a handler for deleting todos
	elgg_register_event_handler('delete', 'object', 'todo_delete_event_listener');

	// Register a handler for assigning users to todos
	elgg_register_event_handler('assign','object','todo_assign_user_event_listener');
	
	// Register a handler for removing assignees from todos
	elgg_register_event_handler('unassign','object','todo_unassign_user_event_listener');
	
	// Register a handler for created submissions 
	elgg_register_event_handler('create', 'object', 'submission_create_event_listener');
	
	// Register a handler for deleted submissions
	elgg_register_event_handler('delete', 'object', 'submission_delete_event_listener');
	
	// Register handlers for submission relationships
	elgg_register_event_handler('create', SUBMISSION_RELATIONSHIP, 'submission_relationship_event_listener');
	//elgg_register_event_handler('delete', SUBMISSION_RELATIONSHIP, 'submission_relationship_event_listener');
	
	// Register a handler for submission comments so that the todo owner is notified
	elgg_register_event_handler('annotate', 'all', 'submission_comment_event_listener');
	
	// Plugin hook for write access
	elgg_register_plugin_hook_handler('access:collections:write', 'all', 'todo_write_acl_plugin_hook');
	
	// Plugin hook for write access
	elgg_register_plugin_hook_handler('access:collections:write', 'all', 'submission_write_acl_plugin_hook');
	
	// Register an annotation handler for comments etc
	elgg_register_plugin_hook_handler('entity:annotate', 'object', 'todo_annotate_comments');
	elgg_register_plugin_hook_handler('entity:annotate', 'object', 'submission_annotate_comments');
	
	// Profile hook	
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'todo_profile_menu');
	
	// Hook into views to post process river/item/wrapper for todo submissions
	elgg_register_plugin_hook_handler('display', 'view', 'todo_submission_river_rewrite');
	
	// Set up url handlers
	elgg_register_entity_url_handler('object', 'todo', 'todo_url');
	elgg_register_entity_url_handler('object', 'todosubmission', 'todo_submission_url');
	
	// Hook for site menu
	elgg_register_plugin_hook_handler('register', 'menu:topbar', 'todo_topbar_menu_setup', 9000);
	
	// Handler to prepare main todo menu
	elgg_register_plugin_hook_handler('register', 'menu:todo-listing-main', 'todo_main_menu_setup');

	// Handler to prepare secondary todo menu
	elgg_register_plugin_hook_handler('register', 'menu:todo-listing-secondary', 'todo_secondary_menu_setup');


	// @TODO clean these up
	// Register actions
	$action_base = elgg_get_plugins_path() . "todo/actions/todo";
	elgg_register_action('todo/save', "$action_base/save.php");
	elgg_register_action('todo/delete', "$action_base/delete.php");
	elgg_register_action('todo/accepttodo', "$action_base/accepttodo.php");
	elgg_register_action('todo/assign', "$action_base/assign.php");
	elgg_register_action('todo/unassign', "$action_base/unassign.php");
	elgg_register_action('todo/sendreminder', "$action_base/sendreminder.php");
	elgg_register_action('todo/completetodo', "$action_base/completetodo.php");
	
	$action_base = elgg_get_plugins_path() . "todo/actions/submission";
	elgg_register_action('submission/save', "$action_base/save.php");
	elgg_register_action('submission/delete', "$action_base/delete.php");


	// Register type
	elgg_register_entity_type('object', 'todo');		


	
	/*create dummy object for metastrings
	$dummy = new ElggObject();
	$dummy->manual_complete = 1;
	$dummy->complete = 1;
	$dummy->one = 1;
	
	$dummy->save();
	$dummy->delete();	
	*/
	return true;	
}

/**
 * Todo page handler
 * @TODO SHOULD BE LIKE BELOW
 * URLs take the form of
 *  All todos:       todo/all
 *  User's todos:    todo/owner/<username>
 *  Assigned todos:  todo/assigned/<username>
 *  View todo:       todo/view/<guid>/<title>
 *  View submission	 todo/view/submission/<guid>
 *  New todo:        todo/add/<guid>
 *  Edit todo:       todo/edit/<guid>/<revision>
 *  Group todo:      todo/group/<guid>/owner
 *  Calendar feed    todo/calendar/<username>
 *
 * AJAX:
 *  todo/loadassignees - get assignee list via ajax
 *
 * Title is ignored
 *
 * @TODO Check usernames
 * 
 * @param array $page
 * @return NULL
 */
function todo_page_handler($page) {	
	elgg_push_breadcrumb(elgg_echo('todo'), elgg_get_site_url() . "todo/all");	
	
	$page_type = $page[0];
	
	switch ($page_type) {
		/* These are admin scripts... put em somewhere else
		case 'updateusercomplete': // Force a user todo complete update
			admin_gatekeeper();
			$entities = elgg_get_entities_from_metadata(array(
				'owner_guid' => ELGG_ENTITIES_ANY_VALUE,
				'subtype' => 'todosubmission',
				'type' => 'object',
				'limit' => 0,
				'order_by_metadata' => array('name' => 'todo_guid', 'as' => 'int'),
			));
			foreach($entities as $entity) {
				echo $entity->owner_guid . " - "  . $entity->todo_guid . "</br>"; 
				add_entity_relationship($entity->owner_guid, COMPLETED_RELATIONSHIP, $entity->todo_guid);
			}
			break;
		case 'updatetodocomplete': // Force a todo complete update
			admin_gatekeeper(); 
			
			$entities = elgg_get_entities(array(
				'type' => 'object',
				'subtype' => 'todo',
				'limit' => 0
			));
			
			foreach($entities as $entity) {
				var_dump(have_assignees_completed_todo($entity->getGUID()));
				update_todo_complete($entity->getGUID());
				var_dump($entity->complete);
			}
			break;
		*/
		case 'add':
			gatekeeper();
			group_gatekeeper();
			$params = todo_get_page_content_edit($page_type, $page[1]);
			break;
		case 'view':
			if ($page[1] == 'submission'){
				$params = todo_get_page_content_view($page[1], $page[2]);
			} else {
				$params = todo_get_page_content_view('todo', $page[1]);
			}
			break;
		case 'edit':
			gatekeeper();
			group_gatekeeper();
			$params = todo_get_page_content_edit($page_type, $page[1]);
			break;
		case 'owner':
			gatekeeper();
			group_gatekeeper();
			$user = get_user_by_username($page[1]);
			elgg_set_page_owner_guid($user->getGUID());
			set_input('username',$page[1]);
			$params = todo_get_page_content_list($page_type, $page[1]);
			break;
		case 'group':
			gatekeeper();
			group_gatekeeper();
			$params = todo_get_page_content_list('owner', $page[1]);
			break;
		case 'assigned':
			gatekeeper();
			group_gatekeeper();
			$user = get_user_by_username($page[1]);
			elgg_set_page_owner_guid($user->getGUID());
			set_input('username',$page[1]);
			$params = todo_get_page_content_list($page_type, $page[1]);
			break;
		case 'all':
		default:
			gatekeeper();
			group_gatekeeper();
			$params = todo_get_page_content_list();
			break;
		case 'calendar':
			echo elgg_view('todo/calendar', array(
				'hash' => get_input('t'), 
				'username' => $page[1]
				));
			exit;
			break;
		case 'loadassignees':
			$guid = get_input('guid');
			echo elgg_view('todo/ajaxassignees', array('guid' => $guid));
			exit;
			break;
	}
	
	// Custom sidebar (none at the moment)
	$params['sidebar'] .= elgg_view('todo/sidebar');

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($params['title'], $body);
}

/**
 * Hook into the framework and provide comments on submission entities.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function submission_annotate_comments($hook, $entity_type, $returnvalue, $params)
{
	$entity = $params['entity'];
	$full = $params['full'];
	
	if (
		($entity instanceof ElggEntity) &&	// Is the right type 
		($entity->getSubtype() == 'todosubmission') &&  // Is the right subtype
		($full) // This is the full view
	)
	{
		// Display comments
		return elgg_view_comments($entity);
	}
	
}

/**
 * Hook into the framework and provide comments on todo entities.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function todo_annotate_comments($hook, $entity_type, $returnvalue, $params) {
	$entity = $params['entity'];
	$full = $params['full'];
	
	if (
		($entity instanceof ElggEntity) &&	// Is the right type 
		($entity->getSubtype() == 'todo') &&  // Is the right subtype
		($full) // This is the full view
	)
	{
		// Display comments
		return elgg_view_comments($entity);
	}
	
}

/**
 * Todo created, so add users to access lists.
 */
function todo_create_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todo') {
		$todo_acl = create_access_collection(elgg_echo('todo:todo') . ": " . $object->title, $object->getGUID());
		if ($todo_acl) {
			$object->assignee_acl = $todo_acl;
			elgg_set_context('todo_acl');
			add_user_to_access_collection($object->owner_guid, $todo_acl);
			elgg_set_context($context);
			if ($object->access_id == TODO_ACCESS_LEVEL_ASSIGNEES_ONLY) {
				$object->access_id = $todo_acl;
				$object->save();
			}
		} else {
			return false;
		}
	}
	return true;
}

/**
 * Todo deleted, so remove access lists.
 */
function todo_delete_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todo') {
		$context = elgg_elgg_get_context();
		elgg_set_context('todo_acl');
		register_error(delete_access_collection($object->assignee_acl));
		elgg_set_context($context);
	}
	return true;
}

/**
 * Listens to a todo assign event and adds a user to the todos's access control
 *
 */
function todo_assign_user_event_listener($event, $object_type, $object) {
	if ($object['todo']->getSubtype() == 'todo') {
		$todo = $object['todo'];
		$user = $object['user'];
		$acl = $todo->assignee_acl;
		
		// This will check and set the complete flag on the todo
		update_todo_complete($todo->getGUID());
		
		$context = elgg_get_context();
		elgg_set_context('todo_acl');
		$result = add_user_to_access_collection($user->getGUID(), $acl);
		elgg_set_context($context);			
	}
	return true;
}

/**
 * Listens to a todo unassign event and removes a user from the todo's access control
 *
 */
function todo_unassign_user_event_listener($event, $object_type, $object) {
	if ($object['todo']->getSubtype() == 'todo') {	
		$todo = $object['todo'];
		$user = $object['user'];
		$acl = $todo->assignee_acl;
		
		// This will check and set the complete flag on the todo
		update_todo_complete($todo->getGUID());
	
		$context = elgg_get_context();
		elgg_set_context('todo_acl');
		remove_user_from_access_collection($user->getGUID(), $acl);
		elgg_set_context($context);	
	}
	return true;
}

/**
 * Return the write access for the current todo if the user has write access to it.
 */
function todo_write_acl_plugin_hook($hook, $entity_type, $returnvalue, $params) {
	if (elgg_in_context('todo_acl')) {
		// get all todos if logged in
		if ($loggedin = elgg_get_logged_in_user_entity()) {
			//$todos = get_users_todos($loggedin->getGUID());
			$todos = elgg_get_entities(array('types' => 'object', 'subtypes' => 'todo'));
			if (is_array($todos)) {
				foreach ($todos as $todo) {
					$returnvalue[$todo->assignee_acl] = elgg_echo('todo:todo') . ': ' . $todo->title;
				}
			}
		}
	}
	return $returnvalue;
}

/**
 * Submission created, so add users to access lists.
 */
function submission_create_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todosubmission') {
		// Get the submissions todo
		$todo = get_entity($object->todo_guid);
		
		// Create an ACL for the submission, only the todo assigner and assignee can see it
		$submission_acl = create_access_collection(elgg_echo('todo:todo') . ": " . $todo->title, $object->getGUID());
		
		if ($submission_acl) {
			$object->submission_acl = $submission_acl;
			$context = elgg_get_context();
			elgg_set_context('submission_acl');
			add_user_to_access_collection($todo->owner_guid, $submission_acl);
			add_user_to_access_collection(elgg_get_logged_in_user_guid(), $submission_acl);
			elgg_set_context($context);
			$object->access_id = $submission_acl;
			$object->save();			
		} else {
			return false;
		}
	}
	return true;
}

/**
 * Submission deleted
 */
function submission_delete_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todosubmission') {
		// Get the submissions todo
		$todo = get_entity($object->todo_guid);
		
		// Make sure we nuke the relationship so the remove event fires
		remove_entity_relationship($object->getGUID(), SUBMISSION_RELATIONSHIP, $todo->getGUID());
		
		// Nuke the ACL
		delete_access_collection($submission_acl);
		
	}
	return true;
}

/**
 * Submission relationship created/removed
 */
function submission_relationship_event_listener($event, $object_type, $object) {
	// The todo is 'guid_two'
	$todo = get_entity($object->guid_two);
	
	// This will check and set the complete flag on the todo
	update_todo_complete($todo->getGUID());
}


/**
 * Return the write access for the current todo submission if the user has write access to it.
 */
function submission_write_acl_plugin_hook($hook, $entity_type, $returnvalue, $params) {
	if (elgg_in_context('submission_acl')) {
		// get all todos if logged in
		if ($loggedin = elgg_get_logged_in_user_entity()) {
			$submissions = elgg_get_entities(array('types' => 'object', 'subtypes' => 'todosubmission'));
			if (is_array($submissions)) {
				foreach ($submissions as $submission) {
					$todo = get_entity($submission->todo_guid);
					$returnvalue[$submission->submission_acl] = elgg_echo('todo:todo') . ': ' . $todo->title;
				}
			}
		}
	}
	return $returnvalue;
}

/**
 * Submission commented, notify todo creator
 */
function submission_comment_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todosubmission') {
		// Get the submissions todo
		$todo = get_entity($object->todo_guid);
		$user = get_entity($object->owner_guid);
		
		// Notify todo owner that the submission was commented on
		notify_user($todo->owner_guid, 
					$user->getGUID(),
					elgg_echo('generic_comment:email:subject'), 
					sprintf(elgg_echo('todo:email:bodysubmissioncomment'), 
							$todo->title,
							$object->getURL(),
							$user->name,
							$user->getURL()
					)
		);
		
	}
	return true;
}

/**
 * Plugin hook to add to do's to users profile block
 * 	
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function todo_profile_menu($hook, $entity_type, $return, $params) {	
	// Only display todo link for users or groups with enabled todos
	if ($params['owner'] instanceof ElggUser || $params['owner']->todo_enable == 'yes') {
		$return[] = array(
			'text' => elgg_echo('todo'),
			'href' => elgg_get_site_url() . "todo/owner/{$params['owner']->username}",
		);
	}
	
	
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "todo/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('todo', elgg_echo('todo'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->todo_enable == "yes") {
			$url = "todo/group/{$params['entity']->guid}/owner";
			$item = new ElggMenuItem('todo', elgg_echo('todo:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}

/** 
 * Comments for submissions on the river are forcefully hidden
 * 
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
 
function todo_submission_river_rewrite($hook, $entity_type, $returnvalue, $params) {
	$view = $params['view'];
	if ($view == 'river/item/wrapper') {
		$submission = get_entity($params['vars']['item']->object_guid);
		if ($submission->getSubtype() == 'todosubmission') {	
			$new_content = "<div class='todo_submission_river_item'>" . $returnvalue . "</div>";
			return $new_content;
		}
	}
}

/**
 * Setup todo submenus
 */
function todo_submenus() {
	$page_owner = elgg_get_page_owner_entity();
			 		
	// Default todo submenus
	if (elgg_in_context('todo')) {

	 	if (!(elgg_instanceof($page_owner, 'group'))) {
			$user = elgg_get_logged_in_user_entity();
			/* Do we really need these?? 		
			// Your todos
			$url =  "todo/assigned/" . $user->username;
			$item = new ElggMenuItem('todo:menu:yourtodos', elgg_echo('todo:menu:yourtodos'), $url);
			elgg_register_menu_item('page', $item);
		
			// Owned todos
			$url =  "todo/owner/" . $user->username;
			$item = new ElggMenuItem('todo:menu:assignedtodos', elgg_echo('todo:menu:assignedtodos'), $url);
			elgg_register_menu_item('page', $item);
		
			// All todos
			$url =  "todo/all";
			$item = new ElggMenuItem('todo:menu:alltodos', elgg_echo('todo:menu:alltodos'), $url);
			elgg_register_menu_item('page', $item);
			*/
		}
	}
		
	// Admin stats
	if (elgg_in_context('admin')) {
		elgg_register_admin_menu_item('administer', 'todo', 'statistics');
	}

}

/**
 * Populates the ->getUrl() method for todo submission entities
 *
 * @param ElggEntity entity
 * @return string request url
 */
function todo_submission_url($entity) {
	return elgg_get_site_url() . "todo/viewsubmission/{$entity->guid}/";
}

/**
 * Populates the ->getUrl() method for todo entities
 *
 * @param ElggEntity entity
 * @return string request url
 */
function todo_url($entity) {	
	return elgg_get_site_url() . "todo/view/{$entity->guid}/";
}

/**
 * Tobar menu hook handler
 * - adds the todo icon to the topbar
 */
function todo_topbar_menu_setup($hook, $type, $return, $params) {		
	$user = elgg_get_logged_in_user_entity();
	$todos = get_users_todos($user->getGUID());
	$count = 0;
	foreach ($todos as $todo) {
		if (!has_user_accepted_todo($user->getGUID(), $todo->getGUID())) {
			$count++;
		}
	}	
	
	$class = "elgg-icon todo-notifier";
	$text = "<span class='$class'></span>";

	if ($count != 0) {
		$text .= "<span class=\"messages-new\">$count</span>";
	}

	// Add logout button
	$options = array(
		'name' => 'todo',
		'text' => $text,
		'href' =>  'todo',
		'priority' => 999,
	);
	$return[] = ElggMenuItem::factory($options);

	return $return;
}

/**
 * Todo main menu setup
 */
function todo_main_menu_setup($hook, $type, $return, $params) {	
	// Set up main nav for todo listings
	$main_tab = get_input('todo_main_tab', 'all');
	
	$user = elgg_get_page_owner_entity();
	
	if (!elgg_instanceof($user, 'user')) {
		$user = elgg_get_logged_in_user_entity();
	}
	
	if ($user == elgg_get_logged_in_user_entity()) {
		$by = elgg_echo('todo:label:me');
	} else {
		$by = $user->name;
	}
	
 	$options = array(
		'name' => 'all',
		'text' => elgg_echo("all"),
		'href' => 'todo/all',
		'selected' => ($main_tab === 'all'),
		'priority' => 1,
	);
	
	$return[] = ElggMenuItem::factory($options);
	
	$options = array(
		'name' => 'assignedtome',
		'text' => elgg_echo("todo:label:assignedto", array($by)),
		'href' => 'todo/assigned/' . $user->username,
		'selected' => ($main_tab === 'assigned'),
		'priority' => 2
	);
	
	$return[] = ElggMenuItem::factory($options);
	
 	$options = array(
		'name' => 'assignedbyme',
		'text' => elgg_echo("todo:label:assignedby", array($by)),
		'href' => 'todo/owner/' . $user->username,
		'selected' => ($main_tab === 'owner'),
		'priority' => 3
	);
	
	$return[] = ElggMenuItem::factory($options);
	
	return $return;
}

/**
 * Todo secondary menu setup
 */
function todo_secondary_menu_setup($hook, $type, $return, $params) {
	// Set up secondary nav for todo listings
	$secondary_tab = get_input('status', 'incomplete');
	
	$direction = get_input('direction', 'ASC');

	if ($direction == 'ASC') {
		$text = "  &#9660;";
		$qs = "&direction=DESC";
	} else if ($direction == 'DESC') {
		$text = "  &#9650;";
		$qs = "&direction=ASC";
	}
	
	$options = array(
		'name' => 'todo_incomplete',
		'text' => elgg_echo('todo:label:incomplete') . ($secondary_tab === 'incomplete' ? $text : ''),
		'href' => "?status=incomplete{$qs}",
		'selected' => ($secondary_tab === 'incomplete'),
		'priority' => 1
	);
	
	$return[] = ElggMenuItem::factory($options);
	
	$options = array(
		'name' => 'todo_complete',
		'text' => elgg_echo('todo:label:complete') . ($secondary_tab === 'complete' ? $text : ''),
		'href' => "?status=complete{$qs}",
		'selected' => ($secondary_tab === 'complete'),
		'priority' => 2
	);
	
	$return[] = ElggMenuItem::factory($options);
	
	return $return;
}
