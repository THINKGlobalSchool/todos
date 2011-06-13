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
					
	$back_button = "<a id='submission-content-back-button'><< Back</a>";
	
	// Content Div's
	$content_list = "<div class='submission-content-pane' id='submission-content-list'>
								<select id='submission-content-select' name='submission_content[]' MULTIPLE>
								</select>
							</div>";
							
	$add_link_div = "<div class='submission-content-pane' id='submission-add-link-container'>
						<form id='submission-link-form'>
							<label>" . elgg_echo('todo:label:addlink') . "</label><br />
							" . elgg_view('input/text', array('id' => 'submission-link', 'name' => 'submission_link')) . "<br />
							" . elgg_view('input/submit', array('id' => 'submission-submit-link', 'name' => 'link_submit', 'value' => 'Submit')) . "
						</form>
					</div>";
					
	
	$add_file_div = "<div class='submission-content-pane' id ='submission-add-file-container'>
						<form id='submission-file-form' method='POST' enctype='multipart/form-data'>
							<label>" . elgg_echo('todo:label:addfile') . "</label><br />
							" . elgg_view("input/file",array('name' => 'upload', 'js' => 'id="upload"')) . "<br />
							" . elgg_view('input/submit', array('id' => 'submission-submit-file', 'name' => 'file_submit', 'value' => 'Submit')) . "
						</form>
					</div>";
	
	// Labels/Input
	$title_label = elgg_echo("todo:label:newsubmission");
	
	$content_label = elgg_echo("todo:label:content");

	$description_label = elgg_echo("todo:label:additionalcomments");
	$description_input = elgg_view("input/plaintext", array('name' => 'submission_description', 
															'id' => 'submission-description', 
															'value' => $description));

	$submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('submit')));
	
	$ajax_spinner = '<div id="submission-ajax-spinner" class="elgg-ajax-loader"></div>';
	
	// Build Form Body
	$form_body = <<<HTML

	<div style='padding: 10px;'>
		<div>
			<h3>$title_label</h3><br />
		</div>
		<div id='submission-content-container'>
			<h3>$content_label</h3><br />
			<div id='submission-content-menu' class='content-menu'>
				$menu_items
			</div>
			<div id='submission-control-back' class='content-menu'>
				$back_button
			</div>
			<div id='submission-content'>
				$content_list
				$add_link_div
				$add_file_div
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
		<div class="elgg-form-footer-alt">
			$submit_input
			$container_hidden
			$entity_hidden
		</div>
	</div>
HTML;

	echo $form_body;
}