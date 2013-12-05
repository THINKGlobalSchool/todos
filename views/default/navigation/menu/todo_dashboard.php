<?php
/**
 * Todo Dashboard Menu
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars Menu vars
 */

// Pass vars on to to filtrate
$menu = elgg_view('navigation/menu/filtrate', $vars);

$js = <<<JAVASCRIPT
	<script type='text/javascript'>
		elgg.filtrate.ajaxListUrl= elgg.get_site_url() + 'ajax/view/todo/list';
		elgg.filtrate.defaultParams	= $.param({
			'context': 'assigned',
			'priority': 0,
			'status': 'incomplete',
			'sort_order': 'DESC'
		});
	</script>
JAVASCRIPT;

echo $js . $menu;