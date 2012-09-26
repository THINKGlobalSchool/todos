<?php
/**
 * Todo library
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
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
		$entity = get_entity($guid);
		if ($entity->enabled && $type == 'todo' && elgg_instanceof($entity, 'object', 'todo')) {
			$owner = $entity->getOwnerEntity();
			$params['title'] = $entity->title;
			$params['content'] = elgg_view_entity($entity, array('full_view' => TRUE));
			$params['content'] .= elgg_view_comments($entity);
			elgg_push_breadcrumb($owner->name, elgg_get_site_url() . "todo/owner/{$owner->username}");
			elgg_push_breadcrumb($entity->title);
			return $params;
		} else if ($entity->enabled && $type == 'submission' && elgg_instanceof($entity, 'object', 'todosubmission')) {
			$params['title'] = elgg_echo('todo:label:viewsubmission');
			$params['content'] = elgg_view_entity($entity, array('full_view' => TRUE));
			
			// Show custom submission annotations
			$params['content'] .= elgg_view('todo/submission_annotations', array('entity' => $entity));
			
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
 * container_guid      => NULL|INT who's todos @todo change this to something else
 * 
 * todo_container_guid => NULL|INT todo container guid (optional)
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
	
	// Default container guid if not supplied
	if (!$params['container_guid']) {
		$params['container_guid'] = elgg_get_logged_in_user_guid();
	}
	
	$user_id = $params['container_guid'];
	
	// Default order by if not supplies
	if (!$params['order_by_metadata']) {
		$params['order_by_metadata'] = 'due_date';
	}
	
	// Default sort order
	if (!$params['sort_order']) {
		$params['sort_order'] = "ASC";
	}
	
	// Default status
	if (!$params['status']) {
		$params['status'] = 'incomplete';
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

	// Show only todo's with container_guid (for groups)
	if ($params['todo_container_guid']) {
		$options['container_guid'] = $params['todo_container_guid'];
	}
	
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
	
	global $CONFIG;
	
	// Without complete/manual wheres (for owned/all)
	$complete = get_metastring_id('complete');
	$manual_complete = get_metastring_id('manual_complete');
	$one_id = get_metastring_id(1);
						
	$without_complete_manual_wheres = array();
	$without_complete_manual_wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$CONFIG->dbprefix}metadata md
			WHERE md.entity_guid = e.guid
				AND md.name_id = $complete
				AND md.value_id = $one_id)";

	$without_complete_manual_wheres[] = "NOT EXISTS (
			SELECT 1 FROM {$CONFIG->dbprefix}metadata md
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

		$suffix = get_access_sql_suffix("mf_table");
		$due_joins = array();
		
		$due_joins[] = "JOIN {$CONFIG->dbprefix}metadata mf_table on e.guid = mf_table.entity_guid";
		$due_joins[] = "JOIN {$CONFIG->dbprefix}metastrings mf_name on mf_table.name_id = mf_name.id";
		$due_joins[] = "JOIN {$CONFIG->dbprefix}metastrings mf_value on mf_table.value_id = mf_value.id";		

	 	$due_where = "(mf_name.string = 'due_date' AND mf_value.string {$due_operand} {$due_date})";
	} else if ($params['due_start'] && $params['due_end']) {
		// Due between start and end date
		$due_start = $params['due_start'];
		$due_end = $params['due_end'];
		
		$suffix = get_access_sql_suffix("mf_table");
		$due_joins = array();
		
		$due_joins[] = "JOIN {$CONFIG->dbprefix}metadata mf_table on e.guid = mf_table.entity_guid";
		$due_joins[] = "JOIN {$CONFIG->dbprefix}metastrings mf_name on mf_table.name_id = mf_name.id";
		$due_joins[] = "JOIN {$CONFIG->dbprefix}metastrings mf_value on mf_table.value_id = mf_value.id";		

	 	$due_where = "(mf_name.string = 'due_date' AND (mf_value.string > {$due_start} AND mf_value.string < {$due_end}))";
	}
	
	// Display by context
	switch($params['context']) {
		case 'all':
		default: 
		/********************* ALL ************************/
			// Show based on status
			if ($params['status'] == 'complete') {
				// Use params, defaults and publshed and complete or manual
				$options = array_merge($options, $published_options, $complete_or_manual);
				$content = $get_from_metadata($options);	
			} else if ($params['status'] == 'incomplete') {
				set_input('display_label', true);

				$options = array_merge($options, $published_options);
				$options['wheres'] = $without_complete_manual_wheres;

				// Due filter
				$joins = $due_joins;
				$options['wheres'][] = $due_where;
				$options['joins'] = $joins;

				$content = $get_from_metadata($options);	
			}
			break;
		case 'owned':
		/********************* OWNED **********************/
			set_input('display_label', true);			
			$container = get_entity($params['container_guid']);
			
			// Show both published and drafts when viewing owned
			$published_options = array(); // Nuke it
			
			if (elgg_instanceof($container, 'group')) {
				$options['container_guid'] = $params['container_guid'];
			} else if (elgg_instanceof($container, 'user')) {
				$options['owner_guid'] = $params['container_guid'];
			}
			
			// Show based on status
			if ($params['status'] == 'complete') {
				// Use params, defaults and complete or manual
				$options = array_merge($options, $complete_or_manual);
				$content = $get_from_metadata($options);	
				
			} else if ($params['status'] == 'incomplete') {
				$options = array_merge($options, $published_options);
				$options['wheres'] = $without_complete_manual_wheres;

				// Due filter
				$joins = $due_joins;
				$options['wheres'][] = $due_where;
				$options['joins'] = $joins;

				$content = $get_from_metadata($options);	
			}

			break;
		case 'assigned':
		/********************* ASSIGNED ********************/
			$test_id = get_metastring_id('manual_complete');
			$one_id = get_metastring_id(1);
			$wheres = array();

			$relationship = COMPLETED_RELATIONSHIP;
			
			// Container guid in this case is the user to whom the todo's are assigned
			$user_id = $params['container_guid'];
			
			if (!$user_id) {
				$user_id = elgg_get_logged_in_user_guid();
			}
			
			// Build list based on status
			if ($params['status'] == 'complete') {
				$wheres[] = "(EXISTS (
						SELECT 1 FROM {$CONFIG->dbprefix}entity_relationships r2 
						WHERE r2.guid_one = '$user_id'
						AND r2.relationship = '$relationship'
						AND r2.guid_two = e.guid) OR 
							EXISTS (
						SELECT 1 FROM {$CONFIG->dbprefix}metadata md
						WHERE md.entity_guid = e.guid
							AND md.name_id = $test_id
							AND md.value_id = $one_id))";


			} else if ($params['status'] == 'incomplete') {	
				set_input('display_label', true);
				// Non existant 'manual complete'
				$wheres[] = "NOT EXISTS (
						SELECT 1 FROM {$CONFIG->dbprefix}metadata md
						WHERE md.entity_guid = e.guid
							AND md.name_id = $test_id
							AND md.value_id = $one_id)";

				$wheres[] = "NOT EXISTS (
						SELECT 1 FROM {$CONFIG->dbprefix}entity_relationships r2 
						WHERE r2.guid_one = '$user_id'
						AND r2.relationship = '$relationship'
						AND r2.guid_two = e.guid)";
						
				// Due filter
				$joins = $due_joins;
				$wheres[] = $due_where;
			}

			$options = array_merge($options, $published_options);
			$options['wheres'] = $wheres;
			$options['joins'] = $joins;
			$options['relationship'] = TODO_ASSIGNEE_RELATIONSHIP;
			$options['relationship_guid'] = $user_id;
			$options['inverse_relationship'] = FALSE;
			
			//set_input('dump', 'dump');
			$content = $get_from_relationship($options);
			break;
	}
	
	error_log($options['limit']);

	// If we have nothing, and we're listing, return a nice no results message
	if (!$content && $params['list']) {
		return "<h3 class='center' style='border-top: 1px dotted #CCCCCC; padding-top: 4px; margin-top: 5px;'>" . elgg_echo('todo:label:noresults') . "</h3>"; 
	} else {
		return $content;
	}
}


