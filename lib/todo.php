<?php
/**
 * Todo library
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 */

/** CONTENT FUNCTIONS **/
/**
 * Get todo view content
 * @param string $type	Page type
 * @param int $guid		Object guid
 */
function todo_get_page_content_view($type, $guid) {
	// Set full view context for menus
	elgg_push_context('todo_full_view');
	
	$params = array(
		'filter' => '',
		'header' => '',
	);

	// This is messed up, deleted todo's DO exist.. need to fix this checking
	if (elgg_entity_exists($guid)) {
		elgg_push_context('todo_test');
		$entity = get_entity($guid);
		if ($entity->enabled && $type == 'todo' && elgg_instanceof($entity, 'object', 'todo')) {
			$owner = $entity->getContainerEntity();
			$params['title'] = $entity->title;
			$params['content'] = elgg_view_entity($entity, array('full_view' => TRUE));
			$params['content'] .= elgg_view_comments($entity);

			if (elgg_instanceof($owner, 'group')) {
				elgg_push_breadcrumb($owner->name, elgg_get_site_url() . "todo/group/dashboard/{$owner->guid}/owner");
			} else {
				elgg_push_breadcrumb($owner->name, elgg_get_site_url() . "todo/owner/{$owner->username}");
			}

			elgg_push_breadcrumb($entity->title);
			return $params;
		} else if ($entity->enabled && $type == 'submission' && elgg_instanceof($entity, 'object', 'todosubmission')) {
			$params['title'] = elgg_echo('todo:label:viewsubmission');
			$params['content'] = elgg_view_entity($entity, array('full_view' => TRUE));
			
			// Show custom submission annotations
			$submission_annotation_options = array(
				'entity' => $entity,
			);

			if (is_todo_admin() || elgg_get_logged_in_user_entity()->is_parent) {
				$submission_annotation_options['show_add_form'] = false;
			}

			$params['content'] .= elgg_view('todo/submission_annotations', $submission_annotation_options);
			
			$todo = get_entity($entity->todo_guid);
			$owner = $todo->getOwnerEntity();
			
			elgg_push_breadcrumb($owner->name, elgg_get_site_url() . "todo/owner/{$owner->username}");
			elgg_push_breadcrumb($todo->title, $todo->getURL());
			elgg_push_breadcrumb(elgg_echo('todo:label:ownersubmission', array($entity->getOwnerEntity()->name)));
			
			
			return $params;
		} else {
			// Most likely a permission issue here
			register_error(elgg_echo('todo:error:permissiondenied'));
			forward();
		}
	}
	
	$params['content'] = elgg_echo('todo:error:invalid');
	return $params;
}

/**
 * Get todo edit content
 * @param string $type 	'add' or 'edit
 * @param int $guid 	object or container
 */
function todo_get_page_content_edit($type, $guid) {
	// No button or filter
	$params = array(
		'filter' => '',
	);
	
	// Form vars
	$vars = array();
	$vars['id'] = 'todo-edit';
	$vars['name'] = 'todo_edit';
	
	if ($type == 'edit') {
		$title = elgg_echo('todo:title:edit', array());
		if (elgg_entity_exists($guid) && elgg_instanceof($todo = get_entity($guid), 'object', 'todo')) {
			$title .= ": \"$todo->title\"";
			$body_vars = todo_prepare_form_vars($todo);
			$content = elgg_view_form('todo/save', $vars, $body_vars);
			elgg_push_breadcrumb($todo->title, $todo->getURL());
			elgg_push_breadcrumb(elgg_echo('edit'));
		} else {
			$content = elgg_echo('todo:error:edit');
		}
	} else {
		$title = elgg_echo('todo:add');
		$body_vars = todo_prepare_form_vars();
		$content = elgg_view_form('todo/save', $vars, $body_vars);
		elgg_push_breadcrumb($title);
	}
	
	$params['content'] = $content;
	$params['title'] = $title;
	return $params;
}

/**
 * Get todo notifications settings content
 */
function todo_get_page_content_settings_notifications() {
	$params['title'] = elgg_echo('todo:label:settings');
	$params['filter'] = '';
	
	$user = elgg_get_logged_in_user_entity();

	$params['content'] = elgg_view_form('todo/settings');
	
	$params['layout'] = 'one_sidebar';
	return $params;
}

/**
 * Get/list todo's based on critera
 * @param array $params:
 * 
 * context  	       => NULL|STRING which context we're viewing (all, assigned, owned)
 *
 * status              => NULL|STRING complete|incomplete|any
 * 
 * assignee_guid       => NULL|INT get todo's assigned to this user guid (used in assigned context)
 *
 * assigner_guid       => NULL|INT get todo's assigned by this user guid (used in owned context )
 * 
 * submission          => 'yes' | 'no' | null - get todos with/without a submission required
 * 
 * container_guid      => NULL|INT get todo's assigned by container guid (used in all/owned context)
 * 
 * todo_category       => NULL | STRING the todo category (basic_task, assessed_task, exam)
 * 
 * sort_order          => STRING ASC|DESC
 * 
 * order_by_metadata   => STRING which metadata to order by (ie: due_date)
 * 
 * list                => BOOL list todo's instead of get (default FALSE)
 * 
 * count               => BOOL count todos (only works with list => FALSE)
 * 
 * due_date            => int due date timestamp
 *
 * due_operand         => string due date operand for use with due date
 * 
 * due_start           => due start date
 *
 * due_end             => due end date
 */
