<?php
/**
 * User submissions ajax view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

$user_guid = get_input('user_guid');
$group_guid = get_input('group_guid');

$user = get_entity($user_guid);

if (!elgg_instanceof($user, 'user')) {
	echo elgg_echo('todo:error:invaliduser');
	return;
}

$view_vars = array('user_guid' => $user_guid);

// Check for a group
if (elgg_instanceof(get_entity($group_guid), 'group')) {
	$view_vars['group_guid'] = $group_guid;
}

// Create subissions module				
$module = elgg_view('modules/genericmodule', array(
	'view' => 'todo/submissions',
	'module_id' => '',
	'view_vars' => $view_vars, 
));

echo $module;

echo <<<JAVASCRIPT
	<script>
		elgg.modules.genericmodule.destroy();
		elgg.modules.genericmodule.init();
	</script>
JAVASCRIPT;

?>