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
	'todo:add' => 'New To Do',
	'item:object:todo' => 'To Do\'s',
	'item:object:todosubmission' => 'To Do Submissions',
	'item:object:todosubmissionfile' => 'To Do Submission File',
	'todo:ingroup' => 'in the group %s',
	
	// Admin
	'admin:todos' => 'To Do\'s',
	'admin:todos:statistics' => 'Statistics',
	'admin:todos:calendars' => 'Calendars',
	'admin:todos:manage' => 'Manage',
	
	// Page titles 
	'todo:title:edit' => 'Edit To Do',
	'todo:title:alltodos' => 'All Site To Do\'s',
	
	// Menu items
	'todo:menu:yourtodos' => 'To Do\'s Assigned To Me',
	'todo:menu:assignedtodos' => 'To Do\'s I\'ve Assigned', 
	'todo:menu:alltodos' => 'All Site To Do\'s',		
	'todo:menu:notifications' => 'To Do Notifications',
	
	// Labels 
	'todo:label:whatisthis' => 'What is this?',
	'todo:label:due' => 'Due: %s',
	'todo:label:noresults' => 'No Results',
	'todo:label:description' => 'Instructions',
	'todo:label:duedate' => 'Due Date',
	'todo:label:startdate' => 'Start Date',
	'todo:label:startdateinfo' => 'Optional: Supplying a start date will display this todo on calendars as starting on this date instead of the date it was created.',
	'todo:label:assignto' => 'Assign To', 
	'todo:label:return' => 'Return',
	'todo:label:returnrequired' => 'Student Submission Required',
	'todo:label:submissionrequired' => 'Submission Required', 
	'todo:label:individuals' => 'Individual(s)',
	'todo:label:groups' => 'Group(s)',
	'todo:label:group' => 'Group',
	'todo:label:currentgroup' => 'Current Group',
	'todo:label:anothergroup' => 'Another Group',
	'todo:label:loggedin' => 'Logged In Users', 
	'todo:label:assigneesonly' => 'Assignees Only',
	'todo:label:accesslevel' => 'View Access Level',
	'todo:label:assessmentrubric' => 'Assessment Rubric', 
	'todo:label:rubricnone' => 'None',
	'todo:label:rubricnew' => 'Create New',
	'todo:label:rubricselect' => 'Select Existing',
	'todo:label:selectgroup' => 'Select Group',
	'todo:label:select' => 'Select...',
	'todo:label:view' => 'View',
	'todo:label:viewrubric' => 'View Rubric',
	'todo:label:assignees' => 'Assignees',
	'todo:label:currentassignees' => 'Current Assignees',
	'todo:label:status' => 'Status',
	'todo:label:publishstatus' => 'Publish Status',
	'todo:label:completetodo' => 'Complete To Do',
	'todo:label:flagcomplete' => 'Close To Do',
	'todo:label:flagopen' => 'Open To Do',
	'todo:label:flagcompleteconfirm' => 'Are you sure you want to close this To Do?',
	'todo:label:flagopenconfirm' => 'Are you sure you want to open this To Do?',
	'todo:label:newsubmission' => 'New Submission',
	'todo:label:additionalcomments' => 'Additional Comments (Optional)',
	'todo:label:assignee' => 'Assignee',
	'todo:label:completed' => 'Completed',
	'todo:label:ontime' => 'On Time',
	'todo:label:ontimeincludingclosed' => 'On Time (Including Closed)',
	'todo:label:datecompleted' => 'Completed',
	'todo:label:submission' => 'Submission',
	'todo:label:submissions' => 'Submissions',
	'todo:label:complete' => 'Complete',
	'todo:label:completeincludingclosed' => 'Complete (Including Closed)',
	'todo:label:accept' => 'Accept',
	'todo:label:accepted' => 'Accepted', 
	'todo:label:new' => 'New',
	'todo:label:incomplete' => 'Upcoming',
	'todo:label:statusincomplete' => 'Incomplete',
	'todo:label:viewsubmission' => 'View Submission',
	'todo:label:assignment' => 'Assignment',
	'todo:label:moreinfo' => 'Additional Information',
	'todo:label:worksubmitted' => 'Work Submitted',
	'todo:label:addlink' => 'Add Link',
	'todo:label:addfile' => 'Add File',
	'todo:label:addcontent' => 'Add Spot Content',
	'todo:label:content' => 'Content',
	'todo:label:rubricpicker' => 'Choose Rubric',
	'todo:label:assignedby' => 'Assigned by %s ',
	'todo:label:assignedto' => 'Assigned to %s',
	'todo:label:submittedby' => 'Submitted by %s',
	'todo:label:me' => 'me',
	'todo:label:acceptconfirm' => 'Accept this To Do?',
	'todo:label:no' => 'No',
	'todo:label:yes' => 'Yes',
	'todo:label:upcomingtodos' => 'Upcoming To Do\'s',
	'todo:label:savenew' => 'Save and New',
	'todo:label:submissiontitleprefix' => 'Submission for: %s',
	'todo:label:signup' => 'Sign Up',
	'todo:label:signupconfirm' => 'Do you want to assign yourself this To Do?',
	'todo:label:ownersubmission' => '%s\'s Submission',
	'todo:label:submittedforsingle' => 'Submitted for 1 To Do', 
	'todo:label:submittedformultiple' => 'Submitted for %s To Do\'s',
	'todo:label:suggestedtags' => 'Student Submission Tag(s)',
	'todo:label:suggestedtagstitle' => 'Submission Tags',
	'todo:label:suggestedtagsinfo' => 'Student submission tags are the tags that students should use for any content they post related to this to do.',
	'todo:label:sortasc' => 'Sort Ascending &#9650;',
	'todo:label:sortdesc' => 'Sort Descending &#9660;',
	'todo:label:linkspotcontent' => 'You are submitting a link to Spot. You should be using Add Spot Content for this. Do you want to continue?',
	'todo:label:prev' => 'Prev',
	'todo:label:next' => 'Next',
	'todo:label:downloadfiles' => 'Download Files',
	'todo:label:upload' => 'Upload',
	'todo:label:zipdelete' => 'Delete Todo Submission Zip Files (Cron period)',
	'todo:label:attached' => 'File Attached',
	'todo:label:groupusersubmissions' => 'User Submissions',
	'todo:label:grades' => 'Grades',
	'todo:label:selectmember' => 'Select Member',
	'todo:label:selectamember' => 'Select a member',
	'todo:label:show' => 'Show',
	'todo:label:date' => 'Date',
	'todo:label:info' => 'Info',
	'todo:label:submissionsadmin' => 'Submission Admin Role',
	'todo:label:facultyrole' => 'Faculty Role',
	'todo:label:graderequired' => 'Grade Required',
	'todo:label:gradetotal' => 'Grade Total',
	'todo:label:grade' => 'Grade',
	'todo:label:notgraded' => 'Not Graded',
	'todo:label:gradedoutof' => 'Graded out of %s',
	'todo:label:notyetgraded' => 'Ungraded',
	'todo:label:child' => 'Child',
	'todo:label:student' => 'Student',
	'todo:label:advisee' => 'Advisee',
	'todo:label:settings' => 'To Do Settings',
	'todo:label:suppress_completion' => 'Suppress To Do Completion Notifications',
	'todo:label:grouplegend' => 'Group Legend',
	'todo:label:move' => 'Move Todo',
	'todo:label:todoguid' => 'Todo GUID',
	'todo:label:groupguid' => 'Group GUID',
	'todo:label:enable_iplan' => 'iPlan Calendars Enabled?',
	'todo:label:submission_tz' => 'Submission Timezone',
	
	// Categories
	'todo:label:category' => 'Category',
	'todo:label:todocategories' => 'To Do Categories',
	'todo:label:basic_task' => 'Basic Task',
	'todo:label:assessed_task' => 'Assessed Task',
	'todo:label:exam' => 'Test/Exam',
	
	// Calendar labels
	'todo:label:calendars' => 'Calendars',
	'todo:label:calendarsalt' => 'Calendar unique hash salt',
	'todo:label:calendarurl' => 'Todo Calendar URL',
	'todo:label:showcategorycalendar' => 'Show these categories on todo calendar',
	'todo:label:categorycolors' => 'Calendar Colors',
	'todo:label:calendarbackground' => 'Background',
	'todo:label:calendarforeground' => 'Foreground',
	'todo:label:iplan' => 'iPlan',
	'todo:label:iplancalendar' => 'iPlan Calendar',
	'todo:label:groupcategories' => 'Group Categories',
	'todo:label:jumptodate' => 'Jump to date',
	'todo:label:palettespread' => 'Palette Spread',
	
	// Time frame labels
	'todo:label:today' => 'Due Today',
	'todo:label:tomorrow' => 'Due Tomorrow',
	'todo:label:pastdue' => 'Past Due',
	'todo:label:nextweek' => 'Due This Week', 
	'todo:label:future' => 'Future To Do\'s',
	
	// Reminder Labels
	'todo:label:reminder' => 'Remind',
	'todo:label:sendreminder' => 'Send', 
	'todo:label:remindconfirm' => 'Send Reminder?', 
	'todo:label:remindall' => 'All',
	
	// Status Labels
	'todo:label:status' => 'Status',
	'todo:status:published' => 'Published', 
	'todo:status:draft' => 'Draft',
	'todo:status:closed' => 'Closed',
	
	// Admin Labels
	'todo:label:admin:totaltodos' => 'To Do\'s',
	'todo:label:admin:totalsubmissions' => 'To Do Submissions',
	'todo:label:admin:assignedusers' => 'Total To Do\'s Assigned to Users',
	'todo:label:admin:assignedgroups' => 'Total Groups Assigned',
	'todo:label:admin:totalcomplete' => 'Completed To Do\'s', 
	'todo:label:admin:totalincomplete' => 'Incomplete To Do\'s', 
	'todo:label:admin:completepercentage' => 'Complete Percentage', 
	'todo:label:admin:manualcomplete' => 'Manually Completed To Do\'s', 
	'todo:label:admin:manualpercentage' => 'Manually Completed Percentage', 
	'todo:label:admin:assignedsubmitted' => 'Submitted/Assigned', 
	'todo:label:admin:completionaverage' => 'Average User Completion',
	
	// River
	'river:create:object:todo' => '%s created To Do titled %s',
	'river:create:object:todosubmission' => '%s completed a To Do titled %s',
	'river:create:object:todosubmission:deleted' => '%s completed a To Do that no longer exists.',
	'river:comment:object:todo' => '%s commented on a To Do titled %s',
	'river:comment:object:todosubmission' => '%s commented on %s', 
	'river:comment:object:todosubmissionfile' => '%s commented on %s',

	
	// Email Notifications 
	'todo:email:subjectsubmission' => "%s has completed the To Do titled \"%s\"",
	'todo:email:bodysubmission' => "%s has completed a To Do assigned by you titled \"%s\"
	