/**
 * Helper function to build menu content
 * @param bool $secondary Show the secondary menu
 * @return HTML
 */
function todo_get_filter_content($secondary = TRUE) {
	// Not displayed if we're looking at a groups todos
	if (!elgg_instanceof(elgg_get_page_owner_entity(), 'group')) {
		// show the main filter menu.
		$content = elgg_view_menu('todo-listing-main', array(
			'sort_by' => 'priority',
			// recycle the menu filter css
			'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
		));
	
		if ($secondary) {
		// show the secondary filter menu.
			$content .= elgg_view_menu('todo-listing-secondary', array(
				'sort_by' => 'priority',
				'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
			));
		}
	
		return $content;
	} else {
		return ' ';
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
		'status' => TODO_STATUS_PUBLISHED,
		'access_level' => TODO_ACCESS_LEVEL_LOGGED_IN,
		'access_id' => NULL,
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
	$groups = get_users_membership(elgg_get_logged_in_user_guid());

	$array = array();
	foreach ($groups as $group) {
		$array[$group->getGUID()] = "Group: " . $group->name;
	}
	
	// If shared_access (channels) is enabled
	if (TODO_CHANNELS_ENABLED) {
		// Get users channels
		$channels = elgg_get_entities(array('relationship' => 'shared_access_member',
											'relationship_guid' => elgg_get_logged_in_user_guid(),
											'inverse_relationship' => FALSE,
											'types' => 'object',
											'subtypes' => 'shared_access',
											'limit' => 0
									  		));
									
		foreach ($channels as $channel) {
			$array[$channel->getGUID()] = "Channel: " . $channel->title;
		}
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
		$rubrics = elgg_get_entities(array('types' => 'object', 'subtypes' => 'rubric'));
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
	$todos = elgg_get_entities_from_relationship(array('relationship' => TODO_ASSIGNEE_RELATIONSHIP, 
													 'relationship_guid' => $user_guid, 
													 'inverse_relationship' => FALSE,
													 'limit' => 9999,
													 'offset' => 0,));
													
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
		'container_guid' => $user_guid,
		'todo_container_guid' => $container_guid,
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
		'container_guid' => $user_guid,
		'todo_container_guid' => $container_guid,
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
		'container_guid' => $user_guid,
		'todo_container_guid' => $container_guid,
		'list' => FALSE,
		'count' => TRUE,
	));
}

