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
$cleanorphanedannotationfiles_go = get_input('cleanorphanedannotationfiles_go');
$cleanorphanedtodofiles_go = get_input('cleanorphanedtodofiles_go');

$todo_subtype = get_subtype_id('object', 'todo');
$submission_subtype = get_subtype_id('object', 'todosubmission');
$todosubmissionfile_subtype = get_subtype_id('object', 'todosubmissionfile');
$submissionannotationfile_subtype = get_subtype_id('object', 'submissionannotationfile');

$dbprefix = elgg_get_config('dbprefix');

echo elgg_view_title('Todo Cleanup Scripts');

// Clean up ACL's
if ($acl_go) {
	$count_query = "SELECT e.*, value.string as acl_id, name.string as metadata_name, COUNT(*) as count FROM {$dbprefix}entities e
		  JOIN {$dbprefix}metadata n_table on e.guid = n_table.entity_guid
		  JOIN {$dbprefix}metastrings name on n_table.name_id = name.id
		  JOIN {$dbprefix}metastrings value on n_table.value_id = value.id
		  WHERE subtype IN ({$todo_subtype}, {$submission_subtype})
		  AND   (name.string = 'assignee_acl' OR name.string = 'submission_acl')
	";

	$result = get_data($count_query);
	$count = $result[0]->count ? $result[0]->count : 0;
	echo "Total acls: " . $count . "<br /><br />";

	// Core access id's
	$core_acls = array(
		ACCESS_DEFAULT, 
		ACCESS_PRIVATE, 
		ACCESS_LOGGED_IN, 
		ACCESS_PUBLIC, 
		ACCESS_FRIENDS
	);

	// Offset/limit
	$offset = 0;
	$limit = 10;

	// Batch update ACL's
	while ($offset < ($count - $limit)) {
		// Get acls
		$acl_query = "SELECT e.*, value.string as acl_id, name.string as metadata_name FROM {$dbprefix}entities e
			  JOIN {$dbprefix}metadata n_table on e.guid = n_table.entity_guid
			  JOIN {$dbprefix}metastrings name on n_table.name_id = name.id
			  JOIN {$dbprefix}metastrings value on n_table.value_id = value.id
			  WHERE subtype IN ({$todo_subtype}, {$submission_subtype})
			  AND   (name.string = 'assignee_acl' OR name.string = 'submission_acl')
			  LIMIT {$offset},{$limit}
		";

		$acls = get_data($acl_query);

		// Loop over acls/objects
		foreach ($acls as $acl) {
			$id = $acl->acl_id;
			$guid = $acl->guid;
			$subtype = $acl->subtype;
			$access_id = $acl->access_id;
			if ($acl_go) {
				if ($subtype == $todo_subtype) {
					$type = "TODO";
				} else if ($subtype == $submission_subtype) {
					$type = "SUBMISSION";
				} else {
					echo "INVALID SUBTYPE!!! <br />";
					continue;
				}

				// Commit..
				if (!$safety) {
					// Remove old acl!
					$deleted = '   -> DELETED!!!';
					delete_access_collection($id);

					// Update access id's (where applicable) and delete metadata
					if ($subtype == $todo_subtype) {
						// If todo access id isn't a core id, set it to assignees only
						if (!in_array($access_id, $core_acls)) {
							$access = TODO_ACCESS_LEVEL_ASSIGNEES_ONLY;
							update_data("UPDATE {$dbprefix}entities SET access_id = $access WHERE guid = $guid");
						}

						access_show_hidden_entities(TRUE);
						$res = elgg_delete_metadata(array(
							'guid' => $guid,
							'metadata_name' => 'assignee_acl',
						));
						access_show_hidden_entities(FALSE);

					} else if ($subtype == $submission_subtype) {
						$access = SUBMISSION_ACCESS_ID;
						update_data("UPDATE {$dbprefix}entities SET access_id = $access WHERE guid = $guid");

						elgg_delete_metadata(array(
							'guid' => $guid,
							'metadata_name' => 'submission_acl',
						));
					}
				}
				echo "$type (guid: $guid, access_id: $access_id) - ACL: " . $id . " $deleted" . "<br />";
			}
		}
		$offset += $limit;
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
				$entity = get_entity($entity_guid);
				if ($entity) {
					// Add new relationship and update access id on file entity
					$rel = SUBMISSION_ANNOTATION_FILE_RELATIONSHIP;
					$done = " ---> Added relationship ({$rel}); updated access id (-11)";
					add_entity_relationship($entity_guid, SUBMISSION_ANNOTATION_FILE_RELATIONSHIP, $submission_guid);
					
					$entity->access_id = SUBMISSION_ACCESS_ID;
					$entity->save();
				} else {
					$done = " ---> Entity no longer exists! Skipped.";
				}
			}

			// Display info
			echo " - Found file entity: $entity_guid - (Submission: $annotation->submission_guid) $done";
			$count++;
		}
		echo "<br />";
	}

	echo "<br />Total submission annotation files: $count";

} else if ($cleanorphanedtodofiles_go) { // Remove orphaned todosubmissionfiles
	$r = TODO_CONTENT_RELATIONSHIP;

	// Get todosubmission files
	$q = "SELECT e.* FROM {$dbprefix}entities e
		  WHERE subtype IN ({$todosubmissionfile_subtype})
		  AND NOT EXISTS (
		  	SELECT 1 from {$dbprefix}entity_relationships 
		  	WHERE guid_one = e.guid 
		  	AND relationship = '{$r}'
		  )
	";

	$files = get_data($q);

	echo "Total orphaned todo submission files: " . count($files) . "<br /><br />";

	foreach ($files as $file) {
		if (!$safety) {
			$entity = get_entity($file->guid);
			$deleted = "----> DELETED!";
			todo_delete_file($entity);
		}
		echo "GUID: $file->guid $deleted<br />";
	}

} else if ($cleanorphanedannotationfiles_go) { // Remove orphaned submissionannotationfile
	 $r = SUBMISSION_ANNOTATION_FILE_RELATIONSHIP;

	// Get todosubmission files
	$q = "SELECT e.* FROM {$dbprefix}entities e
		  WHERE subtype IN ({$submissionannotationfile_subtype})
		  AND NOT EXISTS (
		  	SELECT 1 from {$dbprefix}entity_relationships 
		  	WHERE guid_one = e.guid 
		  	AND relationship = '{$r}'
		  )
	";

	$files = get_data($q);

	echo "Total orphaned submission annotation files: " . count($files) . "<br /><br />";

	foreach ($files as $file) {
		if (!$safety) {
			$entity = get_entity($file->guid);
			$deleted = "----> DELETED!";
			todo_delete_file($entity);
		}
		echo "GUID: $file->guid $deleted<br />";
	}


} else { // Display form
	echo "<form method='GET' action=''>";
	echo "<input type='checkbox' name='safety' value='1' checked='CHECKED' /> Safety? (Uncheck to commit to deletes/changes!)<br /><br />";
	echo "<input type='submit' name='acl_go' value='Delete assignee/submission ACLs' /><br />";
	echo "<input type='submit' name='fixannotationfiles_go' value='Fix Annotation & Files (ACL + Relationship)' /><br />";
	echo "<input type='submit' name='cleanorphanedtodofiles_go' value='Remove orphaned todosubmissionfiles' /><br />";
	echo "<input type='submit' name='cleanorphanedannotationfiles_go' value='Remove orphaned submissionannotationfiles' /><br />";
	echo "</form>";
}

