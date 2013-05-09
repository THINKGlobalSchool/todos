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

.todo span.todo-grade-status {
	margin-left: 15px;
	font-weight: bold;
}

.todo-status-dash {
	color: #bbbbbb;
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
	border-radius: 11px;
	padding: 3px;
	text-align: center;
	margin-top: 3px;
}

.todo-priority-label .label-text {
	color: #ffffff;
	font-weight: bold;
}

.todo-priority-1 {
	background: #CE0000;
}

.todo-priority-2 {
	background: #FA2A02;
}

.todo-priority-3 {
	background: #F87217;
}

.todo-priority-4 {
	background: #FBB917;
}

.todo-priority-5 {
	background: #71BC17;
}

#submission-ajax-spinner {
	float: right;
	margin-right: 340px;
	display: none;
}

/* messages/new messages icon & counter in elgg_topbar */
.todo-notifier {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/todo_topbar.gif) no-repeat left 2px;
	margin-top: -3px !important;
}
.todo-notifier:hover {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/todo_topbar.gif) no-repeat left -16px;
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

.todo-topbar-item:hover #todo-topbar-hover {
	display: block;
}

#todo-topbar-hover {
	position: absolute;
	top: 32px;
	left: 0;
	display: none;
	background: #ffffff;
	padding: 5px;
	-webkit-border-radius: 0 0 4px 4px;
	-moz-border-radius: 0 0 4px 4px;
	border-radius: 0 0 4px 4px;
	width: 145px;
}

#todo-topbar-hover .elgg-table td {
	color: #222222;
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

/** Todo sort menus **/

.elgg-menu-todo-sort {
	text-align: center;
}

.elgg-menu-submissions-sort {
	text-align: left;
}

.elgg-menu-todo-sort li, .elgg-menu-submissions-sort li {
	margin-right: 5px;
	font-size: 10px;
	text-transform: uppercase;
}

.elgg-menu-todo-sort li a, .elgg-menu-submissions-sort li a {
	color: #999;
}

.elgg-menu-todo-sort li.elgg-state-selected a, .elgg-menu-submissions-sort li.elgg-state-selected a {
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

.elgg-menu-item-todo-return-required img,
.elgg-menu-item-category-exam img,
.elgg-menu-item-category-basic-task img, 
.elgg-menu-item-category-assessed-task img {
	margin-top: 3px;
}


/* New entity menu */
.elgg-menu-entity-buttons .elgg-menu-item-todo-complete input,
.elgg-menu-entity-buttons .elgg-menu-item-todo-accept input,
.elgg-menu-entity-buttons .elgg-menu-item-todo-open input,
.elgg-menu-entity-buttons .elgg-menu-item-todo-complete input,
.elgg-menu-entity-buttons .elgg-menu-item-todo-signup input,
.elgg-menu-entity-buttons .elgg-menu-item-todo-create-submission input {
	width: 160px;
	margin-top: 3px;
	margin-bottom: -2px;
}


.elgg-menu-entity-buttons .elgg-menu-item-todo-complete a,
.elgg-menu-entity-buttons .elgg-menu-item-todo-accept a,
.elgg-menu-entity-buttons .elgg-menu-item-todo-open a,
.elgg-menu-entity-buttons .elgg-menu-item-todo-complete a,
.elgg-menu-entity-buttons .elgg-menu-item-todo-signup a,
.elgg-menu-entity-buttons .elgg-menu-item-todo-create-submission a {
	margin-top: 5px;
	width: 146px;
}

.elgg-menu-entity-buttons .elgg-menu-item-todo-accept {
	background: none !important; 
}

.elgg-menu-entity-buttons .elgg-menu-item-todo-accept span {
	width: 100%;
	margin-bottom: 2px;
	display: block;
}

.elgg-menu-entity-buttons .elgg-menu-item-todo-accept span.accepted {
	float: right;
}

/* Ajax submissions */
.todo-ajax-submission {
	width: 650px;
	padding-right: 10px;
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

/* Submission attachments */
.todo-submission-dropzone {
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	border: 1px solid #CCCCCC;
	display: block;
	margin-top: 5px;
	padding: 10px;
	height: 18px;
	width: 95%;
	margin-left: auto;
	margin-right: auto;
}

.todo-submission-dropzone-background {
	background-image: url('<?php echo elgg_get_site_url() . 'mod/todo/graphics/submissiondropzone.png' ?>');
	background-position: center center;
	background-repeat: no-repeat;
}

.todo-submission-dropzone-drag {
	box-shadow: 0px 0px 5px Green;
}

.todo-submission-attachment-upload {
	display: none;
}

.todo-submission-hidden-form {
	display: none;
}

.todo-submission-drop-info .file-size {
	color: #666666;
	font-size: 1.2em;
	margin-left: 20px;
}

.todo-submission-drop-info .file-name {
	color: #333333;
	font-size: 1.2em;
	font-weight: bold;
}

.todo-submission-drop-info .file-replace {
	font-size: 1.2em;
	color: #AAAAAA;
	float: right;
}

.todo-submission-attachment {
	background: none repeat scroll 0 0 white;
	border: 1px solid #BBBBBB;
	margin-right: 10px;
	margin-top: 3px;
	padding-right: 10px;
	display: inline-block;
	min-height: 26px;
}

.todo-submission-attachment .todo-submission-attachment-title {
	font-size: 11px;
	font-weight: bold;
	padding: 6px 4px;
}

.todo-submission-attachment .todo-submission-attachment-icon {
	width: 26px;
}

.todo-submission-attachment .todo-submission-attachment-icon img {
	width: 24px;
	height: 24px;
	padding: 3px 0 0 2px;
}

#submit-empty-loader {
	min-width: 30px;
	min-height: 27px;
	background-size: 27px;
}

/* Dashboard user submissions */
.todo-submission-listing {
	font-size: 12px;
}

.todo-user-submission-return-dropdown,
.todo-user-submission-ontime-dropdown {
	font-size: 10px;
	width: 100px;
}

.todo-user-submissions-content {
	min-height: 400px;
}

#todo-user-submissions-filter-menu {
	position: relative;
}

