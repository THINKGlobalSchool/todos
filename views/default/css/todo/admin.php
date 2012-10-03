<?php
/**
 * Todo Admin CSS
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>


.todoadmin table.todostats {
	border: 1px solid #ccc;
	margin-top: 15px;
	width: 60%;
}

.todoadmin table.todostats tr:nth-child(odd) { 
	background-color:#eee; 
}

.todoadmin table.todostats tr:nth-child(even) { 
	background-color:#fff;
}

.todoadmin table.todostats caption {
	font-size:1.2em;
	line-height:1.0em;
	color: #666666;
	font-weight: bold;
	margin-bottom: 5px;
}

.todoadmin table.todostats td {
	padding: 5px;
}

.todoadmin table.todostats td.label {
	font-weight: bold;
	width: 50%;
	border-right: 1px solid #ddd;
}

.todoadmin table.todostats td.content {
	padding-left: 12px;
}

.elgg-todocalendar-feed {
	border-radius: 10px 10px 10px 10px;
	-moz-border-radius: 10px 10px 10px 10px;
	-webkit-border-radius: 10px 10px 10px 10px;
}
