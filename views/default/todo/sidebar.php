<?php
/**
 * To Do sidebar extension
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

if (elgg_in_context('todo')) {
	$content = <<<HTML
		<div id='todo-main-sidebar'>
		</div>
HTML;
	echo $content;
}