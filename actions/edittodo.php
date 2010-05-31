<?php
	/**
	 * Todo Edit Action
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Only admins can delete
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$guid 			= get_input('todo_guid');
	$title 			= get_input('todo_title');
	$description 	= get_input('todo_description');
	$tags 			= string_to_tag_array(get_input('todo_tags'));
	
	$todo = get_entity($guid);
	
	if ($todo->getSubtype() == "todo" && $todo->canEdit()) {
		
		// Cache to session
		$_SESSION['user']->todo_title = $title;
		$_SESSION['user']->todo_description = $description;
		$_SESSION['user']->todo_tags = $tags;

		// Process
		if (empty($title)) {
			register_error(elgg_echo('todo:error:titleblank'));
			forward($_SERVER['HTTP_REFERER']);
		}
		
		$todo->title 		= $title;
		$todo->description 	= $description;
		$todo->tags			= $tags;
		
		// Save
		if (!$todo->save()) {
			register_error(elgg_echo("todo:error:create"));		
			forward($_SERVER['HTTP_REFERER']);
		}

		// Clear cached info
		remove_metadata($_SESSION['user']->guid,'todo_title');
		remove_metadata($_SESSION['user']->guid,'todo_description');
		remove_metadata($_SESSION['user']->guid,'todot_tags');

		// Save successful, forward to index
		system_message(elgg_echo('todo:success:edit'));
		forward('pg/todo');	
	}
	
	register_error(elgg_echo("todo:error:edit"));		
	forward($_SERVER['HTTP_REFERER']);

?>