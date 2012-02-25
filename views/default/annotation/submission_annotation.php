<?php
/**
 * Todo submission annotation view
 *
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['annotation']  ElggAnnotation object
 * @uses $vars['full_view']   Display fill view or brief view
 */

if (!isset($vars['annotation'])) {
	return true;
}

$full_view = elgg_extract('full_view', $vars, true);

$comment = $vars['annotation'];

$entity = get_entity($comment->entity_guid);
$commenter = get_user($comment->owner_guid);
if (!$entity || !$commenter) {
	return true;
}

$annotation_value = unserialize($comment->value);

$friendlytime = elgg_view_friendly_time($comment->time_created);

$commenter_icon = elgg_view_entity_icon($commenter, 'tiny');
$commenter_link = "<a href=\"{$commenter->getURL()}\">$commenter->name</a>";

$entity_title = $entity->title ? $entity->title : elgg_echo('untitled');
$entity_link = "<a href=\"{$entity->getURL()}\">$entity_title</a>";

if ($full_view) {
	$menu = elgg_view_menu('annotation', array(
		'annotation' => $comment,
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz float-alt',
	));

	$comment_text = elgg_view("output/longtext", array("value" => $annotation_value['comment']));
	$attachment_guid = $annotation_value['attachment_guid'];
	$attachment = get_entity($attachment_guid);
	
	// If we've got an attachment, build an icon for it
	if (elgg_instanceof($attachment, 'object', 'submissionannotationfile')) {
		$attachment = elgg_view('todo/attachment', array('entity' => $attachment));
	}

	$body = <<<HTML
<div class="mbn">
	$menu
	$commenter_link
	<span class="elgg-subtext">
		$friendlytime
	</span>
	$comment_text
	$attachment
</div>
HTML;

	echo elgg_view_image_block($commenter_icon, $body, $attachment_vars);

} else {
	// brief view
	$on = elgg_echo('on');

	$excerpt = elgg_get_excerpt($annotation_value['comment'], 80);

	$body = <<<HTML
<span class="elgg-subtext">
	$commenter_link $on $entity_link ($friendlytime): $excerpt
</span>
HTML;

	echo elgg_view_image_block($commenter_icon, $body);
}
