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
			
		$container_hidden = elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $vars['container_guid']));
		$entity_hidden  = elgg_view('input/hidden', array('internalname' => 'todo_guid', 'value' => $vars['entity']->getGUID()));
	
		if (empty($description)) {
			$description = $vars['user']->todo_description;
			if (!empty($description)) {
				$title = $vars['user']->todo_title;
				$tags = $vars['user']->todo_tags;
				$type = $vars['user']->todo_type;
			}
		}
	
		// Labels/Input
		$title_label = elgg_echo("todo:label:newsubmission");

		$description_label = elgg_echo("todo:label:additionalcomments");
		$description_input = elgg_view("input/plaintext", array('internalname' => 'submission_description', 'internalid' => 'submission_description', 'value' => $description));

		$submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('submit')));


		// Build Form Body
		$form_body = <<<EOT

		<div class='contentWrapper todo'>
			<div>
				<h3>$title_label</h3><br />
			</div>
			<div>
				<label>$description_label</label><br />
		        $description_input
			</div><br />
			<div>
				$submit_input
				$container_hidden
				$entity_hidden
			</div>
		</div>

EOT;
		echo elgg_view('input/form', array('body' => $form_body, 'internalid' => 'todo_submission_form'));
		
	}
?>