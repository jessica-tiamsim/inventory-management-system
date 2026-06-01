# Inventory Management System - JavaScript Implementation

## Prerequisites

Ensure you have the following software installed:

* Node.js (v16 or higher recommended)
* MySQL Server

## Setup Instructions

### 1. Install Dependencies

Navigate to the `/js-implementation` directory and install the necessary Node.js packages:

```bash
npm install

```

### 2. Environment Configuration

Create a `.env` file in the root of the `/js-implementation` directory. This file stores local credentials and must not be committed to Git. A template file named `.env.example` is provided to serve as a guide.

To create your `.env` file, copy the template:

```bash
cp .env.example .env

```

Open the newly created `.env` file and configure the values to match your local system environment:

```text
DB_HOST=localhost
DB_USER=your_database_username
DB_PASS=your_database_password
DB_NAME=inventory_db
PORT=3000

```

### 3. Database Initialization

Set up your schema and sample dataset inside your MySQL database instance:

1. Create a relational database schema matching your `DB_NAME` configuration value (e.g., `inventory_db`).
2. Run the pure Data Definition Language (DDL) script to create tables (such as users, categories, products, and stock movements):
```bash
mysql -u your_database_username -p inventory_db < schema/database.sql

```


3. Run the accompanying seed script to populate default administrative/staff profiles and mock lookup entries:
```bash
mysql -u your_database_username -p inventory_db < schema/seeds.sql

```



### 4. Running the Application

To start up the local server lifecycle process, execute:

```bash
npm start

```

Once initialized, the web dashboard interface will be accessible inside your browser at `http://localhost:3000` (or your chosen environment configuration port).

## Testing Framework

To run the automated command-line suite comprising validation boundaries, access rule checks, and transaction tracking evaluations, call:

```bash
npm test

```

## Mandatory Architecture and Security Standards

Developers working within this repository folder must adhere strictly to these security criteria:

* **Parameterized Queries:** Direct string concatenation of user-provided entries into raw SQL query instructions is strictly prohibited. All queries must utilize placeholders and parameterized statement blocks to neutralize SQL injection vulnerabilities.

* **Centralized Schema Validation:** Do not use scattered, ad-hoc inline input validation inside raw route controllers. All body payloads must filter cleanly through dedicated schema middleware blocks using structured validation layers (such as `express-validator`, `Joi`, or `Zod`).

* **XSS Mitigation & Output Escaping:** Every dynamic string asset rendered onto server-side HTML view components must be strictly escaped using native engine sanitization formats (`<%= %>` rules) before presentation.

* **Production File Logging:** Standard console-logging commands must not be used to dump terminal data streams in live scopes. All unexpected runtime exceptions and error traces must route into localized physical log collections (`logs/app.log`).