<?php
/**
 * Todo Accept todo action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$user = elgg_get_logged_in_user_entity();

$suppress_complete = get_input('suppress_complete');

$value = $suppress_complete ? 1 : 0;

elgg_set_plugin_user_setting('suppress_complete', $value, $user->getGUID(), 'todo');
$val = elgg_get_plugin_user_setting('suppress_complete', $user->getGUID(), 'todo');

system_message(elgg_echo('admin:configuration:success'));
forward(REFERER);