---

To view this To Do, click here:

%s

To view the submission, click here:

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

	'generic_comment:email:body' => "You have a new comment on your item \"%s\" from %s. It reads:


%s


To reply, view, or comment on the original item, click here:

%s

To view %s's profile, click here:

%s",

	'todo:email:bodysubmissioncomment' => "There is a new comment on a submission for the To Do titled %s.

To view or comment on the original submission, click here:

%s

To view %s's profile, click here:

%s",

	// Submission Annotations
	'riveraction:annotation:submission_annotation' => '%s commented on %s',

	'submission_annotations:add' => "Leave a comment",
	'submission_annotations:attach' => "Attach a file (Optional)",
	'submission_annotations:post' => "Post comment",
	'submission_annotations:text' => "Comment",
	'submission_annotations:latest' => "Latest comments",
	'submission_annotation:posted' => "Your comment was successfully posted.",
	'submission_annotation:deleted' => "The comment was successfully deleted.",
	'submission_annotation:blank' => "Sorry, you need to actually put something in your comment before we can save it.",
	'submission_annotation:notfound' => "Sorry, we could not find the specified item.",
	'submission_annotation:notdeleted' => "Sorry, we could not delete this comment.",
	'submission_annotation:failure' => "An unexpected error occurred when adding your comment. Please try again.",
	'submission_annotation:none' => 'No comments',
	'submission_annotation:title' => 'Comment by %s',

	'submission_annotation:email:subject' => 'You have a new comment!',
	'submission_annotation:email:body' => "You have a new comment on your item \"%s\" from %s. It reads:


%s


To reply or view the original item, click here:

%s

To view %s's profile, click here:

%s",
	
	// Messages
	'todo:success:save' => 'To Do Saved',
	'todo:success:savesubmission' => 'Submission Saved',
	'todo:success:delete' => 'To Do successfully deleted',
	'todo:success:submissiondelete' => 'Submission successfully deleted',
	'todo:success:accepted' => 'To Do has been accepted',
	'todo:success:reminder' => 'User(s) reminded',
	'todo:success:flagcomplete' => 'To Do closed',
	'todo:success:flagopen' => 'To Do opened',
	'todo:success:signup' => 'Successfully signed up for To Do',
	'todo:success:assigneeremoved' => 'Assignee Removed',
	'todo:success:grade' => 'Grade saved!',
	'todo:success:calendarsettings' => 'Successfully Saved Calendar Settings',
	'todo:error:requiredfields' => 'One of more required fields are missing',
	'todo:error:requiredtitle' => 'Title is required',
	'todo:error:requireddate' => 'Due date is required',
	'todo:error:requiredgradetotal' => 'Grade Total is required',
	'todo:error:requiredcategory' => 'Category is required',
	'todo:error:create' => 'There was an error creating your Todo',
	'todo:error:edit' => 'There was an error editing the Todo',
	'todo:error:delete' => 'There was an error deleting the Todo',
	'todo:error:savesubmission' => 'There was an error saving the submission',
	'todo:error:duplicatesubmission' => 'You have already completed this todo, please delete your existing submission and try again',
	'todo:error:closedsubmission' => 'You cannot complete a closed todo',
	'todo:error:submissiondelete' => 'There was an error deleting the submission',
	'todo:error:permission' => 'You do not have permission to create/edit this object', 
	'todo:error:permissiondenied' => 'Permission Denied', 
	'todo:error:accepted' => 'There was an error accepting the To Do',
	'todo:error:reminder' => 'There was an error reminding user(s)',
	'todo:error:flagcomplete' => 'There was an error marking the to do as complete',
	'todo:error:flagopen' => 'There was an error opening the to do',
	'todo:error:signup' => 'There was an error signing up for the To Do',
	'todo:error:invalid' => 'Invalid Entity',
	'todo:error:fileupload' => 'There was an error uploading the file',
	'todo:error:nofile' => 'You need to select at least one file',
	'todo:error:assigneeremoved' => 'There was an error removing the assignee',
	'todo:error:invalidurl' => 'Invalid URL',
	'todo:error:zipcreate' => 'There was an error creating the To Do zip file',
	'todo:error:zipfileerror' => 'There was an error adding the file "%s" to zip',
	'todo:error:nofiles' => 'There are no files available to download',
	'todo:error:toomanyfiles' => 'You can only attach one file at a time',
	'todo:error:filetoolarge' => 'File size must be less than 8MB',
	'todo:error:uploadfailed' => 'File upload failed',
	'todo:error:deletefile' => 'Failed to delete comment attachement',
	'todo:error:invalidgroup' => 'Invalid Group',
	'todo:error:invaliduser' => 'Invalid User',
	'todo:error:invalidgrade' => 'Invalid Grade',
	'todo:error:access' => 'Access Denied',
	'todo:error:gradevalue' => 'Grade must be a number!',
	'todo:error:nodata' => 'No data',
	
	// Other content
	'groups:enabletodo' => 'Enable group to do\'s',
	'todo:group' => 'Group to do\'s', 

);

add_translation('en',$english);
