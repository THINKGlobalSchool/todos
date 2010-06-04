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
		$groups = elgg_get_entities(array('types' => 'group'));
		$groups_array = array();
		foreach ($groups as $group) {
			$groups_array[$group->getGUID()] = $group->name;
		}
		return $groups_array;
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
	 * @param int $todo
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
	 * Return an array of users assigned to given todo
	 *
	 * @param int $todo
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
		return elgg_get_entities_from_relationship(array('relationship' => TODO_ASSIGNEE_RELATIONSHIP, 'relationship_guid' => $user_guid, 'inverse_relationship' => FALSE));
	}
	
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
	 * @return bool
	 */
	function has_user_submitted($user_guid, $todo_guid) {
		$submissions = get_todo_submissions($todo_guid);
		foreach ($submissions as $submission) {
			if ($user_guid == $submission->owner_guid) {
				return $submission;
			}
		}
		return false;
	}
	
	
	/**
	 * Clears any cached data
	 * 
	 * @return bool 
	 */	
	function clear_todo_cached_data() {
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