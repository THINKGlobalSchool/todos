<?php
/**
 * Todo edit form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get values/sticky values
$title 				= elgg_extract('title', $vars);
$description 		= elgg_extract('description', $vars);
$tags 				= elgg_extract('tags', $vars);
$suggested_tags		= elgg_extract('suggested_tags', $vars);
$due_date			= elgg_extract('due_date', $vars);
$container_guid 	= elgg_extract('container_guid', $vars);
$return_required	= elgg_extract('return_required', $vars);
$is_rubric_selected	= elgg_extract('rubric_select', $vars);
$rubric_guid		= elgg_extract('rubric_guid', $vars);
$access_id 			= elgg_extract('access_level', $vars);
$status				= elgg_extract('status', $vars);
$guid				= elgg_extract('guid', $vars);

// Check if we've got an entity, if so, we're editing.
if ($guid) {	
	$entity_hidden  = elgg_view('input/hidden', array('name' => 'guid', 'value' => $guid));

	$script .= <<<HTML
		<script type='text/javascript'>
			$(document).ready(function() {
				elgg.todo.loadAssignees({$guid}, 'todo-assignees-container');
			});
		</script>
HTML;
	// Get the actual access id
	$access_id = elgg_extract('access_id', $vars);
	
	// Set it to assignees only if its not ACCESS_LOGGED_IN
	if ($access_id != ACCESS_LOGGED_IN) {
		$access_id = TODO_ACCESS_LEVEL_ASSIGNEES_ONLY;
	}
	
	$submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('save')));		
} else {	
	$submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('save')));	
	$submit_input .= '&nbsp;' . elgg_view('input/submit', array('name' => 'submit_and_new', 'value' => elgg_echo('todo:label:savenew')));
}

$container_guid = get_input('container_guid', elgg_get_page_owner_guid());

$container_hidden = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $container_guid));

// Labels/Input
$title_label = elgg_echo('title');
$title_input = elgg_view('input/text', array(
	'name' => 'title', 
	'value' => $title
));

$description_label = elgg_echo("todo:label:description");
$description_input = elgg_view("input/longtext", array(
	'name' => 'description', 
	'value' => $description
));

$duedate_label = elgg_echo('todo:label:duedate');
$duedate_content = elgg_view('input/date', array(
	'name' => 'due_date', 
	'value' => $due_date
));

$tag_label = elgg_echo('tags');
$tag_input = elgg_view('input/tags', array(
	'name' => 'tags', 
	'value' => $tags
));

$suggested_tags_label = elgg_echo('todo:label:suggestedtags');
$suggested_tags_input = elgg_view('input/tags', array(
	'name' => 'suggested_tags',
	'value' => $suggested_tags ? $suggested_tags : ',',
));

$assign_label = elgg_echo('todo:label:assignto');
$assign_content = elgg_view('input/dropdown', array(
	'name' => 'assignee_type_select',
	'id' => 'todo-assignee-type-select',
	'options_values' =>	array(
		0 => elgg_echo('todo:label:individuals'),
		1 => elgg_echo('todo:label:groups'
	))		
));
													
$user_picker = elgg_view('input/userpicker', array(
	'id' => 'todo-assignee-userpicker'
));

$group_label = elgg_echo('todo:label:selectgroup');
$group_picker = elgg_view('input/dropdown', array(
	'name' => 'members[]', 
	'id' => 'todo-group-assignee-select', 
	'options_values' => get_todo_groups_array(), 
	'class' => 'multiselect', 
	'multiple' => 'MULTIPLE',
	'disabled' => 'DISABLED',
));

$return_label = elgg_echo('todo:label:returnrequired');
$return_content = "<input type='checkbox' class='input-checkboxes' " . ($return_required ? "checked='checked' ": '' ) .  " name='return_required' id='todo_return_required'>";


// Optional content
 
$rubric_html = "";

if (TODO_RUBRIC_ENABLED) {
	$rubric_label = elgg_echo('todo:label:assessmentrubric');
	$rubric_picker_label = elgg_echo('todo:label:rubricpicker');
	$rubric_content = elgg_view('input/dropdown', array(
		'name' => 'rubric_select', 
		'id' => 'rubric_select', 
		'options_values' => array(
			0 => elgg_echo('todo:label:rubricnone'),
			1 => elgg_echo('todo:label:rubricselect'
		)),
		'value' => $is_rubric_selected
	));
	
	$rubric_picker = elgg_view('input/dropdown', array(
		'name' => 'rubric_guid', 
		'id' => 'rubric_picker', 
		'options_values' => get_todo_rubric_array(), 
		'value' => $rubric_guid,
		'disabled' => 'DISABLED',
	));

	$rubric_html = <<<HTML
	
		<script type='text/javascript'>
			$(document).ready(function() {
				var rubric_guid = '$rubric_guid';
				if (rubric_guid) {
					$('#rubric_picker_container').show();
					$('#rubric_picker').removeAttr('disabled');
					$('#rubric_select').val(1);
				}
				$('#rubric_select').change(function() {
					if ($(this).val() == 1) {
						$('#rubric_picker_container').show();
						$('#rubric_picker').removeAttr('disabled');
					} else {
						$('#rubric_picker_container').hide();
						$('#rubric_picker').attr('disabled', 'DISABLED');
					}
				});
			});	
		</script>
HTML;

	$rubric_html .= "<div><label>$rubric_label</label><br />$rubric_content</div><br />
					<div id='rubric_picker_container'>
						<label>$rubric_picker_label</label><br />
						$rubric_picker
						<br /><br />
					</div>";	
}
	

$access_label = elgg_echo('todo:label:accesslevel');
$access_content = elgg_view('input/dropdown', array(
	'name' => 'access_level', 
	'id' => 'todo_access', 
	'options_values' => get_todo_access_array(), 
	'value' => $access_id
));

$status_label = elgg_echo('todo:label:status');
$status_input = elgg_view('input/dropdown', array(
	'name' => 'status',
	'id' => 'todo_status',
	'value' => $status,
	'options_values' => array(
		TODO_STATUS_DRAFT => elgg_echo('todo:status:draft'),
		TODO_STATUS_PUBLISHED => elgg_echo('todo:status:published')
	)
));
		
		
$assignees_label = elgg_echo('todo:label:currentassignees');

$submission_popup_label = elgg_echo('todo:label:suggestedtagswhat');
$submission_popup_title = elgg_echo('todo:label:suggestedtagstitle');
$submission_popup_info = elgg_echo('todo:label:suggestedtagsinfo');			

$popup_content = elgg_view_module('popup', $submission_popup_title, $submission_popup_info, array(
	'id' => 'info',
	'class' => 'hidden todo-help-popup',
));

$popup = "<a style='font-size: 10px;' id='todo-suggested-what' href='#info'>$submission_popup_label</a>" . $popup_content;

// Build Form Body
$form_body = <<<HTML
<div class='margin_top todo'>
	<div>
		<label>$title_label</label><br />
        $title_input
	</div><br />
	<div>
		<label>$description_label</label><br />
        $description_input
	</div><br />
	<div>
		<label>$duedate_label</label><br />
		$duedate_content
	</div><br />
	<div>
		<label>$tag_label</label><br />
        $tag_input
	</div><br />
	<div>
		<label>$suggested_tags_label</label>&nbsp;&nbsp;$popup<br />
        $suggested_tags_input
	</div><br />
	<div>
		<label>$assign_label</label><br />
		$assign_content<br /><br />
		<div id='todo-assign-individual-container'>
			$user_picker
		</div>
		<div id='todo-assign-group-container'>
			<label>$group_label</label><br />
			$group_picker
			<br /><br />
		</div><br />
		<label>$assignees_label</label><br />
		<div id='todo-assignees-container'></div>
	</div><br />
	<div>
		<label>$return_label</label>
		$return_content
	</div><br />
	$rubric_html<br />
	<div>
		<label>$access_label</label><br />
		$access_content
	</div><br />
	<div>
		<label>$status_label</label><br />
		$status_input
	</div>
	<br />
	<div class="elgg-foot">
		$submit_input
		$container_hidden
		$entity_hidden
	</div>
</div>

HTML;

$form_body .= $script;

echo $form_body;
