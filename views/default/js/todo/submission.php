<?php
/**
 * Todo Submission JS Library
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 * 
 */
?>
//<script>
elgg.provide('elgg.todo.submission');

// Store the last submitted grade
elgg.todo.submission.last_grade = null;

elgg.todo.submission.init = function() {
	// Create submission click handler
	$(document).on('click', '.submission-empty', elgg.todo.submission.completeClick);
				
	// Set up submission dialog
	$(".submission-lightbox").colorbox({
		'enableEscapeButton': false,
		'onStart' : function() {
			//$('.tgstheme-entity-menu-actions').fadeOut();
		},
		'onComplete': function() {
			// Set up submission content form
			elgg.todo.submission.formDefault();
			elgg.modules.ajaxmodule.init();

			// Trigger a hook to let plugins know that the lightbox has loaded
			elgg.trigger_hook('submission_lightbox_loaded', 'todos');
		}
	});

	/** Submittion Form Setup **/

	// Submission form submit handler
	$(document).on('click', "#todo-submission-form #submit-create-submission", elgg.todo.submission.formSubmit);
	
	// Hack modules to add an 'add' button	
	$(document).on('mouseenter mouseleave', '#submission-add-content-container .elgg-item', elgg.todo.submission.addHover);
		
	// Make menu items clickable
	$(document).on('click', ".submission-content-menu-item", elgg.todo.submission.contentMenuClick);
	
	// Make submit link button clickable
	$(document).on('click', "#submission-submit-link", elgg.todo.submission.submitLink);
	
	// Back button click action
	$(document).on('click', "#submission-content-back-button", elgg.todo.submission.formDefault);
	
	// Register submit handler for submission file form
	$(document).on('submit', "#submission-file-form", elgg.todo.submission.submitFile);

	// Register submit handler for submission link form
	$(document).on('submit', '#submission-link-form', function(event) {return false;});
	
	// Register click handler for submission content submit
	$(document).on('click', '.submission-content-input-add', elgg.todo.submission.submitContent);

	// Cancel click handler for content 
	$(document).on('click', '.submission-cancel-add', elgg.todo.submission.formDefault);

	/** Ajax View Setup **/

	// Set up a handler for ajax comment clicks
	$(document).on('click', '#todo-submission-annotations form.elgg-form-submission-annotate input.elgg-button', elgg.todo.submission.commentClick);

	// Set up a handler for ajax comment delete clicks
	$(document).on('click', '#todo-submission-annotations .elgg-list-annotation li.elgg-menu-item-delete a', elgg.todo.submission.deleteCommentClick);

	// Click handler for previous 
	$(document).on('click', '.todo-ajax-submission-navigation-prev', function(event) {
		$.colorbox.prev();
		elgg.todo.submission.last_grade = null;
		event.preventDefault();
	});

	// Click handler for next 
	$(document).on('click', '.todo-ajax-submission-navigation-next', function(event) {
		$.colorbox.next();
		elgg.todo.submission.last_grade = null;
		event.preventDefault();
	});

	// Grade focus event handler
	$(document).on('focus', '.submission-grade-input', function(event) {
		if ($(this).val() != typeof undefined) {
			elgg.todo.submission.last_grade = $(this).val();
		}
	});

	// Grade blur event handler
	$(document).on('blur', '.submission-grade-input', elgg.todo.submission.submitGrade);
	
	// Override grade form submission
	$(document).on('submit', 'form.elgg-form-submission-grade', elgg.todo.submission.submitGrade);
	
	// Init submission colorboxen
	elgg.todo.submission.initColorbox();
}

/**
 * Undelegate/teardown
 */
elgg.todo.submission.destroy = function() {
	// Destroy fileupload
	if (typeof $.fileupload !== 'undefined') {
		$('.todo-submission-attachment-upload').fileupload('destroy');
	}
	
	// Undelegate events
	$(document).off('click', '#todo-submission-annotations form.elgg-form-submission-annotate input.elgg-button');
	$(document).off('click', '#todo-submission-annotations .elgg-list-annotation li.elgg-menu-item-delete a');
	$(document).off('click', '.todo-ajax-submission-navigation-prev');
	$(document).off('click', '.todo-ajax-submission-navigation-next');
	$(document).off('blur', '.submission-grade-input');
	$(document).off('submit', 'form.elgg-form-submission-grade');
	$(document).off('click', '.submission-empty');
	$(document).off('click', "#todo-submission-form #submit-create-submission");
	$(document).off('mouseenter mouseleave', '#submission-add-content-container .elgg-item');
	$(document).off('click', ".submission-content-menu-item");
	$(document).off('click', "#submission-submit-link");
	$(document).off('click', "#submission-content-back-button");
	$(document).off('submit', "#submission-file-form");
	$(document).off('submit', '#submission-link-form');
	$(document).off('click', '.submission-content-input-add');
	$(document).off('click', '.submission-cancel-add');
	$(document).off('blur', '.submission-grade-input');

}

