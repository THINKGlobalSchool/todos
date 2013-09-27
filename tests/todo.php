<?php
/**
 * Todo API Tests
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
class TodoAPITest extends ElggCoreUnitTest {
	/**
	 * Called before each test object.
	 */
	public function __construct() {
		parent::__construct();

		$this->ignoreAccess = elgg_get_ignore_access();
		elgg_set_ignore_access(FALSE);

		$this->todos = array();
		$this->submissions = array();
		$this->users = array();

		// Create some users
		for ($i=0; $i<3; $i++) {
			$user = new ElggUser();
			$user->username = 'todo_fake_user_' . rand();
			$user->email = 'todo_fake_email@fake.com' . rand();
			$user->name = 'todo fake user ' . rand();
			$user->access_id = ACCESS_PUBLIC;
			$user->salt = generate_random_cleartext_password();
			$user->password = generate_user_password($user, rand());
			$user->owner_guid = 0;
			$user->container_guid = 0;
			$user->save();
			//$user->delete();
			$this->users[] = $user;
		}
	}

	/**
	 * Called before each test method.
	 */
	public function setUp() {
	}

	/**
	 * Called after each test method.
	 */
	public function tearDown() {
		$this->swallowErrors();
	}

	/**
	 * Called after each test object.
	 */
	public function __destruct() {
		// Delete submissions
		foreach ($this->submissions as $submission) {
			$submission->delete();
		}

		// Delete todos
		foreach ($this->todos as $todo) {
			$todo->delete();
		}

		// Delete users
		foreach ($this->users as $user) {
			$user->delete();
		}

		elgg_set_ignore_access($this->ignoreAccess);

		// all __destruct() code should go above here
		parent::__destruct();
	}

	/**
	 * Create a todo entity, and store in in this test's todo array
	 */
	public function createTodo($todo_params = array(), $assignees = array()) {
		$todo_defaults = array(
			'container_guid' => elgg_get_logged_in_user_guid(),
			'owner_guid' => elgg_get_logged_in_user_guid(),
			'time_published' => time(),
			'access_id' => TODO_ACCESS_LEVEL_LOGGED_IN,
			'title' => 'Test todo title',
			'description' => 'Test todo description',
			'tags' => array('test1', 'test2'),
			'suggested_tags' => array('sugtest1', 'sugtest2'),
			'due_date' => strtotime("+1 week"),
			'start_date' => time(),
			'return_required' => TRUE,
			'grade_required' => TRUE,
			'grade_total' => 100,
			'status' => TODO_STATUS_PUBLISHED,
			'rubric_guid' => NULL,
			'category' => TODO_BASIC_TASK
		);

		$todo_params = array_merge($todo_defaults, $todo_params);


		$todo = new ElggObject();
		$todo->subtype = "todo";

		// Set todo values
		foreach ($todo_params as $k => $v) {
			$todo->$k = $v;
		}

		$todo->save();
		assign_users_to_todo($assignees, $todo->guid);

		// Push to test todo array
		$this->todos[] = $todo;
		return $todo->guid;
	}

	/**
	 * Create a submission for given todo guid
	 */
	public function createTodoSubmission($todo_guid, $submission_params = array()) {
		$submission_defaults = array(
			'container_guid' => elgg_get_logged_in_user_guid(),
			'owner_guid' => elgg_get_logged_in_user_guid(),
			'todo_guid' => $todo_guid,
			'title' => 'Test submission title',
			'description' => 'Test submission description',
			'content' => 0,
		);

		$submission_params = array_merge($submission_defaults, $submission_params);

		$submission = new ElggObject();
		$submission->subtype = 'todosubmission';

		// Set submission values
		foreach ($submission_params as $k => $v) {
			$submission->$k = $v;
		}

		$submission->save();

		// Submission relationships
		add_entity_relationship($submission->guid, SUBMISSION_RELATIONSHIP, $todo_guid);
		add_entity_relationship($submission->owner_guid, COMPLETED_RELATIONSHIP, $todo_guid);

		// Set accepted
		user_accept_todo($submission->owner_guid, $todo_guid);

		$this->submissions[] = $submission;
		return $submission->guid;
	}

	/**
	 * Helper function to test if user has access to an annotation
	 */
	public function hasAccessToAnnotation($annotation, $user) {
		global $CONFIG;

		$access_bit = _elgg_get_access_where_sql(array(
			'user_guid' => $user->getGUID(),
			'table_alias' => 'a',
			'guid_column' => 'entity_guid'
		));

		$query = "SELECT id from {$CONFIG->dbprefix}annotations a WHERE a.id = " . $annotation->id;
		// Add access controls
		$query .= " AND " . $access_bit;
		if (get_data($query)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Test assign_users_to_todo
	 */
	public function testAssignUsersTodo() {
		$user_one = $this->users[0];

		// Create a todo
		$guid = $this->createTodo();

		assign_users_to_todo(array($user_one->guid), $guid);

		// Make sure user is assigned
		$this->assertTrue(is_todo_assignee($guid, $user_one->guid));
	}

	/**
	 * Test basic todo access
	 */
	public function testTodoAccess() {
		$user_one = $this->users[0];

		// Create a todo with the logged in access level
		$guid = $this->createTodo();
		$todo = get_entity($guid);

		// User has has access
		$this->assertTrue(has_access_to_entity($todo, $user_one));

		// Create assignees only todo
		$guid = $this->createTodo(array(
			'access_id' => TODO_ACCESS_LEVEL_ASSIGNEES_ONLY
		));
		$todo = get_entity($guid);

		// Can't access, assignees only
		$this->assertFalse(has_access_to_entity($todo, $user_one));

		// Assign user todo
		assign_user_to_todo($user_one->guid, $todo->guid);

		$this->assertTrue(has_access_to_entity($todo, $user_one));

		// Create a new todo, owned by test user, with assignee only
		$guid = $this->createTodo(array(
			'container_guid' => $user_one->guid,
			'owner_guid' => $user_one->guid,
			'access_id' => TODO_ACCESS_LEVEL_ASSIGNEES_ONLY
		));
		$todo = get_entity($guid);
	
		$this->assertTrue(has_access_to_entity($todo, $user_one));

		// Unassign user @todo (need a function for this..)
	}

	/**
	 * Test todo submission access
	 */
	public function testSubmissionAccess() {
		$user_one = $this->users[0];
		$user_two = $this->users[1];
		$user_three = $this->users[2];

		// Create a todo admin owned, assign test user
		$todo_guid = $this->createTodo(array(), array($user_one->guid));

		// Create a submission for test user for above todo
		$submission_guid = $this->createTodoSubmission($todo_guid, array(
			'owner_guid' => $user_one->guid,
			'container_guid' => $user_one->guid,
		));
		$submission = get_entity($submission_guid);

		// User can access their own submission
		$this->assertTrue(has_access_to_entity($submission, $user_one));

		// Other user can't access
		$this->assertFalse(has_access_to_entity($submission, $user_two));

		// Create a todo owned by test user, assign another test user
		$todo_guid = $this->createTodo(array(
			'container_guid' => $user_one->guid,
			'owner_guid' => $user_one->guid,
		), array($user_two->guid));

		// Create a submission for test user for above todo
		$submission_guid = $this->createTodoSubmission($todo_guid, array(
			'owner_guid' => $user_two->guid,
			'container_guid' => $user_two->guid,
		));
		$submission = get_entity($submission_guid);

		// Todo owner can access submission
		$this->assertTrue(has_access_to_entity($submission, $user_one));

		// User can access their own submission
		$this->assertTrue(has_access_to_entity($submission, $user_two));

		// Other user cannot access
		$this->assertFalse(has_access_to_entity($submission, $user_three));
	}

	/**
	 * Test todo submission file access 
	 */
	public function testSubmissionFileAccess() {
		$user_one = $this->users[0];
		$user_two = $this->users[1];
		$user_three = $this->users[2];

		// Create a todo owned by test user, assign another test user
		$todo_guid = $this->createTodo(array(
			'container_guid' => $user_one->guid,
			'owner_guid' => $user_one->guid,
		), array($user_two->guid));

		// Create a todo submission file
		$file = new FilePluginFile();
		$file->subtype = 'todosubmissionfile';
		$file->title = 'test todo submission file';
		$file->access_id = ACCESS_PRIVATE;
		$file->owner_guid = $user_two->guid;
		$file->container_guid = $user_two->guid;
		$file->save();

		// Create a submission for test user for above todo
		$submission_guid = $this->createTodoSubmission($todo_guid, array(
			'owner_guid' => $user_two->guid,
			'container_guid' => $user_two->guid,
			'content' => serialize(array($file->guid)),
		));

		// Make sure submitter has access to the file
		$this->assertTrue(has_access_to_entity($file, $user_two));

		// Make sure owner has access to the file
		$this->assertTrue(has_access_to_entity($file, $user_one));

		// Make sure other user doesn't have access to file
		$this->assertFalse(has_access_to_entity($file, $user_three));

		// Delete the file
		$file->delete();
	}

	/**
	 * Test todo submission annotation access
	 */
	public function testSubmissionAnnotationAccess() {
		$user_one = $this->users[0];
		$user_two = $this->users[1];
		$user_three = $this->users[2];

		// Create a todo owned by test user, assign another test user
		$todo_guid = $this->createTodo(array(
			'container_guid' => $user_one->guid,
			'owner_guid' => $user_one->guid,
		), array($user_two->guid));

		// Create a submission for test user for above todo
		$submission_guid = $this->createTodoSubmission($todo_guid, array(
			'owner_guid' => $user_two->guid,
			'container_guid' => $user_two->guid,
		));

		$submission = get_entity($submission_guid);

		// Create a todo submission annotation file
		$file = new FilePluginFile();
		$file->subtype = 'submissionannotationfile';
		$file->title = 'test todo submission annotation file';
		$file->access_id = $submission->access_id;
		$file->owner_guid = $user_one->guid;
		$file->container_guid = $user_one->guid;
		$file->save();

		$annotation_content = array(
			'comment' => 'test',
			'attachment_guid' => $file->guid
		);

		add_entity_relationship($file->guid, SUBMISSION_ANNOTATION_FILE_RELATIONSHIP, $submission_guid);

		$annotation_id = create_annotation(
			$submission_guid,
			'submission_annotation',
			serialize($annotation_content),
			"",
			$user_one->guid,
			$submission->access_id
		);

		$annotation = elgg_get_annotation_from_id($annotation_id);

		// Make sure annotation owner has access to the annotation
		$this->assertTrue($this->hasAccessToAnnotation($annotation, $user_one));

		// Make sure submitter has access to the annotation
		$this->assertTrue($this->hasAccessToAnnotation($annotation, $user_two));

		// Make sure other user doesn't have access to annotation
		$this->assertFalse($this->hasAccessToAnnotation($annotation, $user_three));

		// Make sure submitter has access to the file
		$this->assertTrue(has_access_to_entity($file, $user_two));

		// Make sure owner has access to the file
		$this->assertTrue(has_access_to_entity($file, $user_one));

		// Make sure other user doesn't have access to file
		$this->assertFalse(has_access_to_entity($file, $user_three));

		// Create another annotation, with a file owned by submitter
		$file2 = new FilePluginFile();
		$file2->subtype = 'submissionannotationfile';
		$file2->title = 'test todo submission annotation file';
		$file2->access_id = $submission->access_id;
		$file2->owner_guid = $user_two->guid;
		$file2->container_guid = $user_two->guid;
		$file2->save();

		$annotation_content = array(
			'comment' => 'test',
			'attachment_guid' => $file2->guid
		);

		add_entity_relationship($file2->guid, SUBMISSION_ANNOTATION_FILE_RELATIONSHIP, $submission_guid);

		$annotation_id = create_annotation(
			$submission_guid,
			'submission_annotation',
			serialize($annotation_content),
			"",
			$user_two->guid,
			$submission->access_id
		);

		$annotation2 = elgg_get_annotation_from_id($annotation_id);

		// Make sure annotation owner has access to the annotation
		$this->assertTrue($this->hasAccessToAnnotation($annotation2, $user_two));

		// Make sure submitter has access to the annotation
		$this->assertTrue($this->hasAccessToAnnotation($annotation2, $user_one));

		// Make sure other user doesn't have access to annotation
		$this->assertFalse($this->hasAccessToAnnotation($annotation2, $user_three));

		// Make sure submitter has access to the file
		$this->assertTrue(has_access_to_entity($file2, $user_two));

		// Make sure owner has access to the file
		$this->assertTrue(has_access_to_entity($file2, $user_one));

		// Make sure other user doesn't have access to file
		$this->assertFalse(has_access_to_entity($file2, $user_three));

		// Cleanup
		$file->delete();
		$file2->delete();
	}

	/**
	 * Test todo admin access
	 */
	public function testTodoAdminAccess() {
		$user_one = $this->users[0];
		$user_two = $this->users[1];
		$user_three = $this->users[2];

		$todo_admin_role = elgg_get_config('todo_admin_role');

		roles_add_user($todo_admin_role, $user_three->guid);

		// Create a todo owned by test user, assign another test user
		$todo_guid = $this->createTodo(array(
			'container_guid' => $user_one->guid,
			'owner_guid' => $user_one->guid,
			'access_id' => TODO_ACCESS_LEVEL_ASSIGNEES_ONLY
		), array($user_two->guid));

		$todo = get_entity($todo_guid);

		// Make sure todo admin can access todo
		$this->assertTrue(has_access_to_entity($todo, $user_three));

		// Create a todo submission file
		$file = new FilePluginFile();
		$file->subtype = 'todosubmissionfile';
		$file->title = 'test todo submission file';
		$file->access_id = ACCESS_PRIVATE;
		$file->owner_guid = $user_two->guid;
		$file->container_guid = $user_two->guid;
		$file->save();

		// Create a submission for test user for above todo
		$submission_guid = $this->createTodoSubmission($todo_guid, array(
			'owner_guid' => $user_two->guid,
			'container_guid' => $user_two->guid,
			'content' => serialize(array($file->guid)),
		));

		$submission = get_entity($submission_guid);

		// Make sure todo admin can access submission
		$this->assertTrue(has_access_to_entity($submission, $user_three));

		// Make sure todo admin can access submission file
		$this->assertTrue(has_access_to_entity($file, $user_three));

		// Create a todo submission annotation file, owned by todo creator
		$a_file = new FilePluginFile();
		$a_file->subtype = 'submissionannotationfile';
		$a_file->title = 'test todo submission annotation file';
		$a_file->access_id = $submission->access_id;
		$a_file->owner_guid = $user_one->guid;
		$a_file->container_guid = $user_one->guid;
		$a_file->save();

		$annotation_content = array(
			'comment' => 'test',
			'attachment_guid' => $a_file->guid
		);

		add_entity_relationship($a_file->guid, SUBMISSION_ANNOTATION_FILE_RELATIONSHIP, $submission_guid);

		$annotation_id = create_annotation(
			$submission_guid,
			'submission_annotation',
			serialize($annotation_content),
			"",
			$user_one->guid,
			$submission->access_id
		);

		$annotation = elgg_get_annotation_from_id($annotation_id);

		// Make sure todo admin has access to the annotation
		$this->assertTrue($this->hasAccessToAnnotation($annotation, $user_three));

		// Create another todo submission annotation file, owner by submitter
		$a_file2 = new FilePluginFile();
		$a_file2->subtype = 'submissionannotationfile';
		$a_file2->title = 'test todo submission annotation file';
		$a_file2->access_id = $submission->access_id;
		$a_file2->owner_guid = $user_two->guid;
		$a_file2->container_guid = $user_two->guid;
		$a_file2->save();

		$annotation_content = array(
			'comment' => 'test',
			'attachment_guid' => $a_file2->guid
		);

		add_entity_relationship($a_file2->guid, SUBMISSION_ANNOTATION_FILE_RELATIONSHIP, $submission_guid);

		$annotation_id = create_annotation(
			$submission_guid,
			'submission_annotation',
			serialize($annotation_content),
			"",
			$user_two->guid,
			$submission->access_id
		);

		$annotation2 = elgg_get_annotation_from_id($annotation_id);

		// Make sure todo admin has access to the annotation
		$this->assertTrue($this->hasAccessToAnnotation($annotation2, $user_three));

		// Cleanup
		roles_remove_user($todo_admin_role, $user_three->guid);
		$file->delete();
		$a_file->delete();
		$a_file2->delete();
	}

	/**
	 * Test todo parent access
	 */
	public function testTodoParentAccess() {
		$user_one = $this->users[0];
		$user_two = $this->users[1];
		$user_three = $this->users[2];

		// Make user_three a parent
		$user_three->is_parent = 1;

		// Add child relationship
		add_entity_relationship($user_two->guid, PARENT_CHILD_RELATIONSHIP, $user_three->guid);

		// Create a todo owned by test user, assign another test user
		$todo_guid = $this->createTodo(array(
			'container_guid' => $user_one->guid,
			'owner_guid' => $user_one->guid,
			'access_id' => TODO_ACCESS_LEVEL_ASSIGNEES_ONLY
		), array($user_two->guid));

		$todo = get_entity($todo_guid);

		// Create a todo submission file
		$file = new FilePluginFile();
		$file->subtype = 'todosubmissionfile';
		$file->title = 'test todo submission file';
		$file->access_id = ACCESS_PRIVATE;
		$file->owner_guid = $user_two->guid;
		$file->container_guid = $user_two->guid;
		$file->save();

		// Create a submission for test user for above todo
		$submission_guid = $this->createTodoSubmission($todo_guid, array(
			'owner_guid' => $user_two->guid,
			'container_guid' => $user_two->guid,
			'content' => serialize(array($file->guid)),
		));

		$submission = get_entity($submission_guid);

		// Create a todo submission annotation file, owned by todo creator
		$a_file = new FilePluginFile();
		$a_file->subtype = 'submissionannotationfile';
		$a_file->title = 'test todo submission annotation file';
		$a_file->access_id = $submission->access_id;
		$a_file->owner_guid = $user_one->guid;
		$a_file->container_guid = $user_one->guid;
		$a_file->save();

		$annotation_content = array(
			'comment' => 'test',
			'attachment_guid' => $a_file->guid
		);

		add_entity_relationship($a_file->guid, SUBMISSION_ANNOTATION_FILE_RELATIONSHIP, $submission_guid);

		$annotation_id = create_annotation(
			$submission_guid,
			'submission_annotation',
			serialize($annotation_content),
			"",
			$user_one->guid,
			$submission->access_id
		);

		$annotation = elgg_get_annotation_from_id($annotation_id);

		// Create another todo submission annotation file, owner by submitter
		$a_file2 = new FilePluginFile();
		$a_file2->subtype = 'submissionannotationfile';
		$a_file2->title = 'test todo submission annotation file';
		$a_file2->access_id = $submission->access_id;
		$a_file2->owner_guid = $user_two->guid;
		$a_file2->container_guid = $user_two->guid;
		$a_file2->save();

		$annotation_content = array(
			'comment' => 'test',
			'attachment_guid' => $a_file2->guid
		);

		add_entity_relationship($a_file2->guid, SUBMISSION_ANNOTATION_FILE_RELATIONSHIP, $submission_guid);

		$annotation_id = create_annotation(
			$submission_guid,
			'submission_annotation',
			serialize($annotation_content),
			"",
			$user_two->guid,
			$submission->access_id
		);

		$annotation2 = elgg_get_annotation_from_id($annotation_id);
		
		$this->assertTrue(has_access_to_entity($todo, $user_three));
		$this->assertTrue(has_access_to_entity($submission, $user_three));
		$this->assertTrue(has_access_to_entity($file, $user_three));
		$this->assertTrue($this->hasAccessToAnnotation($annotation, $user_three));
		$this->assertTrue($this->hasAccessToAnnotation($annotation2, $user_three));

		// Cleanup
		$user_three->is_parent = false;
		$file->delete();
		$a_file->delete();
		$a_file2->delete();
	}
}



/**
 * This is here becuase either something in Elgg or simpletest is broken
 * For some reason the $CONFIG global is annihilated, sometime before 
 * __destruct is called on the LAST test case run.
 */
class TodoDummyTest extends ElggCoreUnitTest {
	public function testTrue() {
		$this->assertTrue(1);
	}
}