<?php
	/**
	 * Todo Helper functions
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
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
		$groups = get_users_membership(get_loggedin_userid());
	
		$array = array();
		foreach ($groups as $group) {
			$array[$group->getGUID()] = "Group: " . $group->name;
		}
		
		// If shared_access (channels) is enabled
		if (TODO_CHANNELS_ENABLED) {
			// Get users channels
			$channels = elgg_get_entities(array('relationship' => 'shared_access_member',
												'relationship_guid' => get_loggedin_userid(),
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
		foreach($entities as $entity) {
			if ($entity instanceof ElggUser) {
				$assignees[] = $entity;
			} else if ($entity instanceof ElggGroup) {
				foreach ($entity->getMembers() as $member) {
					$assignees[] = $member;
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
		if ($todo->manual_complete || get_user_submission($user_guid, $todo_guid)) {
					
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
		foreach ($submissions as $submission) {
			if ($user_guid == $submission->owner_guid) {
				return $submission;
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
		// Check if the todo has been marked as manually completed
		if (!$todo->manual_complete) {
			$assignees = get_todo_assignees($todo_guid);
			$complete = true;
			foreach ($assignees as $assignee) {
				$complete &= has_user_submitted($assignee->getGUID(), $todo_guid);
			}
			return $complete;
		} else {
			return true;
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
		foreach($todos as $idx => $todo) {
			if ($todo->due_date <= $date) {
				continue;
			} else {
				unset($todos[$idx]);
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
		foreach($todos as $idx => $todo) {
			if ($todo->due_date > $date) {
				continue;
			} else {
				unset($todos[$idx]);
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
		foreach($todos as $idx => $todo) {
			if (($todo->due_date > $start_date) && ($todo->due_date <= $end_date)) {
				continue;
			} else {
				unset($todos[$idx]);
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
		$salt = get_plugin_setting('calsalt', 'todo');
		
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
	 * @param string $context - Which mode we're in (nothing to do with get_context())
	 * @return html
	 */
	function get_todo_content_header($context = 'owned', $new_link = "pg/todo/createtodo/") {
		global $CONFIG;
		
		$tabs = array(
			'all' => array(
				'title' => 'All',
				'url' => $CONFIG->wwwroot . 'pg/todo/everyone/',
				'selected' => ($context == 'all'),
			),
			'assigned' => array(
				'title' => 'Assigned to me',
				'url' => $CONFIG->wwwroot . 'pg/todo/',
				'selected' => ($context == 'assigned'),
			),
			'owned' => array(
				'title' => 'Assigned by me',
				'url' => $CONFIG->wwwroot . 'pg/todo/owned',
				'selected' => ($context == 'owned'),
			)
		);
						
		return elgg_view('page_elements/content_header', array('tabs' => $tabs, 'type' => 'todo', 'new_link' => $CONFIG->url . $new_link));
	}
	
	/**
	 * Clears any cached data
	 * @return bool 
	 */	
	function clear_todo_cached_data() {
		remove_metadata($_SESSION['user']->guid,'is_todo_cached');
		remove_metadata($_SESSION['user']->guid,'todo_title');
		remove_metadata($_SESSION['user']->guid,'todo_description');
		remove_metadata($_SESSION['user']->guid,'todo_tags');
		remove_metadata($_SESSION['user']->guid,'todo_due_date');
		remove_metadata($_SESSION['user']->guid,'todo_assignees');
		remove_metadata($_SESSION['user']->guid,'todo_return_required');
		remove_metadata($_SESSION['user']->guid,'todo_rubric_select');
		remove_metadata($_SESSION['user']->guid,'todo_rubric_guid');
		remove_metadata($_SESSION['user']->guid,'todo_access_level');
		return true;
	}
	
?>