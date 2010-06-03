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
	// - Caching on an oops when editing or creating (Already caching, just
	// need to handle retrieving, too lazy right now)
	// - Which fields on edit form are required? Only title is checked ATM
	// - What happens on delete? Do I need to remove relationships?  
	// - Comments on fullview should use comment framework
	// - Groups have group members assigned, not the group
	// - Permissions... logged in works, but how to do 'assignees'?
	// - Ask Mike
	//		- Need seperate search area? Can just search from top
	
	
	
	
	function todo_init() {
		global $CONFIG;
		
		// Lib
	    include $CONFIG->pluginspath . 'todo/lib/todo.php';

		// Assignment (todo) access levels
		define('TODO_ACCESS_LEVEL_LOGGED_IN', ACCESS_LOGGED_IN);
		define('TODO_ACCESS_LEVEL_ASSIGNEES_ONLY', -10);
		
		// Determine if optional plugins are enabled
		define('TODO_RUBRIC_ENABLED', is_plugin_enabled('rubricbuilder') ? true : false);
		
		// Relationship for assignees
		define('TODO_ASSIGNEE_RELATIONSHIP', 'assignedtodo');
		
		// Relationship for submissions 
		define('SUBMISSION_RELATIONSHIP', 'submittedto');
		
		// View Modes
		define('TODO_MODE_ASSIGNER', 0);
		define('TODO_MODE_ASSIGNEE', 1);
		
		get_todo_groups_array();
		
		// Extend CSS
		elgg_extend_view('css','todo/css');
		
		// Extend Metatags (for js)
		elgg_extend_view('metatags','todo/metatags');
		
		// Page handler
		register_page_handler('todo','todo_page_handler');

		// Add to tools menu
		add_menu(elgg_echo("todo:title"), $CONFIG->wwwroot . 'pg/todo');

		// Add submenus
		register_elgg_event_handler('pagesetup','system','todo_submenus');
		
		// Set up url handlers
		register_entity_url_handler('todo_url','object', 'todo');
		register_entity_url_handler('todo_submission_url','object', 'todo_submission');

		// Register actions
		register_action('todo/createtodo', false, $CONFIG->pluginspath . 'todo/actions/createtodo.php');
		register_action('todo/deletetodo', false, $CONFIG->pluginspath . 'todo/actions/deletetodo.php');
		register_action('todo/edittodo', false, $CONFIG->pluginspath . 'todo/actions/edittodo.php');
		register_action('todo/unassign', false, $CONFIG->pluginspath . 'todo/actions/unassign.php');
		register_action('todo/createsubmission', false, $CONFIG->pluginspath . 'todo/actions/createsubmission.php');
		/*
		register_action('todo/deletesubmission', false, $CONFIG->pluginspath . 'todo/actions/deletetodo.php');
		register_action('todo/editsubmission', false, $CONFIG->pluginspath . 'todo/actions/edittodo.php');
		*/
		
		// Register type
		register_entity_type('object', 'todo');		

		return true;
	}

	function todo_page_handler($page) {
		global $CONFIG;

		
		switch ($page[0])
		{
			case 'createtodo':
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
			case 'owned':
				include $CONFIG->pluginspath . 'todo/pages/ownedtodos.php';
				break;
			case 'everyone':
				include $CONFIG->pluginspath . 'todo/pages/alltodos.php';
				break;
			case 'assigned':
			default:
				include $CONFIG->pluginspath . 'todo/pages/assignedtodos.php';
				break;
		}
		
		return true;
	}

	function todo_submenus() {
		global $CONFIG;
		
		if (get_context() == 'todo') {
			add_submenu_item(elgg_echo("todo:menu:yourtodos"), $CONFIG->wwwroot . 'pg/todo');
			add_submenu_item(elgg_echo("todo:menu:assignedtodos"), $CONFIG->wwwroot . 'pg/todo/owned/');
			add_submenu_item(elgg_echo("todo:menu:alltodos"), $CONFIG->wwwroot . 'pg/todo/everyone/');			
			add_submenu_item(elgg_echo("todo:menu:createtodo"), $CONFIG->wwwroot . 'pg/todo/createtodo/');
		}
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
	
	/**
	 * Populates the ->getUrl() method for todo submission entities
	 *
	 * @param ElggEntity entity
	 * @return string request url
	 */
	function todo_submission_url($entity) {
		global $CONFIG;
		
		return $CONFIG->url . "pg/todo/view/{$entity->guid}/";
	}
	
	


	register_elgg_event_handler('init', 'system', 'todo_init');
?>