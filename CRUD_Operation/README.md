# IT Asset & Hardware Maintenance Log System

Built from the supplied DBMS proposal.

## Main pages

- `overview.php` — full dashboard/overview
- `create.php` — Create asset
- `read.php` — Read/search/filter assets
- `update.php` — Update asset by AssetID
- `delete.php` — Delete asset

## Shared files

- `config.php` — PDO/MySQL connection
- `database.sql` — database, tables, constraints, indexes and demo data
- `style.css` — responsive UI
- `script.js` — confirmation and small client-side interactions

## Database model

Users 1-to-many Assets.
Assets 1-to-many Maintenance_Log.

The three core tables follow the proposal: `Users`, `Assets`, and `Maintenance_Log`.

## XAMPP setup

1. Start Apache and MySQL.
2. Open phpMyAdmin.
3. Import `database.sql`.
4. Put this folder inside `C:\xampp\htdocs\`.
5. Visit `http://localhost/crud10/overview.php`.

The proposal specifies HTML5, CSS3, JavaScript, PHP, MySQL and XAMPP as the technology stack.