function get_todos(array $params) {
	// Set list action
	if (!$params['list']) {
		$get_from_metadata = 'elgg_get_entities_from_metadata';
		$get_from_relationship = 'elgg_get_entities_from_relationship';
		$count = $params['count'] ? TRUE : FALSE;
	} else {
		$get_from_metadata = 'elgg_list_entities_from_metadata';
		$get_from_relationship = 'elgg_list_entities_from_relationship';
		$count = FALSE;
	}
	
	// Default order by if not supplies
	if (!$params['order_by_metadata']) {
		$params['order_by_metadata'] = 'due_date';
	}
	
	// Default sort order
	if (!$params['sort_order']) {
		$params['sort_order'] = "ASC";
	}

	// Default limit 
	if (!$params['limit']) {
		$params['limit'] = get_input('limit', 10);
	}
	
	// Default offset 
	if (!$params['offset']) {
		$params['offset'] = get_input('offset', 0);
	}
	
	// Common options
	$options = array(
		'type' => 'object',
		'subtype' => 'todo',
		'full_view' => FALSE,
		'order_by_metadata' => array('name' => $params['order_by_metadata'], 'as' => 'int', 'direction' => $params['sort_order']),
		'limit' => $params['limit'], 
		'offset' => $params['offset'],
		'count' => $count,
	);
	
	// Published status options
	$published_options = array(
		'metadata_name' => 'status',
		'metadata_value' => TODO_STATUS_PUBLISHED,	
	);
	
	$complete_or_manual = array(
		'metadata_name_value_pairs' => array(
			array(
				'name' => 'complete',
				'value' => 1, 
				'operand' => '='),
			array(
				'name' => 'manual_complete',
				'value' => 1,
				'operand' => '=',
			)),
		'metadata_name_value_pairs_operator' => 'OR',
	);	

	$dbprefix = elgg_get_config('dbprefix');
	
	// Without complete/manual wheres (for owned/all)
	$complete = get_metastring_id('complete');
	$manual_complete = get_metastring_id('manual_complete');
	$one_id = get_metastring_id(1);
						
	$without_complete_manual_wheres = array();
	$without_complete_manual_wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$dbprefix}metadata md
			WHERE md.entity_guid = e.guid
				AND md.name_id = $complete
				AND md.value_id = $one_id)";

	$without_complete_manual_wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$dbprefix}metadata md
			WHERE md.entity_guid = e.guid
				AND md.name_id = $manual_complete
				AND md.value_id = $one_id)";
			
	// Include due date and operand if set 
	if ($params['due_date']) {
		if (!$params['due_operand']) {
			$params['due_operand'] = '=';
		}
		
		$due_date = $params['due_date'];
		$due_operand = $params['due_operand'];

		$suffix = _elgg_get_access_where_sql(array("table_alias" => "mf_table", "guid_column" => "entity_guid"));
		$due_joins = array();
		
		$due_joins[] = "JOIN {$dbprefix}metadata mf_table on e.guid = mf_table.entity_guid";
		$due_joins[] = "JOIN {$dbprefix}metastrings mf_name on mf_table.name_id = mf_name.id";
		$due_joins[] = "JOIN {$dbprefix}metastrings mf_value on mf_table.value_id = mf_value.id";		

	 	$due_where = "(mf_name.string = 'due_date' AND mf_value.string {$due_operand} {$due_date})";
	} else if ($params['due_start'] && $params['due_end']) {

		// Due between start and end date
		$due_start = $params['due_start'];
		$due_end = $params['due_end'];
		
		$suffix = _elgg_get_access_where_sql(array("table_alias" => "mf_table", "guid_column" => "entity_guid"));
		$due_joins = array();
		
		$due_joins[] = "JOIN {$dbprefix}metadata mf_table on e.guid = mf_table.entity_guid";
		$due_joins[] = "JOIN {$dbprefix}metastrings mf_name on mf_table.name_id = mf_name.id";
		$due_joins[] = "JOIN {$dbprefix}metastrings mf_value on mf_table.value_id = mf_value.id";		

	 	$due_where = "(mf_name.string = 'due_date' AND (mf_value.string > {$due_start} AND mf_value.string <= {$due_end}))";
	}

	// Check for submission param
	if (in_array($params['submission'], array('yes', 'no'))) {
		$submission_joins[] = "JOIN {$dbprefix}metadata msr_table on e.guid = msr_table.entity_guid";
		$submission_joins[] = "JOIN {$dbprefix}metastrings msr_name on msr_table.name_id = msr_name.id";
		$submission_joins[] = "JOIN {$dbprefix}metastrings msr_value on msr_table.value_id = msr_value.id";

		if ($params['submission'] == 'yes') {
			$submission_where = "(msr_name.string = 'return_required' AND msr_value.string = 1)";
		} else {
			$submission_where = "(msr_name.string = 'return_required' AND msr_value.string = 0)";
		}
	} 

	// Check for todo category
	if (in_array($params['todo_category'], array(TODO_BASIC_TASK, TODO_ASSESSED_TASK, TODO_EXAM))) {
		$category_joins[] = "JOIN {$dbprefix}metadata msc_table on e.guid = msc_table.entity_guid";
		$category_joins[] = "JOIN {$dbprefix}metastrings msc_name on msc_table.name_id = msc_name.id";
		$category_joins[] = "JOIN {$dbprefix}metastrings msc_value on msc_table.value_id = msc_value.id";
		$category_where = "(msc_name.string = 'category' AND msc_value.string = '{$params['todo_category']}')";
	}

	// Get options by context
	switch($params['context']) {
		case 'all':
		default: 
		/********************* ALL ************************/
			// Show only todo's with container_guid (ie: groups)
			if ($params['container_guid']) {
				$options['container_guid'] = $params['container_guid'];
			}

			// Show based on status
			if ($params['status'] == 'complete') {
				// Use params, defaults and publshed and complete or manual
				$options = array_merge($options, $published_options, $complete_or_manual);
			} else if ($params['status'] == 'incomplete') {
				set_input('display_label', true);

				$options = array_merge($options, $published_options);
				$options['wheres'] = $without_complete_manual_wheres;
			}

			// Due filter
			$joins = $due_joins;
			$options['wheres'][] = $due_where;
			$options['joins'] = $joins;

			break;
		case 'owned':
		/********************* OWNED **********************/
			set_input('display_label', true);

			$container_guid = $params['container_guid'];
			$assigner_guid = $params['assigner_guid'];

			// Got both an assigner and container!
			if ($container_guid && $assigner_guid) {
				// Both the same (probably shouldn't happen)
				if ($container_guid === $assigner_guid) {
					$options['container_guid'] = $container_guid;
				} else {
					// Different container/assigner
					$options['container_guid'] = $container_guid;
					$options['owner_guid'] = $assigner_guid;
				}
			} else if ($container_guid) {
				$options['container_guid'] = $container_guid;
			} else {
				// Only got an assigner, but this could be a group
				if (elgg_instanceof(get_entity($assigner_guid), 'group')) {
					$options['container_guid'] = $assigner_guid;
				} else {
					$options['owner_guid'] = $assigner_guid;
				}
			}

			// Check for assingee_guid (may be looking for todos owned by x, assigned to y)
			// NOTE: This excludes assigner == assignee
			if ($params['assignee_guid'] && $params['assignee_guid'] != $options['owner_guid'] && $params['assignee_guid'] != $options['container_guid']) {
				$options['relationship'] = TODO_ASSIGNEE_RELATIONSHIP;
				$options['relationship_guid'] = $params['assignee_guid'];
				$options['inverse_relationship'] = TRUE;
			}
			
			// Show both published and drafts when viewing owned
			$published_options = array(); // Nuke it

			// Show based on status
			if ($params['status'] == 'complete') {
				// Use params, defaults and complete or manual
				$options = array_merge($options, $complete_or_manual);	
			} else if ($params['status'] == 'incomplete') {
				$options = array_merge($options, $published_options);
				$options['wheres'] = $without_complete_manual_wheres;					
			}

			// Due filter
			$joins = $due_joins;
			$options['wheres'][] = $due_where;
			$options['joins'] = $joins;

			break;
		case 'assigned':
		/********************* ASSIGNED ********************/
			$test_id = get_metastring_id('manual_complete');
			$one_id = get_metastring_id(1);
			$wheres = array();

			$relationship = COMPLETED_RELATIONSHIP;
			
			// The user to whom the todo's are assigned
			$user_id = $params['assignee_guid'];

			// Check and see if we also have an assigner guid
			// NOTE: This excludes assigner == assignee
			if ($params['assigner_guid'] && $params['assigner_guid'] != $user_id) {
				$options['owner_guid'] = $params['assigner_guid'];
			}
			
			if (!$user_id) {
				$user_id = elgg_get_logged_in_user_guid();
			}

			// This is a new addition, I don't see why we can't pass 
			// a container guid in this context
			if ($params['container_guid']) {
				$options['container_guid'] = $params['container_guid'];
			}
			
			// Build list based on status
			if ($params['status'] == 'complete') {
				$wheres[] = "(EXISTS (
						SELECT 1 FROM {$dbprefix}entity_relationships r2 
						WHERE r2.guid_one = '$user_id'
						AND r2.relationship = '$relationship'
						AND r2.guid_two = e.guid) OR 
							EXISTS (
						SELECT 1 FROM {$dbprefix}metadata md
						WHERE md.entity_guid = e.guid
							AND md.name_id = $test_id
							AND md.value_id = $one_id))";


			} else if ($params['status'] == 'incomplete') {	
				set_input('display_label', true);
				// Non existant 'manual complete'
				$wheres[] = "NOT EXISTS (
						SELECT 1 FROM {$dbprefix}metadata md
						WHERE md.entity_guid = e.guid
							AND md.name_id = $test_id
							AND md.value_id = $one_id)";

				$wheres[] = "NOT EXISTS (
						SELECT 1 FROM {$dbprefix}entity_relationships r2 
						WHERE r2.guid_one = '$user_id'
						AND r2.relationship = '$relationship'
						AND r2.guid_two = e.guid)";
			}

			$joins = $due_joins;
			$wheres[] = $due_where;

			$options = array_merge($options, $published_options);
			$options['wheres'] = $wheres;
			$options['joins'] = $joins;
			$options['relationship'] = TODO_ASSIGNEE_RELATIONSHIP;
			$options['relationship_guid'] = $user_id;
			$options['inverse_relationship'] = FALSE;

			break;
	}

	// Add other global joins
	if (!is_array($options['joins'])) {
		$options['joins'] = $submission_joins;
	} else if (is_array($submission_joins)) {
		$options['joins'] = array_merge($options['joins'], $submission_joins);
	}

	if (!is_array($options['joins'])) {
		$options['joins'] = $category_joins;
	} else if (is_array($category_joins)) {
		$options['joins'] = array_merge($options['joins'], $category_joins);
	}

	// Add other global wheres
	$options['wheres'][] = $submission_where;
	$options['wheres'][] = $category_where;

	// Trigger a hook to allow plugins to provice extra options when getting todos
	$options = elgg_trigger_plugin_hook('get_options', 'todo', $params, $options);

	// Get/list todos
	$content = $get_from_relationship($options);	

	// If we have nothing, and we're listing, return a nice no results message
	if (!$content && $params['list']) {
		return "<h3 class='center' style='border-top: 1px dotted #CCCCCC; padding-top: 4px; margin-top: 5px;'>" . elgg_echo('todo:label:noresults') . "</h3>"; 
	} else {
		return $content;
	}
}

