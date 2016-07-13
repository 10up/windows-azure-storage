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
    $('#windows-azure-storage-media-button').on('click', function ( event ) {
      event.preventDefault();
      var elem = $( event.currentTarget ),
        editor = elem.data('editor') + '-azure',
        options = {
          frame:    'post',
          state:    'iframe:browse',
          title:    wp.media.view.l10n.addMedia,
          multiple: true
        };

      elem.blur();
      wp.media.editor.open( editor, options );
    });
  } );

})( jQuery, this );

