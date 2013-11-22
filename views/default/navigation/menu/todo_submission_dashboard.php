<?php
/**
 * Todo Submission Dashboard Menu
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

// Use the todo dashboard menu
echo elgg_view('navigation/menu/todo_dashboard', $vars);