/**
 * Pull together todo variables for the save form
 *
 * @param ElggObject       $todo
 * @return array
 */
function todo_prepare_form_vars($todo = NULL) {

	// input names => defaults
	$values = array(
		'title' => NULL,
		'description' => NULL,
		'due_date' => NULL,
		'start_date' => NULL,
		'status' => TODO_STATUS_PUBLISHED,
		'access_level' => TODO_ACCESS_LEVEL_LOGGED_IN,
		'access_id' => NULL,
		'category' => NULL,
		'tags' => NULL,
		'suggested_tags' => NULL,
		'container_guid' => NULL,
		'guid' => NULL,
		'return_required' => 0,
		'rubric_select' => 0,
		'rubric_guid' => NULL,
		'grade_required' => 0,
		'grade_total' => NULL,
	);


	if ($todo) {
		foreach (array_keys($values) as $field) {
			if (isset($todo->$field)) {
				$values[$field] = $todo->$field;
			}
		}
	}

	if (elgg_is_sticky_form('todo_edit')) {
		$sticky_values = elgg_get_sticky_values('todo_edit');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}
	
	elgg_clear_sticky_form('todo_edit');

	return $values;
}

/**
 * Assign users to a todo. 
 * Takes an array of guids, can be either users or groups 
 * If a group guid is encountered, the users from the group
 * will be assigned. 
 *
 * @param array $assignee_guids
 * @param int $todo_guid
 * @return bool 
 */
