<?php
/**
 * User submissions view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 */
echo $user_guid;
$user_guid = get_input('user_guid', elgg_extract('user_guid', $vars, NULL));
$group_guid = get_input('group_guid', elgg_extract('group_guid', $vars, NULL));

// Make sure we can view the list
if (!submissions_gatekeeper($user_guid, $group_guid)) {
	echo elgg_echo('todo:error:access');
	return;
}

$user = get_entity($user_guid);

if (!elgg_instanceof($user, 'user')) {
	echo elgg_echo('todo:error:invaliduser');
	return;
}

$view_vars = array('user_guid' => $user_guid);

// Check for a group
if (elgg_instanceof(get_entity($group_guid), 'group')) {
	$view_vars['group_guid'] = $group_guid;
	$view_vars['limit'] = 15;
}

$view_vars['filter_return'] = 1; // Default to return

// Output menu
$filter_menu = elgg_view_menu('todo_submission_dashboard', array(
	'class' => 'elgg-menu-hz elgg-menu-submissions-sort',
	'sort_by' => 'priority',
));

$menu_name = 'todo_submission_dashboard';
$list_url = elgg_get_site_url() . 'ajax/view/todo/submissions';
elgg_set_page_owner_guid($group_guid);

set_input('blah', 'sadjasdkjsdakkjdsjkdsjkdsajkasdjkadjks');

$context = json_encode(array('user_guid' => $user_guid, 'group_guid' => $group_guid));

$content = elgg_view('filtrate/dashboard', array(
	'menu_name' => 'todo_submission_dashboard',
	'list_url' => elgg_get_site_url() . 'ajax/view/todo/group_submissions',
	'default_params' => array(
		'sort_order' => 'DESC',
		'filter_return' => 1,
		'filter_ontime' => 'all',
		'user' => $user->username
	),
	'page_context' => $context
));

$js = <<<JAVASCRIPT
	<script type='text/javascript'>
		elgg.register_hook_handler('init', 'system', function() {	
			// Re-init the chosen elements after filtrate
			$('.tgstheme-chosen-select').each(function(idx) {
				elgg.tgstheme.defaultChosenInit($(this));
			});
		}, 1);
	</script>
JAVASCRIPT;

$content = <<<HTML
	$content
	$js
HTML;

echo $content;