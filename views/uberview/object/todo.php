<?php
/**
 * Todo Uberview Entity View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$todo = elgg_extract('entity', $vars, FALSE);

if (!$todo) {
	return TRUE;
}

$owner = $todo->getOwnerEntity();
$container = $todo->getContainerEntity();
$categories = elgg_view('output/categories', $vars);

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array(
	'href' => "todo/owner/$owner->username",
	'text' => $owner->name,
));

$author_text = elgg_echo('todo:label:assignedby', array($owner_link));


$tags = elgg_view('output/tags', array('tags' => $todo->tags));

$comments_count = $todo->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $todo->getURL() . '#todo-comments',
		'text' => $text,
	));
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $todo,
	'handler' => 'todo',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "<p>$author_text $comments_link</p>";
$subtitle .= $categories;

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}


// listing view
$params = array(
	'entity' => $todo,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
	'tags' => $tags,
	'content' => '',
);

$list_body = elgg_view('page/components/summary', $params);

echo elgg_view_image_block($owner_icon, $list_body);
