<?php 
	/**
	 * Todo user settings form
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	global $CONFIG;
	
	$user = get_loggedin_user();
	$hash = generate_todo_user_hash($user);
	
	echo "<label>" . elgg_echo('todo:label:calendarurl') . ": </label><br />";
	
?>
<input type='text' size='100' style='font-size: 12px;' readonly=readonly name='params[calurl]' value="<?php echo $CONFIG->wwwroot . "pg/todo/calendar/" . $user->username . "?t=" . $hash . '&bogo=' . time(); ?>" />
<br />
