# Ecommerce API

REST API for an e-commerce platform built with Laravel.

> **Project status:** Study / Practice / Portfolio
>
> This repository is intentionally focused on backend engineering, API architecture, testing, security, observability, and performance. It is not currently intended for production deployment.

## About

This project was developed as a structured backend exercise, evolving from a basic API foundation into a more complete e-commerce backend.

The project follows a layered approach:

```text
Route
  ↓
Controller
  ↓
Form Request
  ↓
Service
  ↓
Model
  ↓
Database
```

The goal is to keep HTTP concerns in controllers and requests, business rules in services, and persistence in Eloquent models and migrations.

## Main Features

### API Foundation
- API versioning with `/api/v1`
- Standardized API responses
- Centralized exception handling
- API health check
- Feature and unit testing conventions
- Database conventions
- CI validation

### Authentication & Authorization
- Registration
- Login
- Logout
- Current authenticated user
- Email verification
- Sanctum authentication
- Role-based access control
- Policies / authorization rules

### Catalog
- Categories
- Products
- Product/category association
- Product inventory
- Product search
- Category filtering
- Price filtering
- Stock filtering
- Configurable sorting
- Pagination

### Shopping
- Shopping cart
- Cart items
- Quantity updates
- Cart ownership/isolation
- Checkout
- Order creation
- Order items
- Order status handling

### Payments
- Payment creation
- Payment status management
- Payment gateway abstraction
- Fake payment gateway for development/testing
- Payment webhooks
- Webhook idempotency
- Order confirmation after successful payment

### Customer Experience
- Multiple customer addresses
- Address ownership/isolation
- Default address management
- Order history
- Order pagination and status filtering
- Customer profile retrieval/update
- Wishlist persistence
- Wishlist item management
- Wishlist uniqueness and ownership

### Security & Hardening
- Authentication rate limiting
- General API rate limiting
- Payment rate limiting
- Checkout rate limiting
- Webhook rate limiting
- Input validation and sanitization
- Authorization auditing
- Mass assignment protection
- Security headers
- API security testing

### Observability & Performance
- Logging
- API monitoring
- Caching
- Query optimization
- Performance testing

## Current Roadmap

```text
PHASE 1 — API FOUNDATION
#1  API Foundation
#2  API Versioning
#3  Standardize API Responses
#4  API Exception Handling                         ✅

PHASE 2 — DEVELOPMENT INFRASTRUCTURE
#5  API Health Check
#6  Testing Conventions
#7  Database & Model Conventions
#8  CI Pipeline                                    ✅

PHASE 3 — AUTHENTICATION & AUTHORIZATION
#9  Authentication Foundation — Sanctum           ✅
#10 User Model & Authentication                    ✅
#11 Email Verification                             ✅
#12 Authorization / RBAC                          ✅

PHASE 4 — CATALOG
#13 Categories                                    ✅
#14 Products                                      ✅
#15 Product Categories                             ✅
#16 Product Inventory                              ✅
#17 Product Filtering & Sorting                    ✅

PHASE 5 — SHOPPING
#19 Shopping Cart                                 ✅
#20 Cart Items                                    ✅
#21 Checkout                                      ✅
#22 Order Creation, Items & Status                ✅

PHASE 6 — PAYMENT SYSTEM
#25 Payment System                                ✅

PHASE 7 — CUSTOMER EXPERIENCE
#29 Customer Experience                           ✅

PHASE 8 — SECURITY & HARDENING
#33 Rate Limiting                                 ✅
#34 Input Validation & Sanitization                ⏳
#35 Authorization Audit                            ⬜
#36 Mass Assignment Protection                     ⬜
#37 Security Headers                               ⬜
#38 API Security Testing                           ⬜

PHASE 9 — OBSERVABILITY & PERFORMANCE
#39 Logging                                        ⬜
#40 API Monitoring                                 ⬜
#41 Caching                                        ⬜
#42 Query Optimization                             ⬜
#43 Performance Testing                            ⬜

PHASE 10 — PRODUCTION
#44 Docker                                        ⬜
#45 Production Configuration                       ⬜
#46 CI/CD                                         ⬜
#47 Deployment                                    ⬜
#48 Production Documentation                      ⬜
```

## Tech Stack

- PHP
- Laravel
- Laravel Sanctum
- Eloquent ORM
- PHPUnit
- Laravel Pint
- SQLite for local/testing workflows
- Git / GitHub
- GitHub Actions

## Testing

The project uses automated Feature and Unit tests.

Run the full test suite:

```bash
php artisan test
```

Run Pint validation:

```bash
vendor/bin/pint --test
```

Focused tests can be executed by file, for example:

```bash
php artisan test tests/Feature/RateLimitTest.php
```

## Development Workflow

The project follows a feature-oriented Git workflow:

```text
Feature branch
    ↓
Implementation
    ↓
Tests
    ↓
Pint / CI
    ↓
Pull Request
    ↓
Code Review
    ↓
Merge
```

The objective is to keep changes isolated, tested, and reviewable.

## Project Goals

This repository is primarily intended to demonstrate:

- REST API design
- Laravel architecture
- Business-rule separation
- Database modeling
- Authentication and authorization
- Automated testing
- API security
- Rate limiting
- Payment workflows
- Webhooks and idempotency
- Observability
- Performance analysis

The project is expected to end at Phase 9 for the current study/portfolio scope. Phase 10 remains in the roadmap as a production-oriented extension and is not currently part of the main delivery goal.

## Status

Current development is in:

**PHASE 8 — SECURITY & HARDENING**

Current issue:

**#34 — Input Validation & Sanitization**
