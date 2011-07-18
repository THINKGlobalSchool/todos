<?php
/**
 * Todo CSS
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>

.todo .multiselect {
	border: 2px solid #bbbbbb;
	font-size: 120%;
	width: auto;
	height: auto;
	padding: 10px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

#submission-content-select {
	border: 2px solid #bbbbbb;
	font-size: 120%;
	width: 100%;
	height: auto;
	padding: 10px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}


div.todo-assignee-container {
	padding: 5px;
	margin: 3px;
}

#todo-assign-group-container {
	display: none;
}

#rubric_picker_container {
	display: none;
}

.todo span.complete {
	color: green;
	font-weight: bold;
}

.todo span.incomplete {
	color: red;
	font-weight: bold;
}

span.accepted {
	font-weight: bold !important;
	color: green !important;
}

span.unviewed {
	font-weight: bold !important;
	color: #9D1520 !important;
}

#submission-content-container .content-menu {
	width: 20%;
	float: left;
}

#submission-content-container #submission-control-back {
	display: none;
}

#submission-content-container .content-menu  a {
	font-size: 120%;
}

#submission-content-container #submission-content {
	width: 79%;
	float: left;
}

#submission-error-message {
	color: red;
	font-weight: bold;
	display:none;
}

.todo-priority-label {
	width: 100px;
	-webkit-border-radius: 11px; 
	-moz-border-radius: 11px;
	padding: 3px;
	text-align: center;
	margin-top: 3px;
}

.todo-priority-label .label-text {
	color: #ffffff;
	font-weight: bold;
}

.todo-priority-1 {
	/**border: 2px solid #E83131;**/
	background: #FFECEC;
	background: #FA2A02;
}

.todo-priority-2 {
	/**border: 2px solid #F19F45;**/
	background: #FFFFCC;
	background: #FFAB25;
}

.todo-priority-3 {
	/**border: 2px solid #438743;**/
	background: #E1FFE1;
	background: #71BC17;
}

#submission-ajax-spinner {
	float: right;
	margin-right: 340px;
	display: none;
}

/* messages/new messages icon & counter in elgg_topbar */
.todo-notifier {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/images/todo_topbar.gif) no-repeat left 2px;
	margin-top: -3px !important;
}
.todo-notifier:hover {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/images/todo_topbar.gif) no-repeat left -16px;
}

/** POPUP DIALOG **/

#todo-submission-dialog  {
	width: 735px;
	padding: 10px;
}


/** Resets and tweaks for todo sidebar in groups **/
.elgg-todo-sidebar .elgg-image-block {
	border-bottom: 1px dotted #CCC;
}

.elgg-todo-sidebar .elgg-button {
	display: inline-block;
}
