<?php
/**
 * Todo Submission Entity View
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$valid = false;

//Check for valid entity
if (isset($vars['entity']) && $vars['entity'] instanceof ElggObject) {
	$todo = get_entity($vars['entity']->todo_guid);
	$todo_owner = get_entity($todo->owner_guid);
		
	// Hacky way to check security on todo submissions
	if (elgg_get_logged_in_user_entity() == $todo_owner || elgg_get_logged_in_user_entity() == get_entity($vars['entity']->owner_guid) || elgg_is_admin_logged_in()) {
		$valid = true;
	}
}

if ($valid) {

	$url = $vars['entity']->getURL();
	$owner = $vars['entity']->getOwnerEntity();	
	
	$canedit = $vars['entity']->canEdit();
	$title = $vars['entity']->title;
	$contents = unserialize($vars['entity']->content);
	
	$assignee_label = elgg_echo('todo:label:assignee');
	$assignee_content = $owner->name;
	
	$todo_title_label = elgg_echo('todo:label:todo');
	$todo_title_content = elgg_view('output/url', array('href' => $todo->getURL(), 'text' => $todo->title));
	
	$date_label = elgg_echo('todo:label:datecompleted');
	$date_content =  date("F j, Y", $vars['entity']->time_created);
	
	if ($contents) {
		$work_submitted_label = elgg_echo('todo:label:worksubmitted');
	
		foreach ($contents as $content) {
			$guid = (int)$content;
			if (is_int($guid) && $entity = get_entity($guid)) {
				$href = $entity->getURL();
				$text = $entity->title;
			} else {
				$href = $text = $content;
			}
			$work_submitted_content .= "<li>" . elgg_view('output/url', array('href' => $href, 'text' => $text)). "</li>";
		}
	}
	
	if ($moreinfo_content = $vars['entity']->description) {
		$moreinfo_label = elgg_echo('todo:label:moreinfo');
	}
	
	
	// Content
	$strapline = sprintf(elgg_echo("todo:strapline"), date("F j, Y",$vars['entity']->time_created));
	$strapline .= " " . elgg_echo('by') . " <a href='{$vars['url']}todo/{$owner->username}'>{$owner->name}</a> ";
	$strapline .= sprintf(elgg_echo("comments")) . " (" . $vars['entity']->countComments() . ")";
	
	if ($canedit) {
			$controls .= elgg_view("output/confirmlink", 
									array(
										'href' => $vars['url'] . "action/todo/deletesubmission?submission_guid=" . $vars['entity']->getGUID(),
										'text' => elgg_echo('todo:label:deletesubmission'),
										'confirm' => elgg_echo('deleteconfirm'),
									)) . "&nbsp;&nbsp;&nbsp;";
									
	}
	
	$info = <<<HTML
				<div class='todo margin_top' style='border-bottom:1px dotted #CCCCCC; margin-bottom: 4px; padding-bottom: 10px;'>
					<div class='strapline'>
						<div class='entity_metadata' style='float: left; color: black; margin: 0;'>
							<b>$assignee_label: </b>
							$assignee_content | 
							<b>$todo_title_label: </b>
							$todo_title_content | 
							<b>$date_label: </b>
							$date_content
						</div>
						<div class='entity_metadata' style='float: right;'>
							$controls
						</div>
						<div style='clear: both;'></div>
					</div>
					<div class='work_submitted margin_top'>
						<label>$work_submitted_label</label><br />
						<ul>
						$work_submitted_content
						</ul>
					</div><br />
					<div class='description'>
						<label>$moreinfo_label</label>
						$moreinfo_content
					</div>
				</div>
HTML;
	echo $info;
	
	
} else {
	// If were here something went wrong..
	$owner = $vars['user'];
	$canedit = false;
	forward();
}
