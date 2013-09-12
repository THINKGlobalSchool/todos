<?
// Update todo categories
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

// No time limit.. could be a while
set_time_limit(0);

$go = get_input('go', FALSE);

$options = array(
	'type' => 'object',
	'subtype' => 'todosubmission',
	'created_time_lower' => '1346457600', // September 1st, 2012 00:00:00 UTC
	'limit' => 0,
);

$subs = new ElggBatch('elgg_get_entities', $options);

echo "<pre>";
echo "OFFSET TODOS SINCE SEPTEMBER 1ST 2012 TO BSAS TIME<br />";

foreach ($subs as $s) {
	// Check for utc metadata
	if (!$s->utc_created) {
		// Original friendly date
		$orig_date = date('D, d M Y H:i:s', $s->time_created);
		$orig_ts = $s->time_created;

		if ($go) {
			$new_ts = strtotime('-3 hours', $s->time_created);
			$new_date = date('D, d M Y H:i:s', $new_ts);
			$updated = "----> NEW TS: {$new_ts} ({$new_date})";
		}

		echo "{$s->guid} - TS: {$s->time_created} ({$orig_date}) $updated<br />";

		if ($go) {
			// Update timestamp and save
			$s->utc_created = $orig_ts;
			$s->time_created = $new_ts;
			$s->save();
		}
	}
}