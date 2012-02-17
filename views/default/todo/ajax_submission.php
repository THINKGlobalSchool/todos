<?php
/**
 * Ajax Submission View
 * - Wraps the object/todosubmission view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['guid']
 * 
 */

$guid = elgg_extract('guid', $vars);

$entity = get_entity($guid);

$owner = $entity->getOwnerEntity();

$vars['full_view'] = TRUE;

$owner_link = elgg_view('output/url', array(
	'href' => "profile/$owner->username",
	'text' => $owner->name,
));

$title = elgg_echo('todo:label:submission') . ': ' . $owner_link;

$prev = elgg_view('output/url', array(
	'text' => '◄ ' . elgg_echo('todo:label:prev'),
	'href' => '#',
	'class' => 'todo-ajax-submission-navigation-prev',
));

$next = elgg_view('output/url', array(
	'text' => elgg_echo('todo:label:next') . ' ►',
	'href' => '#',
	'class' => 'todo-ajax-submission-navigation-next',
));

$navigation = <<<HTML
	<table width='100%'>
		<tr>
			<td width='12%' style='text-align:left;'>$prev</td>
			<td width='76%' style='text-align:center;'>$title</td>
			<td width='12%' style='text-align:right;'>$next</td>
		</tr>
	</table>
HTML;

elgg_push_context('ajax_submission');
$object_view = elgg_view_entity($entity, array('full_view' => TRUE));
elgg_pop_context();

$comments = elgg_view_comments($entity);

$module = elgg_view_module('info', $navigation, $object_view . $comments);

$content = <<<HTML
	<div class='todo-ajax-submission'>
		$module
	</div>
HTML;

echo $content;