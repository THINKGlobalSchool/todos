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
	$(document).delegate('.todo-ajax-submission form.elgg-form-comments-add input.elgg-button', 'click', elgg.todo.submission.commentClick);

	// Set up a handler for ajax comment delete clicks
	$(document).delegate('.todo-ajax-submission .elgg-list-annotation li.elgg-menu-item-delete a', 'click', elgg.todo.submission.deleteCommentClick);

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
	
	// Set up submission dialog
	$(".todo-submission-lightbox").fancybox({
		//'modal': true,
		'onComplete': function() {						
			$('.todo-ajax-submission').closest('#fancybox-outer').addClass('todo-ajax-submission-navigation');
			var id = $('.todo-ajax-submission').find('.elgg-input-longtext').attr('id');
			
			if ($('#fancybox-right').is(':visible')) {
				$('.todo-ajax-submission-navigation-next').fadeIn();
			}

			if ($('#fancybox-left').is(':visible')) {
				$('.todo-ajax-submission-navigation-prev').fadeIn();
			}

			if (typeof(tinyMCE) !== 'undefined') {
				tinyMCE.EditorManager.execCommand('mceAddControl', false, id);
			}
		},
		'onCleanup': function() {
			var id = $('.todo-ajax-submission').find('.elgg-input-longtext').attr('id');
			if (typeof(tinyMCE) !== 'undefined') {
	    		tinyMCE.EditorManager.execCommand('mceRemoveControl', false, id);
			}
		},
		'onClosed': function() {
			window.location.hash = '';
		}
	});
}

// Click handler for comment submit
elgg.todo.submission.commentClick = function(event) {
	// Get the form
	var $form = $(this).closest('form.elgg-form');
	
	$(this).attr('disabled', 'disabled');
	
	$_this = $(this);

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

	// Post comment
	elgg.action('comments/add', {
		data: {
			entity_guid: entity_guid, 
			generic_comment: comment,
		}, 
		success: function(data) {
			// Check for bad status 
			if (data.status == -1) {
				// Error
				$_this.removeAttr('disabled');
			} else {
				// Clear tinymce
				var id = $('.todo-ajax-submission').find('.elgg-input-longtext').attr('id');
				if (typeof(tinyMCE) !== 'undefined') {
		    		tinyMCE.EditorManager.execCommand('mceRemoveControl', false, id);
				}

				// Load in new comments
				load_url = elgg.get_site_url() + 'ajax/view/todo/ajax_comments?guid=' + entity_guid;
				$form.closest('.elgg-comments')
					.html('')
					.addClass('elgg-ajax-loader')
					.load(load_url, function() {
						$(this).removeClass('elgg-ajax-loader');
						var id = $('.todo-ajax-submission').find('.elgg-input-longtext').attr('id');
						if (typeof(tinyMCE) !== 'undefined') {
							tinyMCE.EditorManager.execCommand('mceAddControl', false, id);
						}
					});	
			}
		}
	});

	event.preventDefault();
}

// Click handler for comment delete click
elgg.todo.submission.deleteCommentClick = function(event) {
	if (!$(this).hasClass('disabled')) {
		$(this).addClass('disabled');
		$_this = $(this);

		// Extract annotation ID from the href
		var string = $(this).attr('href');
		var annotation_id = string.substring(string.indexOf('annotation_id=') + 14, string.indexOf('&'));
		
		// Get the form
		var $form = $(this).closest('.todo-ajax-submission').find('form.elgg-form');
		
		// Get entity guid
		var entity_guid = $form.find('input[name="entity_guid"]').val();

		// Delete comment
		elgg.action('comments/delete', {
			data: {
				annotation_id: annotation_id,
			}, 
			success: function(data) {
				// Check for bad status 
				if (data.status == -1) {
					// Error
					$_this.removeClass('disabled');
				} else {
					// Clear tinymce
					var id = $('.todo-ajax-submission').find('.elgg-input-longtext').attr('id');
					if (typeof(tinyMCE) !== 'undefined') {
			    		tinyMCE.EditorManager.execCommand('mceRemoveControl', false, id);
					}

					// Load in new comments
					load_url = elgg.get_site_url() + 'ajax/view/todo/ajax_comments?guid=' + entity_guid;
					$form.closest('.elgg-comments')
						.html('')
						.addClass('elgg-ajax-loader')
						.load(load_url, function() {
							$(this).removeClass('elgg-ajax-loader');
							var id = $('.todo-ajax-submission').find('.elgg-input-longtext').attr('id');
							if (typeof(tinyMCE) !== 'undefined') {
								tinyMCE.EditorManager.execCommand('mceAddControl', false, id);
							}
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
					$(this).trigger('click');

					// Break out of each
					return false;
				}
			});
		}
	}
}

elgg.register_hook_handler('init', 'system', elgg.todo.submission.init);