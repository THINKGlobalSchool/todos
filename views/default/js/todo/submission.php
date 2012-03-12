<?php
/**
 * Todo Submission JS Library
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>
//<script>
elgg.provide('elgg.todo.submission');

elgg.todo.submission.init = function() {
	// Set up a handler for ajax comment clicks
	$(document).delegate('#todo-submission-annotations form.elgg-form-submission-annotate input.elgg-button', 'click', elgg.todo.submission.commentClick);

	// Set up a handler for ajax comment delete clicks
	$(document).delegate('#todo-submission-annotations .elgg-list-annotation li.elgg-menu-item-delete a', 'click', elgg.todo.submission.deleteCommentClick);

	// Click handler for previous 
	$(document).delegate('.todo-ajax-submission-navigation-prev', 'click', function(event) {
		$.fancybox.prev();
		event.preventDefault();
	});

	// Click handler for next 
	$(document).delegate('.todo-ajax-submission-navigation-next', 'click', function(event) {
		$.fancybox.next();
		event.preventDefault();
	});
	
	// Init submission fancyboxen
	elgg.todo.submission.initFancybox();
}

/**
 * Undelegate/teardown
 */
elgg.todo.submission.destroy = function() {
	// Destroy fileupload
	$('.todo-submission-attachment-upload').fileupload('destroy');
	
	// Undelegate events
	$(document).undelegate('#todo-submission-annotations form.elgg-form-submission-annotate input.elgg-button', 'click');
	$(document).undelegate('#todo-submission-annotations .elgg-list-annotation li.elgg-menu-item-delete a', 'click');
	$(document).undelegate('.todo-ajax-submission-navigation-prev', 'click');
	$(document).undelegate('.todo-ajax-submission-navigation-next', 'click');
}

// Init the fancybox
elgg.todo.submission.initFancybox = function() {
	// Set up submission dialog
	$(".todo-submission-lightbox").fancybox({
		'onComplete': function() {		
			// Add todo navigation class to the fancybox container				
			$('.todo-ajax-submission').closest('#fancybox-outer').addClass('todo-ajax-submission-navigation');

			// Fix duplicate menu items
			var menu_id = $('.todo-ajax-submission .tgstheme-entity-menu-actions').attr('id');
			$('body > #' + menu_id).remove();

			// Show todo navigation right if the fancybox right control is visible
			if ($('#fancybox-right').is(':visible')) {
				$('.todo-ajax-submission-navigation-next').fadeIn();
			}

			// Show todo navigation left if the fancybox left control is visible
			if ($('#fancybox-left').is(':visible')) {
				$('.todo-ajax-submission-navigation-prev').fadeIn();
			}

			// Fix tinymce control for submission text field
			var id = $('.todo-ajax-submission').find('.elgg-input-longtext').attr('id');

			if (typeof(tinyMCE) !== 'undefined') {
				tinyMCE.EditorManager.execCommand('mceAddControl', false, id);
			}

			// Init drag and drop input
			elgg.todo.submission.initDragDrop();
		},
		'onCleanup': function() {
			// Fix tinymce control for submission text field
			var id = $('.todo-ajax-submission').find('.elgg-input-longtext').attr('id');
			if (typeof(tinyMCE) !== 'undefined') {
	    		tinyMCE.EditorManager.execCommand('mceRemoveControl', false, id);
			}
		},
		'onClosed': function() {
			// Reset location hash
			window.location.hash = '';
		}
	});
}

