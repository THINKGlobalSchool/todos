<?
// Remove all todo related access collections, and perform related cleanup
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

// No time limit.. could be a while
set_time_limit(0);

// Safety switch!
$safety = get_input('safety');

// Action inputs
$acl_go = get_input('acl_go', FALSE);
$fixannotationfiles_go = get_input('fixannotationfiles_go');

$todo_subtype = get_subtype_id('object', 'todo');
$submission_subtype = get_subtype_id('object', 'todosubmission');

$dbprefix = elgg_get_config('dbprefix');

echo elgg_view_title('Remove To Do ACLs');

// Clean up ACL's
if ($acl_go) {
	// Get acls
	$q = "SELECT e.*, value.string as acl_id FROM {$dbprefix}entities e
		  JOIN {$dbprefix}metadata n_table on e.guid = n_table.entity_guid
		  JOIN {$dbprefix}metastrings name on n_table.name_id = name.id
		  JOIN {$dbprefix}metastrings value on n_table.value_id = value.id
		  WHERE subtype IN ({$todo_subtype}, {$submission_subtype})
		  AND   (name.string = 'assignee_acl' OR name.string = 'submission_acl')
	";

	$acls = get_data($q);

	echo "Total acls: " . count($acls) . "<br /><br />";

	foreach ($acls as $acl) {
		$id = $acl->acl_id;
		if ($acl_go) {
			// Commit..
			if (!$safety) {
				// Remove old acl!
				$deleted = '   -> DELETED!!!';
				delete_access_collection($id);
			}

			echo $id . " $deleted" . "<br />";
		}
	}
} else if ($fixannotationfiles_go) { // Fix annotations and annotation files
	// Select submission info/submission annotation info
	$q = "SELECT e.*, 
				 value.string as content, 
				 n_table.access_id as annotation_access_id, 
				 n_table.id as annotation_id, 
				 n_table.entity_guid as submission_guid 
		  FROM {$dbprefix}entities e
		  JOIN {$dbprefix}annotations n_table on e.guid = n_table.entity_guid
		  JOIN {$dbprefix}metastrings name on n_table.name_id = name.id
		  JOIN {$dbprefix}metastrings value on n_table.value_id = value.id
		  WHERE subtype IN ({$submission_subtype})
		  AND   (name.string = 'submission_annotation')
	";

	$annotations = get_data($q);

	$count = 0;
	foreach ($annotations as $annotation) {
		// Get annotation content
		$annotation_content = unserialize($annotation->content);

		// Get annotation id
		$annotation_id = $annotation->annotation_id;

		// Get old access id
		$old_access_id = $annotation->annotation_access_id;

		// Display info
		echo "Annotation: $annotation_id - original id: $old_access_id";

		// New access id to be..
		$new_access_id = SUBMISSION_ACCESS_ID;

		// Commit..
		if (!$safety) {
			// Set new access id on annotations in general
			update_data("UPDATE {$dbprefix}annotations SET access_id=$new_access_id WHERE id=$annotation_id");
			echo " new id: $new_access_id";
		}

		// If we've got an attachment guid, we'll need to update that too
		if (isset($annotation_content['attachment_guid'])) {
			// Get entity guid
			$entity_guid = $annotation_content['attachment_guid'];

			// Get submission entity guid
			$submission_guid = $annotation->submission_guid;

			// Commit..
			if (!$safety) {
				// Add new relationship and update access id on file entity
				$rel = SUBMISSION_ANNOTATION_FILE_RELATIONSHIP;
				$done = " ---> Added relationship ({$rel}); updated access id (-11)";
				add_entity_relationship($entity_guid, SUBMISSION_ANNOTATION_FILE_RELATIONSHIP, $submission_guid);
				$entity = get_entity($entity_guid);
				$entity->access_id = SUBMISSION_ACCESS_ID;
				$entity->save();
			}

			// Display info
			echo " - Found file entity: $entity_guid - (Submission: $annotation->submission_guid) $done";
			$count++;
		}
		echo "<br />";
	}

	echo "<br />Total submission annotation files: $count";

} else { // Display form
	echo "<form method='GET' action=''>";
	echo "<input type='checkbox' name='safety' value='1' checked='CHECKED' /> Safety? (Uncheck to commit to deletes/changes!)<br /><br />";
	echo "<input type='submit' name='acl_go' value='Delete all ACLs' /><br />";
	echo "<input type='submit' name='fixannotationfiles_go' value='Fix Annotation & Files' /><br />";
	echo "</form>";
}

