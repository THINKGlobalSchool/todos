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

.todo-topbar-item {
	height: 40px;
}

.todo-topbar-item .unaccepted {
	background-color: #cc0000;
}

.todo-topbar-item .incomplete {
	background-color: #000099;
}

.todo-topbar-item:hover #todo-hover-stats {
	display: block;
}

#todo-hover-stats {
	position: absolute;
	top: 32px;
	left: 0;
	display: none;
	background: #ffffff;
	padding: 5px;
	-webkit-border-radius: 0 0 4px 4px;
	-moz-border-radius: 0 0 4px 4px;
	border-radius: 0 0 4px 4px;
}

/** Todo info toggler **/
.todo-entity-info {
	display: none;
	position: absolute;
	min-height: 18px;
	z-index: 9000;
	background: #FFF;
	padding: 4px;
	-webkit-box-shadow: 1px 1px 5px #000;
	-moz-box-shadow: 1px 1px 5px #000;
	box-shadow: 1px 1px 5px #000;
}

.todo-entity-info a {
	display: block;
	width: 100%;
	padding-left: 2px;
	padding-right: 2px;
}

.todo-entity-info a:hover {
	background: #DDD;
	text-decoration: none;
}

/** Help popups */
.todo-help-popup { 
    width: 200px;
	padding: 5px;
	position: absolute;
}

/** POPUP DIALOG **/

#todo-submission-dialog  {
	width: 735px;
	padding: 10px;
}

.submission-content-pane {
	display: none;
}

/** Todo sort menu **/

.elgg-menu-todo-sort {
	text-align: center;
}

.elgg-menu-todo-sort li {
	margin-right: 5px;
	font-size: 10px;
	text-transform: uppercase;
}

.elgg-menu-todo-sort li a {
	color: #999;
}

.elgg-menu-todo-sort li.elgg-state-selected a {
	font-weight: bold;
	color: inherit;
}

/** Todo dashboard controls **/

.todo-dashboard {
}

.todo-dashboard > .ui-tabs-panel {

}

/** Resets and tweaks for todo sidebar in groups **/
.elgg-todo-sidebar .elgg-image-block {
	border-bottom: 1px dotted #CCC;
}

.elgg-todo-sidebar .elgg-button {
	display: inline-block;
}

/** Hover add Menu **/
.add-menu {
	text-align: right;
	font-weight: bold;
	color: #FFFFFF;
	
	/*http://www.colorzilla.com/gradient-editor/*/
	background: -moz-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(0,0,0,0.4) 16%, rgba(0,0,0,0.55) 22%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(255,255,255,0)), color-stop(16%,rgba(0,0,0,0.4)), color-stop(22%,rgba(0,0,0,0.55))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(left, rgba(255,255,255,0) 0%,rgba(0,0,0,0.4) 16%,rgba(0,0,0,0.55) 22%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(left, rgba(255,255,255,0) 0%,rgba(0,0,0,0.4) 16%,rgba(0,0,0,0.55) 22%); /* Opera11.10+ */
	background: -ms-linear-gradient(left, rgba(255,255,255,0) 0%,rgba(0,0,0,0.4) 16%,rgba(0,0,0,0.55) 22%); /* IE10+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#8c000000',GradientType=1 ); /* IE6-9 */
	background: linear-gradient(left, rgba(255,255,255,0) 0%,rgba(0,0,0,0.4) 16%,rgba(0,0,0,0.55) 22%); /* W3C */
}

.submission-content-input-add {
	vertical-align: middle;
	margin-top: 2px;
}

.todo-content-added {
	font-weight: bold;
	margin-top: 4px;
	margin-right: 8px;
}

/* Entity Menu Items */
.elgg-menu-item-todo-complete {
	margin-top: -3px;
}

.elgg-menu-item-todo-open {
	margin-top: -3px;
}

.elgg-menu-item-todo-create-submission {
	margin-top: -3px;	
}

.elgg-menu-item-todo-accept input {
	margin-top: -3px;
}

/* Ajax submissions */
.todo-ajax-submission {
	width: 550px;
	max-height: 750px;
	overflow-x: hidden;
}

.todo-ajax-submission-navigation #fancybox-left {
	left: -9999px;
	width: 0px;
}

.todo-ajax-submission-navigation #fancybox-right {
	left: -9999px;
	width: 0px;
}

.todo-ajax-submission-navigation-prev,
.todo-ajax-submission-navigation-next {
	font-weight: bold;
	display: none;
	text-decoration: none;
}

.todo-ajax-submission-navigation-next:hover,
.todo-ajax-submission-navigation-prev:hover {
	text-decoration: none;
} 