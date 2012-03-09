<?php
/**
 * Submissions ajax view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

// Get inputs
$user_guid = get_input('user_guid');
$group_guid = get_input('group_guid');
$sort_order = get_input('sort_order', 'DESC');
$filter_return = get_input('filter_return');
$time_lower = get_input('time_lower', FALSE);
$time_upper = get_input('time_upper', FALSE);

// Empty wheres/joins arrays
$wheres = array();
$joins = array();

$db_prefix = elgg_get_config('dbprefix');

// Access suffixen
$n1_suffix = get_access_sql_suffix("n_table1");
$t1_suffix = get_access_sql_suffix("t1");

$wheres[] = "(msn1.string IN ('todo_guid')) AND ({$n1_suffix}) AND ({$t1_suffix})";
$joins[] = "JOIN {$db_prefix}metadata n_table1 on e.guid = n_table1.entity_guid";
$joins[] = "JOIN {$db_prefix}metastrings msn1 on n_table1.name_id = msn1.id";
$joins[] = "JOIN {$db_prefix}metastrings msv1 on n_table1.value_id = msv1.id";
$joins[] = "JOIN {$db_prefix}entities t1 on msv1.string = t1.guid";

// If we were provided with a return filter
if ($filter_return !== NULL) {
	// Access SQL
	$n2_suffix = get_access_sql_suffix("n_table2");
	
	// Wheres for return required
	$wheres[] = "((msn2.string IN ('return_required')) AND ({$n2_suffix}))";
	$wheres[] = "((msv2.string IN ('{$filter_return}')) AND ({$n2_suffix}))";

	// Joins for return required
	$joins[] = "JOIN {$db_prefix}metadata n_table2 on t1.guid = n_table2.entity_guid";
	$joins[] = "JOIN {$db_prefix}metastrings msn2 on n_table2.name_id = msn2.id";
	$joins[] = "JOIN {$db_prefix}metastrings msv2 on n_table2.value_id = msv2.id";
}

// Check for a group guid, include another where clause
if ($group_guid) {
	$wheres[] = "((t1.container_guid = {$group_guid}))";	
}

$options = array(
	'type' => 'object',
	'subtype' => 'todosubmission',
	'owner_guid' => $user_guid,
	'full_view' => FALSE,
	'order_by' => "e.time_created {$sort_order}",
	'wheres' => $wheres,
	'joins' => $joins,
	'limit' => 15,
);

// Add time lower if supplied
if ($time_lower) {
	$options['created_time_lower'] = $time_lower;
}

// Add time upper if supplied
if ($time_upper) {
	$options['created_time_upper'] = $time_upper;
}

// Get content
echo elgg_list_entities($options, 'elgg_get_entities', 'todo_view_entities_simple');