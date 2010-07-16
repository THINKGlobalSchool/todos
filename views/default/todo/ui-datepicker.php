<?php
	/**
	 * Datepicker CSS
	 *
	 */
?>
	#ui-datepicker-div {
		background: white;
		border: 2px solid black;
		padding: 10px;
	}
	
	/* Overlays */
	.ui-widget-overlay { background: #aaaaaa url(images/ui-bg_flat_0_aaaaaa_40x100.png) 50% 50% repeat-x; opacity: .30;filter:Alpha(Opacity=30); }
	.ui-widget-shadow { margin: -8px 0 0 -8px; padding: 8px; background: #aaaaaa 50% 50% repeat-x; opacity: .30;filter:Alpha(Opacity=30); -moz-border-radius: 8px; -webkit-border-radius: 8px; }/* Datepicker
	----------------------------------*/
	.ui-datepicker { width: 17em; padding: .2em .2em 0; }
	.ui-datepicker .ui-datepicker-header { position:relative; padding:.2em 0; }
	.ui-datepicker .ui-datepicker-prev, .ui-datepicker .ui-datepicker-next { position:absolute; top: 2px; width: 1.8em; height: 1.8em; }
	.ui-datepicker .ui-datepicker-prev-hover, .ui-datepicker .ui-datepicker-next-hover { top: 1px; }
	.ui-datepicker .ui-datepicker-prev { left:2px; }
	.ui-datepicker .ui-datepicker-next { right:2px; }
	.ui-datepicker .ui-datepicker-prev-hover { left:1px; }
	.ui-datepicker .ui-datepicker-next-hover { right:1px; }
	.ui-datepicker .ui-datepicker-prev span, .ui-datepicker .ui-datepicker-next span { display: block; position: absolute; left: 50%; margin-left: -8px; top: 50%; margin-top: -8px;  }
	.ui-datepicker .ui-datepicker-title { margin: 0 2.3em; line-height: 1.8em; text-align: center; }
	.ui-datepicker .ui-datepicker-title select { float:left; font-size:1em; margin:1px 0; }
	.ui-datepicker select.ui-datepicker-month-year {width: 100%;}
	.ui-datepicker select.ui-datepicker-month, 
	.ui-datepicker select.ui-datepicker-year { width: 49%;}
	.ui-datepicker .ui-datepicker-title select.ui-datepicker-year { float: right; }
	.ui-datepicker table {width: 100%; font-size: .9em; border-collapse: collapse; margin:0 0 .4em; }
	.ui-datepicker th { padding: .7em .3em; text-align: center; font-weight: bold; border: 0;  }
	.ui-datepicker td { border: 0; padding: 1px; }
	.ui-datepicker td span, .ui-datepicker td a { display: block; padding: .2em; text-align: right; text-decoration: none; }
	.ui-datepicker .ui-datepicker-buttonpane { background-image: none; margin: .7em 0 0 0; padding:0 .2em; border-left: 0; border-right: 0; border-bottom: 0; }
	.ui-datepicker .ui-datepicker-buttonpane button { float: right; margin: .5em .2em .4em; cursor: pointer; padding: .2em .6em .3em .6em; width:auto; overflow:visible; }
	.ui-datepicker .ui-datepicker-buttonpane button.ui-datepicker-current { float:left; }

	/* with multiple calendars */
	.ui-datepicker.ui-datepicker-multi { width:auto; }
	.ui-datepicker-multi .ui-datepicker-group { float:left; }
	.ui-datepicker-multi .ui-datepicker-group table { width:95%; margin:0 auto .4em; }
	.ui-datepicker-multi-2 .ui-datepicker-group { width:50%; }
	.ui-datepicker-multi-3 .ui-datepicker-group { width:33.3%; }
	.ui-datepicker-multi-4 .ui-datepicker-group { width:25%; }
	.ui-datepicker-multi .ui-datepicker-group-last .ui-datepicker-header { border-left-width:0; }
	.ui-datepicker-multi .ui-datepicker-group-middle .ui-datepicker-header { border-left-width:0; }
	.ui-datepicker-multi .ui-datepicker-buttonpane { clear:left; }
	.ui-datepicker-row-break { clear:both; width:100%; }

	/* RTL support */
	.ui-datepicker-rtl { direction: rtl; }
	.ui-datepicker-rtl .ui-datepicker-prev { right: 2px; left: auto; }
	.ui-datepicker-rtl .ui-datepicker-next { left: 2px; right: auto; }
	.ui-datepicker-rtl .ui-datepicker-prev:hover { right: 1px; left: auto; }
	.ui-datepicker-rtl .ui-datepicker-next:hover { left: 1px; right: auto; }
	.ui-datepicker-rtl .ui-datepicker-buttonpane { clear:right; }
	.ui-datepicker-rtl .ui-datepicker-buttonpane button { float: left; }
	.ui-datepicker-rtl .ui-datepicker-buttonpane button.ui-datepicker-current { float:right; }
	.ui-datepicker-rtl .ui-datepicker-group { float:right; }
	.ui-datepicker-rtl .ui-datepicker-group-last .ui-datepicker-header { border-right-width:0; border-left-width:1px; }
	.ui-datepicker-rtl .ui-datepicker-group-middle .ui-datepicker-header { border-right-width:0; border-left-width:1px; }

	/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
	.ui-datepicker-cover {
	    display: none; /*sorry for IE5*/
	    display/**/: block; /*sorry for IE5*/
	    position: absolute; /*must have*/
	    z-index: -1; /*must have*/
	    filter: mask(); /*must have*/
	    top: -4px; /*must have*/
	    left: -4px; /*must have*/
	    width: 200px; /*must have*/
	    height: 200px; /*must have*/
	}``