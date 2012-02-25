<?php
/**
 * List submission comments with optional add form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['entity']        ElggEntity
 * @uses $vars['show_add_form'] Display add form or not
 */

$entity = elgg_extract('entity', $vars);
$show_add_form = elgg_extract('show_add_form', $vars, true);

$class = "elgg-comments {$entity->getSubtype()}-comments";

echo "<div id='todo-submission-annotations' class=\"$class\">";

$options = array(
	'guid' => $entity->getGUID(),
	'annotation_names' => array('generic_comment', 'submission_annotation'),
);
$html = elgg_list_annotations($options);
if ($html) {
	echo '<h3>' . elgg_echo('comments') . '</h3>';
	echo $html;
}

if ($show_add_form) {
	$form_vars = array(
		'name' => 'elgg_add_comment',
		'enctype' => 'multipart/form-data',
	);
	echo elgg_view_form('submission/annotate', $form_vars, $vars);
}

echo '</div>';