.todo-user-submissions-datepicker {
	font-size: 10px;
}

.todo-user-submissions-date-input {
	display: inline;
	font-size: 11px;
	width: 140px;
}

.elgg-menu-item-todo-user-submissions-sort {
	float: right;
}

.todo-submissions-table {
	width: 100%;
	margin-top: 10px;
}

.todo-submissions-table th {
	font-weight: bold;
	color: #666666;
}

.todo-submissions-table td, todo-submissions-table th {
	
}

.todo-submissions-table td.todo-submission-column {
	width: 20%
}

.todo-submissions-table td.todo-submission-info-column {
	padding: 0px;
	width: 30%
}

.todo-submission-info-table {
	width: 100%;
} 

.todo-submission-info-table td {
	border: 0px;
	padding: 2px 4px;
}

.todo-submission-info-table td:first-child {
	border-right: 1px solid #CCCCCC;
}

.todo-submission-info-table td.submission-info-label,
.todo-submission-info-table td.submission-info-value {
	color: #555555;
	font-size: 10px;
}

.todo-submission-info-table td.submission-info-label {
	font-weight: bold;
	width: 70%;
}

.todo-submission-info-table td.submission-info-value {
	width: 30%;
}

.submission-complete-info {
	margin-top: 5px;
	margin-bottom: 8px;
}

.submission-complete-info span {
	margin-right: 10px;
	font-size: 90%;
}

/* Dashboard group user submissions */
.todo-group-user-submissions-container .todo-user-submissions-container {
	float: left;
	width: 74%;
}

.todo-group-user-submissions-container .todo-group-members-container {
	float: left;
	width: 25%;
	margin-right: 6px;
}

.todo-group-member {
	display: block;
	margin: 2px 0;
	padding: 4px;
	font-weight: bold;
}

.todo-group-member:hover {
	background: #CCCCCC;
}

/** Submission Grade Form **/
div.todo-submission-grade-container {
	text-align: right;
	float: right;
}

.todo-submission-grade-label {
	color: #666666;
	font-size: 1.1em;
	font-weight: bold;
	line-height: 2.2em;
}

form.elgg-form-submission-grade {

}


input.submission-grade-input {
	width: 100px;
	border: 3px solid #DDD;
}

