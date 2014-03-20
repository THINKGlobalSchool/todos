<?php
/**
 * Todo widget content
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 */

echo elgg_view('filtrate/dashboard', array(
	'menu_name' => 'todo_dashboard',
	'list_url' => elgg_get_site_url() . 'ajax/view/todo/list',
	'default_params' => array(
		'context' => 'assigned',
		'priority' => 0,
		'status' => 'incomplete',
		'sort_order' => 'DESC'
	),
	'disable_advanced' => true,
	'disable_history' => true
));