<?php
/**
 * Todo Start
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * This plugin requires the apache2 zip module
 * 
 */

/*********************** @TODO: (Code related) ************************/
// - Improve rubric selection interface
// - Improve libs (cleanup gross/unused)
// - Dream up some unit tests

elgg_register_event_handler('init', 'system', 'todo_init');

function todo_init() {	
	// Library
	elgg_register_library('elgg:todo', elgg_get_plugins_path() . 'todo/lib/todo.php');
	elgg_load_library('elgg:todo');

	// Assignment (todo) access levels
	define('TODO_ACCESS_LEVEL_LOGGED_IN', ACCESS_LOGGED_IN);
	define('TODO_ACCESS_LEVEL_ASSIGNEES_ONLY', -10);
	
	// Determine if optional plugins are enabled
	define('TODO_RUBRIC_ENABLED', elgg_is_active_plugin('rubrics') ? true : false);
	define('TODO_CHANNELS_ENABLED', elgg_is_active_plugin('shared_access') ? true : false);
	
	// Relationship for assignees
	define('TODO_ASSIGNEE_RELATIONSHIP', 'assignedtodo');
	
	// Relationship for accepting todo's
	define('TODO_ASSIGNEE_ACCEPTED', 'acceptstodo');
	
	// Relationship for submissions 
	define('SUBMISSION_RELATIONSHIP', 'submittedto');
	
	define('TODO_CONTENT_RELATIONSHIP', 'submitted_for_todo');
	
	// Relationship for complete todos
	define('COMPLETED_RELATIONSHIP', 'completedtodo');
	
	// Priorities (currently just used for a pretty display)
	define('TODO_PRIORITY_HIGH', 1);
	define('TODO_PRIORITY_TODAY', 2);
	define('TODO_PRIORITY_TOMORROW', 3);
	define('TODO_PRIORITY_MEDIUM', 4);
	define('TODO_PRIORITY_LOW', 5);
	
	// Todo status's 
	define('TODO_STATUS_DRAFT', 0);
	define('TODO_STATUS_PUBLISHED', 1);
	
	// Todo Categories
	define('TODO_BASIC_TASK', 'basic_task');
	define('TODO_ASSESSED_TASK', 'assessed_task');
	define('TODO_EXAM', 'exam');

	// Extend CSS
	elgg_extend_view('css/elgg','css/todo/css');
	
	// Admin CSS
	elgg_extend_view('css/admin', 'css/todo/admin');
	
	// Register todo JS
	$todo_js = elgg_get_simplecache_url('js', 'todo/todo');
	elgg_register_simplecache_view('js/todo/todo');
	elgg_register_js('elgg.todo', $todo_js);
	
	// Submission JS
	$s_js = elgg_get_simplecache_url('js', 'todo/submission');
	elgg_register_simplecache_view('js/todo/submission');
	elgg_register_js('elgg.todo.submission', $s_js);
	
	// Register and load global todo JS
	$g_js = elgg_get_simplecache_url('js', 'todo/global');
	elgg_register_simplecache_view('js/todo/global');
	elgg_register_js('elgg.todo.global', $g_js);
	elgg_load_js('elgg.todo.global');
	
	// Register admin todo JS
	$g_js = elgg_get_simplecache_url('js', 'todo/admin');
	elgg_register_simplecache_view('js/todo/admin');
	elgg_register_js('elgg.todo.admin', $g_js);

	// Register jquery ui widget (for jquery file upload)
	$js = elgg_get_simplecache_url('js', 'jquery_ui_widget');
	elgg_register_simplecache_view('js/jquery_ui_widget');
	elgg_register_js('jquery.ui.widget', $js);
	
	// Register JS File Upload
	$j_js = elgg_get_simplecache_url('js', 'jquery_file_upload');
	elgg_register_simplecache_view('js/jquery_file_upload');
	elgg_register_js('jquery-file-upload', $j_js);

	// Register jquery.iframe-transport (for jquery file upload)
	$j_js = elgg_get_simplecache_url('js', 'jquery_iframe_transport');
	elgg_register_simplecache_view('js/jquery_iframe_transport');
	elgg_register_js('jquery.iframe-transport', $j_js);
	
	// Register DataTables JS
	$dt_js = elgg_get_simplecache_url('js', 'datatables');
	elgg_register_simplecache_view('js/datatables');
	elgg_register_js('DataTables', $dt_js);
	
	// Register DataTables CSS
	$dt_css = elgg_get_simplecache_url('css', 'todo/datatables');
	elgg_register_simplecache_view('css/todo/datatables');
	elgg_register_css('DataTables', $dt_css);
	
	// Register JS for tiptip
	$tt_js = elgg_get_simplecache_url('js', 'tiptip');
	elgg_register_simplecache_view('js/tiptip');
	elgg_register_js('jquery.tiptip', $tt_js, 'head', 501);

	// Register CSS for tiptip
	$t_css = elgg_get_simplecache_url('css', 'tiptip');
	elgg_register_simplecache_view('css/tiptip');
	elgg_register_css('jquery.tiptip', $t_css);
	
	// Register JS for fullcalendar
	$fc_js = elgg_get_simplecache_url('js', 'fullcalendar');
	elgg_register_simplecache_view('js/fullcalendar');
	elgg_register_js('tgs.fullcalendar', $fc_js);

	// Register CSS for fullcalendar
	$fc_css = elgg_get_simplecache_url('css', 'fullcalendar');
	elgg_register_simplecache_view('css/fullcalendar');
	elgg_register_css('tgs.fullcalendar', $fc_css);
	
	// Register JS for fullcalendar
	$qt_js = elgg_get_simplecache_url('js', 'qtip');
	elgg_register_simplecache_view('js/qtip');
	elgg_register_js('jquery.qtip', $qt_js);
	
	// Uncached url that calls the views and builds the colors for the calendars
	$d_url = 'ajax/view/css/todo/calendars_dynamic';
	elgg_register_css('tgs.calendars_dynamic', $d_url, 999);

	// Register datepicker JS
	$daterange_js = elgg_get_site_url(). 'mod/todo/vendors/daterangepicker.jQuery.js';
	elgg_register_js('jquery.daterangepicker', $daterange_js);
	
	// Register custom theme CSS
	$ui_url = elgg_get_site_url() . 'mod/todo/vendors/smoothness/todo.smoothness.css';
	elgg_register_css('todo.smoothness', $ui_url);

	// Register datepicker css
	$daterange_css = elgg_get_site_url(). 'mod/todo/vendors/ui.daterangepicker.css';
	elgg_register_css('jquery.daterangepicker', $daterange_css);

	// Extend groups sidebar
	elgg_extend_view('page/elements/sidebar', 'todo/group_sidebar');
		
	// Extend admin view to include some extra styles
	elgg_extend_view('layouts/administration', 'todo/admin/css');
	
	// add the group pages tool option     
	add_group_tool_option('todo',elgg_echo('groups:enabletodo'),true);

	// Page handler
	elgg_register_page_handler('todo','todo_page_handler');

	// Add submenus
	elgg_register_event_handler('pagesetup','system','todo_submenus');
			
	// Register a handler for creating todos
	elgg_register_event_handler('create', 'object', 'todo_create_event_listener');

	// Register a handler for deleting todos
	elgg_register_event_handler('delete', 'object', 'todo_delete_event_listener');

	// Register a handler for assigning users to todos
	elgg_register_event_handler('assign','object','todo_assign_user_event_listener');
	
	// Register a handler for removing assignees from todos
	elgg_register_event_handler('unassign','object','todo_unassign_user_event_listener');
	
	// Register a handler for created submissions 
	elgg_register_event_handler('create', 'object', 'submission_create_event_listener');
	
	// Register a handler for deleted submissions
	elgg_register_event_handler('delete', 'object', 'submission_delete_event_listener');
	
	// Register handlers for submission relationships
	elgg_register_event_handler('create', SUBMISSION_RELATIONSHIP, 'submission_relationship_event_listener');
	
	// Register a handler for submission comments so that the todo owner is notified
	elgg_register_event_handler('annotate', 'all', 'submission_comment_event_listener');

	// Hook into views to post process river/item/wrapper for todo submissions
	elgg_register_plugin_hook_handler('view', 'river/elements/footer', 'todo_submission_river_rewrite');
	
	// Handler to prepare main todo menu
	elgg_register_plugin_hook_handler('register', 'menu:todo-listing-main', 'todo_main_menu_setup');

	// Handler to prepare secondary todo menu
	elgg_register_plugin_hook_handler('register', 'menu:todo-listing-secondary', 'todo_secondary_menu_setup');
	
	// Prepare dashboard menus
	elgg_register_plugin_hook_handler('register', 'menu:todo-dashboard-listing-main', 'todo_dashboard_main_menu_setup');
	
	// Todo entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'todo_entity_menu_setup');
	
	// Submission entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'submission_entity_menu_setup', 9999);
	
	// Generic entity menu handler
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'todo_content_entity_menu_setup');
	
	// Remove comments from todo complete river entries
	elgg_register_plugin_hook_handler('register', 'menu:river', 'submission_river_menu_setup');
	
	// Interrupt output/access view
	elgg_register_plugin_hook_handler('view', 'output/access', 'todo_output_access_handler');
	
	// Register handler for todo submission files 
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'submission_file_icon_url_override');
	
	// Register todos as a group copyable subtype
	elgg_register_plugin_hook_handler('cangroupcopy', 'entity', 'todo_can_group_copy_handler');

	// Register handler for post todo group copy
	elgg_register_plugin_hook_handler('groupcopy', 'entity', 'todo_group_copy_handler');

	// Register permissions check handler for todo submissions
	elgg_register_plugin_hook_handler('permissions_check', 'object', 'submission_can_edit');

	// Register for unit tests
	elgg_register_plugin_hook_handler('unit_test', 'system', 'todo_test');

	// Register get_access_sql_suffix hook handler for todos
	//elgg_register_plugin_hook_handler('access:get_sql_suffix', 'user', 'todos_access_handler');
	
	// Logged in users init
	if (elgg_is_logged_in()) {
		// Owner block hook (for logged in users)
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'todo_profile_menu');
		
		// Hook for site menu
		elgg_register_plugin_hook_handler('register', 'menu:topbar', 'todo_topbar_menu_setup', 9000);
	}

	// Cron hook for todo zip cleanup
	$delete_period = elgg_get_plugin_setting('zipdelete', 'todo');
	
	if (!$delete_period) {
		$delete_period = 'daily';
	}

	elgg_register_plugin_hook_handler('cron', $delete_period, 'todo_cleanup_cron');

	// Handler to add delete button to submission annotations
	elgg_register_plugin_hook_handler('register', 'menu:annotation', 'todo_submission_annotation_menu_setup');

	// Override comment counting for todo submissions
	elgg_register_plugin_hook_handler('comments:count', 'object', 'todo_submission_comment_count');

	// Set up url handlers
	elgg_register_entity_url_handler('object', 'todo', 'todo_url');
	elgg_register_entity_url_handler('object', 'todosubmission', 'todo_submission_url');
	elgg_register_entity_url_handler('object', 'todosubmissionfile', 'submission_file_url');

	// Whitelist ajax views
	elgg_register_ajax_view('todo/list');
	elgg_register_ajax_view('todo/ajax_submission');
	elgg_register_ajax_view('todo/submissions');
	elgg_register_ajax_view('todo/user_submissions');
	elgg_register_ajax_view('todo/group_user_submissions');
	elgg_register_ajax_view('todo/group_submission_grades');
	elgg_register_ajax_view('todo/category_calendars');
	elgg_register_ajax_view('todo/category_calendars_sidebar');
	elgg_register_ajax_view('todo/category_calendar_group_legend');
	elgg_register_ajax_view('todo/calendar_feed');
	elgg_register_ajax_view('css/todo/calendars_dynamic');

	// Register actions
	$action_base = elgg_get_plugins_path() . "todo/actions/todo";
	elgg_register_action('todo/save', "$action_base/save.php");
	elgg_register_action('todo/delete', "$action_base/delete.php");
	elgg_register_action('todo/accept', "$action_base/accept.php");
	elgg_register_action('todo/assign', "$action_base/assign.php");
	elgg_register_action('todo/unassign', "$action_base/unassign.php");
	elgg_register_action('todo/sendreminder', "$action_base/sendreminder.php");
	elgg_register_action('todo/complete', "$action_base/complete.php");
	elgg_register_action('todo/open', "$action_base/open.php");
	elgg_register_action('todo/upload', "$action_base/upload.php");
	elgg_register_action('todo/checkcontent', "$action_base/checkcontent.php");
	elgg_register_action('todo/settings', "$action_base/settings.php");
	elgg_register_action('todo/calendars', "$action_base/calendars.php", 'admin');
	elgg_register_action('todo/move', "$action_base/move.php", 'admin');
	
	$action_base = elgg_get_plugins_path() . "todo/actions/submission";
	elgg_register_action('submission/save', "$action_base/save.php");
	elgg_register_action('submission/delete', "$action_base/delete.php");
	elgg_register_action('submission/annotate', "$action_base/annotate.php");
	elgg_register_action('submission/grade', "$action_base/grade.php");
	elgg_register_action('submission/delete_annotation', "$action_base/delete_annotation.php");
	elgg_register_action('submission/copy_content', "$action_base/copy_content.php");

	// Register type
	elgg_register_entity_type('object', 'todo');		

	// Register one once for todos
	run_function_once("todo_run_once");
	
	return TRUE;	
}

