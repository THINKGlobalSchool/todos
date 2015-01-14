<?php
/**
 * Todo Start
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 * This plugin requires the apache2 zip module
 */

/*********************** @TODO: (Code related) ************************/
// - Implement calendar views, link in extras bar to switch
// - Widget

elgg_register_event_handler('init', 'system', 'todo_init');

function todo_init() {	

	// Library
	elgg_register_library('elgg:todo', elgg_get_plugins_path() . 'todos/lib/todo.php');
	elgg_load_library('elgg:todo');

	// Todo access levels
	define('TODO_ACCESS_LEVEL_LOGGED_IN', ACCESS_LOGGED_IN);
	define('TODO_ACCESS_LEVEL_ASSIGNEES_ONLY', -10);

	// Submission access level
	define('SUBMISSION_ACCESS_ID', -11);
	
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

	// Relationship for submission annotation files (annotation file belongs to submission)
	define('SUBMISSION_ANNOTATION_FILE_RELATIONSHIP', 'file_annotation_for');
	
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
	
	// Register custom theme CSS
	$ui_url = elgg_get_site_url() . 'mod/todos/vendors/smoothness/todo.smoothness.css';
	elgg_register_css('todo.smoothness', $ui_url);

	// Register datepicker css
	$daterange_css = elgg_get_site_url(). 'mod/todos/vendors/ui.daterangepicker.css';
	elgg_register_css('jquery.daterangepicker', $daterange_css);

	// Extend groups sidebar
	//elgg_extend_view('page/elements/sidebar', 'todo/group_sidebar');
		
	// Extend admin view to include some extra styles
	elgg_extend_view('layouts/administration', 'todo/admin/css');

	// Extend todo title menu
	//elgg_extend_view('navigation/menu/default', 'todo/header');
	
	// add the group pages tool option     
	add_group_tool_option('todo',elgg_echo('groups:enabletodo'),true);

	// Page handler
	elgg_register_page_handler('todo','todo_page_handler');

	// Page setup
	elgg_register_event_handler('pagesetup','system','todo_page_setup');

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

	// Register a handler for submission annotation delete events
	elgg_register_event_handler('delete', 'annotations', 'submission_annotation_delete_event_listener');

	// Hook into views to post process river/item/wrapper for todo submissions
	elgg_register_plugin_hook_handler('view', 'river/elements/footer', 'todo_submission_river_rewrite');
	
	// Todo entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'todo_entity_menu_setup');
	
	// Submission entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'submission_entity_menu_setup', 9999);
	
	// Generic entity menu handler
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'todo_content_entity_menu_setup');
	
	// Remove comments from todo complete river entries
	elgg_register_plugin_hook_handler('register', 'menu:river', 'submission_river_menu_setup');

	// Handler to add delete button to submission annotations
	elgg_register_plugin_hook_handler('register', 'menu:annotation', 'todo_submission_annotation_menu_setup');

	// Set up the todo dashboard menu
	elgg_register_plugin_hook_handler('register', 'menu:todo_dashboard', 'todo_dashboard_menu_setup');

	// Set up the todo dashboard tabs menu
	elgg_register_plugin_hook_handler('register', 'menu:todo_dashboard_tabs', 'todo_dashboard_tab_menu_setup');

	// Set up submission dashboard menu
	elgg_register_plugin_hook_handler('register', 'menu:todo_submission_dashboard', 'todo_submission_dashboard_menu_setup');

	// Set up group admin tools menu
	elgg_register_plugin_hook_handler('register', 'menu:groups:admin', 'todo_groups_admin_menu_setup');

	// Set up secondary todo header menu
	elgg_register_plugin_hook_handler('register', 'menu:todo-secondary-header', 'todo_secondary_header_menu_setup');

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

	// Register todos with ECML
	elgg_register_plugin_hook_handler('get_views', 'ecml', 'todo_ecml_views_hook');

	// Modify widget menu
	elgg_register_plugin_hook_handler('register', 'menu:widget', 'todo_widget_menu_setup', 501);

	// Register for unit tests
	elgg_register_plugin_hook_handler('unit_test', 'system', 'todo_test');

	// Register _elgg_get_access_where_sql hook handler for todos
	if (elgg_is_logged_in() && !elgg_is_admin_logged_in()) {
		elgg_register_plugin_hook_handler('get_sql', 'access', 'todo_access_handler');	
	}
	
	// Logged in users init
	if (elgg_is_logged_in()) {
		// Owner block hook (for logged in users)
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'todo_profile_menu');
		
		// Hook for site menu
		elgg_register_plugin_hook_handler('register', 'menu:topbar', 'todo_topbar_menu_setup', 9000);
	}

	// Cron hook for todo zip cleanup
	$delete_period = elgg_get_plugin_setting('zipdelete', 'todos');
	
	if (!$delete_period) {
		$delete_period = 'daily';
	}

	elgg_register_plugin_hook_handler('cron', $delete_period, 'todo_cleanup_cron');

	// Override comment counting for todo submissions
	elgg_register_plugin_hook_handler('comments:count', 'object', 'todo_submission_comment_count');

	// Register todo roles widget
	elgg_register_widget_type('todo', elgg_echo('todo:widget:todo_title'), elgg_echo('todo:widget:todo_desc'), 'rolewidget');

	// Set up url handlers
	elgg_register_entity_url_handler('object', 'todo', 'todo_url');
	elgg_register_entity_url_handler('object', 'todosubmission', 'todo_submission_url');
	elgg_register_entity_url_handler('object', 'todosubmissionfile', 'submission_file_url');

	// Whitelist ajax views
	elgg_register_ajax_view('todo/list');
	elgg_register_ajax_view('todo/ajax_submission');
	elgg_register_ajax_view('todo/submissions');
	elgg_register_ajax_view('todo/group_submissions');
	elgg_register_ajax_view('todo/user_submissions');
	elgg_register_ajax_view('todo/group_user_submissions');
	elgg_register_ajax_view('todo/group_submission_grades');
	elgg_register_ajax_view('todo/category_calendars');
	elgg_register_ajax_view('todo/category_calendar_filters');
	elgg_register_ajax_view('todo/category_calendar_group_legend');
	elgg_register_ajax_view('todo/calendar_feed');
	elgg_register_ajax_view('todo/connect_howto');
	elgg_register_ajax_view('css/todo/calendars_dynamic');

	// Register actions
	$action_base = elgg_get_plugins_path() . "todos/actions/todo";
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
	
	$action_base = elgg_get_plugins_path() . "todos/actions/submission";
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
	
	// Set global todo admin role in config
	elgg_set_config('todo_admin_role', elgg_get_plugin_setting('todoadminrole', 'todos'));

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

	$layout = 'content';

	// iPlan title menu options
	$iplan_title_options = array(
		'name' => 'group-iplan',
		'href' => elgg_get_site_url() . 'todo/iplan',
		'text' => elgg_echo('todo:label:iplancalendar'),
		'link_class' => 'elgg-button elgg-button-submit',
		'priority' => 501
	);

	switch ($page_type) {
		default:
			// Fwd to dashboard
			forward('todo/dashboard');
			break;
		case 'dashboard':
			gatekeeper();

			elgg_load_css('jquery.daterangepicker');
			elgg_load_css('todo.smoothness');
			elgg_load_css('tgs.fullcalendar');
			elgg_load_css('tgs.calendars_dynamic');
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
		
			$params['title'] = elgg_echo('todo:title:dashboard');
			$params['filter'] = FALSE;

			set_input('owner_block_force_hidden', true);
			if (!elgg_view_exists('topbaronly')) {
				$layout = 'one_column_content';
			} else {
				$layout = 'content';
			}

			// Handle tab menu
			if ($page[1] == 'submissions') { // Submissions tab
				set_input('submission_tab_selected', true);

				$user = get_user_by_username($page[2]);
				if (!$user) {
					$user = elgg_get_logged_in_user_entity();
				}		
				elgg_set_page_owner_guid($user->guid);
				elgg_push_breadcrumb($user->name, "todo/dashboard/{$user->username}");
				elgg_push_breadcrumb(elgg_echo('todo:label:submissions'));
	
				if (submissions_gatekeeper(elgg_get_logged_in_user_guid())) {
					$content = elgg_view('filtrate/dashboard', array(
						'menu_name' => 'todo_submission_dashboard',
						'list_url' => elgg_get_site_url() . 'ajax/view/todo/submissions',
						'default_params' => array(
							'sort_order' => 'DESC',
							'filter_return' => 1,
							'filter_ontime' => 'all',
						)
					));
				} else {
					forward('todo/dashboard');
				}
			} else { // Todo tab
				$user = get_user_by_username($page[1]);
				if (!$user) {
					$user = elgg_get_logged_in_user_entity();
				}		
				elgg_set_page_owner_guid($user->guid);
				elgg_push_breadcrumb($user->name);

				set_input('todo_tab_selected', true);
				set_input('assigner_guid', $user->guid);
				set_input('assignee_guid', $user->guid);
				$content = elgg_view('filtrate/dashboard', array(
					'menu_name' => 'todo_dashboard',
					'list_url' => elgg_get_site_url() . 'ajax/view/todo/list',
					'default_params' => array(
						'context' => 'assigned',
						'priority' => 0,
						'status' => 'incomplete',
						'sort_order' => 'DESC'
					)
				));
			}

			// Add title button
			if (elgg_get_page_owner_guid() == elgg_get_logged_in_user_guid()) {
				elgg_register_title_button();
			}

			// Secondary header
			$params['content'] = elgg_view('todo/header');

			// Output the dashboard tab menu
			$params['content'] .= elgg_view_menu('todo_dashboard_tabs', array(
				'sort_by' => 'priority',
				'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
			));
 
			$params['content'] .= $content;

			break;
		case 'iplan':
			elgg_load_css('todo.smoothness');
			elgg_load_css('tgs.fullcalendar');
			elgg_load_css('tgs.calendars_dynamic');
			elgg_load_js('tgs.fullcalendar');
			elgg_load_js('jquery.qtip');

			elgg_push_breadcrumb(elgg_echo('todo:label:iplan'));

			$params['title'] = elgg_echo('todo:label:iplancalendar');
			$params['filter'] = FALSE;

			// Secondary header
			$params['content'] = elgg_view('todo/header');

			// Output the dashboard tab menu
			$params['content'] .= elgg_view_menu('todo_dashboard_tabs', array(
				'sort_by' => 'priority',
				'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
			));
			$params['content'] .= elgg_view('todo/category_calendars');

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
			set_input('todo_dashboard', 1);

			elgg_load_css('todo.smoothness');	
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
				elgg_set_page_owner_guid($group->guid);
				elgg_register_title_button();
				$params['title'] = 'To Do Dashboard';
				$params['filter'] = FALSE;

				// Handle tab menu
				if ($page[3] == 'submissions') { // Submissions tab
					set_input('submission_tab_selected', true);
					elgg_push_breadcrumb($group->name, "todo/group/dashboard/{$group->guid}/owner");
					elgg_push_breadcrumb(elgg_echo('todo:label:submissions'));

					$content = elgg_view('todo/group_user_submissions', array(
						'group_guid' => $group->guid
					));
				} else if ($page[3] == 'grades') { // Grades tab
					set_input('grade_tab_selected', true);
					elgg_push_breadcrumb($group->name, "todo/group/dashboard/{$group->guid}/owner");
					elgg_push_breadcrumb(elgg_echo('todo:label:grades'));

					$content = elgg_view('todo/group_submission_grades', array(
						'group_guid' => $group->guid
					));
				} else { // Regular list of todos
					set_input('todo_tab_selected', true);
					elgg_push_breadcrumb($group->name);

					$content = elgg_view('filtrate/dashboard', array(
						'menu_name' => 'todo_dashboard',
						'list_url' => elgg_get_site_url() . 'ajax/view/todo/list',
						'default_params' => array(
							'context' => 'assigned',
							'priority' => 0,
							'status' => 'incomplete',
							'sort_order' => 'DESC'
						)
					));
				}

				// Output the dashboard tab menu
				$params['content'] = elgg_view_menu('todo_dashboard_tabs', array(
					'sort_by' => 'priority',
					'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
				));

				$params['content'] .= $content;
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
		case 'download_submissions':
			set_input('guid', $page[1]);
			include elgg_get_plugins_path() . 'todos/pages/todo/download_submissions.php';
			return TRUE;
			break;
		case 'download_grades':
			set_input('guid', $page[1]);
			include elgg_get_plugins_path() . 'todos/pages/todo/download_grades.php';
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
		case 'debug':
			// access_show_hidden_entities(TRUE);
			$nukes = elgg_get_entities(array(
				'type' => 'object',
				'subtypes' => array('todo', 'todosubmission'),
				'limit' => 0
			));
			foreach ($nukes as $nuke) {
				$nuke->delete();
			}
			break;
	}
	
	// Custom sidebar (none at the moment)
	$params['sidebar'] .= elgg_view('todo/sidebar');

	$body = elgg_view_layout($layout, $params);

	echo elgg_view_page($params['title'], $body);
}

