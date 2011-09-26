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

// MIGRATION TODOS
// - Test assigning users (when userpicker works)
// - Test saving/updating w/ rubrics (when up to date)

elgg_register_event_handler('init', 'system', 'todo_init');

function todo_init() {	
	// Library
	elgg_register_library('elgg:todo', elgg_get_plugins_path() . 'todo/lib/todo.php');
	elgg_load_library('elgg:todo');

	// Assignment (todo) access levels
	define('TODO_ACCESS_LEVEL_LOGGED_IN', ACCESS_LOGGED_IN);
	define('TODO_ACCESS_LEVEL_ASSIGNEES_ONLY', -10);
	
	// Determine if optional plugins are enabled
	define('TODO_RUBRIC_ENABLED', elgg_is_active_plugin('rubrics') ? true : false);
	define('TODO_CHANNELS_ENABLED', elgg_is_active_plugin('shared_access') ? true : false);
	
	// Relationship for assignees
	define('TODO_ASSIGNEE_RELATIONSHIP', 'assignedtodo');
	
	// Relationship for accepting todo's
	define('TODO_ASSIGNEE_ACCEPTED', 'acceptstodo');
	
	// Relationship for submissions 
	define('SUBMISSION_RELATIONSHIP', 'submittedto');
	
	define('TODO_CONTENT_RELATIONSHIP', 'submitted_for_todo');
	
	// Relationship for complete todos
	define('COMPLETED_RELATIONSHIP', 'completedtodo');
	
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
	
	// Register todo JS
	$todo_js = elgg_get_simplecache_url('js', 'todo/todo');
	elgg_register_js('elgg.todo', $todo_js);
	
	// Register and load global todo JS
	$g_js = elgg_get_simplecache_url('js', 'todo/global');
	elgg_register_js('elgg.todo.global', $g_js);
	elgg_load_js('elgg.todo.global');
	
	// Need newer jquery form plugin (temporarily I hope)
	elgg_register_js('jquery.form', 'mod/todo/vendors/jquery/jquery.form.js');
		
	// Extend groups sidebar
	elgg_extend_view('page/elements/sidebar', 'todo/group_sidebar');
		
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
		
	// Profile hook	
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'todo_profile_menu');
	
	// Hook into views to post process river/item/wrapper for todo submissions
	elgg_register_plugin_hook_handler('view', 'river/elements/footer', 'todo_submission_river_rewrite');
	
	// Set up url handlers
	elgg_register_entity_url_handler('object', 'todo', 'todo_url');
	elgg_register_entity_url_handler('object', 'todosubmission', 'todo_submission_url');
	elgg_register_entity_url_handler('object', 'todosubmissionfile', 'submission_file_url');
	
	// Hook for site menu
	elgg_register_plugin_hook_handler('register', 'menu:topbar', 'todo_topbar_menu_setup', 9000);
	
	// Handler to prepare main todo menu
	elgg_register_plugin_hook_handler('register', 'menu:todo-listing-main', 'todo_main_menu_setup');

	// Handler to prepare secondary todo menu
	elgg_register_plugin_hook_handler('register', 'menu:todo-listing-secondary', 'todo_secondary_menu_setup');
	
	// Prepare dashboard menus
	elgg_register_plugin_hook_handler('register', 'menu:todo-dashboard-listing-main', 'todo_dashboard_main_menu_setup');
	
	// Todo entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'todo_entity_menu_setup');
	
	// Submission entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'submission_entity_menu_setup');
	
	// Generic entity menu handler
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'todo_content_entity_menu_setup');
	
	// Remove comments from todo complete river entries
	elgg_register_plugin_hook_handler('register', 'menu:river', 'submission_river_menu_setup');
	
	// Interrupt output/access view
	elgg_register_plugin_hook_handler('view', 'output/access', 'todo_output_access_handler');
	
	// Register handler for todo submission files 
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'submission_file_icon_url_override');

	// Register actions
	$action_base = elgg_get_plugins_path() . "todo/actions/todo";
	elgg_register_action('todo/save', "$action_base/save.php");
	elgg_register_action('todo/delete', "$action_base/delete.php");
	elgg_register_action('todo/accept', "$action_base/accept.php");
	elgg_register_action('todo/assign', "$action_base/assign.php");
	elgg_register_action('todo/unassign', "$action_base/unassign.php");
	elgg_register_action('todo/sendreminder', "$action_base/sendreminder.php");
	elgg_register_action('todo/complete', "$action_base/complete.php");
	elgg_register_action('todo/open', "$action_base/open.php");
	elgg_register_action('todo/upload', "$action_base/upload.php");
	elgg_register_action('todo/checkcontent', "$action_base/checkcontent.php");
	
	$action_base = elgg_get_plugins_path() . "todo/actions/submission";
	elgg_register_action('submission/save', "$action_base/save.php");
	elgg_register_action('submission/delete', "$action_base/delete.php");


	// Register type
	elgg_register_entity_type('object', 'todo');		

	// Register one once for todos
	run_function_once("todo_run_once");
	
	return true;	
}

