<?php
/**
 * Todo Topbar Display
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$user = elgg_get_logged_in_user_entity();

$todos = get_users_todos($user->getGUID());

$count = 0;
foreach ($todos as $todo) {
	if (!has_user_accepted_todo($user->getGUID(), $todo->getGUID())) {
		$count++;
	}
}	

if ($count > 0) {
	$title = sprintf(elgg_echo('todo:label:unviewed'), $count,(($count > 1) ? 's' : ''));
	$class = 'todonotifier new';
} else {
	$title = elgg_echo('todo:label:nounviewed');
	$class = 'todonotifier';
}
?>

<a id="todo_topbar_link" href="<? echo elgg_get_site_url(); ?>todo" class="<?php echo $class; ?>" style="margin-right: -4px;">
	<span>
		<?php 
			if ($count > 0) {
				echo $count;
			} else {
				echo "&nbsp;";
			}
		?>
	</span>
<div class='<?php echo ($count > 0) ? 'todoexists' : 'todolabel'; ?>'>To Do's</div>
</a> 

