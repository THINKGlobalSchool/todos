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

.todo {
	
}

.todo .right {
	float: right;
	text-align: right;
}

.todo .left {
	float: left;
	text-align: left;
}

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

.todo_icon {
	float:left;
	margin:3px 0 0 0;
	padding:0;
}


.todo .listingstrapline {
	margin: 0 0 0 0px;
	padding:0;
	color: #aaa;
	line-height:1em;
}

.todo .strapline {
	padding:10px;
	height: auto;
	min-height: 16px;
	background: #EEEEEE;
	border: 1px solid #D4DAE6;
	margin: 0 0 0 0px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo .description img[align="left"] {
	margin: 10px 10px 10px 0;
	float:left;
}

.todo p.fulltags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 7px 0px;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.todo p.listingtags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 0px 0px;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.todo p.gallerytags {
	background:transparent url(<?php echo $vars['url']; ?>_graphics/icon_tag.gif) no-repeat scroll left 2px;
	margin:0 0 0 0;
	padding:0pt 0pt 0pt 16px;
	min-height:22px;
}

.todo .todo_header {
	width: 98%;
}

.todo .todo_header .todo_header_title {
	width: 50%;
	float: left;
}

.todo .todo_header .todo_header_controls {
	float: left;
	width: 50%;
	text-align: right;
}

.todo .assignee_table {
	width: 98%;
	margin: 4px;

	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo .assignee_table td.assignee {
	padding: 5px;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

.todo .assignee_table td.alt {
	background: #eeeeee;
}

.todo .status_table {
	width: 100%;
	margin-top: 4px;
	border: 1px solid #aaaaaa;
}

.todo .status_table td {
	padding: 5px;
}

.todo .status_table th {
	padding: 5px;
	
	font-weight: bold;
	color: #666666;
	border-bottom: 1px solid #aaaaaa;
}

.todo .status_table td.alt {
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

.todo_listing {
	margin: 4px;
	width: 98%;
}

.todo_listing .todo_listing_icon {
	width: 25px;
	height: 25px;
	float: left;
}

.todo_listing .todo_listing_info {
	height: 25px;
	width: auto;
	padding-left: 10px;
	float: left;
}

.todo_listing .todo_listing_options {
	float: right;
	width: 100px;
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

.todo #add_content_area {
	width: 100%;
}

.todo #add_content_area .content_menu {
	width: 20%;
	float: left;
	display: none;
}

.todo #add_content_area .content_menu  a {
	font-size: 120%;
}

.todo #add_content_area #content_container {
	width: 79%;
	float: left;
}

.todo .content_div {
	display:none;
}

.todo #submission_error_message {
	color: red;
	font-weight: bold;
	display:none;
}


.todo_seperator {
	-webkit-border-radius: 8px; 
	-moz-border-radius: 8px;
	-webkit-box-shadow: -2px 2px 4px rgba(0, 0, 0, 0.50); /* safari v3+ */
	-moz-box-shadow: -2px 2px 4px rgba(0, 0, 0, 0.50); /* FF v3.5+ */
	margin-bottom:5px;
	margin-top: 5px;
	padding: 5px 5px 5px 10px;
}

.todo_seperator h3 {
	color: #ffffff;
	text-shadow: #000 -1px 1px 2px;
	font-weight: bold;
}

.todo_priority_1 {
	/**border: 2px solid #E83131;**/
	background: #FFECEC;
	background: #FA2A02;
}

.todo_priority_2 {
		/**border: 2px solid #F19F45;**/
	background: #FFFFCC;
	background: #FFAB25;
}

.todo_priority_3 {
		/**border: 2px solid #438743;**/
	background: #E1FFE1;
	background: #71BC17;
}

#submission_ajax_spinner {
	float: right;
	margin-right: 340px;
	display: none;
}

.todo_owner_block {
	float: left; 
	color: black; 
	margin: 0;
	font-size: 75%;
	font-style: italic;
}

/** POPUP DIALOG **/

/** Popups **/

.ui-widget-overlay
{
	position: fixed;
	top: 0px;
	left: 0px;
    background-color: #000000;
    opacity: 0.5;
	-moz-opacity: 0.5; 
	z-index: 1001 !important;
}

#submission_dialog  {
	border: 8px solid #555555;
	background: #ffffff;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

/** Top Bar **/

#todo_topbar_link{
	margin-left:4px !important;
}

/* messages/new messages icon & counter in elgg_topbar */
a.todonotifier {
	background:transparent url(<?php echo $vars['url']; ?>mod/todo/images/todo_topbar.gif) no-repeat left 2px;
	padding-left:16px;
	margin:3px 15px 0 5px;
	cursor:pointer;
}
a.todonotifier:hover {
	text-decoration: none;
	background:transparent url(<?php echo $vars['url']; ?>mod/todo/images/todo_topbar.gif) no-repeat left -16px;
}
a.todonotifier.new {
	background:transparent url(<?php echo $vars['url']; ?>mod/todo/images/todo_topbar.gif) no-repeat left 2px;
	padding-left:18px;
	margin:3px 15px 0 5px;
	color:white;
}
a.todonotifier.new:hover {
	text-decoration: none;
	background:transparent url(<?php echo $vars['url']; ?>mod/todo/images/todo_topbar.gif) no-repeat left -16px;
}
a.todonotifier.new span {
	background-color: red;
	-webkit-border-radius: 10px; 
	-moz-border-radius: 10px;
	-webkit-box-shadow: -2px 2px 4px rgba(0, 0, 0, 0.50); /* safari v3+ */
	-moz-box-shadow: -2px 2px 4px rgba(0, 0, 0, 0.50); /* FF v3.5+ */
	color:white;
	display:block;
	float:right;
	padding:0;
	position:relative;
	text-align:center;
	top:-2px;
	right:5px;
	min-width: 16px;
	height:16px;
	font-size:10px;
	font-weight:bold;
	left: -53px;
}

#todo_topbar_link img {
	margin-top: 2px;
}

div.todolabel {
	width: auto;
	bottom: 2px;
	left: 0px;
	position: relative;
	display: inline;
}

div.todoexists {
	width: auto;
	bottom: 2px;
	left: 16px;
	position: relative;
	display: inline;
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

/** Tweaks for todo sidebar in groups **/
.todo-sidebar .entity_listing_info {
	width: auto;
}
}
