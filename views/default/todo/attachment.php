<?php
/**
 * Todo submission attachment view
 *
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['entity']  ElggAnnotation object
 */

$entity = elgg_extract('entity', $vars);

if ($entity) {
	$download_url = elgg_get_site_url() . "file/download/{$entity->guid}";

	$params = array(
		'href' => $download_url,
		'text' => elgg_view_entity_icon($entity, 'small', array('href' => '')),
		'target' => '_blank',
	);


	$icon = elgg_view('output/url', $params);
	$label = elgg_echo('todo:label:attached');

	$content = <<<HTML
		<div class='todo-submission-attachment'>
			<table width="100%">
				<tr>
					<td class='todo-submission-attachment-icon'>$icon</td>
					<td class='todo-submission-attachment-title'>
						<a target="_blank" href='$download_url'>$entity->title</a>
					</td>
				</tr>
			</table>			
		</div>
HTML;

	echo $content;
}