/**
 * Todo page handler
 *
 * URLs take the form of
 *  All todos:       todo/all
 *  User's todos:    todo/owner/<username>
 *  Assigned todos:  todo/assigned/<username>
 *  View todo:       todo/view/<guid>/<title>
 *  View submission	 todo/view/submission/<guid>
 *  New todo:        todo/add/<guid>
 *  Edit todo:       todo/edit/<guid>
 *  Group todo:      todo/group/<guid>/owner
 *  Calendar feed    todo/calendar/<username>
 *
 * AJAX:
 *  todo/loadassignees - get assignee list via ajax
 *
 * Title is ignored
 * 
 * @param array $page
 * @return NULL
 */
function todo_page_handler($page) {	
	elgg_push_breadcrumb(elgg_echo('todo'), elgg_get_site_url() . "todo/dashboard");	
	
	// Load JS lib
	elgg_load_js('elgg.todo');
	
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
		case 'dashboard':
			$params['title'] = 'To Do Dashboard';
			$params['filter'] = FALSE;
			$user = get_user_by_username($page[1]);
			if (!$user) {
				$user = elgg_get_logged_in_user_entity();
			}		
			elgg_set_page_owner_guid($user->guid);
			
			if (elgg_get_page_owner_guid() == elgg_get_logged_in_user_guid()) {
				elgg_register_title_button();
			}
			
			elgg_push_breadcrumb($user->name, 'todo/dashboard/' . $user->username);
			
			$params['content'] = elgg_view('todo/dashboard');
			break;
		case 'add':
			gatekeeper();
			group_gatekeeper();
			$params = todo_get_page_content_edit($page_type, $page[1]);
			break;
		case 'view':
			gatekeeper();
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
			elgg_set_page_owner_guid($user->guid);
			set_input('username',$user->username);
			$params = todo_get_page_content_list($page_type, $user->guid);
			break;
		case 'group':
			gatekeeper();
			group_gatekeeper();
			//$params = todo_get_page_content_list('owner', $page[1]);
			$group = get_entity($page[2]);
			if (elgg_instanceof($group, 'group')) {
				elgg_push_breadcrumb($group->name, 'todo/group/dashboard/' . $group->guid);
				elgg_set_page_owner_guid($group->guid);
				elgg_register_title_button();
				$params['title'] = 'To Do Dashboard';
				$params['filter'] = FALSE;
				$params['content'] = elgg_view('todo/dashboard');
			} else {
				forward('todo/dashboard');
			}
			break;
		case 'assigned':
			gatekeeper();
			group_gatekeeper();
			$user = get_user_by_username($page[1]);
			if (!$user) {
				$user = elgg_get_logged_in_user_entity();
			}
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
			echo todo_get_page_content_assignees($guid);
			exit;
			break;
	}
	
	// Custom sidebar (none at the moment)
	$params['sidebar'] .= elgg_view('todo/sidebar');

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($params['title'], $body);
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
		$context = elgg_get_context();
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

			// Set permissions for any attached content (files)
			$contents = unserialize($object->content);
			foreach ($contents as $content) {
				$guid = (int)$content;
				$entity = get_entity($guid);
				if (elgg_instanceof($entity, 'object')) {
					// If content is a todosubmissionfile entitity, set its ACL to that of the submission
					if (elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
						$entity->access_id = $submission_acl;
					}

					// Set up a todo content relationship for the entity
					$r = add_entity_relationship($entity->guid, TODO_CONTENT_RELATIONSHIP, $todo->guid);
					
					// Set content tags to todo suggested tags
					todo_set_content_tags($entity, $todo);

					$entity->save();
				} 
			}
						
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

		// Reset permissions for any attached content (files)
		$contents = unserialize($object->content);
		foreach ($contents as $content) {
			$guid = (int)$content;
			$entity = get_entity($guid);
			if (elgg_instanceof($entity, 'object')) {
				// If content is a valid entitity, set its ACL back to private
				if (elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
					$entity->access_id = ACCESS_PRIVATE;
				}
				
				// Remove todo content relationship
				remove_entity_relationship($entity->guid, TODO_CONTENT_RELATIONSHIP, $todo->guid);
				
				$entity->save();
			} 
		}
		
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
		$url = "todo/dashboard/{$params['entity']->username}";
		$item = new ElggMenuItem('todo', elgg_echo('todo'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->todo_enable == "yes") {
			$url = "todo/group/dashboard/{$params['entity']->guid}/owner";
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
	$entity = get_entity($params['vars']['item']->object_guid);
	if (elgg_instanceof($entity, 'object', 'todosubmission')) {	
		return ' ';
	}
}

/**
 * Setup todo submenus
 */
function todo_submenus() {
	$page_owner = elgg_get_page_owner_entity();

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
	return elgg_get_site_url() . "todo/view/submission/{$entity->guid}/";
}

/*
 * Populates the ->getUrl() method for submission file objects
 *
 * @param ElggEntity $entity File entity
 * @return string File URL
 */
function submission_file_url($entity) {
	$title = $entity->title;
	$title = elgg_get_friendly_title($title);
	return "file/view/" . $entity->getGUID() . "/" . $title;
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
		if (!has_user_accepted_todo($user->getGUID(), $todo->getGUID()) && !$todo->manual_complete) {
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
		'text' => $text . elgg_echo('todo'),
		'href' =>  'todo/assigned/' . elgg_get_logged_in_user_entity()->username,
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
	
	$direction = get_input('direction', 'DESC');

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

/**
 * Todo main menu setup
 */
function todo_dashboard_main_menu_setup($hook, $type, $return, $params) {	
	// Set up main nav for todo listings
	$main_tab = get_input('todo_main_tab', 'all');
	
	$user = elgg_get_page_owner_entity();
		
	if (!elgg_instanceof($user, 'user') && !elgg_instanceof($user, 'group')) {
		$user = elgg_get_logged_in_user_entity();
	}
	
	if ($user == elgg_get_logged_in_user_entity()) {
		$by = elgg_echo('todo:label:me');
		
		$options = array(
			'name' => 'all',
			'text' => elgg_echo("all"),
			'class' => 'todo-ajax-list',
			'item_class' => 'todo-ajax-list-item',
			'href' => 'ajax/view/todo/list?type=all',
			'priority' => 1,
		);

		$return[] = ElggMenuItem::factory($options);
	} else {
		$by = $user->name;
	}
	
	
	if (elgg_instanceof($user, 'user')) {
		$options = array(
			'name' => 'assigned',
			'text' => elgg_echo("todo:label:assignedto", array($by)),
			'class' => 'todo-ajax-list',
			'item_class' => 'todo-ajax-list-item',
			'href' => 'ajax/view/todo/list?type=assigned&u=' . $user->guid,
			'priority' => 2
		);
	}
	
	$return[] = ElggMenuItem::factory($options);
	
 	$options = array(
		'name' => 'owned',
		'text' => elgg_echo("todo:label:assignedby", array($by)),
		'class' => 'todo-ajax-list',
		'item_class' => 'todo-ajax-list-item',
		'href' => 'ajax/view/todo/list?type=owned&u=' . $user->guid,
		'priority' => 3
	);
	
	$return[] = ElggMenuItem::factory($options);
	
	return $return;
}


/**
 * Add todo specific links/info to entity menu
 */
function todo_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}
	
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'todo') {
		return $return;
	}
	
	$entity = $params['entity'];


	// Add status
	if ($entity->canEdit()) {
		if ($entity->status == TODO_STATUS_DRAFT) {
			$status_text = elgg_echo('todo:status:draft'); 
		} else if ($entity->status == TODO_STATUS_PUBLISHED) {
			$status_text = elgg_echo('todo:status:published');
		}
				
		$options = array(
			'name' => 'todo_status',
			'text' => "<span>$status_text</span>",
			'href' => false,
			'priority' => 150,
		);
		$return[] = ElggMenuItem::factory($options);
	}
	
	// Different actions depending if user is assignee or not
	$user_guid = elgg_get_logged_in_user_guid();
	// Is assignee
	if (is_todo_assignee($entity->getGUID(), $user_guid)) { 
		// Add accept button
		if (has_user_accepted_todo($user_guid, $entity->getGUID())) {
			$text = "<span class='accepted'>✓ Accepted</span>";
		} else {
			$text = "<span class='unviewed'>";
			$text .= elgg_view("output/confirmlink", array(
				'href' => elgg_get_site_url() . "action/todo/accept?guid=" . $entity->getGUID(),
				'text' => 'Accept',
				'confirm' => elgg_echo('todo:label:acceptconfirm'),
				'class' => 'elgg-button elgg-button-action'
			));
			$text .= "</span>";
		}
		$options = array(
			'name' => 'todo_accept',
			'text' => $text,
			'href' => false,
			'priority' => 1,
		);
		$return[] = ElggMenuItem::factory($options);
		
		// Full view only
		if (elgg_in_context('todo_full_view')) {
			// If user has submitted
			if (has_user_submitted($user_guid, $entity->getGUID()) && $submission = get_user_submission($user_guid, $entity->getGUID())) {
				$options = array(
					'name' => 'todo_view_submission',
					'text' => elgg_echo("todo:label:viewsubmission"),
					'href' => $submission->getURL(),
					'priority' => 999,
				);
				$return[] = ElggMenuItem::factory($options);
			} else { // User has not submitted
				if ($entity->manual_complete) {
					$options = array(
						'name' => 'todo_closed',
						'text' => '<strong>' . elgg_echo("todo:status:closed") . '</strong>',
						'href' => false,
						'priority' => 1000,
					);
					$return[] = ElggMenuItem::factory($options);
				} else {
					elgg_load_js('lightbox');
					
					// If we need to return something for this todo, the complete link will point to the submission form
					$id = $entity->return_required ? '' : 'empty';
					$options = array(
						'name' => 'todo_create_submission',
						'text' => elgg_echo("todo:label:completetodo"),
						'href' => '#todo-submission-dialog',
						'priority' => 1000,
						//'link_class' => "elgg-button elgg-button-action todo-create-submission $id",
						'link_class' => "elgg-button elgg-button-action todo-lightbox $id",
					);
					$return[] = ElggMenuItem::factory($options);
				}
			}
		}
	} else { // Not assignee
		// full view only
		if (elgg_in_context('todo_full_view')) {
			if ($entity->manual_complete != true && $entity->owner_guid != elgg_get_logged_in_user_guid()) {
			
				$text = elgg_view("output/confirmlink", array(
					'href' => elgg_get_site_url() . "action/todo/assign?todo_guid=" . $entity->getGUID(),
					'text' => elgg_echo('todo:label:signup'),
					'confirm' => elgg_echo('todo:label:signupconfirm'),
					'class' => 'elgg-button elgg-button-action'
				));
			
				$options = array(
					'name' => 'todo_signup',
					'text' => $text,
					'href' => false,
					'priority' => 997,
				);
				$return[] = ElggMenuItem::factory($options);		
			}
		}
	}
	
	// Close todo button, owners only
	if (elgg_in_context('todo_full_view') && $entity->canEdit()) {
		if ($entity->manual_complete) {
			/*
            $options = array(
				'name' => 'todo_closed',
				'text' => '<strong>' . elgg_echo("todo:status:closed") . '</strong>',
				'href' => false,
				'priority' => 1000,
			);
			*/
			$text = elgg_view("output/confirmlink", array(
				'href' => "action/todo/open?guid=" . $entity->getGUID(),
				'text' => elgg_echo('todo:label:flagopen'),
				'confirm' => elgg_echo('todo:label:flagopenconfirm'),
				'class' => 'elgg-button elgg-button-action'
            ));
			$options = array(
				'name' => 'todo_open',
				'text' => $text,
				'href' => false,
				'priority' => 1000,
			);
			$return[] = ElggMenuItem::factory($options);
        } else {
			$text = elgg_view("output/confirmlink", array(
				'href' => "action/todo/complete?guid=" . $entity->getGUID(),
				'text' => elgg_echo('todo:label:flagcomplete'),
				'confirm' => elgg_echo('todo:label:flagcompleteconfirm'),
				'class' => 'elgg-button elgg-button-action'
            ));
			$options = array(
				'name' => 'todo_complete',
				'text' => $text,
				'href' => false,
				'priority' => 1000,
			);
			$return[] = ElggMenuItem::factory($options);
		}
	}
	
	// Show the duelabel 
	if (!elgg_in_context('todo_full_view') && get_input('status') != 'complete') {
		$text = elgg_view('todo/duelabel', array('entity' => $entity));
		$options = array(
			'name' => 'todo_duelabel',
			'text' => $text,
			'href' => false,
			'priority' => 998,
		);
		$return[] = ElggMenuItem::factory($options);
	}
		
	return $return;
}


