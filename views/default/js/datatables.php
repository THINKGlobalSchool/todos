<?php
/**
 * Todo DataTables simplecache view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
$js_path = elgg_get_config('path');
$d_js = "{$js_path}mod/todos/vendors/datatables/media/js/jquery.dataTables.min.js";
$fc_js = "{$js_path}mod/todos/vendors/datatables/extras/FixedColumns/media/js/FixedColumns.min.js";

include $d_js;
include $fc_js;