function assign_users_to_todo($assignee_guids, $todo_guid) {
	$todo = get_entity($todo_guid);
	// Set up relationships for asignees, can be users or groups (multiple)
	if (is_array($assignee_guids)) {
		$success = true;
		foreach ($assignee_guids as $assignee) {
			$entity = get_entity($assignee);
			if ($entity instanceof ElggUser) {
				$success &= assign_user_to_todo($assignee, $todo_guid);
			} else if ($entity instanceof ElggGroup) {
				// If we've got a group, we need to assign each member of that group
				foreach ($entity->getMembers(0) as $member) {
					if ($member->getGUID() == $todo->owner_guid) {
						continue;
					}
					$success &= assign_user_to_todo($member->getGUID(), $todo_guid);
					
				}
			} else if (TODO_CHANNELS_ENABLED && $entity->getSubtype() == 'shared_access') {
				// If shared access (channels) is enabled, we need to assign the users of that channel																			
				$channel_members = elgg_get_entities_from_relationship(array(
																		'relationship' => 'shared_access_member',
																		'relationship_guid' => $entity->getGUID(),
																		'inverse_relationship' => TRUE,
																		'types' => 'user',
																		'limit' => 0
																	));

				foreach($channel_members as $member) {
					if ($member->getGUID() == $todo->owner_guid) {
						continue;
					}
					$success &= assign_user_to_todo($member->getGUID(), $todo_guid);
				}
			}
		}
		return $success;
	}
	return true; 
}

/**
 * Assign a single user to a todo
 * 
 * @param int $user_guid
 * @param int $todo_guid
 * @return bool 
 */
function assign_user_to_todo($user_guid, $todo_guid) {
	// Check if user is already assigned, if not, assign.
	if (!is_todo_assignee($todo_guid, $user_guid)) {
		$todo = get_entity($todo_guid);
		$owner = get_entity($todo->container_guid);
		if (add_entity_relationship($user_guid, TODO_ASSIGNEE_RELATIONSHIP, $todo_guid)) {
			return elgg_trigger_event('assign', 'object', array('todo' => get_entity($todo_guid), 'user' => get_entity($user_guid)));
		} else {
			return false;
		}
	} else {
		return true;
	}
}

/** 
 * Notifies a todo's users that the todo has been created and assigned to them
 * 
 * @param int $todo_guid
 * @return bool
 */
function notify_todo_users_assigned($todo) {
	if ($todo->getSubtype() == 'todo') {
		$owner = get_entity($todo->container_guid);
		$assignees = get_todo_assignees($todo->getGUID());
		$success = true;
		foreach ($assignees as $assignee) {
			$success &= notify_user($assignee->getGUID(), 
									$todo->container_guid,
									elgg_echo('todo:email:subjectassign'), 
									sprintf(elgg_echo('todo:email:bodyassign'), 
									$owner->name, 
									$todo->title, 
									$todo->getURL())
									);
		}
		return $success;
	} 
	return false;		
}

/**
 * Set 'accepted' for a user on given todo
 * 
 * @param int $user_guid
 * @param int $todo_guid
 * @return bool 
 */
function user_accept_todo($user_guid, $todo_guid) {
	// Check if user has already accepted
	if (!has_user_accepted_todo($todo_guid, $user_guid)) {
		return add_entity_relationship($user_guid, TODO_ASSIGNEE_ACCEPTED, $todo_guid);
	} else {
		return true;
	}
}

/**
 * Return an array containing the todo access levels
 * 
 * @return array
 */
function get_todo_access_array() {
	$access = array(TODO_ACCESS_LEVEL_LOGGED_IN => elgg_echo('todo:label:loggedin'),
					TODO_ACCESS_LEVEL_ASSIGNEES_ONLY => elgg_echo('todo:label:assigneesonly'));
	return $access;
}

/**
 * Return an array containing a list of all site groups for use
 * in a pulldown/dropdown box
 * 
 * @return array 
 */
function get_todo_groups_array() {
	// Get user's groups
	$options = array(
		'relationship' => 'member',
		'relationship_guid' => elgg_get_logged_in_user_guid(),
		'inverse_relationship' => FALSE,
		'joins' => array("JOIN " . elgg_get_config("dbprefix") . "groups_entity ge ON e.guid = ge.guid"),
		'order_by' => 'ge.name ASC',
		'limit' => 100,
	);
	$groups = elgg_get_entities_from_relationship($options);

	$array = array();
	foreach ($groups as $group) {
		$array[$group->getGUID()] = $group->name;
	}

	return $array;
}

/**
 * If enabled, return an array of rubrics for use in pulldowns
 * 
 * @return mixed
 */
function get_todo_rubric_array() {
	if (TODO_RUBRIC_ENABLED) {
		$rubrics = elgg_get_entities(array(
			'types' => 'object', 
			'subtypes' => 'rubric',
			'limit' => 35,
			'order_by' => 'e.time_created DESC',
			'owner_guid' => elgg_get_logged_in_user_guid()
		));
		$rubrics_array = array();
		
		foreach ($rubrics as $rubric) {
			$rubrics_array[$rubric->getGUID()] = $rubric->title;
		}
		return $rubrics_array;
	}
	return false;
}

/**
 * Return an array of users assigned to given todo
 *
 * @param int $guid // todo guid
 * @return array
 */
function get_todo_assignees($guid) {
	$db_prefix = elgg_get_config('dbprefix');
	
	$options = array(
		'relationship' => TODO_ASSIGNEE_RELATIONSHIP,
		'relationship_guid' => $guid,
		'inverse_relationship' => TRUE,
		'types' => array('user', 'group'),
		'limit' => 0,
		'offset' => 0,
		'count' => false,
		// Order by user name
		'joins' => array("JOIN {$db_prefix}users_entity ue on ue.guid = e.guid"),
		'order_by' => 'ue.name ASC',
	);
	
	$entities = elgg_get_entities_from_relationship($options);
		
	$assignees = array();
	
	// Need to be flexible, most likely will have either just users, or just 
	// groups, but will take into account both just in case
	if ($entities) {
		foreach($entities as $entity) {
			if (elgg_instanceof($entity, 'user')) {
				$assignees[] = $entity;
			} else if (elgg_instanceof($entity, 'group')) {
				foreach ($entity->getMembers() as $member) {
					$assignees[] = $member;
				}
			}
		}
	}
	
	return $assignees;
}

/**
 * Return an elgg batch of submissions for given todo
 *
 * @param int $todo_guid todo_guid
 * @return array
 */
