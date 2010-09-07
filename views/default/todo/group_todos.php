<?php

	/**
	 * To Do Group list
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
			
	// get the groups todo's
	$todos = elgg_get_entities(array('type' => 'object', 'subtype' => 'todo', 
										'container_guids' => page_owner(), 'limit' => 6));
										
	foreach ($todos as $idx => $todo) {
		if (have_assignees_completed_todo($todo->getGUID())) {
			unset($todos[$idx]);
		}
	}
?>
<div class="group_tool_widget todo" style='height: auto; margin-bottom: 5px;'>
<span class="group_widget_link"><a href="<?php echo $vars['url'] . "pg/todo/owned/" . page_owner_entity()->username; ?>"><?php echo elgg_echo('link:view:all')?></a></span>
<h3><?php echo elgg_echo('todo:label:upcomingtodos') ?></h3>
<?php	
if($todos){
	foreach($todos as $todo){
			
		//get the owner
		$owner = $todo->getOwnerEntity();

		//get the time
		$due_date = date("F j, Y", $todo->due_date);
		//$friendlytime = friendly_time($todo->time_created);
		
	    $info = "<div class='entity_listing_icon'>" . elgg_view('profile/icon',array('entity' => $todo->getOwnerEntity(), 'size' => 'tiny')) . "</div>";

		//get the bookmark entries body
		$info .= "<div class='entity_listing_info'><p class='entity_title'><a href=\"{$todo->getURL()}\">{$todo->title}</a></p>";
				
		//get the user details
		$info .= "<p class='entity_subtext'><b>Due: {$due_date}</b></p>";
		$info .= "</div>";
		//display 
		echo "<div class='entity_listing clearfloat'>" . $info . "</div>";
	} 
} else {
	$create_todo = $vars['url'] . "pg/todo/createtodo/?container_guid=" . page_owner_entity()->username;
	echo "<p class='margin_top'><a href=\"{$create_todo}\">" . elgg_echo("todo:new") . "</a></p>";
}
echo "</div>";