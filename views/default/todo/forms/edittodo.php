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


// JS
$script = <<<HTML
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#assign_individual_container').show();
				$("#group_assignee_picker").attr("disabled","disabled");

				$('#assignee_picker').change(function() {
					if ($(this).val() == 0) {
						$('#assign_individual_container').show();
						$('#assign_group_container').hide();
						$("#user_assignee_picker").removeAttr("disabled");
						$("#group_assignee_picker").attr("disabled","disabled");
					} else {
						$('#assign_individual_container').hide();
						$('#assign_group_container').show();
						$("#user_assignee_picker").attr("disabled","disabled");
						$("#group_assignee_picker").removeAttr("disabled");
					}
				});
			});
		</script>
HTML;

// Check if we've got an entity, if so, we're editing.
if (isset($vars['entity'])) {
	
	if (!$vars['entity']) {
		forward('todo');
	}
	
	$action 			= "todo/edittodo";
	$title 		 		= $vars['entity']->title;
	$description 		= $vars['entity']->description;
	$tags 				= $vars['entity']->tags;
	$due_date			= $vars['entity']->due_date;
	$return_required	= $vars['entity']->return_required;
	$access_id			= $vars['entity']->access_id;
	$status				= $vars['entity']->status;
	
	if ($access_id != TODO_ACCESS_LEVEL_LOGGED_IN) {
		$access_id = TODO_ACCESS_LEVEL_ASSIGNEES_ONLY;
	} 
	
	$entity_hidden  = elgg_view('input/hidden', array('internalname' => 'todo_guid', 'value' => $vars['entity']->getGUID()));
	
	if (TODO_RUBRIC_ENABLED && $vars['entity']->rubric_guid) {
		$rubric_guid = $vars['entity']->rubric_guid;
	}
	
	$assignees_url = elgg_get_site_url() . 'mod/todo/pages/ajax/assignees.php';
	
	$script .= <<<HTML
		<script type='text/javascript'>
			$(document).ready(function() {
				loadAssignees({$vars['entity']->getGUID()});
			});
			
			function loadAssignees(guid) {
				$.ajax({
					type: "GET",
					url: "$assignees_url",
					data: {guid: guid},
					cache: false,
					success: function(data){
						$("#current_assignees_container").html(data);
					}
				});
			}
		</script>
HTML;
	
	$submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('save')));	
	
	
} else {
// No entity, creating new one
	$action 			= "todo/createtodo";
	$title 				= "";
	$description 		= "";
	$return_required 	= 0;
	$is_rubric_selected = 0;
	$entity_hidden = "";
	$status = TODO_STATUS_PUBLISHED;
	
	$submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('save')));	
	$submit_input .= '&nbsp;' . elgg_view('input/submit', array('internalname' => 'submit_and_new', 'value' => elgg_echo('todo:label:savenew')));
}

$container_guid = get_input('container_guid', page_owner());

$container_hidden = elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $container_guid));


// Load sticky form values
if (elgg_is_sticky_form('todo_post_forms')) {
	$title = elgg_get_sticky_value('todo_post_forms', 'title');
	$description = elgg_get_sticky_value('todo_post_forms', 'description');
	$tags = elgg_get_sticky_value('todo_post_forms', 'tags');
	$due_date = elgg_get_sticky_value('todo_post_forms', 'due_date');		
	$assignees = elgg_get_sticky_value('todo_post_forms', 'assignee_guids');
	$return_required = elgg_get_sticky_value('todo_post_forms', 'return_required');
	$is_ribric_selecred = elgg_get_sticky_value('todo_post_forms', 'rubric_select');
	$rubric_guid = elgg_get_sticky_value('todo_post_forms', 'rubric_guid');
	$access_id = elgg_get_sticky_value('todo_post_forms', 'access_level');
	$status = elgg_get_sticky_value('todo_post_forms', 'status');
}



// Labels/Input
$title_label = elgg_echo('title');
$title_input = elgg_view('input/text', array('internalname' => 'title', 'value' => $title));

