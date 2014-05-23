<?php
/**
 * Submission form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
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

	// Automatically build views/menu items from these types (priority => type)
	$content_types = array(
		100 => 'content',
		200 => 'file',
		300 => 'link'
	);

	// Trigger a hook to allow plugins to add another content type
	$content_types = elgg_trigger_plugin_hook('get_submission_content_types', 'todo', null, $content_types);

	// Register menu items and get content modules for content types
	foreach ($content_types as $priority => $type) {
		// Register content menu items
		elgg_register_menu_item('todo_submission_content_type', array(
			'name' => $type,
			'text' => elgg_echo("todo:label:add{$type}"),
			'href' => "#submission-add-{$type}-container",
			'priority' => $priority,
			'class' => 'submission-content-menu-item',
			'id' => "add-{$type}",
		));

		$content_type_modules .= elgg_view("forms/submission/content_modules/{$type}");
	}

	// Output the dashboard tab menu
	$menu_items = elgg_view_menu('todo_submission_content_type', array(
		'sort_by' => 'priority'
	));

	$back_button = "<a id='submission-content-back-button'><< Back</a>";
	
	// Content list div
	$content_list = "<select id='submission-content-select' name='submission_content[]' MULTIPLE></select>";
	
	$content_list_module = elgg_view_module('info', elgg_echo("todo:label:content"), $content_list, array(
		'id' => 'submission-content-list',
		'class' => 'submission-content-pane',
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

	<div id='todo-submission-form' style='padding: 10px;'>
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
				$content_type_modules
				<div id="submission-notice-message">
				</div>
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