/**
 * Windows Azure Storage Plugin admin JavaScript handlers.
 * 10up
 * http://10up
 *
 * Copyright (c) 2016 10up
 *
 */

(function ( $, window, undefined ) {
  'use strict';
  $( document ).ready( function () {
    $( '#windows-azure-storage-media-button' ).on( 'click', function ( event ) {
      event.preventDefault();
      var elem = $( event.currentTarget ),
        editor = elem.data( 'editor' ) + '-azure',
        options = {
          frame: 'post',
          state: 'iframe:browse',
          title: wp.media.view.l10n.addMedia,
          multiple: false
        };

      elem.blur();
      wp.azureFrame = wp.media.editor.open( editor, options );
      wp.azureFrame.on( 'azure:selected', function ( selectedImage ) {
        var imgContent = '<img src="' + selectedImage.url + '" />';
        if ( !selectedImage.isImage ) {
          imgContent = '<a href="' + selectedImage.url + '">' + selectedImage.url + '</a>';
        }
        wp.media.editor.activeEditor = 'content';
        wp.media.editor.insert( imgContent );
        wp.azureFrame.close();
      } );
      wp.azureFrame.on( 'close', function () {
        wp.azureFrame.off( 'azure:selected' );
        wp.media.editor.activeEditor = 'content';
      } );
    } );
  } );

})( jQuery, this );

