<?php
/**
 * Todo settings form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 * 
 */

$user = elgg_get_logged_in_user_entity();

$suppress_complete = elgg_get_plugin_user_setting('suppress_complete', $user->guid, 'todos');

$suppress_complete_input = elgg_view('input/checkboxes', array(
	'name' => "suppress_complete", 
	'value' => $suppress_complete,  
	'options' => array(elgg_echo('todo:label:suppress_completion') => 1)
));

$submit_input = elgg_view('input/submit', array(
	'value' => elgg_echo('save'), 
	'class' => 'elgg-button elgg-button-submit'
));

$form_body = <<<HTML
	<div>
		$suppress_complete_input
	</div>
	<div>
		$submit_input
	</div>
HTML;

echo $form_body;