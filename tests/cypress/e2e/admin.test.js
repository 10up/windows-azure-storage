describe("Admin can login and open dashboard", () => {
  beforeEach(() => {
    cy.login();
  });

  it("Open dashboard", () => {
    cy.visit(`/wp-admin`);
    cy.get("h1").should("contain", "Dashboard");
  });

  it("Activate Azure Storage and deactivate it back", () => {
    cy.deactivatePlugin("windows-azure-storage");
    cy.activatePlugin("windows-azure-storage");
  });
});
