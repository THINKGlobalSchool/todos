<?php
	/**
	 * Todo edit form
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// Check if we've got an entity, if so, we're editing.
	if (isset($vars['entity'])) {
		
		$action 		= "todo/edittodo";
		$title 		 	= $vars['entity']->title;
		$description 	= $vars['entity']->description;
		$tags 			= $vars['entity']->tags;	
		
		$container_hidden = elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $vars['container_guid']));
		$entity_hidden  = elgg_view('input/hidden', array('internalname' => 'todo_guid', 'value' => $vars['entity']->getGUID()));
		
		
	} else {
	// No entity, creating new one
		$action = "todo/createtodo";
		$title = "";
		$description = "";
		$type = "";
		$tags = "";
		
		$container_hidden = "";
		$entity_hidden = "";
	}
	
	if (empty($description)) {
		$description = $vars['user']->todo_description;
		if (!empty($description)) {
			$title = $vars['user']->todo_title;
			$tags = $vars['user']->todo_tags;
			$type = $vars['user']->todo_type;
		}
	}
	
	
	// Labels/Input
	$title_label = elgg_echo('title');
	$title_input = elgg_view('input/text', array('internalname' => 'todo_title', 'value' => $title));
	
	$description_label = elgg_echo("description");
	$description_input = elgg_view("input/longtext", array('internalname' => 'todo_description', 'value' => $description));
	
	$tag_label = elgg_echo('tags');
    $tag_input = elgg_view('input/tags', array('internalname' => 'todo_tags', 'value' => $tags));
	
	$submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('submit')));
		
	// Build Form Body
	$form_body = <<<EOT
	
	<div class='contentWrapper'>
		<p>
			<label>$title_label</label><br />
	        $title_input
		</p>
		<p>
			<label>$description_label</label><br />
	        $description_input
		</p>
		<p>
			<label>$tag_label</label><br />
	        $tag_input
		</p>
		<p>
			$submit_input
			$container_hidden
			$entity_hidden
		</p>
	</div>
	
EOT;

	echo elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'body' => $form_body, 'internalid' => 'todo_post_forms'));
?>