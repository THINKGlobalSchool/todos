<?php
/**
 * Group user submissions ajax view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['group_guid'] The group guid
 */

elgg_load_js('elgg.filtrate');
elgg_load_js('elgg.filtrate.utilities');

$group_guid = elgg_extract('group_guid', $vars);

$group = get_entity($group_guid);

// Check for valid group
if (!elgg_instanceof($group, 'group')) {
	echo elgg_echo('todo:error:invalidgroup');
	return;
}

// Get group members
$group_members = $group->getMembers(array('limit' => 0));

// Headers
$members_header = elgg_echo('todo:label:selectmember');
$submissions_header = "<span class='todo-user-submissions-header'>" . elgg_echo('todo:label:submissions') . "</span>";

// Content
$submissions_instructions = elgg_echo('todo:label:selectamember');

$submissions_content = <<<HTML
	<div class='todo-user-submissions-content'>
		<strong>&#9668; $submissions_instructions</strong>
	</div>

	<style type='text/css'>
		.filtrate-menu-advanced {
			display: none !important;
		}
		.elgg-menu-item-advanced {
			display: none !important;
		}
	</style>

	<script type='text/javascript'>
		// Smack down the default init handler.. kind of gross
		elgg.register_hook_handler('init', 'chosen.js', function(){return function(){return false;};});
	</script>
HTML;

$members_content = "";

// Add members content
foreach ($group_members as $member) {
	$href = elgg_get_site_url() . "ajax/view/todo/user_submissions?user_guid={$member->guid}&group_guid={$group_guid}";
	$members_content .= "<a href='$href' class='todo-group-member'>$member->name</a>";
}

// Modules
$members_module = elgg_view_module('info', $members_header, $members_content);
$submissions_module = elgg_view_module('info', $submissions_header, $submissions_content);

// Main content
$content = <<<HTML
	<div id='todo-group-user-submissions-$group_guid' class='todo-group-user-submissions-container'>
		<div class='todo-group-members-container'>
			$members_module
		</div>
		<div class='todo-user-submissions-container'>
			$submissions_module
		</div>
	</div>
HTML;

echo $content;
