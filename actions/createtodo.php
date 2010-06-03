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
	
	// TODO: What's required?
	
	// must be logged in
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$title 				= get_input('title');
	$description 		= get_input('description');
	$tags 				= string_to_tag_array(get_input('tags'));
	$due_date			= get_input('due_date');
	$assignees			= get_input('assignee_guids');
	$return_required	= get_input('return_required');
	$rubric_select		= get_input('rubric_select');
	$rubric_guid		= get_input('rubric_guid');
	$access_level		= get_input('access_level');
	
	
	/*&
	print_r_html("Title: " . $title . "<br />");		
	print_r_html("Desc: " . $description . "<br />"); 		
	print_r_html("Tags: " . $tags) . "<br />"; 				
	print_r_html("Due: " . $due_date . "<br />");			
	print_r_html("Assignees: " . $assignees . "<br />");
	print_r_html($assignees);	
	print_r_html("Required" . $return_required . "<br />");	
	var_dump($return_required);
	print_r_html("Rubric Present: " . $rubric_select . "<br />");		
	print_r_html("Rubric Guid: " . $rubric_guid . "<br />");		
	print_r_html("Access: " . $access_level . "<br />");
	*/
	
	
	
	// Cache to session
	$_SESSION['user']->todo_title = $title;
	$_SESSION['user']->todo_description = $description;
	$_SESSION['user']->todo_tags = $tags;
	$_SESSION['user']->todo_due_date = $due_date;
	$_SESSION['user']->todo_assignees = $assignees;
	$_SESSION['user']->todo_return_required = $return_required;
	$_SESSION['user']->todo_rubric_select = $rubric_select;
	$_SESSION['user']->todo_rubric_guid = $rubric_guid;
	$_SESSION['user']->todo_access_level = $access_level;

	
	// Check values
	if (empty($title)) {
		register_error(elgg_echo('todo:error:titleblank'));
		forward($_SERVER['HTTP_REFERER']);
	}
	
	$todo = new ElggObject();
	$todo->subtype 		= "todo";
	$todo->title 		= $title;
	$todo->description 	= $description;
	$todo->access_id 	= $access_level; 
	$todo->tags 		= $tags;
	$todo->due_date		= $due_date;
	//$todo->assignees	= serialize($assignees); // Store the array of guids just in case.. No point.
	$todo->return_required = $return_required;
	
	if ($rubric_select) 
		$todo->rubric_guid = $rubric_guid;
	
	
	// Save
	if (!$todo->save()) {
		register_error(elgg_echo("todo:error:create"));		
		forward($_SERVER['HTTP_REFERER']);
	}
	
	// Set up relationships for asignees, can be users or groups (multiple)
	if (is_array($assignees)) {
		foreach ($assignees as $assignee) {
			// This states: 'Jeff' is 'assignedtodo' 'Task/Assignment' 
			// Or, groups 'Group X' 'assignedtodo' 'Task/Assignment'
			add_entity_relationship($assignee, TODO_ASSIGNEE_RELATIONSHIP, $todo->getGUID());
		}
	}
	
	// Clear Cached info
	clear_todo_cached_data();

	// Save successful, forward to index
	system_message(elgg_echo('todo:success:create'));
	forward('pg/todo');
?>