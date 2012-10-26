<?php
/**
 * Todo move form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$todo_label = elgg_echo('todo:label:todoguid');
$todo_input = elgg_view('input/text', array(
	'name' => 'todo_guid',
));

$group_label = elgg_echo('todo:label:groupguid');
$group_input = elgg_view('input/text', array(
	'name' => 'group_guid',
));

$submit_input = elgg_view('input/submit', array(
	'name' => 'todo_move',
	'class' => 'elgg-button elgg-button-action todo-admin-move-button',
	'value' => elgg_echo('todo:label:move'),
));

$content = <<<HTML
	<div>
		<label>$todo_label</label>
		$todo_input
	</div>
	<div>
		<label>$group_label</label>
		$group_input
	</div>
	<div>
		$submit_input
	</div>
HTML;

echo $content;