# Phase 2 – Admin Platform Foundation (Filament Core)

## Goal
Build the internal backoffice platform that will serve as the control center for the entire system.

Filament is the only admin interface. No secondary admin panels are allowed.

---

## Step 1 – Install Filament Admin Panel

### Objective
Install and configure Filament as the central backoffice UI.

### Instructions for LLM
1. Install Filament in the Laravel project.
2. Publish Filament configuration.
3. Configure admin route at `/admin`.
4. Create initial admin user seeder.
5. Verify that login and dashboard load correctly.

---

## Step 2 – Install Roles & Permissions System

### Objective
Implement internal access control for administrators and operators.

### Instructions for LLM
1. Install `spatie/laravel-permission`.
2. Publish migrations and run them.
3. Create base roles:
   - admin
   - operator
   - marketing
4. Assign `admin` role to the first user.

---

## Step 3 – Secure Admin Panel Access

### Objective
Restrict admin access to internal users only.

### Instructions for LLM
1. Protect Filament routes by role-based middleware.
2. Deny access to unauthorized users.
3. Ensure only users with valid roles may access `/admin`.

---

## Step 4 – Create Domain-Driven Folder Structure

### Objective
Prepare clean architecture for scalable domain logic.

### Instructions for LLM
1. Create base folders:

app/
 ├── Domain/
 │    ├── Subscriber/
 │    ├── Content/
 │    ├── Commerce/
 │    └── Funnel/
 ├── Application/
 └── Infrastructure/

2. Ensure all new business logic is placed inside these layers.

---

## Step 5 – Implement Subscriber Core Model

### Objective
Create the central marketing identity entity.

### Instructions for LLM
1. Create `subscribers` table with fields:
   - id
   - email (unique)
   - status
   - source
   - timestamps
2. Create `Subscriber` model.
3. Create service/repository layer for subscriber operations.
4. Enforce email uniqueness.

---

## Step 6 – Implement Page Domain Model

### Objective
Create the base CMS entity for website content.

### Instructions for LLM
1. Create `pages` table with fields:
   - id
   - slug (unique)
   - title
   - content (json)
   - seo_meta (json)
   - status
   - published_at
2. Create `Page` model with proper casts.

---

## Step 7 – Implement Product Domain Model

### Objective
Create the lightweight commerce product entity.

### Instructions for LLM
1. Create `products` table with fields:
   - id
   - name
   - price
   - active
2. Create `Product` model.
3. Ensure correct money casting.

---

## Step 8 – Create Filament Resources

### Objective
Expose core entities in the admin UI.

### Instructions for LLM
1. Create Filament Resources for:
   - Subscriber
   - Page
   - Product
2. Each resource must support:
   - list
   - create
   - edit
   - delete

---

## Expected Outcome

After completing Phase 2:

- A secure Filament backoffice is available.
- Core domain entities exist.
- Admin users can manage subscribers, pages, and products.
- Architecture is ready for CMS, forms, and automation modules.
