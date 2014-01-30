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

// Just in case this view isn't loaded via AJAX
if (!elgg_is_xhr()) {
	// Forward to regular view
	forward($entity->getURL());
}

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

// Show custom submission annotations
$submission_annotation_options = array(
	'entity' => $entity,
);

if (is_todo_admin() || elgg_get_logged_in_user_entity()->is_parent) {
	$submission_annotation_options['show_add_form'] = false;
}

$comments = elgg_view('todo/submission_annotations', $submission_annotation_options);

$module = elgg_view_module('info', $navigation, $object_view . $comments);

$todo_guid = $entity->todo_guid;

$content = <<<HTML
	<div class='todo-ajax-submission'>
		$module
	</div>
	<script type='text/javascript'>
		var initialURL = window.location.href;
		var guid = '$guid';
		var todo_guid = '$todo_guid';
		var url = elgg.get_site_url() + 'todo/view/' + todo_guid + "?submission=" + guid;

		if (elgg.filtrate) {
			history.pushState({'url': url, 'initialURL': initialURL, 'guid': guid, 'type': 'todo_submission_fancybox'}, '', url);
		} else {
			history.replaceState({}, '', url);
		}	
	</script>
HTML;

echo $content;