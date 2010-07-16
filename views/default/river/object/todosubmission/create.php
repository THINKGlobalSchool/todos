<?php
	/**
	 * Todo submission reated river view
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	global $CONFIG;

	$performed_by = get_entity($vars['item']->subject_guid); // $statement->getSubject();
	$object = get_entity($vars['item']->object_guid);

	$todo = get_entity($object->todo_guid);
	$url = $todo->getURL();
	
	$url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
	$contents = strip_tags($object->txt); //strip tags from the contents to stop large images etc blowing out the river view
	$contents = elgg_view('output/longtext', array('value' => $contents));
	$string = sprintf(elgg_echo("todosubmission:river:created"),$url) . " ";
	$string .= elgg_echo("todosubmission:river:create") . " <a href=\"" . $todo->getURL() . "\">" . $todo->title  .  "</a>";
	$string .= " <span class='entity_subtext'>" . friendly_time($object->time_created);
	if (isloggedin()){
		$string .= "<a class='river_comment_form_button link'>Comment</a>";
		$string .= elgg_view('likes/forms/link', array('entity' => $object));
	}
	$string .= "</span>";
?>

<?php echo $string;  ?>