/**
 * Todo page handler
 *
 * URLs take the form of
 *  Dashboard:       todo/dashboard
 *  View todo:       todo/view/<guid>/<title>
 *  View submission	 todo/view/submission/<guid>
 *  New todo:        todo/add/<guid>
 *  Edit todo:       todo/edit/<guid>
 *  Group todo:      todo/group/<guid>/owner
 *  Calendar feed    todo/calendar/<username>
 *
 * AJAX:
 *  todo/loadassignees - get assignee list via ajax
 *
 * Title is ignored
 * 
 * @param array $page
 * @return NULL
 */
function todo_page_handler($page) {	
	elgg_push_breadcrumb(elgg_echo('todo'), elgg_get_site_url() . "todo/dashboard");	
	
	// Load JS lib
	elgg_load_js('elgg.todo');
	
	$page_type = $page[0];
	
	switch ($page_type) {
		case 'dashboard':
		default:
			gatekeeper();
			elgg_load_css('jquery.daterangepicker');
			elgg_load_css('todo.smoothness');
			elgg_load_css('tgs.fullcalendar');
			elgg_load_css('tgs.calendars_dynamic');
			elgg_load_js('jquery.daterangepicker');
			elgg_load_js('tinymce');
			elgg_load_js('elgg.tinymce');
			elgg_load_js('jquery.ui.widget');
			elgg_load_js('jquery-file-upload');
			elgg_load_js('jquery.iframe-transport');
			elgg_load_js('elgg.todo.submission');
			elgg_load_js('tinymce');
			elgg_load_js('elgg.tinymce');
			elgg_load_js('tgs.fullcalendar');
			elgg_load_js('jquery.qtip');
		
			$params['title'] = 'To Do Dashboard';
			$params['filter'] = FALSE;
			$user = get_user_by_username($page[1]);
			if (!$user) {
				$user = elgg_get_logged_in_user_entity();
			}		
			elgg_set_page_owner_guid($user->guid);
			
			if (elgg_get_page_owner_guid() == elgg_get_logged_in_user_guid()) {
				elgg_register_title_button();
			}
			
			if ($user) {
				elgg_push_breadcrumb($user->name, 'todo/dashboard/' . $user->username);
			}
			
			$params['content'] = elgg_view('todo/dashboard');
			break;
		case 'add':
			gatekeeper();
			group_gatekeeper();
			$params = todo_get_page_content_edit($page_type, $page[1]);
			break;
		case 'view':
			elgg_load_js('lightbox');
			elgg_load_js('elgg.todo.submission');
			elgg_load_js('jquery.form');
			elgg_load_js('jquery.ui.widget');
			elgg_load_js('jquery-file-upload');
			elgg_load_js('jquery.iframe-transport');
			gatekeeper();
			if ($page[1] == 'submission'){
				$params = todo_get_page_content_view($page[1], $page[2]);
			} else {
				$params = todo_get_page_content_view('todo', $page[1]);
			}
			break;
		case 'edit':
			gatekeeper();
			group_gatekeeper();
			$params = todo_get_page_content_edit($page_type, $page[1]);
			break;
		case 'group':
			elgg_load_css('jquery.daterangepicker');
			elgg_load_css('todo.smoothness');	
			elgg_load_js('jquery.daterangepicker');
			elgg_load_js('jquery.ui.widget');
			elgg_load_js('jquery-file-upload');
			elgg_load_js('jquery.iframe-transport');
			elgg_load_js('elgg.todo.submission');
			elgg_load_js('tinymce');
			elgg_load_js('elgg.tinymce');
			elgg_load_js('DataTables');
			elgg_load_css('DataTables');
			elgg_load_js('jquery.tiptip');
			elgg_load_css('jquery.tiptip');

			gatekeeper();
			group_gatekeeper();

			$group = get_entity($page[2]);
			if (elgg_instanceof($group, 'group')) {
				elgg_push_breadcrumb($group->name, 'todo/group/dashboard/' . $group->guid);
				elgg_set_page_owner_guid($group->guid);
				elgg_register_title_button();
				$params['title'] = 'To Do Dashboard';
				$params['filter'] = FALSE;

				// iPlan group link
				$params['content'] = elgg_view('output/url', array(
					'text' => elgg_echo('todo:label:iplancalendar'),
					'href' => elgg_get_site_url() . 'todo/dashboard?tab=iplan',
					'class' => 'todo-iplan-float elgg-button elgg-button-submit',
					'target' => '_blank',
				));

				$params['content'] .= elgg_view('todo/dashboard');
			} else {
				forward('todo/dashboard');
			}
			break;
		case 'calendar':
			echo elgg_view('todo/calendar', array(
				'hash' => get_input('t'), 
				'username' => $page[1]
				));
			exit;
			break;
		case 'loadassignees':
			$guid = get_input('guid');
			$assignees = get_todo_assignees($guid);	
			echo elgg_view('todo/assignees', array('assignees' => $assignees, 'todo_guid' => $guid));
			exit;
			break;
		case 'download':
			set_input('guid', $page[1]);
			include elgg_get_plugins_path() . 'todo/pages/todo/download.php';
			return TRUE;
			break;
		case 'settings':
			gatekeeper();
			elgg_set_context('settings');
			switch ($page_type) {
				default:
				case 'notifications':
					$params = todo_get_page_content_settings_notifications();
					break;
			}
			break;
	}
	
	// Custom sidebar (none at the moment)
	$params['sidebar'] .= elgg_view('todo/sidebar');

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($params['title'], $body);
}

