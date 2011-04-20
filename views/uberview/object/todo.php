<?php
/**
 * Todo uberview listing view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 	
 */

$container = get_entity($vars['entity']->container_guid);

// Determine how we are going to view this todo
$user = get_loggedin_user();
$is_owner = $vars['entity']->canEdit();
$is_assignee = is_todo_assignee($vars['entity']->getGUID(), $user->getGUID());

$url = $vars['entity']->getURL();
$owner = $vars['entity']->getOwnerEntity();
$canedit = $is_owner; 
$title = $vars['entity']->title;
$due_date = is_int($vars['entity']->due_date) ? date("F j, Y", $vars['entity']->due_date) : $vars['entity']->due_date;

// Content
$icon = elgg_view('profile/icon', array('entity' => $owner, 'size' => 'tiny'), FALSE, FALSE, 'default');

$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));

// If container is a group, show the group name as well as the author in the info
$group_name = $container instanceof ElggGroup ? " (<a href='{$container->getURL()}'>$container->name</a>)" : '';			

$strapline = "<b>" . sprintf(elgg_echo("todo:strapline"), $due_date) . "</b><br />";
$strapline .= sprintf(elgg_echo('todo:label:assignedby') , "<a href='{$vars['url']}pg/todo/{$owner->username}'>{$owner->name}</a> $group_name");
		
		
echo <<<___END
<div class="blog $status_class entity_listing clearfix">
	<div class="entity_listing_icon">
		$icon
	</div>
	<div class="entity_listing_info">
		<div class="entity_metadata" style='min-width: 0px;'>$edit</div>
		<p class="entity_title"><a href='$url'>$title</a></p>
		<p class="entity_subtext">
			$strapline
		</p>
		$tags
	</div>
</div>
___END;
