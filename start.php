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
	 * DEPENDS ON EMBEDENABLER PLUGIN (Not sure why at the moment)
	 */
	
	/*********************** TODO: (Code related) ************************/
	// - Cleaner way to handle different content attachments (views, callbacks.. yadda)
	// - Prettier everything (Rubric select, view rubric modal popup, etc.. )
	// - File permissions

	function todo_init() {
		global $CONFIG;
		
		// Lib
	    include $CONFIG->pluginspath . 'todo/lib/todo.php';

		// Assignment (todo) access levels
		define('TODO_ACCESS_LEVEL_LOGGED_IN', ACCESS_LOGGED_IN);
		define('TODO_ACCESS_LEVEL_ASSIGNEES_ONLY', -10);
		
		// Determine if optional plugins are enabled
		define('TODO_RUBRIC_ENABLED', is_plugin_enabled('rubricbuilder') ? true : false);
		define('TODO_CHANNELS_ENABLED', is_plugin_enabled('shared_access') ? true : false);
		
		// Relationship for assignees
		define('TODO_ASSIGNEE_RELATIONSHIP', 'assignedtodo');
		
		// Relationship for accepting todo's
		define('TODO_ASSIGNEE_ACCEPTED', 'acceptstodo');
		
		// Relationship for submissions 
		define('SUBMISSION_RELATIONSHIP', 'submittedto');
		
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
		elgg_extend_view('css','todo/css');
		elgg_extend_view('css','todo/ui-datepicker');
		
		// Extend Metatags (for js)
		elgg_extend_view('metatags','todo/metatags');
		
		// Add groups menu
		elgg_extend_view('groups/menu/links', 'todo/menu'); 
		
		// Extend groups profile page
		//elgg_extend_view('groups/tool_latest','todo/group_todos');
		elgg_extend_view('group-extender/sidebar','todo/group_todos', 2);
		
		// Extend topbar
		elgg_extend_view('elgg_topbar/extend','todo/todo_topbar');
		
		// Extend profile_ownerblock
		elgg_extend_view('profile_ownerblock/extend', 'todo/profile_link');
		
		// add the group pages tool option     
        add_group_tool_option('todo',elgg_echo('groups:enabletodo'),true);

		// Page handler
		register_page_handler('todo','todo_page_handler');

		// Add to tools menu
		//add_menu(elgg_echo("todo:title"), $CONFIG->wwwroot . 'pg/todo');

		// Add submenus
		register_elgg_event_handler('pagesetup','system','todo_submenus');
				
		// Register a handler for creating todos
		register_elgg_event_handler('create', 'object', 'todo_create_event_listener');

		// Register a handler for deleting todos
		register_elgg_event_handler('delete', 'object', 'todo_delete_event_listener');

		// Register a handler for assigning users to todos
		register_elgg_event_handler('assign','object','todo_assign_user_event_listener');
		
		// Register a handler for removing assignees from todos
		register_elgg_event_handler('unassign','object','todo_unassign_user_event_listener');
		
		// Plugin hook for write access
		register_plugin_hook('access:collections:write', 'all', 'todo_write_acl_plugin_hook');
		
		// Register an annotation handler for comments etc
		register_plugin_hook('entity:annotate', 'object', 'todo_annotate_comments');
		register_plugin_hook('entity:annotate', 'object', 'submission_annotate_comments');
		
		// Profile hook	
		register_plugin_hook('profile_menu', 'profile', 'todo_profile_menu');	
			
		// Set up url handlers
		register_entity_url_handler('todo_url','object', 'todo');
		register_entity_url_handler('todo_submission_url','object', 'todosubmission');

		// Register actions
		register_action('todo/createtodo', false, $CONFIG->pluginspath . 'todo/actions/createtodo.php');
		register_action('todo/deletetodo', false, $CONFIG->pluginspath . 'todo/actions/deletetodo.php');
		register_action('todo/edittodo', false, $CONFIG->pluginspath . 'todo/actions/edittodo.php');
		register_action('todo/accepttodo', false, $CONFIG->pluginspath . 'todo/actions/accepttodo.php');
		register_action('todo/unassign', false, $CONFIG->pluginspath . 'todo/actions/unassign.php');
		register_action('todo/createsubmission', false, $CONFIG->pluginspath . 'todo/actions/createsubmission.php');
		register_action('todo/deletesubmission', false, $CONFIG->pluginspath . 'todo/actions/deletesubmission.php');
		register_action('todo/sendreminder', false, $CONFIG->pluginspath . 'todo/actions/sendreminder.php');
		register_action('todo/completetodo', false, $CONFIG->pluginspath . 'todo/actions/completetodo.php');

		// Register type
		register_entity_type('object', 'todo');		

		return true;
		
	}

	function todo_page_handler($page) {
		global $CONFIG;
		
		elgg_push_breadcrumb(elgg_echo('todo:menu:alltodos'), "{$CONFIG->site->url}pg/todo/everyone");	
		
		switch ($page[0]) {
			case 'createtodo':
				// Just in case there is still cached data from an error
				clear_todo_cached_data();
				include $CONFIG->pluginspath . 'todo/pages/createtodo.php';
				break;
			case 'viewtodo':
				set_input("todo_guid", $page[1]);
				include $CONFIG->pluginspath . 'todo/pages/viewtodo.php';
				break;
			case 'edittodo':
				if ($page[1]) {
					set_input('todo_guid', $page[1]);
				}
				include $CONFIG->pluginspath . 'todo/pages/edittodo.php';
				break;
			case 'viewsubmission':
				set_input("submission_guid", $page[1]);
				include $CONFIG->pluginspath . 'todo/pages/viewsubmission.php';
				break;
			case 'owned':
				// Set page owner
				if (isset($page[1])) {
					set_input('username',$page[1]);
				}
				include $CONFIG->pluginspath . 'todo/pages/ownedtodos.php';
				break;
			case 'everyone':
				include $CONFIG->pluginspath . 'todo/pages/alltodos.php';
				break;
			case 'calendar':
				set_input('user', $page[1]);
				include $CONFIG->pluginspath . 'todo/pages/todocalendar.php';
				break;
			case 'assigned':
			default:
				if (isset($page[0])) {
					set_input('username',$page[0]);
				}
				include $CONFIG->pluginspath . 'todo/pages/assignedtodos.php';
				break;
		}
		
		return true;
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
			$todo_acl = create_access_collection(elgg_echo('todo:todo') . ":" . $object->title, $object->getGUID());
			if ($todo_acl) {
				$object->assignee_acl = $todo_acl;
				set_context('todo_acl');
				add_user_to_access_collection($object->owner_guid, $todo_acl);
				set_context($context);
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
			$context = get_context();
			set_context('todo_acl');
			register_error(delete_access_collection($object->assignee_acl));
			set_context($context);
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
			
			$context = get_context();
			set_context('todo_acl');
			$result = add_user_to_access_collection($user->getGUID(), $acl);
			set_context($context);			
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
		
			$context = get_context();
			set_context('todo_acl');
			remove_user_from_access_collection($user->getGUID(), $acl);
			set_context($context);	
		}
		return true;
	}
	
	/**
	 * Return the write access for the current todo if the user has write access to it.
	 */
	function todo_write_acl_plugin_hook($hook, $entity_type, $returnvalue, $params) {
		if (get_context() == 'todo_acl') {
			// get all todos if logged in
			if ($loggedin = get_loggedin_user()) {
				//$todos = get_users_todos($loggedin->getGUID());
				$todos = elgg_get_entities(array('types' => 'object', 'subtypes' => 'todo'));
				if (is_array($todos)) {
					foreach ($todos as $todo) {
						$returnvalue[$todo->assignee_acl] = elgg_echo('todo:todo') . ':' . $todo->title;
					}
				}
			}
		}
		return $returnvalue;
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
	function todo_profile_menu($hook, $entity_type, $return_value, $params) {
		global $CONFIG;

		$return_value[] = array(
			'text' => elgg_echo('todo'),
			'href' => "{$CONFIG->url}pg/todo/owned/{$params['owner']->username}",
		);

		return $return_value;
	}
	
	/**
	 * Setup todo submenus
	 */
	function todo_submenus() {
		global $CONFIG;
		$page_owner = page_owner_entity();
				 	
		// Default todo submenus
		if (get_context() == 'todo' && !($page_owner instanceof ElggGroup) && $page_owner == get_loggedin_user()) {	
			add_submenu_item(elgg_echo("todo:menu:yourtodos"), $CONFIG->wwwroot . 'pg/todo', 'userview');
			add_submenu_item(elgg_echo("todo:menu:assignedtodos"), $CONFIG->wwwroot . 'pg/todo/owned/', 'userview');
			add_submenu_item(elgg_echo("todo:menu:alltodos"), $CONFIG->wwwroot . 'pg/todo/everyone/', 'userview');			
		}
		
		// Groups context submenus
		if (get_context() == 'groups' && $page_owner instanceof ElggGroup) {
			if($page_owner->todo_enable != "no") {
				//add_submenu_item(sprintf(elgg_echo("todo:group"),$page_owner->name), $CONFIG->wwwroot . "pg/todo/owned/" . $page_owner->username);
			}
		}
	}
	
	/**
	 * Populates the ->getUrl() method for todo submission entities
	 *
	 * @param ElggEntity entity
	 * @return string request url
	 */
	function todo_submission_url($entity) {
		global $CONFIG;
		
		return $CONFIG->url . "pg/todo/viewsubmission/{$entity->guid}/";
	}
	
	/**
	 * Populates the ->getUrl() method for todo entities
	 *
	 * @param ElggEntity entity
	 * @return string request url
	 */
	function todo_url($entity) {
		global $CONFIG;
		
		return $CONFIG->url . "pg/todo/viewtodo/{$entity->guid}/";
	}
	
	register_elgg_event_handler('init', 'system', 'todo_init');
?>