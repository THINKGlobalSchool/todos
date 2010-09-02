<?php
	/**
	 * Todo link for profile block
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	$user = page_owner_entity();
	echo "<li><a href=\"{$vars['url']}pg/todo/{$user->username}\">" . elgg_echo('todo:title') . "</a></li>";
?>
