<?php
/**
 * Todo Datatables CSS
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>

.dataTables_scroll {
	clear: both;
}

.DTFC_ScrollWrapper {
	border: 1px solid #999999;
}

.DTFC_LeftHeadWrapper, .DTFC_LeftBodyWrapper, .DTFC_LeftFootWrapper {
	border-right: 1px solid #999999;
}

.dataTables_scrollBody {
	*margin-top: -1px;
	-webkit-overflow-scrolling: touch;
}

/** Sorting **/

#todo-grade-table_wrapper .sorting_asc {
	background: url('<?php echo elgg_get_site_url(); ?>mod/todo/vendors/datatables/media/images/sort_asc.png') no-repeat center right #DDDDDD;
}

#todo-grade-table_wrapper .sorting_desc {
	background: url('<?php echo elgg_get_site_url(); ?>mod/todo/vendors/datatables/media/images/sort_desc.png') no-repeat center right #DDDDDD;
}

#todo-grade-table_wrapper .sorting {
	background: url('<?php echo elgg_get_site_url(); ?>mod/todo/vendors/datatables/media/images/sort_both.png') no-repeat center right #EEEEEE;
}

#todo-grade-table_wrapper .sorting_asc_disabled {
	background: url('<?php echo elgg_get_site_url(); ?>mod/todo/vendors/datatables/media/images/sort_asc_disabled.png') no-repeat center right #EEEEEE;
}

#todo-grade-table_wrapper .sorting_desc_disabled {
	background: url('<?php echo elgg_get_site_url(); ?>mod/todo/vendors/datatables/media/images/sort_desc_disabled.png') no-repeat center right #EEEEEE; 
}
 
/*
 * Sorting classes for columns
 */
/* For the standard odd/even */
#todo-grade-table_wrapper tr.odd td.sorting_1 {
	background-color: #F0F0F0;
}

#todo-grade-table_wrapper tr.even td.sorting_1 {
	background-color: #DDDDDD;
}

/** Further grade table styles **/
#todo-grade-table_wrapper th {
	font-weight: bold;
	color: #444444;
}

#todo-grade-table .assignee-name {
	min-width: 100px;
}

#todo-grade-table .todo-title-link {
	min-width: 120px;
}

#todo-grade-table_filter {
	margin-bottom: 10px;
	margin-top: 10px;
}

#todo-grade-table_filter label {
	width: 50%;
	text-align: right;
}

#todo-grade-table_filter input {
	width: 250px;
	font-size: 11px;
}

#todo-grade-table_wrapper .elgg-table {
	border: 0px;
}

#todo-grade-table_wrapper .elgg-table td, #todo-grade-table_wrapper .elgg-table th {
	border-style: none solid none none;
	border-width: 0 1px 0 0;
}

#todo-grade-table_wrapper .elgg-table td:last-child, #todo-grade-table_wrapper .elgg-table th:last-child {
	border: 0;
}