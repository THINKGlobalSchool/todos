<?php
	/**
	 * Todo English language translation
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
$english = array(
	
	// Generic Title
	'todo:title' => 'Todo\'s',
	'item:object:todo' => 'Todo\'s',
	
	// Page titles 
	'todo:title:yourtodos'	=> 'Your Todo\'s',
	'todo:title:create' => 'Create New Todo',
	'todo:title:edit' => 'Edit Todo',
	
	// Menu items
	'todo:menu:yourtodos' => 'Your Todos',
	'todo:menu:createtodo' => 'Create Todo',
	'todo:menu:admin' => 'todo Admin',
		
	// Labels 

	// Messages
	'todo:success:create' => 'Todo successfully submitted',
	'todo:success:edit' => 'Todo successfully edited',
	'todo:success:delete' => 'Todo successfully deleted',
	'todo:error:titleblank' => 'Todo title cannot be blank',
	'todo:error:create' => 'There was an error creating your Todo',
	'todo:error:edit' => 'There was an error editing the Todo',
	'todo:error:delete' => 'There was an error deleting the Todo',
	
	// Other content
	'todo:strapline' => '%s',

);

add_translation('en',$english);

?>