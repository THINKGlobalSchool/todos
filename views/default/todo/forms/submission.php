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
			
		$container_hidden = elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $vars['container_guid']));
		$entity_hidden  = elgg_view('input/hidden', array('internalname' => 'todo_guid', 'value' => $vars['entity']->getGUID()));
	
		if (empty($description)) {
			$description = $vars['user']->todo_description;
			if (!empty($description)) {
				$title = $vars['user']->todo_title;
				$tags = $vars['user']->todo_tags;
				$type = $vars['user']->todo_type;
			}
		}
	
		// Content Menu Items
		$menu_items .= "<a href='#' id='add_link' onclick=\"javascript:todoShowDiv('add_link_container');return false;\">" . elgg_echo('todo:label:addlink') . "</a>";
		$back_button = "<a href='#' id='back_button' onclick=\"javascript:showDefault();return false;\"><< Back</a>";
		
		// Content Div's
		$content_display_div = "<div class='content_div' id='content_display_div'>
									<select class='submission_content_select' id='submission_content' name='submission_content[]' MULTIPLE>
									</select>
								</div>";
								
		$add_link_div = "<div class='content_div' id='add_link_container'>
							<form id='link_form'>
								<label>" . elgg_echo('todo:label:link') . "</label><br />
								" . elgg_view('input/text', array('internalid' => 'submission_link', 'internalname' => 'submission_link')) . "<br />
								" . elgg_view('input/submit', array('internalid' => 'link_submit', 'internalname' => 'link_submit', 'value' => 'Submit')) . "
							</form>
						</div>";
	
		// Labels/Input
		$title_label = elgg_echo("todo:label:newsubmission");
		
		$content_label = elgg_echo("todo:label:content");

		$description_label = elgg_echo("todo:label:additionalcomments");
		$description_input = elgg_view("input/plaintext", array('internalname' => 'submission_description', 
																'internalid' => 'submission_description', 
																'value' => $description));

		$submit_input = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('submit')));


		$script = <<<EOT
			<script type="text/javascript">
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
			
			function showDefault() {
				$("div.content_div").hide();
				$("div#content_display_div").show();
				$("div#main_content_menu").show();
				$("div#back_content_menu").hide();
				//$("select#submission_content option:odd").css({'background-color' : '#dedede'});
			}
			
			function todoShowDiv(tab_id)
			{
				var div_name = "div#" + tab_id;
				$("div.content_div").hide();
				$("div#main_content_menu").hide();
				$("div#back_content_menu").show();
				$(div_name).show();
			}
			</script>
EOT;

		// Build Form Body
		$form_body = <<<EOT

		<div class='contentWrapper todo'>
			<div>
				<h3>$title_label</h3><br />
			</div>
			<div id='add_content_area'>
				<h3>$content_label</h3><br />
				<div id='main_content_menu' class='content_menu'>
					$menu_items
				</div>
				<div id='back_content_menu' class='content_menu'>
					$back_button
				</div>
				<div id='content_container'>
					$content_display_div
					$add_link_div
				</div>
				<div style='clear:both;'></div>
				<br />
				<div id="submission_error_message">
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

EOT;
		echo $script . elgg_view('input/form', array('body' => $form_body, 'internalid' => 'todo_submission_form'));
		
	}
?>