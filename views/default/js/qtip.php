<?php
/**
 * Todos simplecache view for qtip JS library
 *
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
$js_path = elgg_get_config('path');
$js_path = "{$js_path}mod/todo/vendors/qtip/jquery.qtip.js";

include $js_path;