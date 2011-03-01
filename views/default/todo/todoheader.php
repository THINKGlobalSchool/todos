<?php
/**
 * Todo header
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['value'] - Text to display
 * @uses $vars['priority'] - Display different color depending on priority, either: TODO_PRIORITY_HIGH, TODO_PRIORITY_MEDIUM, TODO_PRIORITY_LOW
 */
?>
<div class="todo_seperator todo_priority_<?php echo $vars['priority']; ?>">
			<h3>
				<?php 
				echo $vars['value'];
				?>
			</h3>
			<!--<div style='margin-top: 0px; border-bottom: 2px solid #888; width: 100%;'></div>-->
</div>