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
	$can_grade = $todo->canEdit();

	// Hacky way to check security on todo submissions
	if (elgg_get_logged_in_user_entity() == $todo_owner 
		|| elgg_get_logged_in_user_entity() == get_entity($vars['entity']->owner_guid) 
		|| elgg_is_admin_logged_in()) 
	{
		$valid = true;
	}
}

if ($valid) {
	// General submission info
	$submission = $vars['entity'];
	
	$url = $submission->getURL();
	$owner = $submission->getOwnerEntity();
	$date_content =  date("F j, Y", $submission->time_created);
	
	if ($vars['full_view']) {
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
	
		$comments_count = $submission->countComments();
		
		//only display if there are commments
		if ($comments_count != 0) {
			$text = elgg_echo("comments") . " ($comments_count)";
			$comments_link = elgg_view('output/url', array(
				'href' => '#comments',
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
				$icon = false;
				$guid = (int)$content;
				if (is_int($guid) && $entity = get_entity($guid)) {
					// If this is a 'downloadable' file (file or todosubmission file)
					if (elgg_instanceof($entity, 'object', 'file') || elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
						// Url should point directly to the file, not the view
						$href = "file/download/{$entity->guid}";

						if (elgg_instanceof($entity, 'object', 'file')) {
							$icon = $entity->getURL();
						} else {
							$icon = false;
						}

					} else {
						$href = $entity->getURL();
						$icon = $href;
					}
					$text = $entity->title;
				} else {
					$href = $text = $content;
				}

				if ($icon) {
					$content_icon = elgg_view('output/url', array(
						'text' => elgg_view('output/img', array(
							'src' => elgg_get_site_url() . 'mod/todo/graphics/spot_content.png'
						)) . "<span>" . elgg_echo('todo:label:viewonspot') . "</span>", 
						'class' => 'todo-spot-content-link',
						'target' => '_blank',
						'href' => $icon
					));
				} else {
					$content_icon = null;
				}

				$work_submitted_content .= "<li>" . elgg_view('output/url', array('href' => $href, 'text' => $text, 'target' => '_blank')). "$content_icon</li>";
			}
		}
	
		if ($submission->description) {
			$moreinfo_content = elgg_view('output/longtext', array('value' => $submission->description));
			$moreinfo_label = elgg_echo('todo:label:moreinfo');
		}
		
		if ($todo->grade_required) {
			if ($can_grade) {
				$grade_content = elgg_view_form('submission/grade', array('name' => 'submission_grade_form'), array(
					'todo' => $todo,
					'submission' => $submission,
				));
			} else {
				$grade_content = "<span class='todo-submission-grade-label'>";
				if ($submission->grade !== NULL) {
					$grade_content .= $submission->grade . "&nbsp;&#47;&nbsp;" . $todo->grade_total;
				} else {
					$grade_content .= elgg_echo('todo:label:notyetgraded');
				}
				$grade_content .= "</span>";
			}
			$grade_content = "<div class='todo-submission-grade-container'>" . $grade_content . "</div>";
		}
		


		$content = <<<HTML
			<br />
			$assignment_content
			<div>
				<label>$work_submitted_label</label><br />
				<ul class='todo-work-submitted-list'>
				$work_submitted_content
				</ul>
			</div><br />
			<div class='description'>
				<label>$moreinfo_label</label><br />
				$moreinfo_content
			</div>
			$grade_content
HTML;
	
		$params = array(
			'entity' => $submission,
			'metadata' => $metadata,
			'subtitle' => $subtitle,
			'content' => $content,
		);
	
		$list_body = elgg_view('object/elements/summary', $params);

		echo elgg_view_image_block($owner_icon, $list_body);
	} else if (!$vars['full_view'] && elgg_is_xhr()) {
		// XHR Brief view

		// Todo Link
		$todo_link = elgg_view('output/url', array(
			'text' => $todo->title,
			'href' => $todo->getURL(),
			'target' => '_blank',
		));
		
		$ajax_url = elgg_get_site_url() . 'ajax/view/todo/ajax_submission?guid=' . $submission->guid;
		
		// Submission link
		$submission_link = elgg_view('output/url', array(
			'text' => elgg_echo('todo:label:view'),
			'href' => $ajax_url,
			'target' => '_blank',
			'class' => 'todo-submission-lightbox',
			'rel' => 'todo-submission-lightboxen',
		));
		
		// Display comments
		$comments_count = $submission->countComments();
		$comments_label = elgg_echo("comments");

		// Was a return required for this submission?
		$return_label = elgg_echo('todo:label:returnrequired');
		$has_return = $submission->content ? 'yes' : 'no';

		$completed_label = elgg_echo('todo:label:completed');
		$completed_content = date('m/d/y', $submission->time_created);
		
		// Determine if submitted on time
		$ontime_label = elgg_echo('todo:label:ontime');
		
		$submission_created_day = strtotime(date('Y-m-d', $submission->time_created));
		$todo_due_day = strtotime(date('Y-m-d', $todo->due_date));
		
		$ontime = $submission_created_day <= $todo_due_day ? 'yes' : 'no';
		
		// Display more info if not looking at a groups submissions
		if (!elgg_in_context('group_todo_submissions')) {	
			// Get container/owner		
			$container_entity = $todo->getContainerEntity();
			$owner_entity = $todo->getOwnerEntity();

			// Owner link
			$owner_link = elgg_view('output/url', array(
				'href' => "profile/$owner_entity->username",
				'text' => $owner_entity->name,
			));

			$assigned_by = $owner_link;

			// If container is a group, display both group and owner
			if (elgg_instanceof($container_entity, 'group')) {
				$group_link = elgg_view('output/url', array(
					'text' => $container_entity->name,
					'href' => $container_entity->getURL(),
				));

				$owner_link = elgg_view('output/url', array(
					'href' => "profile/$owner_entity->username",
					'text' => "({$owner_entity->name})",
				));

				$assigned_by = $group_link . " - {$owner_link}";
			} 
			
			$subtext_content = elgg_echo('todo:label:assignedby', array($assigned_by));
			
			$todo_subtext = "<p class='elgg-subtext'>{$subtext_content}</p>";
		}

		$content = <<<HTML
			<tr>
				<td>
					<strong>$todo_link</strong><br />
					$todo_subtext
				</td>
				<td class='todo-submission-column'>
					$submission_link
				</td>
				<td class='todo-submission-info-column'>
					<table class='todo-submission-info-table'>
						<tbody>
							<tr>
								<td class='submission-info-label'>$completed_label</td>
								<td class='submission-info-value'>$completed_content</td>
							</tr>
							<tr>
								<td class='submission-info-label'>$ontime_label</td>
								<td class='submission-info-value'>$ontime</td>
							</tr>
							<tr>
								<td class='submission-info-label'>$return_label</td>
								<td class='submission-info-value'>$has_return</td>
							</tr>
							<tr>
								<td class='submission-info-label'>$comments_label</td>
								<td class='submission-info-value'>$comments_count</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
HTML;
		echo $content;
	}
}