function get_todo_submissions_batch($todo_guid, $limit = 10) {
	$options = array(
		'relationship' => SUBMISSION_RELATIONSHIP,
		'relationship_guid' => $todo_guid,
		'inverse_relationship' => TRUE,
		'type' => 'object',
		'subtype' => 'todosubmission',
		'limit' => $limit,
	);

	$entities = new ElggBatch('elgg_get_entities_from_relationship', $options);

	return $entities;
}

/**
 * Return a count of submissions for a given todo
 */
function get_todo_submissions_count($todo_guid) {
	$options = array(
		'relationship' => SUBMISSION_RELATIONSHIP,
		'relationship_guid' => $todo_guid,
		'inverse_relationship' => TRUE,
		'type' => 'object',
		'subtype' => 'todosubmission',
		'limit' => 0,
		'count' => TRUE,
	);
	
	return elgg_get_entities_from_relationship($options);
}

/**
 * Return all todos a user has been assigned
 * @TODO this should go away..
 * @param int 
 * @return array 
 */
function get_users_todos($user_guid) {
	$todos = elgg_get_entities_from_relationship(array(
		'relationship' => TODO_ASSIGNEE_RELATIONSHIP, 
		'relationship_guid' => $user_guid, 
		'inverse_relationship' => FALSE,
		'limit' => 9999,
		'offset' => 0
	));
													
	// Do not include draft todos
	foreach ($todos as $idx => $todo) {
		if ($todo->status == TODO_STATUS_DRAFT) {
			unset($todos[$idx]);
		}
	} 
	
	return $todos;
}

/** 
 * Determine if given user is an assignee of given todo
 * 
 * @param int $todo_guid
 * @param int $user_guid
 * @return bool
 */
function is_todo_assignee($todo_guid, $user_guid) {
	$object = check_entity_relationship($user_guid, TODO_ASSIGNEE_RELATIONSHIP , $todo_guid);
	if ($object) {
		return true;
	} else {
		return false;
	}
}

/**
 * Determine if given user has made a submission to given todo
 * 
 * @param int $user_guid
 * @param int $todo_guid
 * @return mixed
 */
function has_user_submitted($user_guid, $todo_guid) {
	$todo = get_entity($todo_guid);
	if (get_user_submission($user_guid, $todo_guid)) {
		return true;
	} else {
		return false;
	}
}

/** 
 * Returns a given users submission for a todo, if any
 * 
 * @param int $user_guid
 * @param int $todo_guid
 * @return mixed
 */
function get_user_submission($user_guid, $todo_guid) {
	$options = array(
		'relationship' => SUBMISSION_RELATIONSHIP,
		'relationship_guid' => $todo_guid,
		'owner_guid' => $user_guid,
		'inverse_relationship' => TRUE,
		'type' => 'object',
		'subtype' => 'todosubmission',
		'limit' => 1,
	);

	$entities = elgg_get_entities_from_relationship($options);

	if ($entities) {
		return $entities[0];
	}

	return FALSE;
}

/** 
 * Determine if given user is an assignee of given todo
 * 
 * @param int $todo_guid
 * @param int $user_guid
 * @return bool
 */
function has_user_accepted_todo($user_guid, $todo_guid) {
	$object = check_entity_relationship($user_guid, TODO_ASSIGNEE_ACCEPTED , $todo_guid);
	if ($object) {
		return true;
	} else {
		return false;
	}
}

/**
 * Count user's unaccepted todo's
 * 
 * @param $user_guid int
 * @return int
 */
function count_unaccepted_todos($user_guid) {

	$options = array(
		'type' => 'object',
		'subtype' => 'todo',
		'relationship' => TODO_ASSIGNEE_RELATIONSHIP, 
		'relationship_guid' => $user_guid, 
		'inverse_relationship' => FALSE,
		'metadata_name' => 'status',
		'metadata_value' => TODO_STATUS_PUBLISHED,
		'count' => TRUE,
	);
	
	$accepted = TODO_ASSIGNEE_ACCEPTED;
	$dbprefix = elgg_get_config('dbprefix');
	
	$wheres = array();

	$wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$dbprefix}entity_relationships r2 
			WHERE r2.guid_one = '$user_guid'
			AND r2.relationship = '$accepted'
			AND r2.guid_two = e.guid)";
			
	$completed = COMPLETED_RELATIONSHIP;
			
	$wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$dbprefix}entity_relationships r3
			WHERE r3.guid_one = '$user_guid'
			AND r3.relationship = '$completed'
			AND r3.guid_two = e.guid)";
			
	$test_id = get_metastring_id('manual_complete');
	$one_id = get_metastring_id(1);
	
	$wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$dbprefix}metadata md
			WHERE md.entity_guid = e.guid
				AND md.name_id = $test_id
				AND md.value_id = $one_id)";

	$options['wheres'] = $wheres;
	
	return (int)elgg_get_entities_from_relationship($options);
}

/**
 * Count user's complete todo's
 *
 * @param $user_guid      int
 * @param $container_guid int (optional) todos assigned by group
 * @return int
 */
function count_complete_todos($user_guid, $container_guid = NULL) {
	return get_todos(array(
		'context' => 'assigned',
		'status' => 'complete',
		'assignee_guid' => $user_guid,
		'container_guid' => $container_guid,
		'list' => FALSE,
		'count' => TRUE,
	));
}

/** 
 * Count user's incomplete todo's
 *
 * @param $user_guid      int
 * @param $container_guid int (optional) todos assigned by group
 * @return int
 */
function count_incomplete_todos($user_guid, $container_guid = NULL) {
	return get_todos(array(
		'context' => 'assigned',
		'status' => 'incomplete',
		'assignee_guid' => $user_guid,
		'container_guid' => $container_guid,
		'list' => FALSE,
		'count' => TRUE,
	));
}

/** 
 * Count user's assigned todos
 *
 * @param $user_guid int
 * @param $container_guid int (optional) todos assigned by group
 * @return int
 */
function count_assigned_todos($user_guid, $container_guid = NULL) {
	return get_todos(array(
		'context' => 'assigned',
		'status' => 'any',
		'assignee_guid' => $user_guid,
		'container_guid' => $container_guid,
		'list' => FALSE,
		'count' => TRUE,
	));
}

