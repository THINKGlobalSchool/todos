<?php
/**
 * To Do Group sidebar listing
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
		
$group = elgg_get_page_owner_entity();

// Only display sidebar todo's if enabled
if (elgg_instanceof($group, 'group') && $group->todo_enable == 'yes') {	
	// get the groups todo's
	$todos = elgg_get_entities(array('type' => 'object', 'subtype' => 'todo', 
										'container_guids' => page_owner(), 'limit' => 6));
													
	foreach ($todos as $idx => $todo) {
		if (have_assignees_completed_todo($todo->getGUID()) || $todo->status == TODO_STATUS_DRAFT || $todo->manual_complete) {
			unset($todos[$idx]);
		}
	}
	
	$all_link = elgg_view('output/url', array(
		'href' => 'todo/group/' . $group->getGUID() . '/owner/',
		'text' => elgg_echo('link:view:all')

	));
	
	$group_todos = elgg_echo('todo:label:upcomingtodos');
	$content = '';
	
	if($todos){
		foreach ($todos as $todo) {
			$owner = $todo->getOwnerEntity();
			$time = elgg_echo('todo:label:due', array(date("F j, Y", $todo->due_date)));
			$icon = elgg_view_entity_icon($owner, 'tiny');
			$title = "<a href=\"{$todo->getURL()}\">{$todo->title}</a>";

			$params = array(
					'entity' => $todo,
					'title' => $title,
					'subtitle' => $time,
				);
			$list_body = elgg_view('page/components/summary', $params);
			$content .= elgg_view_image_block($icon, $list_body);
		}
	}
	
	if ($group->isMember(elgg_get_logged_in_user_entity())) {
		$create_url = "todo/add/" . $group->getGUID();
		$content .= elgg_view('output/url', array(
			'href' => $create_url,
			'text' => elgg_echo('todo:add'),
			'class' => 'elgg-button elgg-button-action mtm clearfix'
		));
	}
	
	$title = "$group_todos <span class=\"right small\">$all_link</span>";
	
	echo elgg_view_module('aside', $title, $content, array('class' => 'elgg-todo-sidebar'));
}

return true;