/**
 * Todo created, so add users to access lists.
 */
function todo_create_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todo') {
		$todo_acl = create_access_collection(elgg_echo('todo:todo') . ": " . $object->title, $object->getGUID());
		if ($todo_acl) {
			$object->assignee_acl = $todo_acl;
			try {
				add_user_to_access_collection($object->owner_guid, $todo_acl);
			} catch (DatabaseException $e) {
				//
			}
			if ($object->access_id == TODO_ACCESS_LEVEL_ASSIGNEES_ONLY) {
				$object->access_id = $todo_acl;
				$object->save();
			}
		} else {
			return false;
		}
	}
	return true;
}

/**
 * Todo deleted, so remove access lists.
 */
function todo_delete_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todo') {
		delete_access_collection($object->assignee_acl);
	}
	return true;
}

/**
 * Listens to a todo assign event and adds a user to the todos's access control
 *
 */
function todo_assign_user_event_listener($event, $object_type, $object) {
	if ($object['todo']->getSubtype() == 'todo') {
		$todo = $object['todo'];
		$user = $object['user'];
		$acl = $todo->assignee_acl;

		// This will check and set the complete flag on the todo
		update_todo_complete($todo->getGUID());

		try {
			$result = add_user_to_access_collection($user->getGUID(), $acl);
		} catch (DatabaseException $e) {
			//
		}	
	}
	return true;
}

/**
 * Listens to a todo unassign event and removes a user from the todo's access control
 *
 */
function todo_unassign_user_event_listener($event, $object_type, $object) {
	if ($object['todo']->getSubtype() == 'todo') {	
		$todo = $object['todo'];
		$user = $object['user'];
		$acl = $todo->assignee_acl;

		// This will check and set the complete flag on the todo
		update_todo_complete($todo->getGUID());

		$result = remove_user_from_access_collection($user->getGUID(), $acl);
	}
	return true;
}

/**
 * Submission created, so add users to access lists.
 */