/**
 * Count user todo's by due date
 *
 * @param $user_guid   int     user's guid
 * @param $date_params array   array(
 *                               'start' => timestamp, 
 *                               'end' => timestamp, (optional, if both start and end, then we'll check between dates)
 *                               'operand' => string, (required if only start date is supplied: >, <, =)
 *                             )
 *                             
 * @param $due_operand string  operand for single due date (>, <, =)
 * @param $status      string  (incomplete|complete) 
 * @return int
 */
function count_assigned_todos_by_due_date($user_guid, $date_params, $status = 'incomplete') {
	// Common options
	$options = array(
		'type' => 'object',
		'subtype' => 'todo',
		'count' => TRUE,
		'metadata_name_value_pairs' => array(array(
			'name' => 'status',
			'value' => TODO_STATUS_PUBLISHED, 
			'operand' => '='
		))
	);

	// Check for just start date and operand
	if (!$date_params['end'] && $date_params['operand']) {
		// Just start date metadata value pairs
		$options['metadata_name_value_pairs'][] = array(
			'name' => 'due_date',
			'value' => $date_params['start'],
			'operand' => $date_params['operand'],
		);
	} else if ($date_params['start'] && $date_params['end']) {
		// Got start and end, we'll be checking for items in this range

		// Start date
		$options['metadata_name_value_pairs'][] = array(
			'name' => 'due_date',
			'value' => $date_params['start'],
			'operand' => '>',
		);
		
		// End date
		$options['metadata_name_value_pairs'][] = array(
			'name' => 'due_date',
			'value' => $date_params['end'],
			'operand' => '<=',
		);
	} else {
		// Insufficient params..
		return FALSE;
	}
	


	$test_id = get_metastring_id('manual_complete');
	$one_id = get_metastring_id(1);
	$dbprefix = elgg_get_config('dbprefix');
	
	$wheres = array();

	$relationship = COMPLETED_RELATIONSHIP;

	if (!$user_guid) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	// Count based on status
	if ($status == 'complete') {
		$wheres[] = "(EXISTS (
				SELECT 1 FROM {$dbprefix}entity_relationships r2 
				WHERE r2.guid_one = '$user_guid'
				AND r2.relationship = '$relationship'
				AND r2.guid_two = e.guid) OR 
					EXISTS (
				SELECT 1 FROM {$dbprefix}metadata md
				WHERE md.entity_guid = e.guid
					AND md.name_id = $test_id
					AND md.value_id = $one_id))";


	} else if ($status == 'incomplete') {	
		// Non existant 'manual complete'
		$wheres[] = "NOT EXISTS (
				SELECT 1 FROM {$dbprefix}metadata md
				WHERE md.entity_guid = e.guid
					AND md.name_id = $test_id
					AND md.value_id = $one_id)";

		$wheres[] = "NOT EXISTS (
				SELECT 1 FROM {$dbprefix}entity_relationships r2 
				WHERE r2.guid_one = '$user_guid'
				AND r2.relationship = '$relationship'
				AND r2.guid_two = e.guid)";
	}

	$options['wheres'] = $wheres;
 	$options['relationship'] = TODO_ASSIGNEE_RELATIONSHIP;
	$options['relationship_guid'] = $user_guid;
	$options['inverse_relationship'] = FALSE;
	elgg_push_context('todo_db');
	$count = elgg_get_entities_from_relationship($options);
	elgg_pop_context();
	return (int)$count;
}

/**
 * Count user submissions
 *
 * @param $user_guid      int  user's guid
 * @param $container_guid int  container guid for groups (optional) 
 * @param $ontime         bool include only on time submissions
 */
function count_submissions($user_guid, $container_guid = NULL, $ontime = FALSE) {
	// Empty wheres/joins arrays
	$wheres = array();
	$joins = array();

	$db_prefix = elgg_get_config('dbprefix');
	
	// Access suffixen
	$n1_suffix = _elgg_get_access_where_sql(array("table_alias" => "n_table1", "guid_column" => "entity_guid"));
	$n2_suffix = _elgg_get_access_where_sql(array("table_alias" => "n_table2", "guid_column" => "entity_guid"));
	$t1_suffix = _elgg_get_access_where_sql(array("table_alias" => "t1"));

	$joins[] = "JOIN {$db_prefix}metadata n_table1 on e.guid = n_table1.entity_guid";
	$joins[] = "JOIN {$db_prefix}metastrings msn1 on n_table1.name_id = msn1.id";
	$joins[] = "JOIN {$db_prefix}metastrings msv1 on n_table1.value_id = msv1.id";
	$joins[] = "JOIN {$db_prefix}entities t1 on msv1.string = t1.guid";

	$wheres[] = "(msn1.string IN ('todo_guid')) AND ({$n1_suffix})";
	$wheres[] = "{$t1_suffix}";
	
	// On time wheres/joins
	if ($ontime) {
		$joins[] = "JOIN {$db_prefix}metadata n_table2 on t1.guid = n_table2.entity_guid";
		$joins[] = "JOIN {$db_prefix}metastrings msn2 on n_table2.name_id = msn2.id";
		$joins[] = "JOIN {$db_prefix}metastrings msv2 on n_table2.value_id = msv2.id";
		$wheres[] = "(msn2.string IN ('due_date')) AND ({$n2_suffix})";
		$wheres[] = "(UNIX_TIMESTAMP(FROM_UNIXTIME(e.time_created, '%Y%m%d')) <= UNIX_TIMESTAMP(FROM_UNIXTIME(msv2.string, '%Y%m%d')))";
	}
	
	// Check for a group guid, include another where clause
	if ($container_guid) {
		$wheres[] = "((t1.container_guid = {$container_guid}))";
	}

	$options = array(
		'type' => 'object',
		'subtype' => 'todosubmission',
		'owner_guid' => $user_guid,
		'wheres' => $wheres,
		'joins' => $joins,
		'limit' => 0,
		'count' => TRUE,
	);

	return elgg_get_entities($options);
}

/**
 * Determine if all users for a given todo have submiited to
 * or complete the todo
 *
 * @param int $todo_guid
 * @return bool
 */
function have_assignees_completed_todo($todo_guid) {
	$todo = get_entity($todo_guid);
	
	$assignees = get_todo_assignees($todo_guid);
	if (count($assignees) == 0) {
		return false;
	}
	$complete = true;
	foreach ($assignees as $assignee) {
		$complete &= has_user_submitted($assignee->getGUID(), $todo_guid);
	}
	return $complete;
}

