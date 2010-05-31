<?php
	/**
	 * Todo Entity View
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// Check for valid entity
	if (isset($vars['entity']) && $vars['entity'] instanceof ElggObject) {
				
		$url = $vars['entity']->getURL();
		$owner = $vars['entity']->getOwnerEntity();
		$canedit = true; // TODO: Who can edit?
		$title = $vars['entity']->title;
		$description = $vars['entity']->description;
		
		// Content
		$icon = elgg_view("graphics/icon", array('entity' => $vars['entity'],'size' => 'small'));
		$user_icon = elgg_view("profile/icon",array('entity' => $owner, 'size' => 'tiny'));
		$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
		$strapline = sprintf(elgg_echo("todo:strapline"), date("F j, Y",$vars['entity']->time_created));
		$strapline .= " " . elgg_echo('by') . " <a href='{$vars['url']}pg/todo/{$owner->username}'>{$owner->name}</a> ";
		$strapline .= sprintf(elgg_echo("comments")) . " (" . elgg_count_comments($vars['entity']) . ")";
		
		if ($canedit) {
			
				$controls .= elgg_view("output/confirmlink", 
										array(
											'href' => $vars['url'] . "action/todo/deletetodo?todo_guid=" . $vars['entity']->getGUID(),
											'text' => elgg_echo('delete'),
											'confirm' => elgg_echo('deleteconfirm'),
										)) . "&nbsp;&nbsp;&nbsp;";
										
				$controls .= "<a href={$vars['url']}pg/todo/edittodo/{$vars['entity']->getGUID()}>" . elgg_echo("edit") . "</a>";
		}

		
		

		// Figure out which viewing mode we're in
		if ($vars['full']) {
			$mode = 'full';
		} else {
			if (get_input('search_viewtype') == "gallery") {
				$mode = 'gallery';				
			} else {
				$mode = 'listing';
			}
		}
		
		// Default info for gallery/listing mode
		$info = <<<EOT
			<div class='todo'>
				<p>
					<b><a href='$url'>$title</a></b>
				</p>
				<p class='listingstrapline'>
					$strapline
				</p>
				<p class='{$mode}tags'>
					$tags
				</p>
				<p class='controls'>
					$controls
				</p>
			</div>
EOT;
		
		switch ($mode) {
			case 'full':
			$comments = elgg_view_comments($vars['entity']);
			$info = <<<EOT
					<div class='contentWrapper singleview'>
						<div class='todo'>
							<h3><a href='$url'>$title</a></h3>
							<div class="todo_icon">
								$user_icon
							</div>
							<p class='strapline'>
								$strapline
							</p>
							<p class='{$mode}tags'>
								$tags
							</p>
							<div class='clearfloat'></div>
							<div class='description'>
								$description
							</div>
							$controls
						</div>
					</div>
					$comments
EOT;
				echo $info;
				break;
			case 'listing':
				echo elgg_view_listing($icon, $info);
				break;
			case 'gallery':
				echo elgg_view_listing("", $info);
				break;
		}
		
	} else {
		// If were here something went wrong..
		$url = 'javascript:history.go(-1);';
		$owner = $vars['user'];
		$canedit = false;
	}
?>