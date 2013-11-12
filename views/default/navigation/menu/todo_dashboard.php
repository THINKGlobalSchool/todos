<?php
/**
 * Todo Dashboard Menu
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['menu']        Array of menu items
 * @uses $vars['item_class']  Additional CSS class for each menu item
 */

$content = "<div id='todo-dashboard-menu-container'>";

$item_class = elgg_extract('item_class', $vars, '');

// Main section
$content .= elgg_view('navigation/menu/elements/todo_dashboard_section', array(
	'items' => $vars['menu']['main'],
	'class' => "todo-dashboard-menu-main",
	'section' => 'main',
	'name' => 'dashboard',
	'item_class' => $item_class
));

// Advanced section
$content .= elgg_view('navigation/menu/elements/todo_dashboard_section', array(
	'items' => $vars['menu']['advanced'],
	'class' => "todo-dashboard-menu-advanced",
	'section' => 'advanced',
	'name' => 'dashboard',
	'item_class' => $item_class
));


// Extras section
$content .= elgg_view('navigation/menu/elements/todo_dashboard_section', array(
	'items' => $vars['menu']['extras'],
	'class' => "todo-dashboard-menu-extras",
	'section' => 'extras',
	'name' => 'dashboard',
	'item_class' => $item_class
));

echo $content;