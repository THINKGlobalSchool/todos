<div class="elgg_horizontal_tabbed_nav margin_top">
	<center>
	<ul>
	<?php 
		$user = elgg_get_page_owner_entity();
		$status = get_input('status', 'incomplete');
		
		if (!in_array($status, array('complete', 'incomplete'))) {
			$status = 'incomplete';
		}
		
		$direction = get_input('direction', 'ASC');
		
		if ($direction == 'ASC') {
			$text = "  &#9660;";
			$qs = "&direction=DESC";
 		} else if ($direction == 'DESC') {
			$text = "  &#9650;";
			$qs = "&direction=ASC";
		}
		
		echo "<li class='" . ($status == "incomplete" ? 'selected ' : '') . " edt_tab_nav'>" 
				. elgg_view('output/url', array('href' => $vars['url'] . $vars['return_url'] . "/{$user->username}?status=incomplete{$qs}", 
												'text' => elgg_echo("todo:label:incomplete") . ($status == "incomplete" ? $text : ''), 
												'class' => 'todo')) . 
			 "</li>"; 
			
		echo "<li class='" . ($status == "complete" ? 'selected ' : '') . " edt_tab_nav'>" 
				. elgg_view('output/url', array('href' => $vars['url'] . $vars['return_url'] . "/{$user->username}?status=complete{$qs}", 
												'text' => elgg_echo("todo:label:complete") . ($status == "complete" ? $text : ''), 
												'class' => 'todo')) . 
			 "</li>";
	?>
	</ul>
	</center>
</div>