/**
 * Customize todo submission entity menu
 */
function submission_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}
	
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'submission') {
		return $return;
	}
	
	$entity = $params['entity'];
	
	// Nuke menu
	$return = array();
	
	if ($entity->canEdit()) {
		// Add delete link
		$options = array(
			'name' => 'delete',
			'text' => elgg_view_icon('delete'),
			'title' => elgg_echo('delete:this'),
			'href' => "action/$handler/delete?guid={$entity->getGUID()}",
			'confirm' => elgg_echo('deleteconfirm'),
			'priority' => 300,
		);
		$return[] = ElggMenuItem::factory($options);
	}
			
	return $return;
}

/**
 * Customize entity menu, display link to todo if entity was submitted as content
 */
function todo_content_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}
	
	if (!elgg_is_logged_in()) {
		return $return;
	}
	
	$ia = elgg_get_ignore_access();
	elgg_set_ignore_access(TRUE);
		
	$entity = $params['entity'];
	
	$options = array(
		'relationship' => TODO_CONTENT_RELATIONSHIP,
		'relationship_guid' => $entity->guid,
		'inverse_relationship' => FALSE,
		'types' => array('object'),
		'subtypes' => array('todo'),
		'limit' => 0,
		'offset' => 0,
		'count' => TRUE,
	);
	
	// Grab count
	$todo_count = elgg_get_entities_from_relationship($options);
	
	$options['count'] = FALSE;
	
	// Grab todo's
	$todos = elgg_get_entities_from_relationship($options);
	
	
	// If this item was submitted to at least one todo
	if ($todo_count) {
		
		// If only submitted to one todo
		if ($todo_count == 1) {
			$text = elgg_echo('todo:label:submittedforsingle');
		} else { // Multiple todo's
			$text = elgg_echo('todo:label:submittedformultiple', array($todo_count));
		}	
			
		$toggle_box = "<div id='todo-entity-info-{$entity->guid}' class='todo-entity-info'>";
		
		foreach($todos as $todo) {
			$container = $todo->getContainerEntity();
			$toggle_box .= "<a class='multi-todo' href='{$todo->getURL()}'>{$todo->title} ({$container->name})</a>";
		}
		$toggle_box .= "</div>";

		$options = array(
			'name' => "submitted_for_multiple_todos",
			'text' =>  $text . $toggle_box,
			'href' => '#todo-entity-info-' . $entity->guid,
			'id' => 'todo-entity-' . $entity->guid,
			'class' => 'todo-show-info',
			//'rel' => 'toggle',
			'priority' => 2000,
		);
		
			
		$return[] = ElggMenuItem::factory($options);
	}
	elgg_set_ignore_access($ia);

	return $return;
}

