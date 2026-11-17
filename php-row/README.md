# PHP Shop Lab

## Run with XAMPP

1. Start Apache and MySQL from the XAMPP Control Panel.
2. Open phpMyAdmin, select **Import**, and import `database.sql`. This creates the `shop_db` database, its product/order tables, and sample products.
3. Open `http://localhost/crud-php-lv-react/php-row/`.

The default connection is `127.0.0.1`, database `shop_db`, user `root`, and an empty password (the usual local XAMPP defaults). Configure `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` in the PHP environment to use different credentials.

Use **Manage Products** to add, edit, or delete products. The storefront keeps the cart in the PHP session, and checkout saves the order and its items while reducing stock in a database transaction.

This is a local lab project: product management has no login, and checkout records orders but does not process payments. Do not expose it as-is to the public internet.
