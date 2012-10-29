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

// Get page owner to determine if we're creating as a group
$page_owner = elgg_get_page_owner_entity();

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
$category           = elgg_extract('category', $vars);
$status				= elgg_extract('status', $vars);
$grade_required		= elgg_extract('grade_required', $vars);
$grade_total		= elgg_extract('grade_total', $vars);
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
	
	// Hide current assignees section until at least one is selected
	$assignees_hidden = 'hidden';
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

// Set assignee options depending on page owner
if (elgg_instanceof($page_owner, 'group')) {
	$assign_options = array(
		2 => elgg_echo('todo:label:currentgroup'),
		1 => elgg_echo('todo:label:anothergroup'),
		0 => elgg_echo('todo:label:individuals'),
	);
	$userpicker_hidden = 'hidden';
	
	$current_group_hidden = elgg_view('input/hidden', array(
		'id' => 'todo-current-group-select',
		'name' => 'members[]',
		'value' => $page_owner->guid,
	));
	
} else {
	$assign_options = array(
		0 => elgg_echo('todo:label:individuals'),
		1 => elgg_echo('todo:label:groups'),
	);
}

$assign_label = elgg_echo('todo:label:assignto');
$assign_content = elgg_view('input/dropdown', array(
	'name' => 'assignee_type_select',
	'id' => 'todo-assignee-type-select',
	'options_values' =>	$assign_options,
));
													
$user_picker = elgg_view('input/userpicker', array(
	'id' => 'todo-assignee-userpicker',
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

if (!$return_required) {
	$suggested_tags_display = "display: none;";
}

$grade_required_label = elgg_echo('todo:label:graderequired');
$grade_required_input = "<input type='checkbox' class='input-checkboxes' " . ($grade_required ? "checked='checked' ": '' ) .  " name='grade_required' id='todo-grade-required-input'>";

if (!$grade_required) {
	$grade_required_display = "display: none;";
}

$grade_total_label = elgg_echo('todo:label:gradetotal');
$grade_total_input = elgg_view('input/text', array(
	'name' => 'grade_total',
	'id' => 'todo-grade-total-input',
	'value' => $grade_total,
));

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

$category_label = elgg_Echo('todo:label:category');
$category_input = elgg_view('input/dropdown', array(
	'name' => 'category',
	'id' => 'todo_category',
	'value' => $category,
	'options_values' => array(
		TODO_BASIC_TASK => elgg_echo('todo:label:basic_task'),
		TODO_ASSESSED_TASK => elgg_echo('todo:label:assessed_task'),
		TODO_EXAM => elgg_echo('todo:label:exam'),
	)
));

$status_label = elgg_echo('todo:label:publishstatus');
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
		<label>$assign_label</label><br />
		$assign_content<br /><br />
		<div id='todo-assign-individual-container' class='$userpicker_hidden'>
			$user_picker
			<br />
		</div>
		<div id='todo-assign-group-container'>
			<label>$group_label</label><br />
			$group_picker
			$current_group_hidden
			<br /><br />
		</div>
		<div class='$assignees_hidden'>
			<label>$assignees_label</label><br />
			<div id='todo-assignees-container'></div>
			<br />
		</div>
	</div>
	<div>
		<label>$return_label</label>
		$return_content
	</div><br />
	<div id='todo-suggested-tags-container' style='$suggested_tags_display'>
		<label>$suggested_tags_label</label>&nbsp;&nbsp;$popup<br />
        $suggested_tags_input<br />
	</div>
	<div>
		<label>$grade_required_label</label>
		$grade_required_input
	</div><br />
	<div id='todo-grade-total-container' style='$grade_required_display'>
		<label>$grade_total_label</label>
		$grade_total_input<br /><br />
		$rubric_html
	</div>
	<div>
		<label>$category_label</label><br />
		$category_input
	</div><br />
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
