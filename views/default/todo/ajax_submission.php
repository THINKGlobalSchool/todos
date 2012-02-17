<?php
/**
 * Ajax Submission View
 * - Wraps the object/todosubmission view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['guid']
 * 
 */

$guid = elgg_extract('guid', $vars);

$entity = get_entity($guid);

$vars['full_view'] = TRUE;

echo "<div class='todo-ajax-submission'>";
echo elgg_view_entity($entity, array('full_view' => TRUE));
echo elgg_view_comments($entity);
echo "</div>";