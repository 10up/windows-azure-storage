/*!  - v4.4.1
 * https://github.com/10up/windows-azure-storage#readme
 * Copyright (c) 2024; */
var replaceMedia = function(attachmentID) {
  var mediaUploader;
  
  if (mediaUploader) {
    mediaUploader.open();
    return;
  }

  mediaUploader = wp.media.frames.file_frame = wp.media({
    title: AzureMediaReplaceObject.i18n.title,
    frame: 'select',
    library: {
      search: null,
      type: 'application/pdf',
    },
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
      success: function(result) {
        if ( result.is_image ) {
          var full_path = result.attachment_data.url.replace(result.file_name, '');
          var replace_thumb = result.attachment_data.sizes.medium.file;
          var medium_image = full_path + replace_thumb;
          jQuery('.media-modal').find('.details-image').attr('src', result.attachment_data.url);
          jQuery('.attachments-wrapper').find('li[data-id="'+ result.ID +'"]').find('img').first().attr('src', medium_image);
        }

        jQuery('.attachments-wrapper').find('li[data-id="'+ result.old_ID +'"]').remove();
      }
    });
    
  });
  
  mediaUploader.on('open', function(){
    var tab = document.getElementById('menu-item-upload');
    var browse = document.getElementById('menu-item-browse');
    browse.style.display = 'none';
    tab.click();
  });

  mediaUploader.open();
};
