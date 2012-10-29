<?
// Update todo categories
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
admin_gatekeeper();

// No time limit.. could be a while
set_time_limit(0);

echo elgg_view_title("UPDATE TODO CATEGORIES");

echo "<pre>";

$options = array(
	'type' => 'object',
	'subtype' => 'todo',
	'limit' => 0,
);

$todos = new ElggBatch('elgg_get_entities', $options);

foreach ($todos as $todo) {
	if (!$todo->category) {
		if ($todo->return_required) {
			$set_category = "assessed_task";
		} else {
			$set_category = "basic_task";
		}
		$todo->category = $set_category;
		echo "{$todo->guid} - $set_category\r\n";
	}
}

echo "</pre>";