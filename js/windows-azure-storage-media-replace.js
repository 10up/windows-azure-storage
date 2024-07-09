/*!  - v4.4.2
 * https://github.com/10up/windows-azure-storage#readme
 * Copyright (c) 2024; */
function generateCacheVar(length) {
  var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  var result = '';
  
  for (var i = 0; i < length; i++) {
    var randomIndex = Math.floor(Math.random() * characters.length);
    result += characters[randomIndex];
  }
  
  return result;
}

var alertMessage = function(message,status,fadeOutSeconds) {
  var alert = '<div class="notice notice-' + status + ' is-dismissible"><p>' + message + '</p></div>';
  var $alert = jQuery(alert).insertBefore('.attachment-info .settings-save-status');

  // Fade out after 'fadeOutSeconds' seconds
  setTimeout(function() {
    $alert.fadeOut(function() {
      $alert.remove(); // Remove the element from the DOM after fading out
    });
  }, fadeOutSeconds * 1000);
}

var replaceMedia = function(attachmentID) {
  var mediaUploader;
  
  if (mediaUploader) {
    mediaUploader.open();
    return;
  }

  mediaUploader = wp.media.frames.file_frame = wp.media({
    title: AzureMediaReplaceObject.i18n.title,
    frame: 'select',
    button: {
      text: AzureMediaReplaceObject.i18n.replaceMediaButton,
    },
    multiple: false
  }).on('select', function(){
    var attachment = mediaUploader.state().get( 'selection' ).first().toJSON();
    jQuery.ajax({
      type: 'post',
      url: AzureMediaReplaceObject.ajaxUrl,
      data: {
        action: 'azure-storage-media-replace',
        current_attachment: attachmentID,
        nonce: AzureMediaReplaceObject.nonce,
        replace_attachment: attachment.id,
      },
      dataType: 'JSON',
      beforeSend: function() {
        jQuery('.settings-save-status').find('.spinner').addClass('is-active');
        jQuery('.edit-media-header').find('.media-modal-close').first().prop('disabled', true);
      },
      success: function(result) {
        if ( ( 'success' in result )  && ( ! result.success ) ) {
          jQuery('.settings-save-status').find('.spinner').removeClass('is-active');
          jQuery('.edit-media-header').find('.media-modal-close').first().prop('disabled', false);
          alertMessage(result.data, 'error', 5);
          return;
        }

        jQuery('.settings-save-status').find('.spinner').removeClass('is-active');
        jQuery('.edit-media-header').find('.media-modal-close').first().prop('disabled', false);

        var image = jQuery('.media-modal').find('.details-image').attr('src');
        var cacheVar = generateCacheVar(7);

        jQuery('.media-modal').find('.details-image').attr('src', image + '?v=' + cacheVar );

        var medium_img = image;
        if ( result.meta_data.sizes.medium.length ) {
          medium_img = result.meta_data.sizes.medium;
        } else if( result.meta_data.sizes.thumbnail.length ) {
          medium_img = result.meta_data.sizes.thumbnail;
        }

        jQuery('.attachments-wrapper').find('li[data-id="'+ result.id +'"]').find('img').first().attr('src', medium_img + '?v=' + cacheVar );

        jQuery('.attachments-wrapper').find('li[data-id="'+ result.old_ID +'"]').remove();
      },
      error: function(xhr, status, error) {
        jQuery('.settings-save-status').find('.spinner').removeClass('is-active');
        jQuery('.edit-media-header').find('.media-modal-close').first().prop('disabled', false);

        var alertMessage = "Error: " + error + "\nStatus: " + status;
        alertMessage(alertMessage, 'error', 5);

        console.error("AJAX request failed: ", status, error);
      }
    });
    
  });
  
  mediaUploader.on('open', function(){
    mediaUploader.reset();
    var context = jQuery(mediaUploader.el);
    var tab = context.find('#menu-item-upload');
    var browse = context.find('#menu-item-browse');
    browse.css('display', 'none');
    tab.trigger('click');
  });

  mediaUploader.open();
};
