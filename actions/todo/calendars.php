<?php
/**
 * Todo Calendar Configuration Save Action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$categories = get_input('categories_list');
$categories = serialize($categories);

elgg_set_plugin_setting('calendar_categories', $categories, 'todo');

system_message(elgg_echo('todo:success:calendarsettings'));
forward(REFERER);