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
$group_guid = get_input('group_guid', NULL);
$sort_order = get_input('sort_order', 'DESC');
$filter_return = get_input('filter_return');
$filter_ontime = get_input('filter_ontime');
$start_date = get_input('start_date', FALSE);
$end_date = get_input('end_date', FALSE);
$limit = get_input('limit', 10);

// Get user (may be passed in via username or guid)
$user = get_user_by_username(get_input('user', false));
$user_guid = $user ? $user->guid : get_input('user_guid', elgg_get_logged_in_user_guid());

// Empty wheres/joins arrays
$wheres = array();
$joins = array();

$db_prefix = elgg_get_config('dbprefix');

// Access suffixen
$n1_suffix = _elgg_get_access_where_sql(array("table_alias" => "n_table1", "guid_column" => "entity_guid"));
$t1_suffix = _elgg_get_access_where_sql(array("table_alias" => "t1"));

$joins[] = "JOIN {$db_prefix}metadata n_table1 on e.guid = n_table1.entity_guid";
$joins[] = "JOIN {$db_prefix}metastrings msn1 on n_table1.name_id = msn1.id";
$joins[] = "JOIN {$db_prefix}metastrings msv1 on n_table1.value_id = msv1.id";
$joins[] = "JOIN {$db_prefix}entities t1 on msv1.string = t1.guid";

$wheres[] = "(msn1.string IN ('todo_guid')) AND ({$n1_suffix})";
$wheres[] = "{$t1_suffix}";

// If we were provided a return filter
if ($filter_return !== 'all') {
	// Access SQL
	$n2_suffix = _elgg_get_access_where_sql(array("table_alias" => "n_table2", "guid_column" => "entity_guid"));
	
	// Joins for return required
	$joins[] = "JOIN {$db_prefix}metadata n_table2 on e.guid = n_table2.entity_guid";
	$joins[] = "JOIN {$db_prefix}metastrings msn2 on n_table2.name_id = msn2.id";
	$joins[] = "JOIN {$db_prefix}metastrings msv2 on n_table2.value_id = msv2.id";

	// Wheres for return required
	$wheres[] = "((msn2.string IN ('content')) AND ({$n2_suffix}))";
	
	if ($filter_return) {
		$wheres[] = "((msv2.string != '0') AND ({$n2_suffix}))";
	} else {
		$wheres[] = "((msv2.string IN ('0')) AND ({$n2_suffix}))";
	}
}


// If we were provided an on time filter
if ($filter_ontime !== 'all') {
		$n3_suffix = _elgg_get_access_where_sql(array("table_alias" => "n_table3", "guid_column" => "entity_guid"));
		$joins[] = "JOIN {$db_prefix}metadata n_table3 on t1.guid = n_table3.entity_guid";
		$joins[] = "JOIN {$db_prefix}metastrings msn3 on n_table3.name_id = msn3.id";
		$joins[] = "JOIN {$db_prefix}metastrings msv3 on n_table3.value_id = msv3.id";
		$wheres[] = "(msn3.string IN ('due_date')) AND ({$n3_suffix})";

		// Determine operator (less than/equal to or greater than)
		$operator = (int)$filter_ontime ? "<=" : ">";

		$wheres[] = "(UNIX_TIMESTAMP(FROM_UNIXTIME(e.time_created, '%Y%m%d')) {$operator} UNIX_TIMESTAMP(FROM_UNIXTIME(msv3.string, '%Y%m%d')))";
}

// Check for a group guid, include another where clause
if ($group_guid) {
	$wheres[] = "(t1.container_guid = {$group_guid})";	
	elgg_push_context('group_todo_submissions'); // Display slightly different for groups
}

$options = array(
	'type' => 'object',
	'subtype' => 'todosubmission',
	'owner_guid' => $user_guid,
	'full_view' => FALSE,
	'order_by' => "e.time_created {$sort_order}",
	'wheres' => $wheres,
	'joins' => $joins,
	'limit' => $limit,
);

// Add time lower if supplied
if ($start_date) {
	$options['created_time_lower'] = $start_date;
}

// Add time upper if supplied
if ($end_date) {
	$options['created_time_upper'] = $end_date;
}

// Submission stats
echo elgg_view('todo/submission_stats', array(
	'user_guid' => $user_guid,
	'group_guid' => $group_guid
));

// Get content
echo elgg_list_entities($options, 'elgg_get_entities', 'todo_view_entities_table');

if ($group_guid) {
	// Pop group submissions context
	elgg_pop_context();
}

echo <<<JAVASCRIPT
	<script type='text/javascript'>
		elgg.tinymce.init();
		elgg.todo.submission.destroy();
		elgg.todo.submission.init();
	</script>
JAVASCRIPT;

?>