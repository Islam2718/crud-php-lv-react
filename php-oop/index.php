<?php
require_once __DIR__ . '/connection.php';

$products = new Product(database());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $product = $id ? $products->find($id) : null;

    if (!$product || !$quantity || $quantity < 1 || $quantity > (int) $product['stock']) {
        redirect('index.php?message=' . urlencode('That quantity is not available.'));
    }

    $cart = $_SESSION['cart'] ?? [];
    $newQuantity = (int) ($cart[$id] ?? 0) + $quantity;
    if ($newQuantity > (int) $product['stock']) {
        redirect('index.php?message=' . urlencode('There is not enough stock for that quantity.'));
    }
    $cart[$id] = $newQuantity;
    $_SESSION['cart'] = $cart;
    redirect('index.php?message=' . urlencode('Product added to your cart.'));
}

$items = $products->all();
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shop</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="header">
    <a class="logo" href="index.php">eShop</a>
    <nav><a href="index.php">Home</a><a href="product.php">Manage Products</a><a href="checkout.php">Cart (<?= cartCount() ?>)</a></nav>
  </header>
  <main class="container">
    <h1>Our Products</h1>
    <?php if ($message !== ''): ?><p class="notice"><?= e($message) ?></p><?php endif; ?>
    <?php if ($items === []): ?>
      <p>No products are available yet. <a href="product_form.php">Add a product</a> from the product manager.</p>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($items as $item): ?>
          <article class="card">
            <div class="product-image"><?= e($item['name']) ?></div>
            <h2><a href="product_details.php?id=<?= (int) $item['id'] ?>"><?= e($item['name']) ?></a></h2>
            <p><?= nl2br(e($item['description'])) ?></p>
            <p><strong>Price:</strong> $<?= number_format((float) $item['price'], 2) ?></p>
            <p><strong>Stock:</strong> <?= (int) $item['stock'] ?></p>
            <?php if ((int) $item['stock'] > 0): ?>
              <form action="index.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                <label for="quantity-<?= (int) $item['id'] ?>">Quantity</label>
                <input id="quantity-<?= (int) $item['id'] ?>" name="quantity" type="number" min="1" max="<?= (int) $item['stock'] ?>" value="1" required>
                <button class="button" type="submit">Add to Cart</button>
              </form>
            <?php else: ?>
              <p>Out of stock</p>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