/**
 * Listens to a todo assign event and adds a user to the todos's access control
 *
 */
function todo_assign_user_event_listener($event, $object_type, $object) {
	if ($object['todo']->getSubtype() == 'todo') {
		$todo = $object['todo'];
		$user = $object['user'];

		// This will check and set the complete flag on the todo
		update_todo_complete($todo->getGUID());

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

		// This will check and set the complete flag on the todo
		update_todo_complete($todo->getGUID());

		// Notify todo owner and log the drop out
		$current_user = elgg_get_logged_in_user_guid();

		if (!$todo->canEdit() && (($current_user == $user->guid) && is_todo_assignee($todo->guid, $current_user))) {
			notify_user($todo->owner_guid, 
				$user->guid,
				elgg_echo('todo:email:dropout:subject', array(
					$user->name,
					$todo->title
				)), 
				elgg_echo('todo:email:dropout:body', array(
					$user->name,
					$todo->title,
					$todo->getURL()
				))
			);

			// Create 'dropped' annotation
			create_annotation($todo->guid, "todo_dropped", "1", "integer", $current_user, ACCESS_PRIVATE);
		}
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

		$object->access_id = SUBMISSION_ACCESS_ID;

		// Update timestamp based on timezone
		$time_created = $object->time_created;
		$offset_time_created = $time_created + todo_get_submission_timezone_offset();
		$object->time_created = $offset_time_created;
		$object->utc_created = $time_created; // Store original timestamp for good measure
		$object->save();

		// Set permissions for any attached content (files)		
		if ($object->content) {
			$contents = unserialize($object->content);
			foreach ($contents as $content) {
				// Params for hook
				$content_params = array(
					'todo_guid' => $todo->guid,
					'content' => $content
				);

				// Check if plugins want to handle permissions, etc for submission content
				if (!elgg_trigger_plugin_hook('handle_submission_content_create', 'todo', $content_params, false)) {
					// No dice, lets check for an entity
					$guid = (int)$content;
					$entity = get_entity($guid);

					// If we have an entity, we'll update permission
					if (elgg_instanceof($entity, 'object')) {
						// If content is a todosubmissionfile entitity, set its ACL to that of the submission
						if (elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
							$entity->access_id = $object->access_id;
						}

						// Set up a todo content relationship for the entity
						$r = add_entity_relationship($entity->guid, TODO_CONTENT_RELATIONSHIP, $todo->guid);

						// Set content tags to todo suggested tags
						todo_set_content_tags($entity, $todo);

						$entity->save();
					} 
				}
			}
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
		remove_entity_relationship($object->guid, SUBMISSION_RELATIONSHIP, $todo->guid);

		// Handle objects attached to this submission
		if ($object->content) {
			$contents = unserialize($object->content);
			foreach ($contents as $content) {
				$guid = (int)$content;
				$entity = get_entity($guid);
				if (elgg_instanceof($entity, 'object')) {
					// If content is a file attached to this submission, delete it
					if (elgg_instanceof($entity, 'object', 'todosubmissionfile')) {
						todo_delete_file($entity);
					}
					
					// Remove todo content relationship
					remove_entity_relationship($entity->guid, TODO_CONTENT_RELATIONSHIP, $todo->guid);
					
					$entity->save();
				} 
			}
		}
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
			// Don't notify the todo owner if they comment on their own todo's submission
			if ($todo->owner_guid != elgg_get_logged_in_user_guid()) {
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
			}
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
 * Submission annotation delete handler
 */
function submission_annotation_delete_event_listener($event, $object_type, $object) {
	// Check for submission annotations
	if ($object && $object instanceof ElggAnnotation && $object->name == "submission_annotation") {
		$annotation = unserialize($object->value);
		// Check for attached entity
		if (isset($annotation['attachment_guid'])) {
			// Get attachment entity
			$entity = get_entity($annotation['attachment_guid']);
			// Delete it
			if (elgg_instanceof($entity, 'object', 'submissionannotationfile')) {
				todo_delete_file($entity);
			}
		}
	}
	return true;
}

/**
 * Plugin hook to add to do's to users profile block
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
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
			$url = "todo/dashboard/submissions/{$params['entity']->username}";
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
 * @param string $hook
 * @param string $type
 * @param string $value
 * @param array  $params
 * @return string
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
function todo_page_setup() {
	$page_owner = elgg_get_page_owner_entity();

	// Admin menus
	if (elgg_in_context('admin')) {
		elgg_register_admin_menu_item('administer', 'todos');
		elgg_register_admin_menu_item('administer', 'statistics', 'todos');
		elgg_register_admin_menu_item('administer', 'manage', 'todos');
		elgg_register_admin_menu_item('administer', 'calendars', 'todos');
		elgg_register_admin_menu_item('administer', 'logs', 'todos');
	}

	// Todo notificaton settings
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
		$url = $todo->getURL() . "?submission={$entity->guid}";
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
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
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
	//$text .= elgg_echo('todo:label:mytodos');

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
 * Add todo specific links/info to entity menu
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
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
			'text' => "<img src='" . elgg_get_site_url() . 'mod/todos/graphics/info_icon_large.png' . "' />",
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
			'text' => "<img src='" . elgg_get_site_url() . 'mod/todos/graphics/todo_cat_' . $entity->category . '.png' . "' />",
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
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
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
				'section' => 'other'
			);
			$value[] = ElggMenuItem::factory($options);
		}
	}
			
	return $value;
}

/**
 * Customize entity menu, display link to todo if entity was submitted as content
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
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
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
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
 *
 * @param string  $hook
 * @param string  $type
 * @param string  $value
 * @param array   $params
 * @return string
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
 * @param string  $hook
 * @param string  $type
 * @param string  $value
 * @param array   $params
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
			return "mod/todos/thumbnail.php?file_guid=$file->guid&size=$size&icontime=$ts";
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
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function todo_can_group_copy_handler($hook, $type, $value, $params) {
	$value[] = 'todo';
	return $value;
}

/**
 * Perform extra tasks after a todo had been copied to a group
 *
 * @param string $hook
 * @param string $type
 * @param mixed  $value
 * @param array  $params
 * @return mixed
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
 * @param string $hook
 * @param string $type
 * @param bool   $value
 * @param array  $params
 * @return bool
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
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
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
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function todo_submission_annotation_menu_setup($hook, $type, $value, $params) {
	$annotation = $params['annotation'];

	// Add delete for admins/annotation owner ONLY
	if ($annotation->name == 'submission_annotation' && (elgg_is_admin_logged_in() || $annotation->getOwnerGUID() == elgg_get_logged_in_user_guid())) {
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
 * Set up the todo dashboard menu
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function todo_dashboard_menu_setup($hook, $type, $value, $params) {
	// Filter values
	$context = get_input('context', 'assigned');
	$priority = get_input('priority', 0);
	$status = get_input('status', 'incomplete');
	$container_guid = get_input('container_guid', elgg_get_logged_in_user_guid());

	$page_owner = elgg_get_page_owner_entity();

	// Due filter
	$due_filter_input = elgg_view('input/chosen_dropdown', array(
		'id' => 'todo-due-filter',
		'options_values' => array(
			0 => elgg_echo('all'),
			TODO_PRIORITY_HIGH => elgg_echo("todo:label:pastdue"),
			TODO_PRIORITY_TODAY => elgg_echo("todo:label:today"),
			TODO_PRIORITY_MEDIUM => elgg_echo("todo:label:nextweek"),
			TODO_PRIORITY_LOW => elgg_echo("todo:label:future"),
		),
		'class' => 'filtrate-filter',
		'data-param' => 'priority',
		'value' => $priority,
	));

	$options = array(
		'name' => 'due-filter',
		'href' => false,
		'label' => elgg_echo('todo:menu:due'),
		'text' => $due_filter_input,
		'encode_text' => false,
		'section' => 'main',
		'priority' => 200,
	);

	$value[] = ElggMenuItem::factory($options);

	// Status filter
	$status_filter_input = elgg_view('input/chosen_dropdown', array(
		'id' => 'todo-status-filter',
		'options_values' => array(
			0 => elgg_echo('todo:label:statusany'),
			'incomplete' => elgg_echo('todo:label:incomplete'),
			'complete' => elgg_echo('todo:label:complete')
		),
		'class' => 'filtrate-filter',
		'value' => $status,
		'data-param' => 'status'
	));

	$options = array(
		'name' => 'status-filter',
		'href' => false,
		'label' => elgg_echo('todo:label:status'),
		'text' => $status_filter_input,
		'encode_text' => false,
		'section' => 'main',
		'priority' => 300,
	);

	$value[] = ElggMenuItem::factory($options);

	// Sort filter
	$options = array(
		'name' => 'sort',
		'href' => '#',
		'text' => elgg_echo('todo:label:sortasc'),
		'link_class' => 'menu-sort filtrate-sort filtrate-filter ascending',
		'encode_text' => false,
		'data-param' => 'sort_order',
		'section' => 'extras',
		'priority' => 400,
	);

	$value[] = ElggMenuItem::factory($options);

	// Page owner input
	$options = array(
		'name' => 'todo-hidden-page-owner',
		'href' => false,
		'text' => elgg_view('input/hidden', array(
			'name' => 'page_owner',
			'value' => elgg_get_page_owner_guid(),
			'id' => 'hidden-page-owner',
			'class' => 'filtrate-hidden-filter',
		)),
		'section' => 'extras',
		'priority' => 0,
	);

	$value[] = ElggMenuItem::factory($options);

	// Non-group items
	if (!elgg_instanceof($page_owner, 'group')) {
		// Context filter
		$context_input = elgg_view('input/chosen_dropdown', array(
			'id' => 'todo-context-filter',
			'options_values' => array(
				'all' => elgg_echo('all'),
				'assigned' => elgg_echo('todo:label:assignedtome'),
				'owned' => elgg_echo('todo:label:assignedbyme')
			),
			'class' => 'filtrate-filter',
			'data-param' => 'context',
			'value' => $context,
		));

		$context_options = array(
			'name' => 'context-filter',
			'href' => false,
			'label' => elgg_echo('todo:label:show'),
			'text' => $context_input,
			'encode_text' => false,
			'section' => 'main',
			'priority' => 100,
		);


		// Initial group options
		$group_options = array(
			'type' => 'group',
			'limit' => 0,
			'joins' => array("JOIN " . elgg_get_config("dbprefix") . "groups_entity ge ON e.guid = ge.guid"),
			"order_by" => "ge.name ASC"
		);

		// Todo Admin Options
		if (is_todo_admin() || elgg_is_admin_logged_in()) {
			// Assigned by filter
			$assigned_input = elgg_view('input/autocomplete', array(
				'name' => 'assigner',
				'class' => 'filtrate-clearable filtrate-filter',
				'data-param' => 'assigner',
				'data-match_on' => 'users',
				'data-disables' => '["#todo-context-filter", "#hidden-page-owner"]'
			));

			$options = array(
				'name' => 'assigned-filter',
				'label' => elgg_echo('todo:label:assignedbyuser'),
				'text' => $assigned_input,
				'href' => false,
				'section' => 'advanced',
				'priority' => 100,
			);

			$value[] = ElggMenuItem::factory($options);

			$assignee_input = elgg_view('input/autocomplete', array(
				'name' => 'assignee',
				'class' => 'filtrate-clearable filtrate-filter',
				'data-param' => 'assignee',
				'data-match_on' => 'users',
				'data-disables' => '["#todo-context-filter", "#hidden-page-owner"]'
			));

			// Assigned to filter
			$options = array(
				'name' => 'assignee-filter',
				'label' => elgg_echo('todo:label:assignedtouser'),
				'text' => $assignee_input,
				'href' => false,
				'section' => 'advanced',
				'priority' => 200,
			);

			$value[] = ElggMenuItem::factory($options);

		} else {
			// Group options for regular users
			$group_options['relationship'] = 'member';
			$group_options['relationship_guid'] = elgg_get_logged_in_user_entity()->guid;				
		}

		// Put together the group selector
		$groups = elgg_get_entities_from_relationship($group_options);

		$groups_array = array();

		if (count($groups) >= 1) {
			$groups_array[0] = '';

			foreach ($groups as $group) {
				$groups_array[$group->guid] = $group->name;
			}
		} else {
			$groups_array[''] = elgg_echo('todo:label:nogroups');
		}

		$group_filter_input = elgg_view('input/chosen_dropdown', array(
			'id' => 'todo-group-filter',
			'options_values' => $groups_array,
			'value' => $container_guid,
			'class' => 'filtrate-filter',
			'data-param' => 'container_guid',
			'data-placeholder' => elgg_echo('todo:label:selectagroup')
		));

		$options = array(
			'name' => 'groups-filter',
			'href' => false,
			'label' => elgg_echo('todo:label:groupclass'),
			'text' => $group_filter_input,
			'encode_text' => false,
			'section' => 'main',
			'priority' => 500
		);

		$value[] = ElggMenuItem::factory($options);


	} else {
		// Viewing a group, hard code the context
		$context_input = elgg_view('input/hidden', array(
			'id' => 'todo-hidden-context',
			'class' => 'filtrate-hidden-filter',
			'name' => 'context',
			'value' => 'owned',
		));

		$context_options = array(
			'name' => 'todo-hidden-context',
			'href' => false,
			'text' => $context_input,
			'section' => 'extras',
			'priority' => 0,
		);
	}

	// View as dropdown
	// $view_filter_input = elgg_view('input/chosen_dropdown', array(
	// 	'id' => 'todo-view-filter',
	// 	'options_values' => array(
	// 		0 => elgg_echo('todo:label:list'),
	// 		1 => elgg_echo('todo:label:calendar')
	// 	),
	// 	'value' => 0,
	// 	'class' => 'filtrate-filter',
	// 	// 'data-param' => 'container_guid'
	// ));

	// $options = array(
	// 	'name' => 'view-filter',
	// 	'href' => false,
	// 	'label' => elgg_echo('todo:label:viewas'),
	// 	'text' => $view_filter_input,
	// 	'encode_text' => false,
	// 	'section' => 'main',
	// 	'priority' => 9
	// );

	// $value[] = ElggMenuItem::factory($options);

	// Due start filter
	$start_date_input = elgg_view('input/date', array(
		'value' => $due_start_date,
		'class' => 'filtrate-filter filtrate-clearable',
		'data-param' => 'due_start_date',
		'data-disables' => '["#todo-due-filter"]'
	));

	$options = array(
		'name' => 'due-start-filter',
		'href' => false,
		'label' => elgg_echo('todo:label:startdate'),
		'text' => $start_date_input,
		'encode_text' => false,
		'section' => 'advanced',
		'priority' => 800
	);

	$value[] = ElggMenuItem::factory($options);	

	// Due end filter
	$end_date_input = elgg_view('input/date', array(
		'value' => $due_end_date,
		'class' => 'filtrate-filter filtrate-clearable',
		'data-param' => 'due_end_date',
		'data-disables' => '["#todo-due-filter"]'
	));

	$options = array(
		'name' => 'due-end-filter',
		'href' => false,
		'label' => elgg_echo('todo:label:enddate'),
		'text' => $end_date_input,
		'encode_text' => false,
		'section' => 'advanced',
		'priority' => 900
	);

	$value[] = ElggMenuItem::factory($options);	

	// Submission required advanced filter
	$submission_required_input = elgg_view('input/chosen_dropdown', array(
		'id' => 'todo-submission-filter',
		'options_values' => array(
			0 => '',
			'yes' => elgg_echo('todo:label:yes'),
			'no' => elgg_echo('todo:label:no'),
		),
		'value' => 0,
		'class' => 'filtrate-filter',
		'data-param' => 'submission',
		'data-placeholder' => elgg_echo('todo:label:selectoption'),
	));

	$options = array(
		'name' => 'submission-required-filter',
		'href' => false,
		'label' => elgg_echo('todo:label:submissionrequired'),
		'text' => $submission_required_input,
		'encode_text' => false,
		'section' => 'advanced',
		'priority' => 99
	);

	$value[] = ElggMenuItem::factory($options);

	$categories = todo_get_categories_dropdown();
	array_unshift($categories, '');

	// Todo category filter
	$todo_category_input = elgg_view('input/chosen_dropdown', array(
		'id' => 'todo-category-filter',
		'options_values' => $categories,
		'class' => 'filtrate-filter',
		'data-param' => 'todo_category',
		'data-placeholder' => elgg_echo('todo:label:selectoption'),
	));

	$options = array(
		'name' => 'todo-category-filter',
		'href' => false,
		'label' => elgg_echo('todo:label:todocategory'),
		'text' => $todo_category_input,
		'encode_text' => false,
		'section' => 'advanced',
		'priority' => 99
	);

	$value[] = ElggMenuItem::factory($options);

	// Add context item (conditionally created above)
	$value[] = ElggMenuItem::factory($context_options);

	return $value;
}

/**
 * Set up the todo dashboard tab menu
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function todo_dashboard_tab_menu_setup($hook, $type, $value, $params) {
	$page_owner = elgg_get_page_owner_entity();

	// Inital todos tab options
	$todo_tab_options = array(
		'name' => 'dashboard-tab-todos',
		'href' => 'todo/dashboard',
		'text' => elgg_echo('todo'),
		'encode_text' => false,
		'priority' => 100,
		'selected' => get_input('todo_tab_selected', false)
	);

	// Group/non group options
	if (!elgg_instanceof($page_owner, 'group')) {
		// Submissions tab for users
		$options = array(
			'name' => 'dashboard-tab-submissions',
			'href' => 'todo/dashboard/submissions',
			'text' => elgg_echo('todo:label:mysubmissions'),
			'encode_text' => false,
			'priority' => 200,
			'selected' => get_input('submission_tab_selected', false)
		);

		$value[] = ElggMenuItem::factory($options);

		// iPlan menu
		$options = array(
			'name' => 'iplan',
			'href' => elgg_get_site_url() . 'todo/iplan',
			'text' => elgg_echo('todo:label:iplan'),
			'priority' => 300
		);

		$value[] = ElggMenuItem::factory($options);
	} else {
		// Got a group..

		// Submissions tab for users
		$options = array(
			'name' => 'dashboard-tab-group-submissions',
			'href' => "todo/group/dashboard/{$page_owner->guid}/submissions",
			'text' => elgg_echo('todo:label:groupusersubmissions'),
			'encode_text' => false,
			'priority' => 200,
			'selected' => get_input('submission_tab_selected', false)
		);

		$value[] = ElggMenuItem::factory($options);

		// Grades tab
		$options = array(
			'name' => 'dashboard-tab-group-grades',
			'href' => "todo/group/dashboard/{$page_owner->guid}/grades",
			'text' => elgg_echo('todo:label:grades'),
			'encode_text' => false,
			'priority' => 300,
			'selected' => get_input('grade_tab_selected', false)
		);

		$value[] = ElggMenuItem::factory($options);

		// Set up the todos tab
		$todo_tab_options['href'] = "todo/group/dashboard/{$page_owner->guid}/owner";
	}

	$value[] = ElggMenuItem::factory($todo_tab_options);

	return $value;
}


/**
 * Set up the todo submission filter menu
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function todo_submission_dashboard_menu_setup($hook, $type, $value, $params) {
	// Start date input
	$start_date_input = elgg_view('input/date', array(
		'value' => $start_date,
		'class' => 'filtrate-filter filtrate-clearable',
		'data-param' => 'start_date',
	));

	$options = array(
		'name' => 'todo-submissions-start-filter',
		'href' => false,
		'label' => elgg_echo('todo:label:startdate'),
		'text' => $start_date_input,
		'encode_text' => false,
		'section' => 'main',
		'priority' => 100
	);

	$value[] = ElggMenuItem::factory($options);	

	// End date input
	$end_date_input = elgg_view('input/date', array(
		'value' => $start_date,
		'class' => 'filtrate-filter filtrate-clearable',
		'data-param' => 'end_date',
	));

	$options = array(
		'name' => 'todo-submissions-end-filter',
		'href' => false,
		'label' => elgg_echo('todo:label:enddate'),
		'text' => $end_date_input,
		'encode_text' => false,
		'section' => 'main',
		'priority' => 200
	);

	$value[] = ElggMenuItem::factory($options);	

	// Submission required dropdown	
	$submission_required_input = elgg_view('input/chosen_dropdown', array(
		'id' => 'todo-submission-return-filter',
		'name' => 'todo_user_submission_return_dropdown',
		'options_values' => array(
			'all' => elgg_echo('all'),
			1 => elgg_echo('todo:label:return'),
			0 => 'No&nbsp;' . elgg_echo('todo:label:return'),
		),
		'value' => 1, // Return selected by default
		'class' => 'filtrate-filter',
		'data-param' => 'filter_return',
	));

	$options = array(
		'name' => 'todo_user_submissions_return_filter',
		'href' => false,
		'label' => elgg_echo('todo:label:show'),
		'text' => $submission_required_input,
		'encode_text' => false,
		'section' => 'main',
		'priority' => 300,
	);

	$value[] = ElggMenuItem::factory($options);

	// On time filter
	$ontime_input = elgg_view('input/chosen_dropdown', array(
		'id' => 'todo-submission-ontime-filter',
		'name' => 'todo_user_submission_ontime_dropdown',
		'options_values' => array(
			'all' => elgg_echo('all'),
			1 => elgg_echo('todo:label:ontime'),
			0 => 'Not&nbsp;' . elgg_echo('todo:label:ontime'),
		),
		'class' => 'filtrate-filter',
		'data-param' => 'filter_ontime'
	));

	$options = array(
		'name' => 'todo_user_submissions_ontime_filter',
		'href' => false,
		'label' => elgg_echo('todo:label:status'),
		'text' => $ontime_input,
		'encode_text' => false,
		'section' => 'main',
		'priority' => 400,
	);

	$value[] = ElggMenuItem::factory($options);

	// Sort filter
	$options = array(
		'name' => 'todo_user_submissions_sort',
		'href' => '#ASC',
		'text' => elgg_echo('todo:label:sortascarrow'),
		'link_class' => 'menu-sort todo-user-submissions-sort filtrate-sort filtrate-filter ascending',
		'item_class' => 'elgg-menu-item-sort',
		'encode_text' => false,
		'data-param' => 'sort_order',
		'section' => 'extras',
		'priority' => 400,
	);

	$value[] = ElggMenuItem::factory($options);

	// Admin options
	if (is_todo_admin() || elgg_is_admin_logged_in()) {
		// User filter
		$user_input = elgg_view('input/autocomplete', array(
			'name' => 'user',
			'class' => 'filtrate-clearable filtrate-filter',
			'data-param' => 'user',
			'data-match_on' => 'users',
		));

		$options = array(
			'name' => 'user-filter',
			'label' => elgg_echo('todo:label:submitteduser'),
			'text' => $user_input,
			'href' => false,
			'section' => 'advanced',
			'priority' => 100,
		);

		$value[] = ElggMenuItem::factory($options);
	}



	return $value;
}

/**
 * Set up the todo submission filter menu
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function todo_groups_admin_menu_setup($hook, $type, $value, $params) {
	$group = elgg_get_page_owner_entity();
	$create_url = "todo/add/" . $group->guid;

	$options = array(
			'name' => 'create-todo',
			'text' => elgg_echo('todo:label:createnewtodo'),
			'href' => $create_url
	);
	
	$value[] = ElggMenuItem::factory($options);

	return $value;
}


/**
 * Set up the secondary todo header menu
 *
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function todo_secondary_header_menu_setup($hook, $type, $value, $params) {
	if (elgg_in_context('todo')) {
		elgg_load_js('lightbox');
		elgg_load_css('lightbox');

		$connect = "<div style='display: none;' id='todo-google-connect'></div>";

		// Subscribe link
		$options = array(
			'name' => 'subscribe',
			'title' => elgg_echo('todo:label:subscribetocalendar'),
			'href' => elgg_normalize_url('ajax/views/todo/connect_howto'),
			'text' => elgg_view('output/img', array(
					'src' => elgg_normalize_url('mod/todos/graphics/gcal.gif')
				)) . $connect,
			'priority' => 1,
			'class' => 'elgg-lightbox'
		);

		$value[] = ElggMenuItem::factory($options);
	}
	return $value;
}

/**
 * Override comment comment counting for todo submissions to include both 
 * generic_comment and submission_annotation types
 *
 * @param string $hook
 * @param string $type
 * @param int    $value
 * @param array  $params
 * @return int
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
 * Parse todos for ECML
 */
function todo_ecml_views_hook($hook, $type, $return, $params) {
	$return['object/todo'] = elgg_echo('todo');
	return $return;
}

/**
 * Modify widget menus for todo widget
 */
function todo_widget_menu_setup($hook, $type, $return, $params) {
	if (get_input('custom_widget_controls')) {
		$widget = $params['entity'];

		if ($widget->handler == 'todo') {
			$options = array(
				'name' => 'todo_view_all',
				'text' => elgg_echo('link:view:all'),
				'title' => 'todo_view_all',
				'href' => elgg_get_site_url() . 'todo',
				'class' => 'home-small'
			);

			$return[] = ElggMenuItem::factory($options);
		}

		return $return;
	}

	return $return;
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
 * @param string $hook
 * @param string $type
 * @param array  $value
 * @param array  $params
 * @return array
 */
function todo_test($hook, $type, $value, $params) {
	//$value = array(); // uncomment to just run todo tests
	$value[] = elgg_get_plugins_path() . 'todos/tests/todo.php';
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
function todo_access_handler($hook, $type, $value, $params) {
	// Hook Params
	$ignore_access = $params['ignore_access'];
	$access_column = $params['access_column'];
	$table_alias = $params['table_alias'];
	$guid_column = $params['guid_column'];
	$owner_guid_column = $params['owner_guid_column'];
	$user_guid = $params['user_guid'];

	// Logged in/site admin check
	if ($ignore_access) {
		return $value;
	}

	// ACL's
	$todo_acl = TODO_ACCESS_LEVEL_ASSIGNEES_ONLY;
	$submission_acl = SUBMISSION_ACCESS_ID;

	// Relationships
	$r_sub = SUBMISSION_RELATIONSHIP;
	$r_saf = SUBMISSION_ANNOTATION_FILE_RELATIONSHIP;
	$r_ta = TODO_ASSIGNEE_RELATIONSHIP;
	$r_tc = TODO_CONTENT_RELATIONSHIP;

	// Other vars
	$dbprefix = elgg_get_config('dbprefix');

	// Need to add a '.' to the query if there is a table alias
	$table_alias = $table_alias ? $table_alias . '.' : '';

	// Todo admin check
	$value['ors'][] = "({$user_guid} IN (
			SELECT guid_one FROM {$dbprefix}entity_relationships
			WHERE relationship = 'member_of_role'
			AND guid_two IN (
				SELECT value from {$dbprefix}private_settings
				WHERE name = 'todoadminrole'
			)
		) AND {$table_alias}{$access_column} IN ($todo_acl, $submission_acl))";


	$parent_owner_submission_and = $parent_assigned_sql = '';
	$children_string = false;

	/** Parent check **/
	// Get parents children
	$child_query = "SELECT guid from {$dbprefix}users_entity ue
					JOIN {$dbprefix}entity_relationships er on er.guid_one = ue.guid
					WHERE er.relationship = 'is_child_of'
					AND er.guid_two = {$user_guid}";

	$child_result = get_data($child_query);

	if (count($child_result) && is_array($child_result)) {
		for ($i = 0; $i < count($child_result); $i++) {
			$children_string .= $child_result[$i]->guid;
			if ($i != (count($child_result) -1)) {
				$children_string .= ", ";
			}
		}
	}

	// If we've got children
	if ($children_string) {
		$parent_assigned_sql = "(EXISTS(
			SELECT guid_one FROM {$dbprefix}entity_relationships
			WHERE guid_two = {$table_alias}{$guid_column}
			AND relationship='{$r_ta}'
			AND guid_one IN ({$children_string})
		))"; 

		$value['ors'][] = "({$table_alias}{$access_column} IN ($todo_acl) AND ({$parent_assigned_sql}))";

		// Check if the user is the parent of the submisson's owner
		$parent_owner_submission_and = "EXISTS(
			SELECT owner_guid FROM {$dbprefix}entities se
			WHERE se.guid = {$table_alias}{$guid_column}
			AND owner_guid IN ({$children_string})
		)";

		// Check if the user is the parent of the submisson owner's content/annotations/etc
		$parent_submission_owner_content_object_and = "EXISTS(
			SELECT owner_guid FROM {$dbprefix}entities se
			WHERE se.guid IN (
				SELECT guid_two FROM {$dbprefix}entity_relationships
				WHERE guid_one = {$table_alias}{$guid_column}
				AND relationship IN ('{$r_sub}','{$r_saf}','{$r_tc}'))
			AND owner_guid IN ({$children_string})
		)";

		// Ensure the user is the parent of the user to whom this todo is assigned
		$parent_todo_owner_object_and = "EXISTS(
			SELECT owner_guid FROM {$dbprefix}entities se
			WHERE se.guid IN (
				SELECT guid_two FROM {$dbprefix}entity_relationships
				WHERE guid_one IN (
					SELECT guid_two FROM {$dbprefix}entity_relationships
					WHERE guid_one = {$table_alias}{$guid_column}
					AND relationship = '$r_saf'
				) AND relationship = '$r_sub')
			AND owner_guid IN ({$children_string})
		)";

		
		$value['ors'][] = "({$table_alias}{$access_column} IN ($submission_acl) AND ({$parent_owner_submission_and}))";
		$value['ors'][] = "({$table_alias}{$access_column} IN ($submission_acl) AND ({$parent_submission_owner_content_object_and}))";
		$value['ors'][] = "({$table_alias}{$access_column} IN ($submission_acl) AND ({$parent_todo_owner_object_and}))";
	}	

	// Determine if user is assigned totdo
	$todo_assigned_and = "{$user_guid} IN (
		SELECT guid_one FROM {$dbprefix}entity_relationships
		WHERE guid_two = {$table_alias}{$guid_column}
		AND relationship='{$r_ta}'
	)";


	// SQL to check if the user is the owner of the submission for submission files/annotations
	$submission_owner_content_object_and = "{$user_guid} IN (
		SELECT owner_guid FROM {$dbprefix}entities se
		WHERE se.guid IN (
			SELECT guid_two FROM {$dbprefix}entity_relationships
			WHERE guid_one = {$table_alias}{$guid_column}
			AND relationship IN ('{$r_sub}','{$r_saf}','{$r_tc}')
	))";

	// Check if the user owns the submission object
	$submission_owner_object_and = "{$user_guid} IN (
		SELECT owner_guid FROM {$dbprefix}entities se
		WHERE se.guid = {$table_alias}{$guid_column}
	)";

	// Check if the user owns the todo, for access to the submissions
	$todo_owner_object_and = "{$user_guid} IN (
		SELECT owner_guid FROM {$dbprefix}entities se
		WHERE se.guid = (
			SELECT guid_two FROM {$dbprefix}entity_relationships
			WHERE guid_one = (
				SELECT guid_two FROM {$dbprefix}entity_relationships
				WHERE guid_one = {$table_alias}{$guid_column}
				AND relationship = '$r_saf'
			) AND relationship = '$r_sub')
	)";


	// Todo related ors
	$value['ors'][] = "({$table_alias}{$access_column} IN ($todo_acl) AND ({$todo_assigned_and}))";

	// Submission related ors
	//$value['ors'][] = "({$table_alias}{$access_column} IN ($submission_acl) AND ({$todo_owner_submission_and}))";
	$value['ors'][] = "({$table_alias}{$access_column} IN ($submission_acl) AND ({$submission_owner_content_object_and}))";
	$value['ors'][] = "({$table_alias}{$access_column} IN ($submission_acl) AND ({$submission_owner_object_and}))";
	$value['ors'][] = "({$table_alias}{$access_column} IN ($submission_acl) AND ({$todo_owner_object_and}))";


	return $value;
}