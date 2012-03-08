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

$user_guid = get_input('user_guid');
$group_guid = get_input('group_guid');

$user = get_entity($user_guid);

// Check for a group guid, will need to create bonus SQL
if ($group_guid) {
	$db_prefix = elgg_get_config('dbprefix');
	
	// Access SQL
	$n_suffix = get_access_sql_suffix("n_table");
	$t_suffix = get_access_sql_suffix("t");
	
	// Group Options
	$options = array(
		'type' => 'object',
		'subtype' => 'todosubmission',
		'owner_guid' => $user_guid,
		'full_view' => FALSE,
		// Wheres to grab todos and todo_guid metadata
		'wheres' => array(
			"(msn.string IN ('todo_guid')) AND ({$n_suffix})",
			"((t.container_guid = {$group_guid}) AND ({$t_suffix}))"
		),
		// Joins for metadata/todo
		'joins' => array(
			"JOIN {$db_prefix}metadata n_table on e.guid = n_table.entity_guid",
			"JOIN {$db_prefix}metastrings msn on n_table.name_id = msn.id",
			"JOIN {$db_prefix}metastrings msv on n_table.value_id = msv.id",
			"JOIN {$db_prefix}entities t on msv.string = t.guid",
		),
	);
} else { // No group
	$options = array(
		'type' => 'object',
		'subtype' => 'todosubmission',
		'owner_guid' => $user_guid,
		'full_view' => FALSE,
	);
}

echo elgg_list_entities($options);