/**	
 * Click handler for creating an empty submission
 */
elgg.todo.submission.completeClick = function(event) {
	// Replace with spinner
	var $button = $(this).clone(); // Store original button
	$(this).replaceWith("<div id='submit-empty-loader' class='elgg-ajax-loader'></div>");

	var todo_guid = $('#todo-guid').val();

	// Create empty submission
	if (!elgg.todo.submission.createSubmission(todo_guid, '', '')) {
		// Display button again (retry)
		$('#submit-empty-loader').replaceWith($button);
	}

	event.preventDefault();
}

/**
 * Submit handler for submission form
 */
elgg.todo.submission.formSubmit = function(event) {
	/** May not be tinyMCE (ckeditor???) **/
	if (typeof(tinyMCE) !== 'undefined') {
		//var comment = tinyMCE.get('submission-description').getContent();
		//$("textarea#submission-description").val(comment);
	} else {
		var comment = $("textarea#submission-description").val();
	}
	
	var content = $("#submission-content-select").val();
	var todo_guid = $('#todo-guid').val();
		
	// If we have content (content is required)
	if (content) {
		$('#submit-create-submission').attr('disabled', 'disabled');
		// Create submission
		if (!elgg.todo.submission.createSubmission(todo_guid, content, comment)) {
			// Re-enable button (try again)
			$('#submit-create-submission').removeAttr('disabled');
		}
	} else {
		// error
		$("#submission-error-message").show().html("** Content is required");
	}
	
	event.preventDefault();
}

/**
 * Create submission action
 */
elgg.todo.submission.createSubmission = function(todo_guid, content, comment) {	
	// Replace submit button with spinner	
	var $button = $('#submit-create-submission').clone(); // Store original button
	$('#submit-create-submission').replaceWith("<div id='submit-create-loader' class='elgg-ajax-loader'></div>");

	elgg.action('submission/save', {
		data: {
			submission_description: comment,
			todo_guid: todo_guid, 
			submission_content: content,
		},
		error: function(e) {
			// Display error (will probably look gross)
			$("#submission-error-message").show().html(e);
			elgg.register_error(e);
			$('#submit-create-loader').replaceWith($button);
			return false;
		},
		success: function(json) {
			// Check for bad status 
			if (json.status == -1) {
				$("#submission-error-message").show().html(json.output);
				$('#submit-create-loader').replaceWith($button);
				$button.removeAttr('disabled');
				return false;
			} else {				
				// Close dialog
				$.colorbox.close();
				
				// Reload
				setTimeout('window.location.reload()', 1000);
				return true;
			}
		}
	});
}

/**
 * Handler to add an 'add' button to the modules content listing to allow
 * adding spot content to a todo submission
 */
elgg.todo.submission.addHover = function(event) {
	// For some reason the height is only accurate at this point.. 
	var height = $(this).height();
	if (event.type == 'mouseenter') {
		var $addmenu = $(this).data('addmenu') || null;

		if (!$addmenu) {
			var $addmenu = $("<div class='add-menu'><input type='submit' value='Add'class='elgg-button elgg-button-action submission-content-input-add' /></div>");
			$(this).data('addmenu', $addmenu);
			$addmenu.appendTo($(this));
		}
		
		// Grab guid and check to make sure content is not already selected
		var id = $(this).closest('.elgg-item').attr('id');
		var guid = id.substring(id.lastIndexOf('-') + 1);
		var selected = $('#submission-content-select').val();
		
		if (selected && $.inArray(guid, selected) !== -1) {
			// Update menu data accordingly
			var added = "<span class='todo-content-added'>Added!</span>";
			$addmenu.find('input').replaceWith(added);
		}

		var margin = '-' + height + 'px';

		$addmenu
			.css("width", '90px')
			.css("height", height + 'px')
			.css("z-index", '100')
			.fadeIn('fast')
			.position({
				my: "right top",
				at: "right top",
				of: $(this)
			}).css("margin-bottom", margin);
	} else if (event.type == 'mouseleave') {
		var $addmenu = $(this).data('addmenu');
		$addmenu.fadeOut();
	}
}