function submission_create_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todosubmission') {
		// Get the submissions todo
		$todo = get_entity($object->todo_guid);

		// Create an ACL for the submission, only the todo assigner and assignee can see it
		$submission_acl = create_access_collection(elgg_echo('todo:todo') . ": " . $todo->title, $object->getGUID());

		if ($submission_acl) {
			$object->submission_acl = $submission_acl;

			try {
				$result = add_user_to_access_collection($todo->owner_guid, $submission_acl);
			} catch (DatabaseException $e) {
			}
			
			try {
				$result = add_user_to_access_collection(elgg_get_logged_in_user_guid(), $submission_acl);
			} catch (DatabaseException $e) {
			}

			$object->access_id = $submission_acl;

			// Update timestamp based on timezone
			$time_created = $object->time_created;
			$offset_time_created = $time_created + todo_get_submission_timezone_offset();
			$object->time_created = $offset_time_created;
			$object->utc_created = $time_created; // Store original timestamp for good measure
			$object->save();

			// Set permissions for any attached content (files)
			$contents = unserialize($object->content);
			
			if ($contents) {
				foreach ($contents as $content) {
					$guid = (int)$content;
					$entity = get_entity($guid);
					if (elgg_instanceof($entity, 'object')) {
						// If content is a todosubmissionfile entitity, set its ACL to that of the submission
						if (elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
							$entity->access_id = $submission_acl;
						}

						// Set up a todo content relationship for the entity
						$r = add_entity_relationship($entity->guid, TODO_CONTENT_RELATIONSHIP, $todo->guid);

						// Set content tags to todo suggested tags
						todo_set_content_tags($entity, $todo);

						$entity->save();
					} 
				}
			}			
		} else {
			return false;
		}
	}
	return true;
}

/**
 * Submission deleted
 */
function submission_delete_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todosubmission') {
		// Get the submissions todo
		$todo = get_entity($object->todo_guid);

		// Make sure we nuke the relationship so the remove event fires
		remove_entity_relationship($object->getGUID(), SUBMISSION_RELATIONSHIP, $todo->getGUID());

		// Reset permissions for any attached content (files)
		$contents = unserialize($object->content);
		foreach ($contents as $content) {
			$guid = (int)$content;
			$entity = get_entity($guid);
			if (elgg_instanceof($entity, 'object')) {
				// If content is a valid entitity, set its ACL back to private
				if (elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
					$entity->access_id = ACCESS_PRIVATE;
				}
				
				// Remove todo content relationship
				remove_entity_relationship($entity->guid, TODO_CONTENT_RELATIONSHIP, $todo->guid);
				
				$entity->save();
			} 
		}
		
		// Nuke the ACL
		$result = delete_access_collection($object->submission_acl);
	}
	return true;
}

/**
 * Submission relationship created/removed
 */
function submission_relationship_event_listener($event, $object_type, $object) {
	// The todo is 'guid_two'
	$todo = get_entity($object->guid_two);
	
	// This will check and set the complete flag on the todo
	update_todo_complete($todo->getGUID());
}

/**
 * Submission commented, notify todo creator
 */
function submission_comment_event_listener($event, $object_type, $object) {
	if ($object->getSubtype() == 'todosubmission') {
		// Get the submissions todo
		$todo = get_entity($object->todo_guid);
		$user = get_entity($object->owner_guid);
		
		if (elgg_in_context('create_submission_annotation')) {
			notify_user($todo->owner_guid, 
						$user->getGUID(),
						elgg_echo('submission_annotation:email:subject'), 
						elgg_echo('todo:email:bodysubmissioncomment', array( 
								$todo->title,
								$object->getURL(),
								$user->name,
								$user->getURL()
						))
			);
		} else {
			// Notify todo owner that the submission was commented on
			notify_user($todo->owner_guid, 
						$user->getGUID(),
						elgg_echo('generic_comment:email:subject'), 
						elgg_echo('todo:email:bodysubmissioncomment', array( 
								$todo->title,
								$object->getURL(),
								$user->name,
								$user->getURL()
						))
			);
		}
		
	}
	return true;
}

/**
 * Plugin hook to add to do's to users profile block
 * 	
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function todo_profile_menu($hook, $type, $value, $params) {	
	// Only display todo link for users or groups with enabled todos
	if ($params['owner'] instanceof ElggUser || $params['owner']->todo_enable == 'yes') {
		$value[] = array(
			'text' => elgg_echo('todo'),
			'href' => elgg_get_site_url() . "todo/owner/{$params['owner']->username}",
		);
	}
	
	
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "todo/dashboard/{$params['entity']->username}";
		$item = new ElggMenuItem('todo', elgg_echo('todo'), $url);
		$value[] = $item;
		
		// Add submissions (depends on access)
		if (submissions_gatekeeper($params['entity']->guid)) {
			$url = "todo/dashboard/{$params['entity']->username}?type=assigned&status=submissions";
			$item = new ElggMenuItem('todosubmissions', elgg_echo('item:object:todosubmission'), $url);
			$value[] = $item;
		}
		
	} else {
		if ($params['entity']->todo_enable == "yes") {
			$url = "todo/group/dashboard/{$params['entity']->guid}/owner";
			$item = new ElggMenuItem('todo', elgg_echo('todo:group'), $url);
			$value[] = $item;
		}
	}

	return $value;
}

/** 
 * Comments for submissions on the river are forcefully hidden
 * 
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function todo_submission_river_rewrite($hook, $type, $value, $params) {
	$entity = get_entity($params['vars']['item']->object_guid);
	if (elgg_instanceof($entity, 'object', 'todosubmission')) {	
		return ' ';
	}
}

/**
 * Setup todo submenus
 */
function todo_submenus() {
	$page_owner = elgg_get_page_owner_entity();

	// Admin stats
	if (elgg_in_context('admin')) {
		elgg_register_admin_menu_item('administer', 'statistics', 'todos');
		elgg_register_admin_menu_item('administer', 'manage', 'todos');
		elgg_register_admin_menu_item('administer', 'calendars', 'todos');
	}

	$item = array(
		'name' => 'todo_notification_settings',
		'text' => elgg_echo('todo:menu:notifications'),
		'href' =>  'todo/settings/notifications',
		'contexts' => array('settings'),
		'priority' => 9999,
	);

	elgg_register_menu_item('page', ElggMenuItem::factory($item));
}

/**
 * Populates the ->getUrl() method for todo submission entities
 *
 * @param ElggEntity entity
 * @return string request url
 */
function todo_submission_url($entity) {
	access_show_hidden_entities(TRUE);
	$todo = get_entity($entity->todo_guid);
	if ($todo && $todo->isEnabled()) {
		$url = $todo->getURL() . "#submission:{$entity->guid}";
	} else {
		$url = elgg_get_site_url() . 'todo/view/submission/' . $entity->guid;
	}
	access_show_hidden_entities(FALSE);
	return $url;
}

/*
 * Populates the ->getUrl() method for submission file objects
 *
 * @param ElggEntity $entity File entity
 * @return string File URL
 */
