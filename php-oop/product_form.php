<?php

require_once __DIR__ . '/connection.php';

$products = new Product(database());
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$product = $id > 0 ? $products->find($id) : null;
if ($id > 0 && !$product) {
    http_response_code(404);
    exit('Product not found.');
}

$name = (string) ($product['name'] ?? '');
$description = (string) ($product['description'] ?? '');
$price = (string) ($product['price'] ?? '');
$stock = (string) ($product['stock'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $name = trim((string) ($_POST['name'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $price = trim((string) ($_POST['price'] ?? ''));
    $stock = trim((string) ($_POST['stock'] ?? ''));
    $validPrice = filter_var($price, FILTER_VALIDATE_FLOAT);
    $validStock = filter_var($stock, FILTER_VALIDATE_INT);

    if ($name === '' || strlen($name) > 150) {
        $errors[] = 'Enter a product name no longer than 150 characters.';
    }
    if ($validPrice === false || $validPrice < 0 || $validPrice > 99999999.99) {
        $errors[] = 'Enter a valid price between 0 and 99,999,999.99.';
    }
    if ($validStock === false || $validStock < 0) {
        $errors[] = 'Enter a valid stock quantity of 0 or more.';
    }

    if ($errors === []) {
        if ($id > 0) {
            $products->update($id, $name, $description, (float) $validPrice, (int) $validStock);
        } else {
            $products->create($name, $description, (float) $validPrice, (int) $validStock);
        }
        redirect('product.php?message=' . urlencode($id > 0 ? 'Product updated.' : 'Product added.'));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $id > 0 ? 'Edit' : 'Add' ?> Product</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="header">
    <a class="logo" href="index.php">eShop</a>
    <nav><a href="index.php">Store</a><a href="product.php">Manage Products</a><a href="checkout.php">Cart (<?= cartCount() ?>)</a></nav>
  </header>
  <main class="container">
    <h1><?= $id > 0 ? 'Edit' : 'Add' ?> Product</h1>
    <?php if ($errors !== []): ?>
      <div class="notice error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <form class="panel product-form" action="product_form.php<?= $id > 0 ? '?id=' . $id : '' ?>" method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
      <label for="name">Product Name</label>
      <input id="name" name="name" maxlength="150" value="<?= e($name) ?>" required>
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="4"><?= e($description) ?></textarea>
      <label for="price">Price</label>
      <input id="price" name="price" type="number" min="0" max="99999999.99" step="0.01" value="<?= e($price) ?>" required>
      <label for="stock">Stock</label>
      <input id="stock" name="stock" type="number" min="0" step="1" value="<?= e($stock) ?>" required>
      <button class="button" type="submit">Save Product</button>
      <a href="product.php">Cancel</a>
    </form>
  </main>
</body>
</html>
