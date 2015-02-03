<?php
/**
 * Group submissions ajax view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */


$context = json_decode(get_input('page_context'));

set_input('group_guid', $context->group_guid);
set_input('user_guid', $context->user_guid);
echo elgg_view('todo/submissions');