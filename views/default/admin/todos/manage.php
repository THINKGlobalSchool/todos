<?php 
/**
 * Todo Management Tools
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 */
elgg_load_js('elgg.todo.admin');

$move_label = elgg_echo('todo:label:move');
$move_content = elgg_view_form('todo/move');

$move_module = elgg_view_module('inline', $move_label, $move_content);

echo $move_module;
echo "<div id='todo-admin-output'></div>";
