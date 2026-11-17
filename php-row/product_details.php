<?php

require_once __DIR__ . '/connection.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = $id ? (new Product(database()))->find($id) : null;
if (!$product) {
    http_response_code(404);
    exit('Product not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($product['name']) ?> | eShop</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="header">
    <a class="logo" href="index.php">eShop</a>
    <nav><a href="index.php">Home</a><a href="product.php">Manage Products</a><a href="checkout.php">Cart (<?= cartCount() ?>)</a></nav>
  </header>
  <main class="container">
    <a href="index.php">Back to products</a>
    <section class="detail">
      <div class="product-image"><?= e($product['name']) ?></div>
      <div>
        <h1><?= e($product['name']) ?></h1>
        <p><?= nl2br(e($product['description'])) ?></p>
        <p><strong>Price:</strong> $<?= number_format((float) $product['price'], 2) ?></p>
        <p><strong>Stock:</strong> <?= (int) $product['stock'] ?> available</p>
        <?php if ((int) $product['stock'] > 0): ?>
          <form action="index.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <label for="quantity">Quantity</label>
            <input id="quantity" name="quantity" type="number" min="1" max="<?= (int) $product['stock'] ?>" value="1" required>
            <button class="button" type="submit">Add to Cart</button>
          </form>
        <?php else: ?>
          <p>Out of stock</p>
        <?php endif; ?>
      </div>
    </section>
  </main>
</body>
</html>
