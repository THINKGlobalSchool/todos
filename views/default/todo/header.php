<?php
/**
 * Todo header menu extension
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 */

// If we're on the todo dashboard, display the secondary header menu
if (elgg_in_context('todo') && get_input('todo_dashboard') && $vars['name'] == 'title') {
	$menu = elgg_view_menu('todo-secondary-header', array(
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz'
	));
	echo "<div id='todo-secondary-header'>{$menu}</div>";
}
