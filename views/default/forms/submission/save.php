<?php
/**
 * Submission form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Check if we've got an entity
if (isset($vars['entity'])) {
		
	$container_hidden = elgg_view('input/hidden', array(
		'id' => 'container-guid',
		'name' => 'container_guid', 
		'value' => $vars['container_guid']
	));
	$entity_hidden  = elgg_view('input/hidden', array(
		'id' => 'todo-guid',
		'name' => 'todo_guid', 
		'value' => $vars['entity']->getGUID()
	));

	if (empty($description)) {
		$description = $vars['user']->todo_description;
		if (!empty($description)) {
			$title = $vars['user']->todo_title;
			$tags = $vars['user']->todo_tags;
			$type = $vars['user']->todo_type;
		}
	}

	// Content Menu Items
	$menu_items .= "<a href='#submission-add-link-container' class='submission-content-menu-item'>" . elgg_echo('todo:label:addlink') . "</a><br />";
	$menu_items .= "<a href='#submission-add-file-container' class='submission-content-menu-item'>" . elgg_echo('todo:label:addfile') . "</a><br />";
	$menu_items .= "<a href='#submission-add-content-container' class='submission-content-menu-item'>" . elgg_echo('todo:label:addcontent') . "</a><br />";
					
	$back_button = "<a id='submission-content-back-button'><< Back</a>";
	
	// Content Div's
	$content_list = "<select id='submission-content-select' name='submission_content[]' MULTIPLE></select>";
	
	$content_list_module = elgg_view_module('info', elgg_echo("todo:label:content"), $content_list, array(
		'id' => 'submission-content-list',
		'class' => 'submission-content-pane',
	));
							
	$link_content = "<form id='submission-link-form'>" . elgg_view('input/text', array(
		'id' => 'submission-link', 
		'name' => 'submission_link'
	)) . "<br /><br />";
	
	$link_content .= elgg_view('input/submit', array(
		'id' => 'submission-submit-link', 
		'name' => 'link_submit', 
		'value' => 'Submit'
	)) . "</form>";
	
	$link_module = elgg_view_module('info', elgg_echo('todo:label:addlink'), $link_content, array(
		'id' => 'submission-add-link-container', 
		'class' => 'submission-content-pane',
	));				
				
	$file_content = "<form id='submission-file-form' method='POST' enctype='multipart/form-data'>";
	$file_content .= elgg_view("input/file",array(
		'name' => 'upload', 
		'js' => 'id="upload"'
	)) . "<br /><br />";
	
	$file_content .= elgg_view('input/submit', array(
		'id' => 'submission-submit-file', 
		'name' => 'file_submit', 
		'value' => 'Submit'
	)) . "</form>";
	
	$file_module = elgg_view_module('info', elgg_echo('todo:label:addfile'), $file_content, array(
		'id' => 'submission-add-file-container', 
		'class' => 'submission-content-pane',
	));
	
		
	$content_module = elgg_view('modules/ajaxmodule', array(
		'title' => elgg_echo('todo:label:addcontent'),
		'limit' => 5,
		'subtypes' => array('blog', 'bookmarks', 'image', 'album', 'poll', 'file', 'shared_doc', 'groupforumtopic'),
		'container_guid' => elgg_get_logged_in_user_guid(),
		'listing_type' => 'simpleicon',
		'module_type' => 'info',
		'module_class' => 'submission-content-pane',
		'module_id' => 'submission-add-content-container',
	));
	
		
	// Labels/Input
	$title_label = elgg_view_title(elgg_echo("todo:label:newsubmission"));
	
	$description_label = elgg_echo("todo:label:additionalcomments");
	$description_input = elgg_view("input/plaintext", array(
		'name' => 'submission_description', 
		'id' => 'submission-description', 
		'value' => $description
	));

	$submit_input = elgg_view('input/submit', array(
		'name' => 'submit', 
		'id' => 'submit-create-submission',
		'value' => elgg_echo('submit'),
	));
	
	$ajax_spinner = '<div id="submission-ajax-spinner" class="elgg-ajax-loader"></div>';
	
	// Build Form Body
	$form_body = <<<HTML

	<div style='padding: 10px;'>
		<div>
			$title_label<br />
		</div>
		<div id='submission-content-container'>
			<div id='submission-content-menu' class='content-menu'>
				$menu_items
			</div>
			<div id='submission-control-back' class='content-menu'>
				$back_button
			</div>
			<div id='submission-content'>
				$content_list_module
				$link_module
				$file_module
				$content_module
				$ajax_spinner
				<div id='submission-output' style='display: none;'></div>
			</div>
			<div style='clear:both;'></div>
			<br />
			<div id="submission-error-message">
			</div>
		</div>
		<hr />
		<div>
			<label>$description_label</label><br />
	        $description_input
		</div><br />
		<div class="elgg-foot">
			$submit_input
			$container_hidden
			$entity_hidden
		</div>
	</div>
HTML;

	echo $form_body;
}