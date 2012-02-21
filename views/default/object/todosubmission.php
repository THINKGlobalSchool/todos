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
if (elgg_instanceof($vars['entity'], 'object', 'todosubmission')) {
	$todo = get_entity($vars['entity']->todo_guid);
	$todo_owner = get_entity($todo->owner_guid);
		
	// Hacky way to check security on todo submissions
	if (elgg_get_logged_in_user_entity() == $todo_owner || elgg_get_logged_in_user_entity() == get_entity($vars['entity']->owner_guid) || elgg_is_admin_logged_in()) {
		$valid = true;
	}
}

if ($valid) {
	if ($vars['full_view']) {
		$submission = $vars['entity'];

		$url = $submission->getURL();
		$owner = $submission->getOwnerEntity();	
	
		$canedit = $submission->canEdit();
		$title = $submission->title;
		$contents = unserialize($submission->content);
	
		$assignee_label = elgg_echo('todo:label:assignee');
		$assignee_content = $owner->name;
	
		$owner_icon = elgg_view_entity_icon($owner, 'tiny');
		$owner_link = elgg_view('output/url', array(
			'href' => "profile/$owner->username",
			'text' => $owner->name,
		));
	
		$author_text = elgg_echo('todo:label:submittedby', array($owner_link));
	
		if (!elgg_get_context('ajax_submission')) {
			$todo_title_label = elgg_echo('todo:label:assignment');
			$todo_title_content = elgg_view('output/url', array('href' => $todo->getURL(), 'text' => $todo->title));

			$assignment_content = <<<HTML
				<div>
					<label>$todo_title_label</label><br />
					$todo_title_content
				</div><br />
HTML;
		}

		$date_label = elgg_echo('todo:label:datecompleted');
		$date_content =  date("F j, Y", $submission->time_created);
	
		$comments_count = $submission->countComments();
		//only display if there are commments
		if ($comments_count != 0) {
			$text = elgg_echo("comments") . " ($comments_count)";
			$comments_link = elgg_view('output/url', array(
				'href' => $submission->getURL() . '#comments',
				'text' => $text,
			));
		} else {
			$comments_link = '';
		}
	
		$subtitle = "<strong>$date_content</strong><p>$author_text $comments_link</p>";
	
		$metadata = elgg_view_menu('entity', array(
			'entity' => $submission,
			'handler' => 'submission',
			'sort_by' => 'priority',
			'class' => 'elgg-menu-hz',
		));
	

		if ($contents) {
			$work_submitted_label = elgg_echo('todo:label:worksubmitted');
	
			foreach ($contents as $content) {
				$guid = (int)$content;
				if (is_int($guid) && $entity = get_entity($guid)) {
					// If this is a 'downloadable' file (file or todosubmission file)
					if (elgg_instanceof($entity, 'object', 'file') || elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
						// Url should point directly to the file, not the view
						$href = "file/download/{$entity->guid}";
					} else {
						$href = $entity->getURL();
					}
					$text = $entity->title;
				} else {
					$href = $text = $content;
				}
				$work_submitted_content .= "<li>" . elgg_view('output/url', array('href' => $href, 'text' => $text, 'target' => '_blank')). "</li>";
			}
		}
	
		if ($submission->description) {
			$moreinfo_content = elgg_view('output/longtext', array('value' => $submission->description));
			$moreinfo_label = elgg_echo('todo:label:moreinfo');
		}
	
		$content = <<<HTML
			<br />
			$assignment_content
			<div>
				<label>$work_submitted_label</label><br />
				<ul>
				$work_submitted_content
				</ul>
			</div><br />
			<div class='description'>
				<label>$moreinfo_label</label><br />
				$moreinfo_content
			</div>
HTML;
	
		$params = array(
			'entity' => $submission,
			'metadata' => $metadata,
			'subtitle' => $subtitle,
			'content' => $content,
		);
	
		$list_body = elgg_view('object/elements/summary', $params);

		echo elgg_view_image_block($owner_icon, $list_body);
	}
} else {
	forward();
}
