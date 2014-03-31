<?php
/**
 * Todo Calendar Connect Help
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010  - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */


$user = elgg_get_logged_in_user_entity();
$hash = generate_todo_user_hash($user);
$calendar_url = elgg_get_site_url() . "todo/calendar/" . $user->username . "?t=" . $hash . '&ts=' . time();

$title = elgg_view_title(elgg_echo('todo:label:connecttitle'));

$calendar_input = elgg_view('input/text', array(
	'size' => 100,
	'style' => 'font-size: 12px',
	'readonly' => 'READONLY',
	'id' => 'todo-calendar-connect-input',
	'value' => $calendar_url
));

$step_one = elgg_echo('todo:label:connectone');

$step_two = elgg_echo('todo:label:connecttwo');

$step_three = elgg_echo('todo:label:connectthree');
$step_three_img = elgg_view('output/img', array(
	'src' => elgg_normalize_url('mod/todo/graphics/connect_three.png'),
	'alt' => $step_three
));

$step_four = elgg_echo('todo:label:connectfour');
$step_four_img = elgg_view('output/img', array(
	'src' => elgg_normalize_url('mod/todo/graphics/connect_four.png'),
	'alt' => $step_four
));

$content = <<<HTML
	<div class='todo-connect-help'>
		$title
		<br />
		<ol>
			<li>
				$step_one
				<br /><br />$calendar_input
			</li>
			<li>
				$step_two
			</li>
			<li>
				$step_three<br />
				$step_three_img
			</li>
			<li>
				$step_four<br />
				$step_four_img
			</li>
		</ol>

	</div>
HTML;

echo $content;