/** Submission Gradebook Styles **/
.submission-incomplete {
	font-style: italic;
	color: #555555;
}

.submission-ungraded {
	font-style: italic;
}

.submission-unassigned {
	color: #AAAAAA;
	font-size: 12px;
}

/** Calendar Styles **/
#todo-category-calendar {
	padding-top: 15px;
}

.elgg-todocalendar-feed {
	border-radius: 10px 10px 10px 10px;
	-moz-border-radius: 10px 10px 10px 10px;
	-webkit-border-radius: 10px 10px 10px 10px;
}

#todo-calendar-loader {
    overflow: hidden;
    width: 200px;
}

.todo-calendar-lightbox {
	display: none;
}

#todo-calendar-loader h2 {
	text-align: center;
}

#todo-calendar-loader img {
	margin-left: auto;
	margin-right: auto;
	display: block;
}

.todo-iplan-float {
	float: right;
}

td.todo-iplan-hover {
	text-align: center;
}

td.todo-iplan-hover a {
	font-size: 85%;
}

.todo-sidebar-group-legend {
	padding: 5px;
	font-size: 11px;
	margin-bottom: 5px;
	-webkit-box-shadow: 1px 1px 2px #333;
	-moz-box-shadow: 1px 1px 2px #333;
	box-shadow: 1px 1px 2px #333;
}

.todo-sidebar-group-legend a {
	color: inherit !important;
}

.todo-sidebar-group-legend a:hover {
	color: inherit !important;
	text-decoration: underline;
}

/** Todo Fullcalendar Specific **/

#todo-category-calendar .todo-calendar-event {
	margin-bottom: 5px;
}

#todo-category-calendar .todo-calendar-event .fc-event-title {
	padding: 0 0 !important;
}

#todo-category-calendar .todo-calendar-event .todo-calendar-event-title-container {
	padding-left: 3px;
}

#todo-category-calendar .todo-calendar-event .todo-calendar-event-title {
	font-weight: bold;
}

#todo-category-calendar .todo-calendar-event .todo-calendar-event-subtitle {
	font-style: italic;
}

#todo-category-calendar .todo-calendar-event .todo-calendar-event-subtitle-return-required {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/info_icon.png) no-repeat right bottom;
}

#todo-category-calendar .todo-calendar-event .todo-calendar-event-subtitle-basic-task {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/todo_cat_basic_task_small.png) no-repeat right bottom;
}

#todo-category-calendar .todo-calendar-event .todo-calendar-event-subtitle-assessed-task {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/todo_cat_assessed_task_small.png) no-repeat right bottom;
}

#todo-category-calendar .todo-calendar-event .todo-calendar-event-subtitle-exam {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/todo_cat_exam_small.png) no-repeat right bottom;
}

.todo-calender-tooltip-return-required {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/info_icon.png) no-repeat left center;
	min-width: 150px;
}

.todo-calender-tooltip-basic-task {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/todo_cat_basic_task_small.png) no-repeat left center;
}

.todo-calender-tooltip-assessed-task {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/todo_cat_assessed_task_small.png) no-repeat left center;
}

.todo-calender-tooltip-exam {
	background:transparent url(<?php echo elgg_get_site_url(); ?>mod/todo/graphics/todo_cat_exam_small.png) no-repeat left center;
}

.todo-calendar-icon-padding {
	padding-left: 18px;
}


/** End FC **/

.qtip-wrapper {
	-webkit-box-shadow: 1px 1px 5px #000;
	-moz-box-shadow: 1px 1px 5px #000;
	box-shadow: 1px 1px 5px #000;
}

.todo-required:after {
	color: red;
	content: " *";
}

.todo-work-submitted-list li {
	height: 25px;
	line-height: 25px;
	font-weight: bold;
}

/** Spot content submission icon */
.todo-spot-content-link {
	margin-left: 10px;
	position: relative;
	top: 1px;
	display: inline-block;
	width: 100px;
}

.todo-spot-content-link span {
	display: none;
}

.todo-spot-content-link:hover span {
	display: inline-block;
	font-size: 0.8em;
	text-decoration: none;
	vertical-align: middle;
	position: relative;
	top: -3px;
	margin-left: 6px;
}