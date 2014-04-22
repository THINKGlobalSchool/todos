<?
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$change_status = get_input('change_status', 99);

$area = elgg_view_title("Update Todo's")."<br/>";

//list all todo guids, titles and statuses
$todos = elgg_get_entities(array('type'=>'object','subtype'=>'todo','limit'=>10000));


$area .= "<table class='update_todo_table'>";
$area .= "<tr><th>Title</th><th>Publish Status</th></tr>";
foreach($todos as $todo) {
	if ($change_status == TODO_STATUS_DRAFT) {
		$todo->status = TODO_STATUS_DRAFT;
	} else if ($change_status == TODO_STATUS_PUBLISHED) {
		$todo->status = TODO_STATUS_PUBLISHED;
	}
	$area .= "<tr><td>{$todo->title}</td><td>&nbsp;{$todo->status}</td></tr>";
}
$area .= "</table><br/>";

if ($change_status) {
$area .= "<p><strong>Status updated.</strong></p><br/>";
}

$url = elgg_get_site_url() . "mod/todos/update_todo_status.php";
$area .= "<p>Update all todos to status: <a href='{$url}?change_status=0'>Draft</a> <a href='{$url}?change_status=1'>Published</a></p>";

$body = elgg_view_layout("one_column", $area);
echo elgg_view_page("Update Todo Status",$body);
