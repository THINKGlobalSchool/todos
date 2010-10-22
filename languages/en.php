<?php
	/**
	 * Todo English language translation
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
$english = array(
	
	// Generic
	'todo:title' => 'To Do\'s',
	'todo:todo' => 'To Do',
	'todo' => 'To Do\'s',
	'todo:new' => 'New To Do',
	'item:object:todo' => 'To Do\'s',
	'item:object:todosubmission' => 'To Do Submission',
	
	// Page titles 
	'todo:title:yourtodos'	=> 'To Do\'s I\'ve Assigned',
	'todo:title:assignedtodos' => 'To Do\'s Assigned To me',
	'todo:title:create' => 'Create New To Do',
	'todo:title:edit' => 'Edit To Do',
	'todo:title:alltodos' => 'All Site To Do\'s',
	'todo:title:ownedtodos' => '%s\'s To Do\'s',
	
	// Menu items
	'todo:menu:yourtodos' => 'To Do\'s Assigned To Me',
	'todo:menu:assignedtodos' => 'To Do\'s I\'ve Assigned', 
	'todo:menu:createtodo' => 'Create To Do',
	'todo:menu:admin' => 'todo Admin',
	'todo:menu:alltodos' => 'All Site To Do\'s',	
	'todo:menu:groupassignedtodos' => 'Group Assigned To Do\'s', 	
	'todo:menu:groupcreatetodo' => 'Create Group To Do', 	
	
	// Labels 
	'todo:label:noresults' => 'No Results',
	'todo:label:description' => 'Instructions',
	'todo:label:duedate' => 'Due Date',
	'todo:label:assignto' => 'Assign To', 
	'todo:label:returnrequired' => 'Return Required', 
	'todo:label:individuals' => 'Individual(s)',
	'todo:label:groups' => 'Group or Channel',
	'todo:label:loggedin' => 'Logged In Users', 
	'todo:label:assigneesonly' => 'Assignees Only',
	'todo:label:accesslevel' => 'View Access Level',
	'todo:label:assessmentrubric' => 'Assessment Rubric', 
	'todo:label:rubricnone' => 'None',
	'todo:label:rubricnew' => 'Create New',
	'todo:label:rubricselect' => 'Select Existing',
	'todo:label:selectgroup' => 'Select Group',
	'todo:label:viewrubric' => 'View Rubric',
	'todo:label:assignees' => 'Assignees',
	'todo:label:status' => 'Status',
	'todo:label:completetodo' => 'Complete This To Do',
	'todo:label:flagcomplete' => 'Mark as Complete',
	'todo:label:flagcompleteconfirm' => 'Mark this To Do as completed?',
	'todo:label:newsubmission' => 'New Submission',
	'todo:label:additionalcomments' => 'Additional Comments (Optional)',
	'todo:label:assignee' => 'Assignee',
	'todo:label:datecompleted' => 'Date Completed',
	'todo:label:submission' => 'Submission',
	'todo:label:complete' => 'Complete',
	'todo:label:accepted' => 'Accepted', 
	'todo:label:incomplete' => 'Upcoming',
	'todo:label:statusincomplete' => 'Incomplete',
	'todo:label:viewsubmission' => 'View Submission',
	'todo:label:todo' => 'Assignment',
	'todo:label:moreinfo' => 'Additional Information',
	'todo:label:worksubmitted' => 'Work Submitted',
	'todo:label:addlink' => 'Add Link',
	'todo:label:addfile' => 'Add File',
	'todo:label:link' => 'Link',
	'todo:label:content' => 'Content',
	'todo:label:rubricpicker' => 'Choose Rubric',
	'todo:label:assignedby' => 'Assigned by %s ',
	'todo:label:deletesubmission' => 'Delete Submission',
	'todo:label:unviewed' => 'You have %s unaccepted To Do%s',
	'todo:label:nounviewed' => 'You have no unaccepted To Dos',
	'todo:label:acceptconfirm' => 'Accept this To Do?',
	'todo:label:no' => 'No',
	'todo:label:yes' => 'Yes',
	'todo:label:upcomingtodos' => 'Upcoming To Do\'s',
	'todo:label:savenew' => 'Save and New',
	
	
	// Calendar labels
	'todo:label:calendarsalt' => 'Calendar unique hash salt',
	'todo:label:calendarurl' => 'Todo Calendar URL',
	
	// Time frame labels
	'todo:label:pastdue' => 'Past Due',
	'todo:label:nextweek' => 'Due This Week', 
	'todo:label:future' => 'Future To Do\'s',
	
	// Reminder Labels
	'todo:label:reminder' => 'Reminder',
	'todo:label:sendreminder' => 'Send Reminder', 
	'todo:label:remindconfirm' => 'Send Reminder?', 
	'todo:label:remindall' => 'Remind All',
	
	// Status Labels
	'todo:label:status' => 'Status',
	'todo:status:published' => 'Published', 
	'todo:status:draft' => 'Draft',
	
	// River
	'todo:river:annotate' => "a comment on a todo titled",
	'todo:river:create' => 'a To Do titled',
	'todo:river:created' => "%s created",
	'todosubmission:river:create' => 'a To Do',
	'todosubmission:river:createdeleted' => 'a To Do that no longer exists.',
	'todosubmission:river:created' => "%s completed",
	
	// Email Notifications 
	'todo:email:subjectsubmission' => 'To Do Completed Notification',
	'todo:email:bodysubmission' => "%s has completed a To Do assigned by you titled: %s
	
---

To view this To Do, click here:

%s",

	'todo:email:subjectassign' => 'You have been assigned a To Do',
	'todo:email:bodyassign' => "%s has assigned you To Do titled: %s

---

To view this To Do, click here:

%s",

	'todo:email:subjectreminder' => 'To Do Reminder',
	'todo:email:bodyreminder' => "%s would like to remind you that you need to complete a To Do titled: %s

---

To view this To Do, click here:

%s",
	
	// Messages
	'todo:success:create' => 'To Do successfully submitted',
	'todo:success:edit' => 'To Do successfully edited',
	'todo:success:delete' => 'To Do successfully deleted',
	'todo:success:submissiondelete' => 'Submission successfully deleted',
	'todo:success:accepted' => 'To Do has been accepted',
	'todo:success:reminder' => 'User(s) reminded',
	'todo:success:flagcomplete' => 'To Do marked as complete',
	'todo:error:requiredfields' => 'One of more required fields are missing',
	'todo:error:create' => 'There was an error creating your Todo',
	'todo:error:edit' => 'There was an error editing the Todo',
	'todo:error:delete' => 'There was an error deleting the Todo',
	'todo:error:submissiondelete' => 'There was an error deleting the submission',
	'todo:error:permission' => 'You do not have permission to create/edit this object', 
	'todo:error:permissiondenied' => 'Permission Denied', 
	'todo:error:accepted' => 'There was an error accepting the To Do',
	'todo:error:reminder' => 'There was an error reminding user(s)',
	'todo:error:flagcomplete' => 'There was an error marking the to do as complete',
	
	// Other content
	'todo:strapline' => 'Due: %s',
	'todo:strapline:mode' => '%s',
	'groups:enabletodo' => 'Enable group to do\'s',
	'todo:group' => 'Group to do\'s', 

);

add_translation('en',$english);

?>