function submission_file_url($entity) {
	$title = $entity->title;
	$title = elgg_get_friendly_title($title);
	return "file/view/" . $entity->getGUID() . "/" . $title;
}

/**
 * Populates the ->getUrl() method for todo entities
 *
 * @param ElggEntity entity
 * @return string request url
 */
function todo_url($entity) {	
	return elgg_get_site_url() . "todo/view/{$entity->guid}/";
}

/**
 * Tobar menu hook handler
 * - adds the todo icon to the topbar
 */
function todo_topbar_menu_setup($hook, $type, $value, $params) {		
	$user = elgg_get_logged_in_user_entity();
	$assigned_count = count_unaccepted_todos($user->guid);
	$incomplete_count = count_incomplete_todos($user->guid);

	$today = strtotime(date("F j, Y"));
	$next_week = strtotime("+7 days", $today);
	
	$due_today_count = count_assigned_todos_by_due_date($user_guid, array('start' => $today, 'operand' => '='), 'incomplete');
	$upcoming_count = count_assigned_todos_by_due_date($user_guid, array('start' => $today, 'operand' => '>'), 'incomplete');
	$past_due_count = count_assigned_todos_by_due_date($user_guid, array('start' => $today, 'operand' => '<='), 'incomplete');
	$due_this_week_count = count_assigned_todos_by_due_date($user_guid, array('start' => $today, 'end' => $next_week), 'incomplete');
	
	$class = "elgg-icon todo-notifier";
	$text = "<span class='$class'></span>";

	if ($assigned_count != 0) {
		$text .= "<span class='messages-new unaccepted'>$assigned_count</span>";
	} else if ($incomplete_count != 0) {
		$text .= "<span class='messages-new incomplete'>$incomplete_count</span>";
	}

	$text .= elgg_echo('todo');

	$text .= elgg_view('todo/hoverstats', array(
		'new' => $assigned_count,
		'upcoming' => $upcoming_count,
		'past_due' => $past_due_count,
		'today' => $due_today_count,
		'this_week' => $due_this_week_count,
	));
	
	// Add todo item
	$options = array(
		'name' => 'todo',
		'text' => $text,
		'href' =>  'todo/dashboard/' . elgg_get_logged_in_user_entity()->username,
		'priority' => 999,
		'item_class' => 'todo-topbar-item',
	);
	$value[] = ElggMenuItem::factory($options);

	return $value;
}

/**
 * Todo main menu setup
 */
function todo_main_menu_setup($hook, $type, $value, $params) {	
	// Set up main nav for todo listings
	$main_tab = get_input('todo_main_tab', 'all');
	
	$user = elgg_get_page_owner_entity();
	
	if (!elgg_instanceof($user, 'user')) {
		$user = elgg_get_logged_in_user_entity();
	}
	
	if ($user == elgg_get_logged_in_user_entity()) {
		$by = elgg_echo('todo:label:me');
	} else {
		$by = $user->name;
	}
	
 	$options = array(
		'name' => 'all',
		'text' => elgg_echo("all"),
		'href' => 'todo/all',
		'selected' => ($main_tab === 'all'),
		'priority' => 1,
	);
	
	$value[] = ElggMenuItem::factory($options);
	
	$options = array(
		'name' => 'assignedtome',
		'text' => elgg_echo("todo:label:assignedto", array($by)),
		'href' => 'todo/assigned/' . $user->username,
		'selected' => ($main_tab === 'assigned'),
		'priority' => 2
	);
	
	$value[] = ElggMenuItem::factory($options);
	
 	$options = array(
		'name' => 'assignedbyme',
		'text' => elgg_echo("todo:label:assignedby", array($by)),
		'href' => 'todo/owner/' . $user->username,
		'selected' => ($main_tab === 'owner'),
		'priority' => 3
	);
	
	$value[] = ElggMenuItem::factory($options);
	
	return $value;
}

/**
 * Todo secondary menu setup
 */
function todo_secondary_menu_setup($hook, $type, $value, $params) {
	// Set up secondary nav for todo listings
	$secondary_tab = get_input('status', 'incomplete');
	
	$direction = get_input('direction', 'ASC');

	if ($direction == 'ASC') {
		$text = "  &#9660;";
		$qs = "&direction=DESC";
	} else if ($direction == 'DESC') {
		$text = "  &#9650;";
		$qs = "&direction=ASC";
	}
	
	$options = array(
		'name' => 'todo_incomplete',
		'text' => elgg_echo('todo:label:incomplete') . ($secondary_tab === 'incomplete' ? $text : ''),
		'href' => "?status=incomplete{$qs}",
		'selected' => ($secondary_tab === 'incomplete'),
		'priority' => 1
	);
	
	$value[] = ElggMenuItem::factory($options);
	
	$options = array(
		'name' => 'todo_complete',
		'text' => elgg_echo('todo:label:complete') . ($secondary_tab === 'complete' ? $text : ''),
		'href' => "?status=complete{$qs}",
		'selected' => ($secondary_tab === 'complete'),
		'priority' => 2
	);
	
	$value[] = ElggMenuItem::factory($options);
	
	return $value;
}

/**
 * Todo main menu setup
 */
function todo_dashboard_main_menu_setup($hook, $type, $value, $params) {	
	// Set up main nav for todo listings
	$main_tab = get_input('todo_main_tab', 'all');

	$owner = elgg_get_page_owner_entity();

	if ($owner == elgg_get_logged_in_user_entity()) {
		$by = elgg_echo('todo:label:me');
		
		$options = array(
			'name' => 'all',
			'text' => elgg_echo("all"),
			'class' => 'todo-ajax-list',
			'item_class' => 'todo-ajax-list-item',
			'href' => 'ajax/view/todo/list?type=all',
			'priority' => 1,
		);

		$value[] = ElggMenuItem::factory($options);
	} else {
		$by = $owner->name;
	}

	if (elgg_instanceof($owner, 'user')) {
		$options = array(
			'name' => 'assigned',
			'text' => elgg_echo("todo:label:assignedto", array($by)),
			'class' => 'todo-ajax-list',
			'item_class' => 'todo-ajax-list-item',
			'href' => 'ajax/view/todo/list?type=assigned&u=' . $owner->guid,
			'priority' => 2
		);
		$value[] = ElggMenuItem::factory($options);
	}

	if (elgg_is_logged_in()) {
	 	$options = array(
			'name' => 'owned',
			'text' => elgg_echo("todo:label:assignedby", array($by)),
			'class' => 'todo-ajax-list',
			'item_class' => 'todo-ajax-list-item',
			'href' => 'ajax/view/todo/list?type=owned&u=' . $owner->guid,
			'priority' => 3
		);
		$value[] = ElggMenuItem::factory($options);
	
		// Add group submissions and grades items
		if (elgg_instanceof($owner, 'group') && ($owner->canEdit() /*|| @TODO Submissions Role*/ )) {
			$options = array(
				'name' => 'group_user_submissions',
				'text' => elgg_echo("todo:label:groupusersubmissions", array($by)),
				'class' => 'todo-ajax-list',
				'item_class' => 'todo-ajax-list-item',
				'href' => 'ajax/view/todo/group_user_submissions?group=' . $owner->guid,
				'priority' => 4
			);
			$value[] = ElggMenuItem::factory($options);
			
			$options = array(
				'name' => 'group_grades',
				'text' => elgg_echo("todo:label:grades", array($by)),
				'class' => 'todo-ajax-list',
				'item_class' => 'todo-ajax-list-item',
				'href' => 'ajax/view/todo/group_submission_grades?group=' . $owner->guid,
				'priority' => 5
			);
			$value[] = ElggMenuItem::factory($options);
		} else if (elgg_instanceof($owner, 'group')) {
			// Group todos, but not the group owner's view
		} else if (elgg_get_plugin_setting('enable_iplan', 'todo')) {
			// Not group todos, and iPlan enabled
			$options = array(
				'name' => 'category_calendars',
				'text' => elgg_echo("todo:label:iplan"),
				'class' => 'todo-ajax-list todo-calendars-item',
				'item_class' => 'todo-ajax-list-item',
				'href' => 'ajax/view/todo/category_calendars',
				'priority' => 7
			);
			$value[] = ElggMenuItem::factory($options);
		}
	}
	return $value;
}