/**
 * Count user todo's by due date
 *
 * @param $user_guid   int    user's guid
 * @param $date        int    timestamp
 * @param $due_operand string operand for due date (>, <, =)
 * @param $status      string (incomplete|complete) 
 * @return int
 */
function count_assigned_todos_by_due_date($user_guid, $date, $due_operand, $status = 'incomplete') {
	// Common options
	$options = array(
		'type' => 'object',
		'subtype' => 'todo',
		'count' => TRUE,
		'metadata_name_value_pairs' => array(
			array(
				'name' => 'status',
				'value' => TODO_STATUS_PUBLISHED, 
				'operand' => '='),
			array(
				'name' => 'due_date',
				'value' => $date,
				'operand' => $due_operand,
			))
	);

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
	$n1_suffix = get_access_sql_suffix("n_table1");
	$n2_suffix = get_access_sql_suffix("n_table2");
	$t1_suffix = get_access_sql_suffix("t1");

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
	
	if (!$suggested_tags || !is_array($suggested_tags)) {
		return false; // Nothing to do here
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
 * Sort given todo array by due date, ascending or descending
 * 
 * @param array &$todos 
 * @param bool $descending 
 * 
 */
function sort_todos_by_due_date(&$todos, $descending = false) {
	if ($descending) {
		usort($todos, "compare_todo_due_dates_desc");
	} else {
		usort($todos, "compare_todo_due_dates_asc");	
	}
}

/** 
 * Compare given todos by due_date descending
 *  
 * @param ElggEntity $a 
 * @param ElggEntity $b
 * @return bool
 */
function compare_todo_due_dates_desc($a, $b) {
	if ($a->due_date == $b->due_date) {
		return 0;
	}
	return ($a->due_date > $b->due_date) ? -1 : 1;
}

/** 
 * Compare given todos by due_date ascending
 *  
 * @param ElggEntity $a 
 * @param ElggEntity $b
 * @return bool
 */
function compare_todo_due_dates_asc($a, $b) {
	if ($a->due_date == $b->due_date) {
		return 0;
	}
	return ($a->due_date < $b->due_date) ? -1 : 1;
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
 * Get To Do's content header
 * 
 * @param string $context - Which mode we're in (nothing to do with elgg_get_context())
 * @return html
 */
function get_todo_content_header($context = 'owned', $new_link = "todo/createtodo/") {	
	$tabs = array(
		'all' => array(
			'title' => 'All',
			'url' => elgg_get_site_url() . 'todo/all/',
			'selected' => ($context == 'all'),
		),
		'assigned' => array(
			'title' => 'Assigned to me',
			'url' => elgg_get_site_url() . 'todo/',
			'selected' => ($context == 'assigned'),
		),
		'owned' => array(
			'title' => 'Assigned by me',
			'url' => elgg_get_site_url() . 'todo/owner',
			'selected' => ($context == 'owned'),
		)
	);
					
	return elgg_view('page_elements/content_header', array('tabs' => $tabs, 'type' => 'todo', 'new_link' => elgg_get_site_url() . $new_link));
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

	 //@TODO roles, parents
	/* Not yet implemented
	// Check if we're a member of the submissions role
	$role = get_entity(elgg_get_plugin_setting('todosubmissionsrole', 'todo'));
	if (elgg_instanceof($role, 'object', 'role') && $role->isMember(elgg_get_logged_in_user_entity())) {
		return TRUE;
	}
	*/

	// Check for valid group, and if we can edit
	if ($group_guid) {
		$group = get_entity($group_guid);
		if (elgg_instanceof($group, 'group') && $group->canEdit()) {
			return TRUE;
		}
	}
}
