<?php 
/**
 * Todo settings form
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$delete = $vars['entity']->zipdelete;

if (!$delete) {
	$delete = 'daily';
}

?>
<div>
	<br />
	<?php echo elgg_echo('todo:label:calendarsalt'); ?>
	<input type='text' size='60' name='params[calsalt]' value="<?php echo $vars['entity']->calsalt; ?>" />
</div>
<div>
<?php
	echo elgg_echo('todo:label:zipdelete') . ': ';
	echo elgg_view('input/dropdown', array(
			'name' => 'params[zipdelete]',
			'options_values' => array(
				'minute' => 'minute',
				'fiveminute' => 'fiveminute',
				'fifteenmin' => 'fifteenmin',
				'halfhour' => 'halfhour',
				'hourly' => 'hourly',
				'daily' => 'daily',
				'weekly' => 'weekly',
				'monthly' => 'monthly',
				'yearly' => 'yearly',
		),
		'value' => $delete,
	));
?>
</div>