// Init the drag and drop uploader
elgg.todo.submission.initDragDrop = function() {
	// Init fileupload
	$('.todo-submission-attachment-upload').fileupload({
        dataType: 'json',
		dropZone: $('#todo-submission-dropzone-div'),
		fileInput: $('.todo-submission-attachment-upload'),
		drop: function (e, data) {
			// Remove drag class
			$(e.originalEvent.target).removeClass('todo-submission-dropzone-drag');

			// Make sure we're not dropping multiple files
			if (data.files.length > 1) {
				elgg.register_error(elgg.echo('todo:error:toomanyfiles'));
				e.preventDefault();
			}

			if (data.files[0].size > 8388607) {
				elgg.register_error(elgg.echo('todo:error:filetoolarge'));
				e.preventDefault();
			}
		},
		add: function (e, data) {
			// Get the dropped file
			var file = data.files[0];

			// Set file data on the input, to be used with click event later
			$('.todo-submission-attachment-upload').data('data', data);

			// Remove dropzone classes and display info
			var $div = $('#todo-submission-dropzone-div');
			$div.removeClass('todo-submission-dropzone-background');

			var $drop_name = $(document.createElement('span'));
			$drop_name.addClass('file-name');
			$drop_name.html(file.name);

			var $drop_size = $(document.createElement('span'));
			$drop_size.addClass('file-size');
			$drop_size.html(elgg.todo.submission.calculateSize(file.size));

			var $drop_info = $(document.createElement('span'));
			$drop_info.addClass('todo-submission-drop-info');
			$drop_info.append($drop_name);
			$drop_info.append($drop_size);

			$div.html($drop_info);
			
			e.preventDefault();
		},
		dragover: function (e, data) {
			// Add fancy dragover class
			$(e.originalEvent.target).addClass('todo-submission-dropzone-drag');
		}
    });
}

// Click handler for comment submit
elgg.todo.submission.commentClick = function(event) {
	// Get the form
	var $form = $(this).closest('form.elgg-form');
	
	var $_original = $(this).clone();
	
	$(this).replaceWith("<div class='submission-comment-loader elgg-ajax-loader'></div>");
	
	var $_this = $(this);

	// Get comment input id	
	var comment_id = $form.find('.elgg-input-longtext').attr('id');

	// Get entity guid
	var entity_guid = $form.find('input[name="entity_guid"]').val();
	
	// Get comment, may not be tinyMCE 
	if (typeof(tinyMCE) !== 'undefined') {
		try {
			var comment = tinyMCE.get(comment_id).getContent();
			$("#" + comment_id).val(comment);
		} catch (err) {
			var comment = $("#" + comment_id).val();
		}
	} else {
		var comment = $("#" + comment_id).val();
	}

	var data = $('.todo-submission-attachment-upload').data('data');
	
	// Check if we have file upload data
	if (data) {
		// Post comment with fileupload
		var jqXHR = $('.todo-submission-attachment-upload').fileupload('send',{
				files: data.files,
				entity_guid: entity_guid, 
				comment_text: comment,
			})
			.done(function (result, textStatus, jqXHR) {
				// Success/done check elgg status's
				if (result.status != -1) {
					// Display success
					elgg.system_message(result.system_messages.success);
					
					// New comment html is output by the action
					elgg.todo.submission.pushComment(result.output.annotation_content);

					// Reset drop zone
					var $div = $('#todo-submission-dropzone-div');
					$div.addClass('todo-submission-dropzone-background');
					$div.html('');

					// Reset upload data
					$('.todo-submission-attachment-upload').data('data', null);

					// Prevent the 'are you sure you want to leave' popup
					window.onbeforeunload = function() {};

				} else {
					// There was an error, display it
					elgg.register_error(result.system_messages.error);
				}
			})
	    	.fail(function (jqXHR, textStatus, errorThrown) {
				// If we're here, there was an error making the request
				// or we got some screwy response.. display an error and log it for debugging
				elgg.register_error(elgg.echo('todo:error:uploadfailed'));
				console.log('fail');
				console.log(errorThrown);
				console.log(textStatus);
				console.log(jqXHR);
			})
	    	.always(function (result, textStatus, jqXHR) {
				// Enable the button (try again?)
				$('.submission-comment-loader').replaceWith($_original);
				
				// Clear comment text field
				var id = $('#todo-submission-annotations').find('.elgg-input-longtext').attr('id');
				if (typeof(tinyMCE) !== 'undefined') {
					tinyMCE.get(id).setContent('')
				} else {
					$('#' + id).val('');
				}
			});
	} else {
		// Post comment with a regular elgg action
		elgg.action('submission/annotate', {
			data: {
				entity_guid: entity_guid, 
				comment_text: comment,
			}, 
			success: function(data) {
				// Check for bad status 
				if (data.status == -1) {
					// Error
					$('.submission-comment-loader').replaceWith($_original);
				} else {
					// New comment html is output by the action
					elgg.todo.submission.pushComment(data.output.annotation_content);

					// Clear comment text field
					var id = $('#todo-submission-annotations').find('.elgg-input-longtext').attr('id');
					if (typeof(tinyMCE) !== 'undefined') {
						tinyMCE.get(id).setContent('')
					} else {
						$('#' + id).val('');
					}

					$('.submission-comment-loader').replaceWith($_original);	
				}
			}
		});
	}

	event.preventDefault();
}