/**
 * Checks to see if the todo is complete and sets the 
 * complete metadata accordingly
 * @param int $todo_guid
 * @return bool
 */
function update_todo_complete($todo_guid) {
	$ia = elgg_get_ignore_access();
	elgg_set_ignore_access(true);
	$todo = get_entity($todo_guid);
	if ($todo) { // Make sure we have a legit entity
		if (have_assignees_completed_todo($todo_guid)) {
			$todo->complete = true;
		} else {
			$todo->complete = false;
		}
		elgg_set_ignore_access($ia);
		return true;
	} else {
		elgg_set_ignore_access($ia);
		return false;
	}
}

/**
 * Add todo submission tags to given entity
 * @param ElggEntity $entity
 * @param ElggEntity $todo
 * @return bool
 */
function todo_set_content_tags($entity, $todo) {
	$suggested_tags = $todo->suggested_tags;
	
	if (!$suggested_tags) {
		return false; // Nothing to do here
	}

	// Make sure we have an array
	if (!is_array($suggested_tags)) {
		$suggested_tags = array($suggested_tags);
	}
	
	// Get entity tags
	$tags = $entity->tags;
	
	// If no tags, create new array
	if (!$tags) {
		$tags = array();
	}

	if (!is_array($tags)) {
		$tags = array($tags);
	}

	// Merge array
	$tags = array_merge($tags, $suggested_tags);
	
	$tags = array_unique($tags);
	
	// Set tags
	$entity->tags = $tags;
	
	return true;
}

/**
 * Generate unique user hash 
 *
 * @param ElggUser $user 
 * @return string
 */
function generate_todo_user_hash($user) {
	// Salt defined in plugin settings
	$salt = elgg_get_plugin_setting('calsalt', 'todo');
	
	// Hash username, hash salt, hash user_guid
	$hash = md5($user->username);
	$hash .= md5($salt);
	$hash .= md5($user->getGUID());
	
	// Hash again
	$hash = md5($hash);
		
	// Return 12 digit hash
	return substr($hash, 0, 12);
}

/**
 * Check if given hash is valid
 * 
 * @param string $hash
 * @param ElggUser $user
 * @return bool
 */
function check_todo_user_hash($hash, $user) {
	if ($user) {
		return $hash === generate_todo_user_hash($user);
	}
	return false;
}

/**
 * Alternative viewer function to output simple entity views
 *
 * @see elgg_view_entity_list()
 */
function todo_view_entities_table($entities, $vars = array(), $offset = 0, $limit = 10, $full_view = true,
$list_type_toggle = true, $pagination = true) {

	if (!is_int($offset)) {
		$offset = (int)get_input('offset', 0);
	}

	if (is_array($vars)) {
		// new function
		$defaults = array(
			'items' => $entities,
			'list_class' => 'elgg-list-entity',
			'full_view' => true,
			'pagination' => true,
			'list_type' => $list_type,
			'list_type_toggle' => false,
			'offset' => $offset,
		);

		$vars = array_merge($defaults, $vars);

	} 

	return elgg_view('page/components/submission_table', $vars);
}

/**
 * Helper function to determine is viewing user can view a user's list of submissions
 * 
 * @param int $user_guid  User guid of submissions to view
 * @param int $group_guid (Optional) Check if logged in user is the owner of the group
 */
function submissions_gatekeeper($user_guid, $group_guid = FALSE) {
	// Logged in only
	if (!elgg_is_logged_in()) {
		return FALSE;
	}
	
	// Admins, no prob
	if (elgg_is_admin_logged_in()) {
		return TRUE;
	}
	
	// Logged in user can view their own submissions
	if (elgg_get_logged_in_user_guid() == $user_guid) {
		return TRUE;
	}

	// Check if user is a todo admin
	if (is_todo_admin()) {
		return TRUE;
	}

	// Check for valid group, and if we can edit
	if ($group_guid) {
		$group = get_entity($group_guid);
		if (elgg_instanceof($group, 'group') && $group->canEdit()) {
			return TRUE;
		}
	}
}

function generate_html_palette($r, $g, $b, $spread = 50, $count = 50) {
	$color[0] = $r;
	$color[1] = $g;
	$color[2] = $b;
	
	$colors = array();

    for($i = 0; $i < $count; ++$i) {
    	$r = rand($color[0] - $spread, $color[0] + $spread);
	    $g = rand($color[1] - $spread, $color[1] + $spread);
	    $b = rand($color[2] - $spread, $color[2] + $spread);
		$colors[] = rgb2html($r, $g, $b);
    }
	return $colors;
}

function display_html_palette($r, $g, $b) {
	$spread = 56;

	$color[0] = $r;
	$color[1] = $g;
	$color[2] = $b;
	
    echo "<div style='float:left; background-color:rgb($color[0],$color[1],$color[2]);'>&nbsp;Base Color&nbsp;</div><br/>";

    for($i=0; $i<92; ++$i) {
   	 $r = rand($color[0] - $spread, $color[0] + $spread);
	    $g = rand($color[1] - $spread, $color[1] + $spread);
	    $b = rand($color[2] - $spread, $color[2] + $spread);    
	    echo "<div style='background-color:rgb($r,$g,$b); width:10px; height:10px; float:left;'></div>";
    }    
    echo "<br/>";
}

function html2rgb($color) {
	if ($color[0] == '#') {
		$color = substr($color, 1);
	}

	if (strlen($color) == 6) {
		list($r, $g, $b) = array(
			$color[0] . $color[1],
			$color[2] . $color[3],
			$color[4] . $color[5]
		);
	} elseif (strlen($color) == 3) {
		list($r, $g, $b) = array(
			$color[0] . $color[0], 
			$color[1] . $color[1], 
			$color[2] . $color[2]
		);
	} else {
		return false;
	}

	$r = hexdec($r); 
	$g = hexdec($g); 
	$b = hexdec($b);

	return array($r, $g, $b);
}


function rgb2html($r, $g = -1, $b = -1) {
	if (is_array($r) && sizeof($r) == 3) {
		list($r, $g, $b) = $r;
	}

	$r = intval($r);
	$g = intval($g);
	$b = intval($b);

	$r = dechex($r<0?0:($r>255?255:$r));
	$g = dechex($g<0?0:($g>255?255:$g));
	$b = dechex($b<0?0:($b>255?255:$b));

	$color = (strlen($r) < 2 ? '0' : '') . $r;
	$color .= (strlen($g) < 2 ? '0' : '') . $g;
	$color .= (strlen($b) < 2 ? '0' : '') . $b;
	return '#' . $color;
}

