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

.todo .submission_content_select {
	border: 2px solid #bbbbbb;
	font-size: 120%;
	width: 100%;
	height: auto;
	padding: 10px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}


.todo-assignee-table {
	width: 98%;
	margin: 4px;

	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo-assignee-table td {
	padding: 5px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo-assignee-table tr:nth-child(even) {
	background: #eeeeee;
}

#assign_individual_container {
	display: none;
}

#assign_group_container {
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

#submission-content-container .content_menu {
	width: 20%;
	float: left;
	display: none;
}

#submission-content-container .content_menu  a {
	font-size: 120%;
}

#submission-content-container #submission-content {
	width: 79%;
	float: left;
}

.content_div {
	display:none;
}

#submission-error-message {
	color: red;
	font-weight: bold;
	display:none;
}

.todo-priority-label {
	width: 100px;
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	-webkit-box-shadow: -2px 2px 4px rgba(0, 0, 0, 0.50); /* safari v3+ */
	-moz-box-shadow: -2px 2px 4px rgba(0, 0, 0, 0.50); /* FF v3.5+ */
	margin-bottom:5px;
	margin-top: 5px;
	padding: 5px 5px 5px 10px;
}

.todo-priority-label .label-text {
	color: #ffffff;
	text-shadow: #000 -1px 1px 2px;
	font-weight: bold;
	margin-left: 0px !important;
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

/** POPUP DIALOG **/

/** Popups **/

.ui-widget-overlay
{
	position: fixed;
	top: 0px;
	left: 0px;
    background-color: #000000 !important;
    opacity: 0.5;
	-moz-opacity: 0.5; 
	z-index: 1001 !important;
}

#submission-dialog  {
	border: 8px solid #555555;
	background: #ffffff;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}


/* messages/new messages icon & counter in elgg_topbar */
.todo-notifier {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/images/todo_topbar.gif) no-repeat left 2px;
	margin-top: -2px;
}
.todo-notifier:hover {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/images/todo_topbar.gif) no-repeat left -16px;
}


/** jQuery UI Stuff **/

.ui-dialog .ui-dialog-buttonpane {
	position: absolute; 
	right: .3em; 
	top: 30px; 
	width: 19px; 
	margin: -10px 0 0 0; 
	padding: 1px; height: 18px; 
}

.ui-dialog .ui-dialog-buttonpane button { 
	
	cursor: pointer; 
	padding: .2em .6em .3em .6em; 
	line-height: 1.4em; 
	width:auto; 
	overflow:visible; 

}

.ui-dialog .ui-dialog-buttonpane button {
	-moz-border-radius:4px 4px 4px 4px;
	-webkit-border-radius: 5px 5px 5px 5px;
	background:none repeat scroll 0 0 #000000;
	border:1px solid #000000;
	color:#FFFFFF;
	cursor:pointer;
	font:bold 12px/100% Arial,Helvetica,sans-serif;
	height:25px;
	float: right; margin: .5em .4em .5em 0; 
	padding:2px 6px;
	width:auto;
}
