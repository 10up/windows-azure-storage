describe('Configure admin settings', () => {
    before(() => {
        cy.login();
    });

    it( 'Open settings page', () => {
        cy.visit('wp-admin/options-general.php?page=windows-azure-storage-plugin-options');
    });

    it( 'Enter settings and save', () => {
        cy.get('input[name="azure_storage_account_name"]').clear().type(Cypress.env('MICROSOFT_AZURE_ACCOUNT_NAME'));
        cy.get('input[name="azure_storage_account_primary_access_key"]').clear().type(Cypress.env('MICROSOFT_AZURE_ACCOUNT_KEY'));
        cy.get('input[name="azure-submit-button"]').click();
    });

    it( 'Select container after page reload', () => {
        cy.get('select[name="default_azure_storage_account_container_name"]').select(Cypress.env('MICROSOFT_AZURE_CONTAINER'));
        cy.get('input[name="azure_storage_use_for_default_upload"]').click();
        cy.get('input[name="azure-submit-button"]').click();
    });

    it( 'Upload file and verify location', () => {
        cy.uploadMedia( 'tests/cypress/fixtures/image.jpg' );
        cy.visit('wp-admin/upload.php');
        cy.get('.thumbnail img').should('have.attr', 'src').should('include','azurethumbs');
    });
})
