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

$url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
$string = sprintf(elgg_echo("todosubmission:river:created"),$url) . " ";

// Get associated todo object.. may be deleted or disabled
$access_status = access_get_show_hidden_status();
access_show_hidden_entities(true);
$todo = get_entity($object->todo_guid);
if ($todo->enabled == 'yes'){
	$string .= elgg_echo("todosubmission:river:create") . " titled <a href=\"" . $todo->getURL() . "\">" . $todo->title  .  "</a>";
} else if ($todo->enabled == 'no'){
	$string .= elgg_echo("todosubmission:river:create") . ' titled ' . $todo->title;
} else if (!$todo){
	$string .= elgg_echo("todosubmission:river:createdeleted");
}

$string .= " <span class='entity_subtext'>" . friendly_time($object->time_created) . "</span>";

access_show_hidden_entities($access_status);

echo $string;
