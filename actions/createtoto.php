<?php
	/**
	 * Todo Create Action
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// must be logged in
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$title 			= get_input('todo_title');
	$description 	= get_input('todo_description');
	$tags 			= string_to_tag_array(get_input('todo_tags'));
	
	// Cache to session
	$_SESSION['user']->todo_title = $title;
	$_SESSION['user']->todo_description = $description;
	$_SESSION['user']->todo_tags = $tags;
	
	// Process
	if (empty($title)) {
		register_error(elgg_echo('todo:error:titleblank'));
		forward($_SERVER['HTTP_REFERER']);
	}
	
	$todo = new ElggObject();
	$todo->subtype 		= "todo";
	$todo->title 		= $title;
	$todo->description 	= $description;
	$todo->access_id 	= ACCESS_PRIVATE; //?
	$todo->tags 		= $tags;
	
	// Save
	if (!$todo->save()) {
		register_error(elgg_echo("todo:error:create"));		
		forward($_SERVER['HTTP_REFERER']);
	}
	
	
	// Clear Cached info
	remove_metadata($_SESSION['user']->guid,'todo_title');
	remove_metadata($_SESSION['user']->guid,'todo_description');
	remove_metadata($_SESSION['user']->guid,'todo_tags');

	// Save successful, forward to index
	system_message(elgg_echo('todo:success:create'));
	forward('pg/todo');
?>