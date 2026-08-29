# Contributing to Ecommerce API

Thank you for contributing to the Ecommerce API.

This project follows a structured development workflow focused on maintainability, code quality, security, and clear version control practices.

## Development Workflow

All changes must follow this workflow:

```text
Issue
  ↓
Branch
  ↓
Development
  ↓
Tests
  ↓
Pull Request
  ↓
Code Review
  ↓
Merge into main
```

The `main` branch represents the stable version of the project and should not be modified directly.

## Issues

Every change should be associated with an issue.

Issues should be small, focused, and have a clearly defined objective.

A single issue should preferably address one feature, fix, refactor, test, or configuration change.

Examples:

* Create product model
* Add product migration
* Implement product repository
* Add product creation endpoint
* Add product validation
* Add product feature tests
* Fix product authorization

Avoid combining unrelated tasks into the same issue.

## Branches

Create every branch from the latest `main` branch.

Branch names should follow this convention:

```text
feature/<description>
fix/<description>
hotfix/<description>
refactor/<description>
test/<description>
chore/<description>
```

Examples:

```text
feature/product-crud
feature/user-authentication
fix/order-total-calculation
refactor/product-service
test/order-creation
chore/configure-ci
```

Do not create a new feature branch from another feature branch.

The expected structure is:

```text
main
 ├── feature/*
 ├── fix/*
 ├── refactor/*
 ├── test/*
 └── chore/*
```

## Commits

Commits should be small and describe a single logical change.

Use imperative language when possible.

Examples:

```text
Add product migration
Implement product creation endpoint
Add product validation
Fix order total calculation
Add authentication tests
Configure CI pipeline
```

Avoid vague commit messages such as:

```text
update
changes
fix stuff
new code
adjustments
```

## Pull Requests

All changes must be submitted through a Pull Request targeting `main`.

A Pull Request should:

* Reference the related issue.
* Clearly describe the implemented changes.
* Contain only changes related to the issue.
* Include tests when applicable.
* Pass the automated checks.
* Not introduce unnecessary changes.

Pull Requests should remain small enough to be reviewed efficiently.

Recommended structure:

```text
## Summary

Brief description of the changes.

## Changes

- Change 1
- Change 2
- Change 3

## Tests

Describe the tests performed.

## Related Issue

Closes #<issue-number>
```

## Code Quality

Code should prioritize:

* Readability
* Maintainability
* Separation of responsibilities
* Clear naming
* Reusability where appropriate
* Laravel conventions
* Explicit business rules
* Secure handling of user and application data

Avoid unnecessary abstraction.

Do not introduce a pattern, service, repository, or dependency solely because it is available. Architectural decisions should solve an actual problem in the application.

## API Development

The project is a RESTful API.

API endpoints should follow consistent conventions for:

* HTTP methods
* HTTP status codes
* Request validation
* Response structures
* Error handling
* Authentication
* Authorization
* Resource representation

The API is versioned under:

```text
/api/v1
```

Example:

```text
GET    /api/v1/products
POST   /api/v1/products
GET    /api/v1/products/{id}
PUT    /api/v1/products/{id}
DELETE /api/v1/products/{id}
```

## Validation

User-provided data must be validated before reaching business logic.

Validation rules should be kept separate from controllers whenever appropriate.

Invalid requests should return consistent HTTP responses.

## Authentication and Authorization

Protected resources must require authentication when appropriate.

Authentication and authorization are separate concerns:

```text
Authentication
    ↓
Who is the user?

Authorization
    ↓
What is the user allowed to do?
```

Never rely exclusively on frontend restrictions for authorization.

Authorization must be enforced by the API.

## Security

Security is a first-class concern of this project.

Contributors must avoid:

* Exposing sensitive data
* Trusting client-provided authorization
* Mass assignment vulnerabilities
* SQL injection
* Improper input handling
* Missing authorization checks
* Excessive data exposure
* Unnecessary information in API responses
* Hardcoded credentials or secrets

Sensitive information must never be committed to the repository.

Use environment variables for secrets and environment-specific configuration.

## Testing

New functionality should include automated tests whenever applicable.

Tests should cover:

* Expected behavior
* Validation failures
* Authorization failures
* Authentication requirements
* Important business rules
* Edge cases

Before opening a Pull Request, run the project's test suite locally.

A Pull Request should not be considered ready if the tests are failing.

## Database Changes

Database structure must be modified through Laravel migrations.

Do not manually modify the production database schema.

Migrations should be:

* Reproducible
* Reversible when practical
* Focused on a single logical change
* Consistent with the application's models and business rules

Database constraints should be used when they provide meaningful data integrity guarantees.

## Business Logic

Business rules should not be unnecessarily concentrated inside controllers.

Controllers should primarily coordinate the HTTP layer.

Complex business operations should be isolated into appropriate application components.

For example:

```text
HTTP Request
     ↓
Controller
     ↓
Validation
     ↓
Business Logic
     ↓
Persistence
     ↓
HTTP Response
```

The exact implementation should depend on the complexity of the feature.

## Error Handling

Errors should produce predictable API responses.

Do not expose:

* Stack traces
* Database credentials
* Internal implementation details
* Sensitive application information

Production responses must provide enough information for the client to understand the failure without exposing unnecessary internal details.

## Dependencies

Before adding a new dependency, consider:

* Whether Laravel already provides the required functionality.
* Whether the dependency is actively maintained.
* Its security history.
* Its impact on application complexity.
* Whether the dependency is actually necessary.

Avoid unnecessary dependencies.

## Environment Configuration

Never commit the `.env` file.

Use `.env.example` to document required environment variables.

If a new environment variable is introduced, update `.env.example` accordingly.

## Review Checklist

Before opening a Pull Request, verify:

* [ ] The branch was created from the latest `main`.
* [ ] The issue is clearly addressed.
* [ ] No unrelated changes were included.
* [ ] Code follows Laravel conventions.
* [ ] Validation is implemented where necessary.
* [ ] Authorization is enforced where necessary.
* [ ] Sensitive data is not exposed.
* [ ] Tests were added or updated when applicable.
* [ ] All tests pass locally.
* [ ] Environment variables are documented in `.env.example`.
* [ ] The Pull Request clearly describes the changes.

## Contribution Principle

The goal is not simply to make the code work.

Contributions should leave the codebase in a state that is easier to understand, test, maintain, and secure.

Small changes, clear responsibilities, automated tests, and disciplined version control are preferred over large and difficult-to-review changes.
