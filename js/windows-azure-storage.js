/* global windowsAzureStorageSettings:false, tinyMCE:true */

function createContainer( url ) {
	var htmlForm = document.getElementsByName( 'SettingsForm' )[0];
	var action = document.getElementsByName( 'action' )[0];
	if ( typeof action !== 'undefined' ) {
		action.name = 'action2';
	}

	htmlForm.action = url;
	htmlForm.submit();
}

function onContainerSelectionChanged( show ) {
	var htmlForm = document.getElementsByName( 'SettingsForm' )[0];
	var divCreateContainer = document.getElementById( 'divCreateContainer' );
	if ( '__newContainer__' === htmlForm.elements['default_azure_storage_account_container_name'].value ) {
		divCreateContainer.style.display = 'block';
		htmlForm.elements['submitButton'].disabled = true;

	} else {
		if ( show ) {
			divCreateContainer.style.display = 'block';
		} else {
			divCreateContainer.style.display = 'none';
		}

		htmlForm.elements['submitButton'].disabled = false;
	}
}

function onUpload_ContainerSelectionChanged() {
	var htmlForm = document.getElementsByName( 'UploadNewFileForm' )[0];
	if ( '__newContainer__' === htmlForm.elements['selected_container'].value ) {
		htmlForm.elements['uploadFileTag'].disabled = true;
		htmlForm.elements['uploadFileTag'].style.background = 'gray';
		htmlForm.elements['uploadFileName'].disabled = true;
		htmlForm.elements['uploadFileName'].style.background = 'gray';
		htmlForm.elements['createContainer'].style.visibility = 'visible';
		document.getElementById( 'lblNewContainer' ).style.display = 'block';
		htmlForm.elements['action'].value = 'create';
		htmlForm.elements['submit'].value = windowsAzureStorageSettings.l10n.create;
	} else {
		htmlForm.elements['uploadFileTag'].disabled = false;
		htmlForm.elements['uploadFileTag'].style.background = 'white';
		htmlForm.elements['uploadFileName'].disabled = false;
		htmlForm.elements['uploadFileName'].style.background = 'white';
		htmlForm.elements['createContainer'].style.visibility = 'hidden';
		document.getElementById( 'lblNewContainer' ).style.display = 'none';
		htmlForm.elements['action'].value = 'upload';
		htmlForm.elements['submit'].value = windowsAzureStorageSettings.l10n.upload;
	}
}

function insertImageTag( imgURL, containsSignature ) {
	var imageFullURL = imgURL;
	var imageTag = '';
	if ( containsSignature ) {
		var st = imageFullURL.indexOf( '?st=' );
		if ( - 1 !== st ) {
			imgURL = imageFullURL.substr( 0, st );
		}
	}

	var dot = imgURL.lastIndexOf( '.' );
	if ( - 1 === dot ) {
		imageTag = '<a href="' + imgURL + '">' + imgURL + '</a> ';
	}
	else {
		var extension = imgURL.substr( dot, imgURL.length );
		switch ( extension.toLowerCase() ) {
			case '.jpg':
			case '.jpeg':
			case '.gif':
			case '.bmp':
			case '.png':
			case '.tiff':
				imageTag = '<img src="' + imageFullURL + '"/> ';
				break;

			default:
				imageTag = '<a href="' + imageFullURL + '">' + imageFullURL + '</a> ';
				break;
		}
	}

	var win = window.dialogArguments || opener || parent || top;

	if ( typeof win.send_to_editor === 'function' ) {
		win.send_to_editor( imageTag );
		if ( typeof win.tb_remove === 'function' ) {
			win.tb_remove();
		}

		return false;
	}

	tinyMCE = win.tinyMCE;
	if ( typeof tinyMCE !== 'undefined' && tinyMCE.getInstanceById( 'content' ) ) {
		tinyMCE.selectedInstance.getWin().focus();
		tinyMCE.execCommand( 'mceInsertContent', false, imageTag );
	}
	else {
		win.edInsertContent( win.edCanvas, imageTag );
	}

	return false;
}
