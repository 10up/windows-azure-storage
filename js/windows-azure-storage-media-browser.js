/*!  - v4.1.1
 * https://github.com/lukaspawlik/windows-azure-storage#readme
 * Copyright (c) 2018; */
window.wp = window.wp || {};
(function ( $, window, _ ) {
  $( document ).ready( function () {
    var media = wp.media;
    if ( undefined === media ) {
      return;
    }
    var curManageFrame = media.view.MediaFrame.Manage,
      curAttachmentsBrowser = media.view.AttachmentsBrowser,
      curToolbar = media.view.Toolbar,
      curAttachments = media.view.Attachments,
      curQueryPrototype = _.clone( media.model.Query.prototype ),
      curAttachmentModelPrototype = _.clone( media.model.Attachment.prototype );

    media.view.Attachment.Details = media.view.Attachment.Details.extend( {
      deleteAttachment: function ( event ) {
        event.preventDefault();
        if ( window.confirm( media.view.l10n.warnDelete ) ) {
          this.model.destroy();
        }
      }
    } );
    _.extend( media.model.Attachment.prototype, {
      sync: function ( method, model, options ) {
        if ( _.isUndefined( this.id ) ) {
          return $.Deferred().rejectWith( this ).promise();
        }
        if ( 'delete' === method ) {
          options = options || {};
          options.context = this;
          options.data = _.extend( options.data || {}, {
            action: 'delete-azure-blob',
            id: this.id,
            _wpnonce: this.get( 'nonces' )[ 'delete' ]
          } );

          return wp.media.ajax( options ).done( function () {
            this.destroyed = true;
          } ).fail( function () {
            this.destroyed = false;
          } );
        } else {
          return curAttachmentModelPrototype.sync.apply( this, arguments );
        }
      },

      destroy: function ( options ) {
        options = options || {};
        options.wait = true;
        return curAttachmentModelPrototype.destroy.apply( this, [ options ] );
      }
    } );

    _.extend( media.model.Query.prototype, {
      sync: function ( method, model, options ) {
        var args;
        if ( 'read' === method ) {
          options = options || {};
          options.context = this;
          options.data = _.extend( options.data || {}, {
            action: 'query-azure-attachments',
          } );

          args = _.clone( this.args );
          if ( -1 !== args.posts_per_page ) {
            args.paged = Math.round( this.length / args.posts_per_page ) + 1;
          }
          options.data.query = args;
          return wp.media.ajax( options );
        } else {
          return curQueryPrototype.sync.apply( this, arguments );
        }
      }
    } );

    media.view.Attachments = media.view.Attachments.extend( {
      initialize: function () {
        this.options.refreshThreshold = 3;
        curAttachments.prototype.initialize.apply( this, arguments );
      }
    } );

    media.view.AttachmentsBrowser = media.view.AttachmentsBrowser.extend( {
      initialize: function () {
        this.options.sidebar = true;
        this.options.scrollElement = undefined;
        curAttachmentsBrowser.prototype.initialize.apply( this, arguments );
      }
    } );
    media.view.MediaFrame.Manage = media.view.MediaFrame.Manage.extend( {
      initialize: function () {
        this.options.uploader = false;
        this.options.mode = [ 'grid' ];
        this.options.multiple = false;
        curManageFrame.prototype.initialize.apply( this, arguments );
      },

      bindRegionModeHandlers: function () {
        curManageFrame.prototype.bindRegionModeHandlers.apply( this, arguments );
        this.on( 'toolbar:create', this.createSelectToolbar, this );
      },

      createSelectToolbar: function ( toolbar, options ) {
        options = options || this.options.button || {};
        options.controller = this;
        options.text = _wpMediaGridSettings.l10n.selectText;
        toolbar.view = new wp.media.view.Toolbar.Select( options );
      }
    } );
    media.view.Toolbar = media.view.Toolbar.extend( {
      set: function ( id, view, options ) {
        if ( _.contains( [ 'filters', 'libraryViewSwitcher', 'dateFilterLabel', 'dateFilter', 'selectModeToggleButton', 'deleteSelectedButton' ], id ) ) {
          return;
        } else {
          return curToolbar.prototype.set.apply( this, arguments );
        }
      }
    } );
    var frame = media( {
      frame: 'manage',
      container: $( '#windows-azure-storage-browser' ),
      library: {},
    } ).open();
    frame.on( 'select', function () {
      var selectedImage = frame.state().get( 'selection' ).first().toJSON();
      window.parent.wp.azureFrame.trigger( 'azure:selected', selectedImage );
    } );
  } );

})( jQuery, window, _ );