/**
 * Submission content menu item click handler
 */
elgg.todo.submission.contentMenuClick = function(event) {
	$("div.submission-content-pane").hide();

	$('.elgg-menu-todo-submission-content-type li').removeClass('elgg-state-selected');

	$(this).parent().addClass('elgg-state-selected');
	
	// The id to show is supplied as the items href
	$($(this).attr('href')).show();
	
	event.preventDefault();
}

/**
 * Submit link click handler
 */
elgg.todo.submission.submitLink = function(event) {
	var link = $('#submission-link').val();
	
	if (link) {
		// Check for valid link
		if (!elgg.todo.isValidURL(link)) {
			elgg.register_error(elgg.echo('todo:error:invalidurl'));
			return false;
		}

		// Get a protocol trimmed version of the link, and site url
		var trimmed_link = elgg.todo.global.trimProtocol(link);
		var trimmed_site = elgg.todo.global.trimProtocol(elgg.get_site_url());

		// Check is given url comes from this site
		if (trimmed_link.indexOf(trimmed_site) !== -1) {
			// This url did come from this site, parse out the first number we come across
			var regex = ".*?(\\d+)";
			var p = new RegExp(regex,["i"]);
			var m = p.exec(trimmed_link);
		
			// If we have a match, try to find the elgg object 
			if (m != null) {
				var guid = m[1];
							
				elgg.action('todo/checkcontent', {
					data: {
						guid: guid,
						show_error: 0,
					},
					success: function(data) {
						if (data.status == -1) {
							elgg.todo.submission.confirmLocalLink(link);
						} else {
							$('#submission-content-select').append(
								$('<option></option>').attr('selected', 'selected').val(data.output.entity_guid).html(data.output.entity_title)
							);
							elgg.todo.submission.formDefault();	
						}
					}
				});
			} else {
				elgg.todo.submission.confirmLocalLink(link);
			} 
		} else {
			$('#submission-content-select').append(
				$('<option></option>').attr('selected', 'selected').val(link).html(link)
			);
			elgg.todo.submission.formDefault();
			$('#submission-link').val('');
		}
	}
	event.preventDefault();
}

/** 
 * Submit handler for submission file form
 */ 
elgg.todo.submission.submitFile = function(event) {	
	var options = { 
			url: elgg.security.addToken(elgg.todo.fileUploadURL), 
			type: "POST", 
	        target: '#submission-output',
			clearForm: true,
	        beforeSubmit: function(formData, jqForm, options) {
				$("#submission-ajax-spinner").show();
			}, 
	        success: function(response, statusText, xhr, $form) {
				$("#submission-ajax-spinner").hide();
				var file = eval( "(" + response + ")" );
				$('#submission-content-select').append(
					$('<option></option>').attr('selected', 'selected').val(file.guid).html(file.name)
				);
				elgg.todo.submission.formDefault();	
			},
	    };
	
	$(this).ajaxSubmit(options); 
	
	event.preventDefault();
}

/** 
 * Submit handler for submission content form
 */ 
elgg.todo.submission.submitContent = function(event) {
	var id = $(this).closest('.elgg-item').attr('id');
	
	var guid = id.substring(id.lastIndexOf('-') + 1);
	
	elgg.action('todo/checkcontent', {
		data: {
			guid: guid
		},
		success: function(data) {
			if (data.status == -1) {
				$("#submission-error-message").show().html("** Invalid Content");
			} else {
				$('#submission-content-select').append(
					$('<option></option>').attr('selected', 'selected').val(data.output.entity_guid).html(data.output.entity_title)
				);
				elgg.todo.submission.formDefault();	
				
				var $listitem = $('#elgg-object-' + guid);
				var $addmenu = $listitem.data('addmenu');
				
				// Get values from content select, we don't want to show the add button
				// for already added content
				var selected = $('#submission-content-select').val();
				
				// Check if we've already added this content
				if (selected && $.inArray(guid, selected) !== -1) {
					// Update menu data accordingly
					var added = "<span class='todo-content-added'>Added!</span>";
					$addmenu.find('input').replaceWith(added);
					$listitem.find('input').replaceWith(added);
				}
			}
		}
	});

	event.preventDefault();
}

