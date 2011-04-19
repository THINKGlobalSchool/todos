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
 * List todo's content
 * @param string $type 	Type of listing [owner/assigned/null]
 * @param int $guid 	Owner guid
 * @return array
 */
function todo_get_page_content_list($type = NULL, $guid = NULL) {
	// Sort out who owns what we're looking at
	$page_owner = elgg_get_page_owner_entity();
	
	if ($page_owner == elgg_get_logged_in_user_entity()) {
		$by = elgg_echo('todo:label:me');
	} else {
		$by = $page_owner->name;
	}
	
	// Get status
	$status = get_input('status', 'incomplete');
	
	if ($type == 'assigned') {
		// SHOW ASSIGNED TODOS
		set_input('todo_main_tab', $type);
		$params['filter'] = todo_get_filter_content();
		$title = elgg_echo("todo:label:assignedto", array($by));
		
		$test_id = get_metastring_id('manual_complete');
		$one_id = get_metastring_id(1);
		$wheres = array();

		$user_id = $page_owner->getGUID();		
		$relationship = COMPLETED_RELATIONSHIP;
		
		global $CONFIG;

		// Build list based on status
		if ($status == 'complete') {
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


		} else if ($status == 'incomplete') {	
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
		}

		$content = elgg_list_entities_from_relationship(array(
			'type' => 'object',
			'subtype' => 'todo',
			'relationship' => TODO_ASSIGNEE_RELATIONSHIP, 
			'relationship_guid' => $user_id, 
			'inverse_relationship' => FALSE,
			'metadata_name' => 'status',
			'metadata_value' => TODO_STATUS_PUBLISHED,
			'order_by_metadata' => array('name' => 'due_date', 'as' => 'int', 'direction' => get_input('direction', 'ASC')),
			'full_view' => FALSE,
			'wheres' => $wheres,
		));
		
		
	} else if ($type == 'owner') {
		// SHOW OWNED TODOS
		set_input('todo_main_tab', $type);
		$params['filter'] = todo_get_filter_content(FALSE);
		$title = elgg_echo("todo:label:assignedby", array($by));
		
		$options = array(
			'types' => 'object', 
			'subtypes' => 'todo', 
			'limit' => $limit, 
			'offset' => $offset, 
			'full_view' => FALSE,
			'container_guid' => $guid,
		);
		
		$content = elgg_list_entities($options);
	} else { 
		// SHOW ALL TODOS
		$params['filter'] = todo_get_filter_content();
		$title = elgg_echo('todo:title:alltodos');
		
		$type = 'all';
		
		// Show based on status
		if ($status == 'complete') {
			$content .= elgg_list_entities_from_metadata(array(
				'type' => 'object',
				'subtype' => 'todo',	
				'metadata_name' => 'status',				// Always check for status
				'metadata_value' => TODO_STATUS_PUBLISHED,	
				'metadata_name_value_pairs' => array(array(
														'name' => 'complete',
														'value' => 1, 
														'operand' => '='),
													array(
														'name' => 'manual_complete',
														'value' => 1,
														'operand' => '=',
													)),
				'metadata_name_value_pairs_operator' => 'OR',
				'order_by_metadata' => array('name' => 'due_date', 'as' => 'int', 'direction' => get_input('direction', 'ASC')),
				'full_view' => FALSE,
			));	
		} else if ($status == 'incomplete') {
			set_input('display_label', true);
			// Creating some magic SQL to grab todos without complete metadata
			$complete = get_metastring_id('complete');
			$manual_complete = get_metastring_id('manual_complete');
			$one_id = get_metastring_id(1);
			$wheres = array();
			$wheres[] = "NOT EXISTS (
					SELECT 1 FROM {$CONFIG->dbprefix}metadata md
					WHERE md.entity_guid = e.guid
						AND md.name_id = $complete
						AND md.value_id = $one_id)";

			$wheres[] = "NOT EXISTS (
					SELECT 1 FROM {$CONFIG->dbprefix}metadata md
					WHERE md.entity_guid = e.guid
						AND md.name_id = $manual_complete
						AND md.value_id = $one_id)";


			$content = elgg_list_entities_from_metadata(array(
				'type' => 'object',
				'subtype' => 'todo',
				'metadata_name' => 'status',
				'metadata_value' => TODO_STATUS_PUBLISHED,
				'order_by_metadata' => array('name' => 'due_date', 'as' => 'int', 'direction' => get_input('direction', 'ASC')),
				'full_view' => FALSE,
				'wheres' => $wheres,
			));	
		}
	}
	
	elgg_push_breadcrumb($title, "todo/{$type}/{$page_owner->username}");

	
	// Show status breadcrumb if not looking at owned todos
	if ($type != 'owner') {
		elgg_push_breadcrumb(elgg_echo('todo:label:' . $status));
	}
	
	$params['title'] = $title;
	
	if ($content) {
		$params['content'] = $content;
	} else {
		$params['content'] = "<h3 class='center'>" . elgg_echo('search:no_results') . "</h3>";
	}
	
	return $params;
}

