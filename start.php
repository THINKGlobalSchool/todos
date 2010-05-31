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
	
	function todo_init() {
		global $CONFIG;

		// Extend CSS
		extend_view('css','todo/css');
		
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
		
		return true;
	}

	function todo_page_handler($page) {
		global $CONFIG;

		
		switch ($page[0])
		{
			case 'create':
				include $CONFIG->pluginspath . 'todo/pages/create.php';
				break;
			case 'view':
				set_input("todo_guid", $page[1]);
				include $CONFIG->pluginspath . 'todo/pages/view.php';
				break;
			case 'edit':
				if ($page[1]) {
					set_input('todo_guid', $page[1]);
				}
				include $CONFIG->pluginspath . 'todo/pages/edit.php';
			default:
				include $CONFIG->pluginspath . 'todo/pages/index.php';
				break;
		}
		
		return true;
	}

	function todo_submenus() {
		global $CONFIG;
		
		if (get_context() == 'todo') {
			add_submenu_item(elgg_echo("todo:menu:yourtodos"), $CONFIG->wwwroot . 'pg/todo');
			add_submenu_item(elgg_echo("todo:menu:createtodo"), $CONFIG->wwwroot . 'pg/todo/create/');
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
		
		return $CONFIG->url . "pg/todo/view/{$entity->guid}/";
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