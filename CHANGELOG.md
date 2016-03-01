
3.0.1 / 2016-03-01
==================

  * Fix upload nonce checks
  * Media: fix the AYS checks on browse

3.0.0 / 2016-01-28
==================

  * filter 'srcset' in WordPress 4.4
  * let the browser validate CNAME input
  * don't double up on containers in URLs
  * rename constant to reference "legacy" media
  * fix broken settings check for Azure override
  * fix uploads permissions check
  * use checked function in settings form
  * check nonces for search form
  * tell PHPCS that we're checking nonces differently
  * escape (allthethings)
  * PHPCS fixes: validate nonces for every request
  * PHPCS fixes: escaping HTML output
  * escape all variables before printing
  * add logging to empty try...catch blocks
  * add logging to empty try...catch blocks
  * remove complex HTML element building
  * better output escaping in dialogs
  * Formatting: Yoda conditions
  * escape attributes in elements
  * escape inline JS in form input attributes
  * build strings sanely and escape them
  * use selected() to note selected options
  * don't break lines inside a tag attribute
  * move variable declaration higher up
  * remove empty catch{} and its try{}
  * reformat settings private container warning
  * format code for readability
  * escape HTML output
  * check nonces early
  * verify settings nonce when creating a container
  * fix copy/paste error on settings page
  * use predefined constants for paths
  * misc PHPCS fixes
  * escape plugin activation error message
  * build the editor button element securely
  * strict equality and Yoda conditions
  * group constant definitions
  * remove invalid namespaces
  * remove overzealous escaping
  * remove invalid assignment
  * only show search results after a search
  * sanitize and escape fields in the upload form
  * verify nonces in upload form
  * check nonce and permissions before uploading
  * add permissions check for 'upload'
  * sanitize post fields before using them
  * add $post_id to the upload page
  * don't use REQUEST_URI for form actions
  * use esc_url for form actions
  * escape error messages sanely
  * add TODOs inline
  * create and check nonces for container creation
  * put conditional check in the right place
  * fix logic error left behind from testing
  * don't dismiss final error messages
  * don't die() if error thrown during search
  * sanitize $_POST values before use
  * check permissions for creating containers
  * only show settings page to authorized users
  * use submit_button to generate markup
  * inline docs updates for media tabs
  * update the search form nonce name
  * catch the search nonce failure
  * check for a valid nonce from search
  * clean up the conditional check for search
  * use absolute URLs for form actions
  * copy $post_id to the search iframe
  * add a11y helpers to forms
  * update the search form's action check
  * use submit_button to generate markup
  * don't show the delete link to everyone
  * note to self about what this block is for
  * remove_query_arg needs to modify a URL
  * order matters when submitting a form
  * remove broken inline styles from forms
  * secure the container select element
  * use semantic markup in forms
  * update nonce name for browsing
  * use core attributes when possible for markup
  * remove broken inline styles
  * add notice to delete_all_blobs error
  * notify user if they can't delete a blob
  * match actions to check with form values
  * use the new constant to build URLs
  * pass absolute URLs to form actions
  * add new constant: MSFT_AZURE_PLUGIN_MEDIA_URL
  * add some TODO items
  * don't escape a single-char string
  * don't offer the delete all button to everyone
  * check permissions before deleting all blobs
  * Put the new 'check_action_permissions' in the right class
  * new utility method 'check_action_permissions'
  * rename nonce and submit names
  * add context to the DeleteAllBlobsForm submit button
  * remove extra spacing from deleteAllBlobs
  * remove extraneous hidden form input
  * udpate delete all blobs nonce
  * don't submit via JS when a "submit" will do
  * update legacy media loader tabs filter
  * update tabs filters for media
  * remove empty anchor caused by typo
  * remove remnants of 'deleteBlob'
  * provide type hints for variables
  * remove vestiges of the 'deleteBlob' query arg
  * validate the nonce before deleting a file
  * sanitize the deleteBlob URL
  * get the post ID of the originating editor
  * formatting updates in dialog screen
  * fix the broken form action DeleteAllBlobsForm
  * do something with the exception
  * ask nicely to try again
  * check before deleting large temp files
  * fix typo in $blocks variable
  * format deletion error message properly
  * blobs are actually ListBlobsResult
  * deprecate WindowsAzureStorage::blobExists()
  * deprecate WindowsAzureStorage::blobExists()
  * create methods to check for containers and blobs
  * combine loops in putBlockBlob()
  * '$blocks' isn't a real BlockList
  * add inline docs to putBlockBlob for typing
  * Fixes typo in variable name in putBlockBlob()
  * update docs for putBlockBlob()
  * Add nonces to each of the Azure media forms
  * Cleanup Azure forms to use lowercase actions
  * Avoid double trailing slashes in blob URLs
  * Remove unnecessary 'containsSignature' variable from Azure
  * Use lowercase action names in Azure upload form
  * Localize submit button values in Azure JS
  * Use Yoda conditional checks for strings
  * Remove the settings page check for enqueing JS
  * Use __newContainer__ for option value in Azure
  * Add wp_kses() to the alternate CNAME notice in Azure
  * Run Azure settings translation through wp_kses()
  * Provide sanity check before returning generated URL
  * Build Azure URLs with sprintf()
  * Revert translated strings in Azure JS
  * Remove commented-out code from Azure JS
  * Set the includes path for the plugin
  * Don't pass a boolean as a string
  * Use Yoda conditions and strict comparisons
  * Whitespace: align array items
  * Add CNAME notice for Azure plugin settings
  * Don't use strings when a constant is defined
  * Use 'self' for the class name inside a class
  * Avoid double-slashing URLs
  * Use 'self' for static methods called inside a class
  * Filter the blob URL protocol
  * Rename getStorageUrlPrefix to get_storage_url_base
  * Use WP standards for variable names
  * Docblock update for getStorageUrlPrefix()
  * Require strict equality, Yoda does
  * Make make return strings easier to read
  * Use 'https' for Azure blob URLs
  * Update include_path for PEAR modules to load
  * Move script block into external file in Azure
  * JSHint fixes: tinyMCE is a global
  * JSHint fixes: single quote strings in JS
  * JS whitespace changes for Azure plugin
  * Fix HTML div closing tag in the settings form
  * Specify file-specific JSHint globals in the file
  * Use the localized object in the Azure plugin JS
  * Add the stock WordPress JSHint ruleset
  * Enqueue the plugin script before localizing
  * Localize the JS for the Windows Azure Storage plugin
  * Enqueue the plugin style directly without prior registration
  * Updated docs for windows_azure_storage_dialog_scripts
  * Add a stylesheet to Windows Azure Storage plugin
  * Enqueue JS and CSS instead of directly printing
  * Set constants for use throughout the plugin
  * Use absolute URLs for including assets
  * Whitespace change on a missed file
  * Use the correct textdomain for the plugin
  * Set a sane limit on DB queries
  * Check for valid data after a query
  * Sanitize DB queries with prepare()
  * Don't require more than you need
  * Use full paths to include files
  * Automated whitespace changes
  * Remove closing PHP tags
  * Sanitize inputs
  * Escape output