/**
 * Add todo specific links/info to entity menu
 */
function todo_entity_menu_setup($hook, $type, $value, $params) {
	if (elgg_in_context('widgets')) {
		return $value;
	}
	
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'todo') {
		return $value;
	}
	
	$entity = $params['entity'];

	// Add status
	if ($entity->canEdit()) {
		if ($entity->status == TODO_STATUS_DRAFT) {
			$status_text = elgg_echo('todo:status:draft'); 
		} else if ($entity->status == TODO_STATUS_PUBLISHED) {
			$status_text = elgg_echo('todo:status:published');
		}
				
		$options = array(
			'name' => 'todo_status',
			'text' => "<span>$status_text</span>",
			'href' => false,
			'priority' => 150,
			'section' => 'info',
		);
		$value[] = ElggMenuItem::factory($options);
	}

	// Show closed
	if ($entity->manual_complete) {
		$options = array(
			'name' => 'todo_closed',
			'text' => '<strong>' . elgg_echo("todo:status:closed") . '</strong>',
			'href' => false,
			'priority' => 2,
			'section' => 'info',
		);
		$value[] = ElggMenuItem::factory($options);
	}
	
	// Different actions depending if user is assignee or not
	$user_guid = elgg_get_logged_in_user_guid();
	// Is assignee
	if (is_todo_assignee($entity->getGUID(), $user_guid)) { 
		// Add accept button
		if (has_user_accepted_todo($user_guid, $entity->getGUID())) {
			$text = "<span class='accepted'>✓ Accepted</span>";
			$section = 'info';
		} else {
			$text = "<span class='unviewed'>";
			$text .= elgg_view("input/button", array(
				'href' => elgg_get_site_url() . "action/todo/accept?guid=" . $entity->getGUID(),
				'class' => 'elgg-button elgg-button-action todo-accept-ajax',
				'name' => $entity->getGUID(),
				'value' => 'Accept'
			));
			$text .= "</span>";
			$section = 'buttons';
		}
		$options = array(
			'name' => 'todo_accept',
			'text' => $text,
			'href' => false,
			'priority' => 1,
			'section' => $section,
		);
		$value[] = ElggMenuItem::factory($options);

		// Add a 'drop out' button, if user has not already submitted
		if (!has_user_submitted($user_guid, $entity->getGUID())) {
			$drop_url = elgg_get_site_url() . "action/todo/unassign?todo_guid=" . $entity->getGUID() . "&assignee_guid=" . $user_guid;
			$options = array(
				'name' => 'todo_dropout',
				'text' => elgg_echo('todo:label:dropout'),
				'href' => $drop_url,
				'priority' => 1,
				'confirm' => elgg_echo('todo:label:dropoutconfirm'),
				'section' => 'actions',
			);
			$value[] = ElggMenuItem::factory($options);
		}
		
		// Full view only
		if (elgg_in_context('todo_full_view')) {
			// If user has submitted
			if (has_user_submitted($user_guid, $entity->getGUID()) && $submission = get_user_submission($user_guid, $entity->getGUID())) {
				$ajax_url = elgg_get_site_url() . 'ajax/view/todo/ajax_submission?guid=' . $submission->guid;
				$options = array(
					'name' => 'todo_view_submission',
					'text' => elgg_echo("todo:label:viewsubmission"),
					'href' => $ajax_url,
					'class' => 'todo-submission-lightbox',
					'priority' => 999,
					'section' => 'info',
					'onclick' => "javascript:return false;",
				);
				$value[] = ElggMenuItem::factory($options);
			} else { // User has not submitted
				if (!$entity->manual_complete) {
					elgg_load_js('lightbox');
					
					// If we need to return something for this todo, the complete link will point to the submission form
					$class = $entity->return_required ? 'todo-lightbox' : 'todo-submit-empty';
					$href = $entity->return_required ? '#todo-submission-dialog' : '#';
					
					$options = array(
						'name' => 'todo_create_submission',
						'text' => elgg_echo("todo:label:completetodo"),
						'href' => $href,
						'priority' => 3,
						'link_class' => "elgg-button elgg-button-action $class",
						'section' => 'buttons',
					);
					$value[] = ElggMenuItem::factory($options);
				}
			}
		}
	} else { // Not assignee
		// full view only
		if (elgg_in_context('todo_full_view')) {
			if ($entity->manual_complete != true && $entity->owner_guid != elgg_get_logged_in_user_guid()) {
			
				$text = elgg_view("output/confirmlink", array(
					'href' => elgg_get_site_url() . "action/todo/assign?todo_guid=" . $entity->getGUID(),
					'text' => elgg_echo('todo:label:signup'),
					'confirm' => elgg_echo('todo:label:signupconfirm'),
					'class' => 'elgg-button elgg-button-action'
				));
			
				$options = array(
					'name' => 'todo_signup',
					'text' => $text,
					'href' => false,
					'priority' => 997,
					'section' => 'buttons',
				);
				$value[] = ElggMenuItem::factory($options);		
			}
		}
	}
	
	// Close todo button, owners only
	if (elgg_in_context('todo_full_view') && $entity->canEdit()) {
		if ($entity->manual_complete) {
			/*
            $options = array(
				'name' => 'todo_closed',
				'text' => '<strong>' . elgg_echo("todo:status:closed") . '</strong>',
				'href' => false,
				'priority' => 1000,
			);
			*/
			$text = elgg_view("output/confirmlink", array(
				'href' => "action/todo/open?guid=" . $entity->getGUID(),
				'text' => elgg_echo('todo:label:flagopen'),
				'confirm' => elgg_echo('todo:label:flagopenconfirm'),
				'class' => 'elgg-button elgg-button-action'
            ));
			$options = array(
				'name' => 'todo_open',
				'text' => $text,
				'href' => false,
				'priority' => 1000,
				'section' => 'buttons',
			);
			$value[] = ElggMenuItem::factory($options);
        } else {
			$text = elgg_view("output/confirmlink", array(
				'href' => "action/todo/complete?guid=" . $entity->getGUID(),
				'text' => elgg_echo('todo:label:flagcomplete'),
				'confirm' => elgg_echo('todo:label:flagcompleteconfirm'),
				'class' => 'elgg-button elgg-button-action'
            ));
			$options = array(
				'name' => 'todo_complete',
				'text' => $text,
				'href' => false,
				'priority' => 2,
				'section' => 'buttons',
			);
			$value[] = ElggMenuItem::factory($options);
		}
	}
	
	// Show the duelabel 
	if (!elgg_in_context('todo_full_view') && get_input('status') != 'complete') {
		$text = elgg_view('todo/duelabel', array('entity' => $entity));
		$options = array(
			'name' => 'todo_duelabel',
			'text' => $text,
			'href' => false,
			'priority' => 1500,
			'section' => 'info',
		);
		$value[] = ElggMenuItem::factory($options);
	}
	
	// Show Icon for submission required todos
	if ($entity->return_required) {
		$options = array(
			'name' => 'todo_return_required',
			'text' => "<img src='" . elgg_get_site_url() . 'mod/todo/graphics/info_icon_large.png' . "' />",
			'href' => '#',
			'title' => elgg_echo('todo:label:returnrequired'),
			'priority' => 0,
			'section' => 'info',
		);
		$value[] = ElggMenuItem::factory($options);
	}
	
	// Show Icon for todo categories
	if ($entity->category) {
		$name = "category_" . $entity->category;
		$options = array(
			'name' => $name,
			'text' => "<img src='" . elgg_get_site_url() . 'mod/todo/graphics/todo_cat_' . $entity->category . '.png' . "' />",
			'href' => '#',
			'title' => elgg_echo("todo:label:{$entity->category}"),
			'priority' => 1,
			'section' => 'info',
		);
		$value[] = ElggMenuItem::factory($options);
	}
		
	return $value;
}