/**
 * Confirm that the user wants to submit a local link
 */
elgg.todo.submission.confirmLocalLink = function(link) {
	// If the object doesn't check out, show a confirmation
	var response = confirm(elgg.echo('todo:label:linkspotcontent'));
	
	// If 'ok' is clicked, add the link anyway
	if (response) {
		$('#submission-content-select').append(
			$('<option></option>').attr('selected', 'selected').val(link).html(link)
		);
		elgg.todo.submission.formDefault();
	} else {
		// Reset the form and click the contenet menu item
		elgg.todo.submission.formDefault();
		$('#add-content').click();
	}	
	
	$('#submission-link').val('');
}


/** 
 * Displays the submission content add menu in its default state
 */
elgg.todo.submission.formDefault = function(event) {
	$("div.submission-content-pane").hide();
	$("div#submission-content-list").show();

	$('.elgg-menu-todo-submission-content-type li').removeClass('elgg-state-selected');

	if (event) {
		event.preventDefault();
	}
}


// Init the fancybox
elgg.todo.submission.initColorbox = function() {
	// Set up submission dialog
	$(".todo-submission-lightbox").colorbox({
		'arrowKey': false,
		'loop': false,
		'current': false,
		'onComplete': function() {		
			// Add todo navigation class to the fancybox container				
			$('.todo-ajax-submission').closest('#fancybox-outer').addClass('todo-ajax-submission-navigation');

			// Fix duplicate menu items
			var menu_id = $('.todo-ajax-submission .tgstheme-entity-menu-actions').attr('id');
			$('body > #' + menu_id).remove();

			// Show todo navigation right if the fancybox right control is visible
			if ($('#cboxNext').is(':visible')) {
				$('.todo-ajax-submission-navigation-next').fadeIn();
				$('#cboxNext').hide();
			}

			// Show todo navigation left if the fancybox left control is visible
			if ($('#cboxPrevious').is(':visible')) {
				$('.todo-ajax-submission-navigation-prev').fadeIn();
				$('#cboxPrevious').hide();
			}

			// Init lightbox embed if it exists
			if (typeof(elgg.tgsembed) != 'undefined') {
				elgg.tgsembed.initLightbox();
			}

			// Init drag and drop input
			elgg.todo.submission.initDragDrop();

			// If filtrate is loaded, modify close behavior
			if (elgg.filtrate) {
				// // Unbind events
				// $('#fancybox-close').unbind();
				// $(document).unbind('keydown.fb');

				// var manualCleanup = function(event) {
				// 	// Fix tinymce (ckeditor???) control for submission text field
				// 	// var id = $('.todo-ajax-submission').find('.elgg-input-longtext').attr('id');

				// 	// if (id && typeof(tinyMCE) !== 'undefined') {
				// 	// 	tinyMCE.EditorManager.execCommand('mceRemoveControl', false, id);
				// 	// }
				// 	// $.colorbox.close();
				// 	// history.pushState({'type': 'manual_close', 'initialURL': elgg.filtrate.lastURL}, '', elgg.filtrate.lastURL);
				// }
				
				// // Use our manual cleanup function for the close button and escape key event
				// $('#fancybox-close').bind('click', manualCleanup);
				// $(document).bind('keydown.fb', function(event) {
				// 	manualCleanup(event);
				// 	event.preventDefault();
				// });
			}
		},
		'onClosed': function() {
			// Prevent scrolling
			var scr = document.body.scrollTop;
			document.body.scrollTop = scr;
			elgg.todo.submission.last_grade = null;

			// Regular non-filtrate on close handling
			if (!elgg.filtrate) {
				var url = window.location.href.substring(0, window.location.href.indexOf('?'));
				history.replaceState({}, '',url);
			}
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
			$(e.originalEvent.delegatedEvent.target).addClass('todo-submission-dropzone-drag');
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
	
	var comment = $("#" + comment_id).val();

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

					// Clear comment text field
					var id = $('#todo-submission-annotations').find('.elgg-input-longtext').attr('id');
					$('#' + id).val('');

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
				
				// Clear comment text field (ckeditor???)
				// var id = $('#todo-submission-annotations').find('.elgg-input-longtext').attr('id');
				// if (typeof(tinyMCE) !== 'undefined') {
				// 	tinyMCE.get(id).setContent('')
				// } else {
				// 	$('#' + id).val('');
				// }
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
					$('#' + id).val('');

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
						$_parent = $(this).parent();

						$(this).remove();

						if ($_parent.is(':empty')) {
							$_parent.remove();
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
		//var submission_guid = window.location.hash.replace('#submission:', '');	
	
		// Make sure we have an integer
		if ((parseFloat(submission_guid) == parseInt(submission_guid)) && !isNaN(submission_guid)) {

			// Loop over each todo submission lightbox (if any)
			$('a.todo-submission-lightbox').each(function() {

				// If we have a submission on the page matching the given hash
				if($(this).attr('href').indexOf('guid=' + submission_guid) != -1) {

					// Trigger the click
					$.colorbox.init();
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

		// Prepend the container with the new contents
		$('#todo-submission-annotations').prepend($ul);
		
		var $annotation_list = $('#todo-submission-annotations ul.elgg-annotation-list');
	}

	// Append to the annotation list
	$annotation_list.append($new_comment);

	// Slide it in
	$new_comment.slideDown();
}

// Submit Grade Action
elgg.todo.submission.submitGrade = function(event) {
	var submission_grade = $('form.elgg-form-submission-grade').find('input[name=submission_grade]').val();
	var submission_guid = $('form.elgg-form-submission-grade').find('input[name=submission_guid]').val();

	// If we have a value, set the grade!
	if (submission_grade && submission_grade != elgg.todo.submission.last_grade) {
			var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
			if (numberRegex.test(submission_grade)) {
				// Post comment with a regular elgg action
				elgg.action('submission/grade', {
					data: {
						submission_guid: submission_guid, 
						submission_grade: submission_grade,
					}, 
					success: function(data) {
						// Check for bad status 
						if (data.status == -1) {
							// Error
						} else {
							elgg.trigger_hook('submission', 'graded', data);
							elgg.todo.submission.last_grade = submission_grade;
						}
					}
				});	
			} else {
				elgg.register_error(elgg.echo('todo:error:gradevalue'));
			}

	}

	event.preventDefault();
}

elgg.todo.submission.graded_handler = function(hook, type, params, options) {
	// Grade info
	var owner_guid = params.output.owner_guid;
	var todo_guid = params.output.todo_guid;
	var grade = params.output.grade;
	var grade_total = params.output.grade_total;
	
	// Friendly grade string
	var grade_string = grade + '/' + grade_total;
	
	// Set grade in status table
	$(document).find('#assignee-grade-' + owner_guid).html(grade_string);
	
	// Set grade in grade book
	$(document).find('#submission-grade-' + todo_guid + '-' + owner_guid).html(grade_string);
}

// Interrupt filtrate popstate for fancybox handling
elgg.todo.submission.popstate = function(hook, type, params, value) {
	// Check for null state or any other state besides todo submission fancybox
	if (params.state == null || params.state.type != 'todo_submission_fancybox') {
		// If we've got a fancybox open, close it and replace state
		if ($('a.todo-submission-lightbox[data-open="true"]').length) {
			$.colorbox.close();
			history.replaceState({'intialURL': elgg.filtrate.lastURL, 'type': 'todo_submission_fancybox_newstate'},'', elgg.filtrate.lastURL);
			return false;
		}
	}

	// Check for proper state and fancybox
	if (params.state && params.state.type == 'todo_submission_fancybox') {
		var $fancy = $('a.todo-submission-lightbox[href$=' + params.state.guid + ']');

		// If we've got a fancybox open, close it
		if (window.location.href == params.state.url && $fancy.attr('data-open') == true) {
			$fancy.attr('data-open', false);
			$.colorbox.close();
			history.replaceState({'intialURL': initialURL, 'type': 'todo_submission_fancybox_newstate'},'', params.state.initialURL);
			return false;
		}
		
		// If we're popping state, open up the fancybox
		$fancy.click();
	
		history.replaceState({'intialURL': initialURL, 'type': 'todo_submission_fancybox_newstate'},'', params.state.initialURL);
		return false;
	}
	return value;
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

elgg.register_hook_handler('submission', 'graded', elgg.todo.submission.graded_handler);
elgg.register_hook_handler('init', 'system', elgg.todo.submission.init);
elgg.register_hook_handler('popstate', 'filtrate', elgg.todo.submission.popstate);