// Click handler for comment delete click
elgg.todo.submission.deleteCommentClick = function(event) {
	if (!$(this).hasClass('disabled') && confirm(elgg.echo('deleteconfirm'))) {
		$(this).addClass('disabled');
		$_this = $(this);

		// Extract annotation ID from the href
		var string = $(this).attr('href');

		var search = "annotation_id=";

		var annotation_id = string.substring(string.indexOf(search) + search.length);

		// Get the form
		var $form = $(this).closest('.todo-ajax-submission').find('form.elgg-form');

		// Get entity guid
		var entity_guid = $form.find('input[name="entity_guid"]').val();

		// Delete comment
		elgg.action('submission/delete_annotation', {
			data: {
				annotation_id: annotation_id,
			}, 
			success: function(data) {
				// Check for bad status 
				if (data.status == -1) {
					// Error
					$_this.removeClass('disabled');
				} else {
					// Remove the comment from the DOM
					$_this.closest('li.elgg-item').fadeOut(function(){
						$(this).remove();
					});
				}
			}
		});
	}
	event.preventDefault();
}

/**
 * Check for and process any supplied hash paramaters
 * 
 * - This doesn't do any kind of validation, it simple tries to click 
 * the link that would be on the page if a submission exists. If there's no 
 * match, nothing happens. (Desired behaviour)
 */
elgg.todo.submission.processHash = function(todo_guid) {
	// Check for hash
	if (window.location.hash) {
		// Try to grab submission guid
		var submission_guid = window.location.hash.replace('#submission:', '');	
	
		// Make sure we have an integer
		if ((parseFloat(submission_guid) == parseInt(submission_guid)) && !isNaN(submission_guid)) {

			// Loop over each todo submission lightbox (if any)
			$('a.todo-submission-lightbox').each(function() {

				// If we have a submission on the page matching the given hash
				if($(this).attr('href').indexOf('guid=' + submission_guid) != -1) {

					// Trigger the click
					$.fancybox.init();
					$(this).trigger('click');

					// Break out of each
					return false;
				}
			});
		}
	}
}

// Helper function to push a comment into an annotation list
elgg.todo.submission.pushComment = function(content) {
	// Create an object from content param
	var $new_comment = $(content);
	$new_comment.hide(); // Hide it for special fx

	// Grab annotation
	var $annotation_list = $('#todo-submission-annotations ul.elgg-annotation-list');
	
	// Check for the annotation list, if we dont have one, create it
	if ($annotation_list.length == 0) {
		// Create the annotation list
		var $ul = $(document.createElement('ul'));
		$ul.attr('class', 'elgg-list elgg-list-annotation elgg-annotation-list');

		// Create heading
		var $h3 = $(document.createElement('h3'));
		$h3.html(elgg.echo('comments'));

		// Prepend the container with the new contents
		$('#todo-submission-annotations').prepend($ul);
		$('#todo-submission-annotations').prepend($h3);
		
		var $annotation_list = $('#todo-submission-annotations ul.elgg-annotation-list');
	}

	// Append to the annotation list
	$annotation_list.append($new_comment);

	// Slide it in
	$new_comment.slideDown();
}

// Calculate file size for display
elgg.todo.submission.calculateSize = function(size) {
    if (typeof size !== 'number') {
        return '';
    }
    if (size >= 1000000000) {
        return (size / 1000000000).toFixed(2) + ' GB';
    }
    if (size >= 1000000) {
        return (size / 1000000).toFixed(2) + ' MB';
    }
    return (size / 1000).toFixed(2) + ' KB';
}

elgg.register_hook_handler('init', 'system', elgg.todo.submission.init);