/**
 * Add the comment and like links to river actions menu
 */
function submission_river_menu_setup($hook, $type, $return, $params) {
	if (elgg_is_logged_in()) {
		$item = $params['item'];
		$object = $item->getObjectEntity();
		if (elgg_instanceof($object, 'object', 'todosubmission')) {
			return false;
		}
	}

	return $return;
}

/**
 * Hook to allow output/access to display 'Assignees Only'
 */
function todo_output_access_handler($hook, $type, $return, $params) {
	if ($params['vars']['entity']) {
		if ($params['vars']['entity']->getSubtype() == 'todo' && $params['vars']['entity']->access_id != ACCESS_LOGGED_IN) {
			$return = "<span class='elgg-access'>" . elgg_echo('todo:label:assigneesonly') . "</span>";
		}
	}
	return $return;
}

/**
 * Override the default entity icon for files
 *
 * Plugins can override or extend the icons using the plugin hook: 'file:icon:url', 'override'
 *
 * @return string Relative URL
 */
function submission_file_icon_url_override($hook, $type, $returnvalue, $params) {
	$file = $params['entity'];
	$size = $params['size'];
	if (elgg_instanceof($file, 'object', 'todosubmissionfile')) {

		// thumbnails get first priority
		if ($file->thumbnail) {
			return "mod/file/thumbnail.php?file_guid=$file->guid&size=$size";
		}

		$mapping = array(
			'application/excel' => 'excel',
			'application/msword' => 'word',
			'application/pdf' => 'pdf',
			'application/powerpoint' => 'ppt',
			'application/vnd.ms-excel' => 'excel',
			'application/vnd.ms-powerpoint' => 'ppt',
			'application/vnd.oasis.opendocument.text' => 'openoffice',
			'application/x-gzip' => 'archive',
			'application/x-rar-compressed' => 'archive',
			'application/x-stuffit' => 'archive',
			'application/zip' => 'archive',

			'text/directory' => 'vcard',
			'text/v-card' => 'vcard',

			'application' => 'application',
			'audio' => 'music',
			'text' => 'text',
			'video' => 'video',
		);

		$mime = $file->mimetype;
		if ($mime) {
			$base_type = substr($mime, 0, strpos($mime, '/'));
		} else {
			$mime = 'none';
			$base_type = 'none';
		}

		if (isset($mapping[$mime])) {
			$type = $mapping[$mime];
		} elseif (isset($mapping[$base_type])) {
			$type = $mapping[$base_type];
		} else {
			$type = 'general';
		}

		if ($size == 'large') {
			$ext = '_lrg';
		} else {
			$exit = '';
		}
		
		$url = "mod/file/graphics/icons/{$type}{$ext}.gif";
		$url = elgg_trigger_plugin_hook('file:icon:url', 'override', $params, $url);
		return $url;
	}
}

/**
 * Register entity type objects, subtype todosubmissionfile as
 * ElggFile.
 *
 * @return void
 */
function todo_run_once() {
	// Register a class
	add_subtype("object", "todosubmissionfile", "ElggFile");
	
	// Just in case this metadata doesn't exist yet (It should)
	$dummy = new ElggObject();
	$dummy->manual_complete = 1;
	$dummy->complete = 1;
	$dummy->one = 1;
	
	$dummy->save();
	$dummy->delete();	
}