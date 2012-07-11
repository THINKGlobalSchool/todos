<?php
/**
 * Todo submission grade form
 *
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 * @uses ElggEntity $vars['todo'] Todo this submission belongs to
 * @uses ElggEntity $vars['submission'] The submission 
 */

$todo = elgg_extract('todo', $vars);
$submission = elgg_extract('submission', $vars);

if (elgg_instanceof($submission, 'object', 'todosubmission') && elgg_instanceof($todo, 'object', 'todo')) {
	if ($submission->grade !== NULL) {
		$grade = $submission->grade;
	}

	$grade_label = elgg_echo('todo:label:grade');

	$grade_input = elgg_view('input/text', array(
		'name' => 'submission_grade',
		'id' => "submission-grade-{$submission->guid}",
		'class' => 'submission-grade-input',
		'value' => $grade,
	));

	$submission_hidden = elgg_view('input/hidden', array(
		'name' => 'submission_guid',
		'value' => $submission->guid,
	));

	$grade_total = $todo->grade_total;

	$content = <<<HTML
		<div>
			<table>
				<tr>
					<td class='todo-submission-grade-label'>$grade_label:&nbsp;</td>
					<td>$grade_input</td>
					<td class='todo-submission-grade-label'>&nbsp;&#47;&nbsp;$grade_total</td>
			</table>
		</div>
		$submission_hidden
HTML;

	echo $content;
}