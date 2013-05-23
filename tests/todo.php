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