/**
 * Get todo view content
 */
function todo_get_page_content_view() {
	
}

/**
 * Get todo edit content
 * @param string $type 	'add' or 'edit
 * @param int $guid 	object or container
 */
function todo_get_page_content_edit($type, $guid) {
	$params = array(
		'buttons' => '',
		'filter' => '',
	);
	
	if ($type == 'edit') {
		$title = elgg_echo('todo:title:edit');
	} else {
		$title = elgg_echo('todo:add');

	}
	
	elgg_push_breadcrumb($title);
	
	$params['content'] = $content;
	$params['title'] = $title;
	return $params;
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

/** HELPER FUNCTIONS */

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
				foreach ($entity->getMembers(9999) as $member) {
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
																		'limit' => 9999
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
			return trigger_elgg_event('assign', 'object', array('todo' => get_entity($todo_guid), 'user' => get_entity($user_guid)));
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
	// Check if user is has already accepted
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
											'limit' => 9999
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
	

	
	$entities = elgg_get_entities_from_relationship(array(
														'relationship' => TODO_ASSIGNEE_RELATIONSHIP,
														'relationship_guid' => $guid,
														'inverse_relationship' => TRUE,
														'types' => array('user', 'group'),
														'limit' => 9999,
														'offset' => 0,
														'count' => false,
													));
			
								
	$assignees = array();
	
	// Need to be flexible, most likely will have either just users, or just 
	// groups, but will take into account both just in case
	if ($entities) {
		foreach($entities as $entity) {
			if ($entity instanceof ElggUser) {
				$assignees[] = $entity;
			} else if ($entity instanceof ElggGroup) {
				foreach ($entity->getMembers() as $member) {
					$assignees[] = $member;
				}
			}
		}
	}
	
	return $assignees;
}

/**
 * Return an array submissions for given todo
 *
 * @param int $guid todo_guid
 * @return array
 */
function get_todo_submissions($guid) {
	$entities = elgg_get_entities_from_relationship(array(
														'relationship' => SUBMISSION_RELATIONSHIP,
														'relationship_guid' => $guid,
														'inverse_relationship' => TRUE,
														'types' => array('object'),
														'limit' => 9999,
														'offset' => 0,
														'count' => false,
													));
	
	return $entities;
}

/**
 * Return all todos a user has been assigned
 *
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
	$submissions = get_todo_submissions($todo_guid);
	if ($submissions) {
		foreach ($submissions as $submission) {
			if ($user_guid == $submission->owner_guid) {
				return $submission;
			}
		}
	}
	return false;
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
 * Return todos with a due date before givin date
 *
 * @param array $todos
 * @param int $date (Timestamp)
 * @return array
 */
function get_todos_due_before($todos, $date) {
	if ($todos) {
		foreach($todos as $idx => $todo) {
			if ($todo->due_date <= $date) {
				continue;
			} else {
				unset($todos[$idx]);
			}
		}
	}
	return $todos;
}

/**
 * Return todos with a due date after givin dates
 *
 * @param array $todos
 * @param int $date (Timestamp)
 * @return array
 */
function get_todos_due_after($todos, $date) {
	if ($todos) {
		foreach($todos as $idx => $todo) {
			if ($todo->due_date > $date) {
				continue;
			} else {
				unset($todos[$idx]);
			}
		}
	}
	return $todos;
}

/**
 * Return todos with a due date between givin dates
 *
 * @param array $todos
 * @param int $start_date Timestamp
 * @param int $end_date Timestamp, default null for no end date
 * @return array
 */
function get_todos_due_between($todos, $start_date, $end_date) {
	if ($todos) {
		foreach($todos as $idx => $todo) {
			if (($todo->due_date > $start_date) && ($todo->due_date <= $end_date)) {
				continue;
			} else {
				unset($todos[$idx]);
			}
		}
	}
	return $todos;
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