/**
 * Helper function to format a timezone offset
 * 
 * @param int $offset offset
 * @return string
 */
function todo_format_tz_offet($offset) {
	$hours = $offset / 3600;
	$remainder = $offset % 3600;
	$sign = $hours > 0 ? '+' : '-';
	$hour = (int) abs($hours);
	$minutes = (int) abs($remainder / 60);

	if ($hour == 0 AND $minutes == 0) {
		$sign = ' ';
	}
	return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');
}

/**
 * Helper function to get the timezone offset based on the 
 *
 * @return int
 */
function todo_get_submission_timezone_offset() {
	// Get timezone
	$utc = new DateTimeZone('UTC');

	// Get current date/time
	$current_dt = new DateTime('now', $utc);

	$submission_tz = elgg_get_plugin_setting('submission_tz', 'todo');

	// Might be unset/disabled, so return 0
	if (!$submission_tz) {
		return 0;
	}

	// Get configured time zone object
	$time_zone = new DateTimeZone($submission_tz);

	// Calulate offset
	$offset =  $time_zone->getOffset($current_dt);

	return $offset;
}

/**
 * Helper function to grab an array of todo categories
 * suitable for a dropdown input
 *
 * @param  bool $flip Optional: flip the options/values
 * @return array
 */
function todo_get_categories_dropdown($flip = FALSE) {
	$categories = array(
		TODO_BASIC_TASK => elgg_echo('todo:label:basic_task'),
		TODO_ASSESSED_TASK => elgg_echo('todo:label:assessed_task'),
		TODO_EXAM => elgg_echo('todo:label:exam'),
	);

	if ($flip) {
		$categories = array_flip($categories);
	}

	return $categories;
}

/**
 * General file upload function
 *
 * @param array  $upload     single file item
 * @param string $subtype    subtype for the new file object
 * @param int    $access_id  access id for the file
 * 
 * @return mixed 
 */
function todo_upload_file($upload, $subtype, $access_id = ACCESS_DEFAULT) {
	if (!is_array($upload) || !$subtype) {
		return false;
	}

	// Create new file entity
	$file = new FilePluginFile();
	$file->subtype = $subtype;
	$file->title = $upload['name'];
	$file->access_id = $access_id; 
	
	// Begin processing file uplaod
	$prefix = "file/";

	$filestorename = elgg_strtolower(time() . $upload['name']);

	$mime_type = $file->detectMimeType($upload['tmp_name'], $upload['type']);
	$file->setFilename($prefix . $filestorename);
	$file->setMimeType($mime_type);
	$file->originalfilename = $upload['name'];
	$file->simpletype = file_get_simple_type($mime_type);

	// Open the file to guarantee the directory exists
	$file->open("write");
	$file->close();
	move_uploaded_file($upload['tmp_name'], $file->getFilenameOnFilestore());

	$file->save();
	$file_guid = $file->guid;

	// if image, we need to create thumbnails (this should be moved into a function)
	if ($file_guid && $file->simpletype == "image") {
		$file->icontime = time();
		
		$thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 60, 60, true);
		if ($thumbnail) {
			$thumb = new ElggFile();
			$thumb->setMimeType($upload['type']);

			$thumb->setFilename($prefix."thumb".$filestorename);
			$thumb->open("write");
			$thumb->write($thumbnail);
			$thumb->close();

			$file->thumbnail = $prefix."thumb".$filestorename;
			unset($thumbnail);
		}

		$thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 153, 153, true);
		if ($thumbsmall) {
			$thumb->setFilename($prefix."smallthumb".$filestorename);
			$thumb->open("write");
			$thumb->write($thumbsmall);
			$thumb->close();
			$file->smallthumb = $prefix."smallthumb".$filestorename;
			unset($thumbsmall);
		}

		$thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 600, 600, false);
		if ($thumblarge) {
			$thumb->setFilename($prefix."largethumb".$filestorename);
			$thumb->open("write");
			$thumb->write($thumblarge);
			$thumb->close();
			$file->largethumb = $prefix."largethumb".$filestorename;
			unset($thumblarge);
		}
	}
	return $file;
}

/**
 * Helper function to delete todo/submission annotation files 
 * and their associated thumbnails (if any)
 * 
 * @param ElggObject $entity
 * @return bool
 */
function todo_delete_file($entity) {
	// If we have an image, process the thumbnails (check both mime and simple type due to inconsistent crap)
	if ($entity->simpletype == "image" || file_get_simple_type($entity->getMimeType()) == "image") {
		// Grab thumbnails
		$thumbnail = $entity->thumbnail;
		$smallthumb = $entity->smallthumb;
		$largethumb = $entity->largethumb;

		if ($thumbnail) { //delete standard thumbnail image
			$delfile = new ElggFile();
			$delfile->owner_guid = $entity->getOwnerGUID();
			$delfile->setFilename($thumbnail);
			$delfile->delete();
		}
		if ($smallthumb) { //delete small thumbnail image
			$delfile = new ElggFile();
			$delfile->owner_guid = $entity->getOwnerGUID();
			$delfile->setFilename($smallthumb);
			$delfile->delete();
		}
		if ($largethumb) { //delete large thumbnail image
			$delfile = new ElggFile();
			$delfile->owner_guid = $entity->getOwnerGUID();
			$delfile->setFilename($largethumb);
			$delfile->delete();
		}
	}

	// Delete entity
	return $entity->delete();
}

/**
 * Helper function to check if given user is in the todo admin role
 * if it's available
 *
 * @param int $user_guid The user guid to check
 * @return bool
 */
function is_todo_admin($user_guid = 0) {
    if (!$user_guid) {
        $user_guid = elgg_get_logged_in_user_guid();
    }
  
    $todo_admin_role = elgg_get_config('todo_admin_role');
  
    if (elgg_is_active_plugin('roles') && $todo_admin_role && roles_is_member($todo_admin_role, $user_guid)) {
        return true;
    } else {
        return false;
    }
}


function todo_download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Pragma: public");
	header("Expires: 0");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate, post-check=0, pre-check=0");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}