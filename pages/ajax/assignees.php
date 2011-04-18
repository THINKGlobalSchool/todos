<?php
/**
 * Todo Assignee List
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/engine/start.php");
	
$guid = get_input('guid');
	
$assignees = get_todo_assignees($guid);	
	
if ($assignees) {
	foreach ($assignees as $assignee) {
		$member_list .= elgg_view('todo/assignee', array('entity' => $assignee));
	}
	$assignees_title = "<label>" . elgg_echo('todo:label:currentassignees') . "</label>";
	$member_list .= "<div style='clear: both;'></div>";
} 

$unassign_url = elgg_add_action_tokens_to_url(elgg_get_site_url() . 'mod/todo/actions/todo/unassign.php');

echo <<<HTML
	<script type='text/javascript'>
		var unassign_url = "$unassign_url";
		function unassignAssignee(assignee_guid) {
			$.ajax({
				url: stripJunk(unassign_url),
				type: "POST",
				data: {assignee_guid: assignee_guid, todo_guid: $guid},
				cache: false, 
				dataType: "html", 
				error: function() {
					//alert('There was an error');	
				},
				success: function(data) {
					loadAssignees($guid);
				}
			});
		}
		
	</script>
	$assignees_title
	$member_list
HTML;

?>