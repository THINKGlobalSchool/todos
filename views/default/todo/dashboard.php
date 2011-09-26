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
}

echo elgg_view_menu('todo-dashboard-listing-main', array(
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default',
	'sort_by' => 'priority',
));

?>
<div id='todo-dashboard'></div>
<script>
	$(document).ready(function() {
		$link = $('.<?php echo $click; ?> a');
		$link.attr('href', $link.attr('href') + "&status=<?php echo $status; ?>");
		$link.click();
	});
</script>