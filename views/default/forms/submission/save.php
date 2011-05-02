<?php
/**
 * Submission form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
// Check if we've got an entity
if (isset($vars['entity'])) {
		
	$container_hidden = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $vars['container_guid']));
	$entity_hidden  = elgg_view('input/hidden', array('name' => 'todo_guid', 'value' => $vars['entity']->getGUID()));

	if (empty($description)) {
		$description = $vars['user']->todo_description;
		if (!empty($description)) {
			$title = $vars['user']->todo_title;
			$tags = $vars['user']->todo_tags;
			$type = $vars['user']->todo_type;
		}
	}

	// Content Menu Items
	$menu_items .= "<a href='#' id='add_link' onclick=\"javascript:todoShowDiv('add_link_container');return false;\">" 
					. elgg_echo('todo:label:addlink') . 
					"</a><br />";
	$menu_items .= "<a href='#' id='add_file' onclick=\"javascript:todoShowDiv('add_file_container');return false;\">" 
					. elgg_echo('todo:label:addfile') . 
					"</a><br />";
					
	$back_button = "<a href='#' id='back_button' onclick=\"javascript:showDefault();return false;\"><< Back</a>";
	
	// Content Div's
	$content_display_div = "<div class='content_div' id='content_display_div'>
								<select class='submission_content_select' id='submission_content' name='submission_content[]' MULTIPLE>
								</select>
							</div>";
							
	$add_link_div = "<div class='content_div' id='add_link_container'>
						<form id='link_form'>
							<label>" . elgg_echo('todo:label:addlink') . "</label><br />
							" . elgg_view('input/text', array('id' => 'submission_link', 'name' => 'submission_link')) . "<br />
							" . elgg_view('input/submit', array('id' => 'link_submit', 'name' => 'link_submit', 'value' => 'Submit')) . "
						</form>
					</div>";
					
	
	$add_file_div = "<div class='content_div' id ='add_file_container'>
						<form id='file_form' method='POST' enctype='multipart/form-data'>
							<label>" . elgg_echo('todo:label:addfile') . "</label><br />
							" . elgg_view("input/file",array('name' => 'upload', 'js' => 'id="upload"')) . "<br />
							" . elgg_view('input/submit', array('id' => 'file_submit', 'name' => 'file_submit', 'value' => 'Submit')) . "
						</form>
					</div>";
	
	// Labels/Input
	$title_label = elgg_echo("todo:label:newsubmission");
	
	$content_label = elgg_echo("todo:label:content");

	$description_label = elgg_echo("todo:label:additionalcomments");
	$description_input = elgg_view("input/plaintext", array('name' => 'submission_description', 
															'id' => 'submission_description', 
															'value' => $description));

	$submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('submit')));
	
	$ajax_spinner = '<div id="submission-ajax-spinner" class="elgg-ajax-loader"></div>';

	$file_submit_url = elgg_add_action_tokens_to_url(elgg_get_site_url() . 'mod/todo/actions/todo/upload.php');
	
	$script = <<<HTML
		<script type="text/javascript">
		
		var file_submit_url = "$file_submit_url";
		
		$("div#content_display_div").show();
		showDefault();
		
		$("#link_submit").click(
			function() {
				var link = $('#submission_link').val();
				$('#submission_content').append(
					$('<option></option>').attr('selected', 'selected').val(link).html(link)
				);
				showDefault();
				$('#submission_link').val('');
				return false;
			}
		);
					
		$("#file_form").submit(
			function() {
				var options = { 
						url: stripJunk(file_submit_url), 
						type: "POST", 
				        target:        '#submission-output',   // target element(s) to be updated with server response 
						clearForm: true,
				        beforeSubmit:  showRequest,  // pre-submit callback 
				        success:       showResponse,  // post-submit callback 
						error: fileError
				    };
				$(this).ajaxSubmit(options); 
				return false;
			}
		);
		
		// pre-submit callback 
		function showRequest(formData, jqForm, options) { 
		    var queryString = $.param(formData); 
		    $("#submission-ajax-spinner").show();
		    return true;	
		} 

		// post-submit callback 
		function showResponse(data)  { 
		    $("#submission-ajax-spinner").hide();
			var file = eval( "(" + data + ")" );
			$('#submission_content').append(
				$('<option></option>').attr('selected', 'selected').val(file.guid).html(file.name)
			);
			showDefault();
		}
		
		// error 
		function fileError(XMLHttpRequest, textStatus, errorThrown) {
			//alert(errorThrown + " "  + textStatus);
		}
					
		function showDefault() {
			$("div.content_div").hide();
			$("div#content_display_div").show();
			$("div#submission-content-menu").show();
			$("div#submission-control-back").hide();
			//$("select#submission_content option:odd").css({'background-color' : '#dedede'});
		}
		
		function todoShowDiv(tab_id)
		{
			var div_name = "div#" + tab_id;
			$("div.content_div").hide();
			$("div#submission-content-menu").hide();
			$("div#submission-control-back").show();
			$(div_name).show();
		}
		</script>
HTML;

	// Build Form Body
	$form_body = <<<HTML

	<div style='padding: 10px;'>
		<div>
			<h3>$title_label</h3><br />
		</div>
		<div id='submission-content-container'>
			<h3>$content_label</h3><br />
			<div id='submission-content-menu' class='content_menu'>
				$menu_items
			</div>
			<div id='submission-control-back' class='content_menu'>
				$back_button
			</div>
			<div id='submission-content'>
				$content_display_div
				$add_link_div
				$add_file_div
				$ajax_spinner
				<div id='submission-output' style='display: none;'></div>
			</div>
			<div style='clear:both;'></div>
			<br />
			<div id="submission-error-message">
			</div>
		</div>
		<hr />
		<div>
			<label>$description_label</label><br />
	        $description_input
		</div><br />
		<div>
			$submit_input
			$container_hidden
			$entity_hidden
		</div>
	</div>

HTML;
	echo $script . elgg_view('input/form', array('body' => $form_body, 'id' => 'todo-submission-form'));
	
}