/**
 * Customize todo submission entity menu
 */
function submission_entity_menu_setup($hook, $type, $value, $params) {
	if (elgg_in_context('widgets')) {
		return $value;
	}
	
	$handler = elgg_extract('handler', $params, FALSE);
	if ($handler != 'submission') {
		return $value;
	}
	
	$entity = $params['entity'];
	
	// Nuke menu
	$value = array();
	
	if ($entity->canEdit()) {
		// Can delete flag
		$can_delete = FALSE;

		// Get todo 
		$todo = get_entity($entity->todo_guid);

		// Get time created with offset
		$time_created = $entity->time_created;

		// Current time with offset
		$current_time = time() + todo_get_submission_timezone_offset();

		// Create DateTime objects with timestamps
		$dt_created = new DateTime();
		$dt_created->setTimestamp($time_created);

		$dt_now = new DateTime();
		$dt_now->setTimestamp($current_time);

		// Get date interval diff
		$diff = $dt_now->diff($dt_created);

		// Get total minutes
		$minutes = $diff->days * 24 * 60;
		$minutes += $diff->h * 60;
		$minutes += $diff->i;

		// If we're the todo owner or an admin
		if ($todo && (($todo->owner_guid == elgg_get_logged_in_user_guid()) || elgg_is_admin_logged_in())) {
			$can_delete = TRUE;
			$delete_label = elgg_echo('todo:label:deletesubmission');
		} else if ($todo && (is_todo_assignee($entity->todo_guid, elgg_get_logged_in_user_guid()) && $minutes <= 60)) {
			$can_delete = TRUE;
			$delete_label = elgg_echo('todo:label:deletesubmissionassignee', array(60 - $minutes));
		}

		// If we can delete, show the link with provided label
		if ($can_delete) {
			$options = array(
				'name' => 'delete',
				'text' => $delete_label,
				'title' => elgg_echo('delete:this'),
				'href' => "action/$handler/delete?guid={$entity->getGUID()}",
				'confirm' => elgg_echo('todo:label:deletesubmissionconfirm'),
				'priority' => 300,
			);
			$value[] = ElggMenuItem::factory($options);
		}
	}
			
	return $value;
}

/**
 * Customize entity menu, display link to todo if entity was submitted as content
 */
function todo_content_entity_menu_setup($hook, $type, $value, $params) {
	if (elgg_in_context('widgets')) {
		return $value;
	}
	
	if (!elgg_is_logged_in()) {
		return $value;
	}
	
	$ia = elgg_get_ignore_access();
	elgg_set_ignore_access(TRUE);
		
	$entity = $params['entity'];
	
	$options = array(
		'relationship' => TODO_CONTENT_RELATIONSHIP,
		'relationship_guid' => $entity->guid,
		'inverse_relationship' => FALSE,
		'types' => array('object'),
		'subtypes' => array('todo'),
		'limit' => 0,
		'offset' => 0,
		'count' => TRUE,
	);
	
	// Grab count
	$todo_count = elgg_get_entities_from_relationship($options);
	
	$options['count'] = FALSE;
	
	// Grab todo's
	$todos = elgg_get_entities_from_relationship($options);
	
	
	// If this item was submitted to at least one todo
	if ($todo_count) {
		
		// If only submitted to one todo
		if ($todo_count == 1) {
			$text = elgg_echo('todo:label:submittedforsingle');
		} else { // Multiple todo's
			$text = elgg_echo('todo:label:submittedformultiple', array($todo_count));
		}	
			
		$toggle_box = "<div id='todo-entity-info-{$entity->guid}' class='todo-entity-info'>";
		
		foreach($todos as $todo) {
			$container = $todo->getContainerEntity();
			$toggle_box .= "<a class='multi-todo' href='{$todo->getURL()}'>{$todo->title} ({$container->name})</a>";
		}
		$toggle_box .= "</div>";

		$options = array(
			'name' => "submitted_for_multiple_todos",
			'text' =>  $text . $toggle_box,
			'href' => '#todo-entity-info-' . $entity->guid,
			'id' => 'todo-entity-' . $entity->guid,
			'class' => 'todo-show-info',
			'section' => 'info',
			'priority' => 2000,
		);
		
			
		$value[] = ElggMenuItem::factory($options);
	}
	elgg_set_ignore_access($ia);

	return $value;
}

/**
 * Add the comment and like links to river actions menu
 */
function submission_river_menu_setup($hook, $type, $value, $params) {
	if (elgg_is_logged_in()) {
		$item = $params['item'];
		$object = $item->getObjectEntity();
		if (elgg_instanceof($object, 'object', 'todosubmission')) {
			return array();
		}
	}

	return $value;
}

/**
 * Hook to allow output/access to display 'Assignees Only'
 */
