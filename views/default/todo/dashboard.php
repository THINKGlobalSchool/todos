<?php
/**
 * Todo Dashboard
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 */

echo elgg_view_menu('todo_dashboard', array(
	'sort_by' => 'priority'
));

echo "<div id='todo-dashboard-content-container'></div>";