$description_label = elgg_echo("todo:label:description");
$description_input = elgg_view("input/longtext", array('internalname' => 'description', 'value' => $description));

$duedate_label = elgg_echo('todo:label:duedate');
$duedate_content = elgg_view('input/datepicker', array('internalname' => 'due_date', 'value' => $due_date, 'js' => 'readonly="readonly"'));

$tag_label = elgg_echo('tags');
   $tag_input = elgg_view('input/tags', array('internalname' => 'tags', 'value' => $tags));

$assign_label = elgg_echo('todo:label:assignto');
$assign_content = elgg_view('input/pulldown', array('internalname' => 'assignee_picker',
													'internalid' => 'assignee_picker',
													'options_values' =>	array(	0 => elgg_echo('todo:label:individuals'),
																				1 => elgg_echo('todo:label:groups'))		
													));
													
$user_picker = elgg_view('input/userpicker', array('internalname' => 'assignee_guids', 'internalid' => 'user_assignee_picker'));

$group_label = elgg_echo('todo:label:selectgroup');
$group_picker = elgg_view('input/pulldown', array('internalname' => 'assignee_guids[]', 'internalid' => 'group_assignee_picker', 'options_values' => get_todo_groups_array(), 'class' => 'multiselect', 'js' => 'MULTIPLE'));

$return_label = elgg_echo('todo:label:returnrequired');
$return_content = "<input type='checkbox' class='input-checkboxes' " . ($return_required ? "checked='checked' ": '' ) .  " name='return_required' id='todo_return_required'>";


// Optional content 
$rubric_html = "";
if (TODO_RUBRIC_ENABLED) {
	$rubric_label = elgg_echo('todo:label:assessmentrubric');
	$rubric_picker_label = elgg_echo('todo:label:rubricpicker');
	$rubric_content = elgg_view('input/pulldown', array('internalname' => 'rubric_select', 
														'internalid' => 'rubric_select', 
														'options_values' => array(	0 => elgg_echo('todo:label:rubricnone'),
																		   			1 => elgg_echo('todo:label:rubricselect')),
														'value' => $is_rubric_selected,
														));
	
	$rubric_picker = elgg_view('input/pulldown', array('internalname' => 'rubric_guid', 'internal_id' => 'rubric_picker', 'options_values' => get_todo_rubric_array(), 'value' => $rubric_guid));
			
	$rubric_html = <<<HTML
	
		<script type='text/javascript'>
			$(document).ready(function() {
				var rubric_guid = '$rubric_guid';
				if (rubric_guid) {
					$('#rubric_picker_container').show();
					$('#rubric_select').val(1);
				}
				$('#rubric_select').change(function() {
					if ($(this).val() == 1) {
						$('#rubric_picker_container').show();
					} else {
						$('#rubric_picker_container').hide();
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
$access_content = elgg_view('input/pulldown', array('internalname' => 'access_level', 'internalid' => 'todo_access', 'options_values' => get_todo_access_array(), 'value' => $access_id));

$status_label = elgg_echo('todo:label:status');
$status_input = elgg_view('input/pulldown', array(
	'internalname' => 'status',
	'internalid' => 'todo_status',
	'value' => $status,
	'options_values' => array(
		TODO_STATUS_DRAFT => elgg_echo('todo:status:draft'),
		TODO_STATUS_PUBLISHED => elgg_echo('todo:status:published')
	)
));
		

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
		<div id='assign_individual_container'>
			$user_picker
		</div>
		<div id='assign_group_container'>
			<label>$group_label</label><br />
			$group_picker
			<br /><br />
		</div>
		<div id='current_assignees_container'></div>
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
	<div>
		$submit_input
		$container_hidden
		$entity_hidden
	</div>
</div>

HTML;

echo $script . elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'body' => $form_body, 'internalid' => 'todo_post_forms', 'internalname' => 'todo_post_forms'));
