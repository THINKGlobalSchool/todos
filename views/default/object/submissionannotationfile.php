<?php
/**
 * Todo Submission Annotation object view
 * - Just piggyback on the file view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

elgg_dump($vars['entity']->getFilenameOnFilestore());
elgg_dump($vars['entity']->thumbnail);
elgg_dump($vars['entity']->smallthumb);
elgg_dump($vars['entity']->largethumb);

$contents = elgg_view("object/file", $vars);
echo $contents; 
