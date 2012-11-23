<?php
// Todo ajaxy dashboard

$tab = get_input('tab');
$status = get_input('status', 'incomplete');

if (elgg_instanceof(elgg_get_page_owner_entity(), 'group')) {
	$tab = 'owned';
} 

if (!elgg_is_logged_in()) {
	$tab = 'all';
}

if ($status == 'incomplete' && get_input('filter_priority')) {
	$priority = get_input('filter_priority');
	$filter_priority = "&filter_priority={$priority}";
}

if ($sort_order = get_input('sort_order')) {
	$sort_order = "&sort_order={$sort_order}";
}

switch($tab) {
	case 'all':
		$click = 'elgg-menu-item-all';
		break;
	case 'assigned':
	default:
		$click = 'elgg-menu-item-assigned';
		break;
	case 'owned':
		$click = 'elgg-menu-item-owned';
		break;
	case 'iplan':
		$click = 'elgg-menu-item-category-calendars';
		break;
}

echo elgg_view_menu('todo-dashboard-listing-main', array(
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default',
	'sort_by' => 'priority',
));

?>
<div id='todo-dashboard'></div>
<script>
	// Function to click default tab
	todo_click_tab = function() {
		$link = $('.<?php echo $click; ?> > a.todo-ajax-list');
		$link.attr('href', $link.attr('href') + "&status=<?php echo $status; echo $filter_priority; echo $sort_order; ?>");
		$link.click();	
	}
	// Need to click AFTER elgg is initted
	elgg.register_hook_handler('ready', 'system', todo_click_tab);
</script>