function todo_output_access_handler($hook, $type, $value, $params) {
	if ($params['vars']['entity']) {
		if ($params['vars']['entity']->getSubtype() == 'todo' && $params['vars']['entity']->access_id != ACCESS_LOGGED_IN) {
			$value = "<span class='elgg-access'>" . elgg_echo('todo:label:assigneesonly') . "</span>";
		}
	}
	return $value;
}

/**
 * Override the default entity icon for files
 *
 * Plugins can override or extend the icons using the plugin hook: 'file:icon:url', 'override'
 *
 * @return string Relative URL
 */
function submission_file_icon_url_override($hook, $type, $value, $params) {
	$file = $params['entity'];
	$size = $params['size'];
	if (elgg_instanceof($file, 'object', 'todosubmissionfile') || elgg_instanceof($file, 'object', 'submissionannotationfile')) {

		// thumbnails get first priority
		if ($file->thumbnail) {
			$ts = (int)$file->icontime;
			return "mod/todo/thumbnail.php?file_guid=$file->guid&size=$size&icontime=$ts";
		}

		$mapping = array(
			'application/excel' => 'excel',
			'application/msword' => 'word',
			'application/pdf' => 'pdf',
			'application/powerpoint' => 'ppt',
			'application/vnd.ms-excel' => 'excel',
			'application/vnd.ms-powerpoint' => 'ppt',
			'application/vnd.oasis.opendocument.text' => 'openoffice',
			'application/x-gzip' => 'archive',
			'application/x-rar-compressed' => 'archive',
			'application/x-stuffit' => 'archive',
			'application/zip' => 'archive',

			'text/directory' => 'vcard',
			'text/v-card' => 'vcard',

			'application' => 'application',
			'audio' => 'music',
			'text' => 'text',
			'video' => 'video',
		);

		$mime = $file->mimetype;
		if ($mime) {
			$base_type = substr($mime, 0, strpos($mime, '/'));
		} else {
			$mime = 'none';
			$base_type = 'none';
		}

		if (isset($mapping[$mime])) {
			$type = $mapping[$mime];
		} elseif (isset($mapping[$base_type])) {
			$type = $mapping[$base_type];
		} else {
			$type = 'general';
		}

		if ($size == 'large') {
			$ext = '_lrg';
		} else {
			$exit = '';
		}
		
		$url = "mod/file/graphics/icons/{$type}{$ext}.gif";
		$url = elgg_trigger_plugin_hook('file:icon:url', 'override', $params, $url);
		return $url;
	}
}

/**
 * Register todo as a group copyable subtype
 */
function todo_can_group_copy_handler($hook, $type, $value, $params) {
	$value[] = 'todo';
	return $value;
}

/**
 * Perform extra tasks after a todo had been copied to a group
 */
function todo_group_copy_handler($hook, $type, $value, $params) {
	$new_entity = $params['new_entity'];

	if (elgg_instanceof($new_entity, 'object', 'todo')) {
		// Update (reset) the todo's complete status
		update_todo_complete($new_entity->guid);
	}

	return $value;
}

/**
 * Override the canEdit function to return true for submissions
 * where the user can edit the todo
 *
 */
function submission_can_edit($hook, $type, $value, $params) {
	$entity = $params['entity'];

	if (elgg_instanceof($entity, 'object', 'todosubmission')) {
		if ($entity->owner_guid == elgg_get_logged_in_user_guid()) {
			return true;
		}

		$todo = get_entity($entity->todo_guid);

		if ($todo && $todo->canEdit()) {
			return true;
		}
	}

	return $value;
}

/**
 * Cron to clean up the todo export directory
 */
function todo_cleanup_cron($hook, $type, $value, $params) {
	// Get data root
	$dataroot = elgg_get_config('dataroot');

	$todo_export_dir = "{$dataroot}todo_export";
	
	// Make sure export directory exists
	if (file_exists($todo_export_dir)) {
		// Open directory
		$directory = opendir($todo_export_dir);
		
		// Loop over files in export directory
		while(false !== ($file = readdir($directory))) {
			
			// Don't include . or ..
			if($file != "." && $file != "..") {
				
				// Set file to delete
				$delfile = "{$todo_export_dir}/{$file}";
				
				// Make sure it exists (double-check)
				if (file_exists($delfile)) {
					
					// Nuke it
					$result = unlink($delfile);
					
					// Display error if any
					if (!$result) {
						error_log('TODO CRON CLEANUP - Could not delete: ' . $delfile);
					}
				}
			}
		}
		// Close directory
		closedir($directory);
	}

	return $value;
}

/**
 * Adds a delete link to "submission_annotation" annotations
 */
function todo_submission_annotation_menu_setup($hook, $type, $value, $params) {
	$annotation = $params['annotation'];

	if ($annotation->name == 'submission_annotation' && $annotation->canEdit()) {
		$url = elgg_http_add_url_query_elements('action/submission/delete_annotation', array(
			'annotation_id' => $annotation->id,
		));

		$options = array(
			'name' => 'delete',
			'href' => $url,
			'text' => "<span class=\"elgg-icon elgg-icon-delete\"></span>",
			'encode_text' => false
		);
		$value[] = ElggMenuItem::factory($options);
	}

	return $value;
}

/**
 * Override comment comment counting for todo submissions to include both 
 * generic_comment and submission_annotation types
 */
function todo_submission_comment_count($hook, $type, $value, $params) {
	$entity = $params['entity'];

	if ($entity->getSubtype() == 'todosubmission') {

		$options = array(
			'guid' => $entity->getGUID(),
			'annotation_names' => array('generic_comment', 'submission_annotation'),
			'annotation_calculation' => 'count',
		);
		
		$count = elgg_get_annotations($options);
		
		return (int)$count;
	}

	return $value;
}

/**
 * Register entity type objects, subtype todosubmissionfile as
 * ElggFile.
 *
 * @return void
 */
function todo_run_once() {
	// Register todo submission file class
	add_subtype("object", "todosubmissionfile", "ElggFile");
	add_subtype("object", "submissionannotationfile", "ElggFile");
	
	// Just in case this metadata doesn't exist yet (It should)
	$dummy = new ElggObject();
	$dummy->manual_complete = 1;
	$dummy->complete = 1;
	$dummy->one = 1;
	
	$dummy->save();
	$dummy->delete();	
}


/**
 * Runs unit tests for todos
 *
 * @return array
 */
function todo_test($hook, $type, $value, $params) {
	$value = array();
	$value[] = elgg_get_plugins_path() . 'todo/tests/todo.php';
	return $value;
}

/**
 * Implement access sql suffix hook for todos
 * 	
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function todos_access_handler($hook, $type, $value, $params) {
	// global $superuser_role;

	// $dbprefix = elgg_get_config('dbprefix');

	// $table_prefix = $params['table_prefix'];
	// $owner_guid_column = $params['owner_guid_column'];
	// $user_guid = $params['user_guid'];
	// $role_guid = $superuser_role->guid;

	// $value['ors'][] = "{$user_guid} IN (
	// 	SELECT guid_one FROM {$dbprefix}entity_relationships
	// 	WHERE relationship='belongs_to' AND guid_two={$role_guid}
	// )";

	return $value;
}
