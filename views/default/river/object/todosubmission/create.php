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

$object = $vars['item']->getObjectEntity();

// Get associated todo object.. may be deleted or disabled
$access_status = access_get_show_hidden_status();
access_show_hidden_entities(true);
$todo = get_entity($object->todo_guid);

if ($todo->enabled == 'yes'){
	$params = array(
		'href' => $todo->getURL(),
		'text' => $todo->title,
	);
	
	$link = elgg_view('output/url', $params);
	
	$content = elgg_echo("todosubmission:river:create", array($link));
} else if ($todo->enabled == 'no'){
	$content = elgg_echo("todosubmission:river:create", array($todo->title));
} else if (!$todo){
	$content = elgg_echo("todosubmission:river:createdeleted");
}

access_show_hidden_entities($access_status);

echo $content;
