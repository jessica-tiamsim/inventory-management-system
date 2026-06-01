# 🔷 PRISM | Enterprise Inventory Management System

PRISM is a custom-built, lightweight Inventory Management System engineered with native, object-oriented PHP and MySQL. 

Designed without the overhead of heavy external frameworks, PRISM relies on a strict Model-View-Controller (MVC) architecture to deliver blazing-fast execution, absolute data integrity, and a premium, unified user experience.

## ✨ Key Features

* **🛡️ Immutable Stock Ledger:** All inventory changes (Stock-In, Stock-Out, Adjustments) are recorded as permanent, timestamped historical events. Strict backend validation completely prevents negative stock anomalies.
* **📦 Centralized Product Catalog:** Track SKUs, manage categories, monitor unit costs versus market pricing, and set low-stock reorder thresholds.
* **👥 Role-Based Access Control (RBAC):** Native authorization masking separating `Admin` and `Staff` privileges. Dynamic middleware protects sensitive routes and administrative layout elements.
* **🎨 Unified UI Design System:** A cohesive, standalone card-based interface powered by CSS variables, featuring non-intrusive `prism-overlay` modals for fluid CRUD operations.
* **🔒 Enterprise-Grade Security:** * **SQLi Defense:** 100% PDO Prepared Statements.
  * **XSS Mitigation:** Universal `htmlspecialchars()` output serialization.
  * **Cryptography:** Native Blowfish-based bcrypt hashing via `password_hash()`.
  * **Session Integrity:** Server-side UNIX timestamp tracking mapped to a dedicated `sessions_table`.

---

## 🛠️ System Architecture (MVC)

PRISM utilizes a clean separation of concerns to ensure high maintainability:

```text
prism-inventory/
│
├── app/
│   ├── controllers/      # Core business logic and validation rules
│   ├── middlewares/      # Session and RBAC gatekeepers
│   ├── models/           # Secure database interaction streams
│   └── views/            # UI rendering and HTML layouts
│
├── public/               # Web Root (Document Root)
│   ├── assets/           # Application images and icons
│   ├── css/              # Global CSS variables and module styles
│   ├── js/               # Overlay modal logic and async events
│   └── index.php         # The master traffic router
│
├── config/               # Database and environment constants
└── db_setup.sql          # Master schema and seed generation script

🚀 Installation & Setup
PRISM is designed to run seamlessly on any standard LAMP, WAMP, or XAMPP stack.

1. Prepare the Environment
Clone or download this repository into your local server's document root (e.g., C:\xampp\htdocs\prism).

2. Database Initialization
Open your database manager (like phpMyAdmin or MySQL Workbench).

Create a new, empty database named prism_db.

Import the db_setup.sql file provided in the root directory.

This script automatically resolves foreign key dependencies, builds the 5 core tables, and generates the initial seed data for testing.

3. Configure the Application
Open your configuration file (e.g., config/database.php or config.php) and map your local database credentials:

PHP
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventory_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Update this if you are using a specific subdirectory folder!
define('BASE_URL', 'http://localhost/prism'); 
4. Launch
Ensure your Apache and MySQL modules are running. Open your browser and navigate to your BASE_URL.
