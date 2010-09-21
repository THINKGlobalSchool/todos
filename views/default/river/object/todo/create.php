<?php
	/**
	 * Todo created river view
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
	$url = $object->getURL();
	$published = $object->time_published ? $object->time_published : $object->time_created;
	
	$url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
	$contents = strip_tags($object->txt); //strip tags from the contents to stop large images etc blowing out the river view
	$contents = elgg_view('output/longtext', array('value' => $contents));
	$string = sprintf(elgg_echo("todo:river:created"),$url) . " ";
	$string .= elgg_echo("todo:river:create") . " <a href=\"" . $object->getURL() . "\">" . $object->title  .  "</a>";
	$string .= " <span class='entity_subtext'>" . elgg_echo('todo:status:published') . ": " . friendly_time($published);
	if (isloggedin()){
		$string .= "<a class='river_comment_form_button link'>Comment</a>";
		$string .= elgg_view('likes/forms/link', array('entity' => $object));
	}
	$string .= "</span>";
?>

<?php echo $string; ?>
