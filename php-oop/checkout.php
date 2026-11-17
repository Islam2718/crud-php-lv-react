<?php

require_once __DIR__ . '/connection.php';

$products = new Product(database());
$error = '';
$customer = [
    'name' => '',
    'email' => '',
    'address' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $action = $_POST['action'] ?? '';
    $cart = $_SESSION['cart'] ?? [];

    if ($action === 'remove') {
        $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        if ($id) {
            unset($cart[$id]);
            $_SESSION['cart'] = $cart;
        }
        redirect('checkout.php');
    }

    if ($action === 'update') {
        $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $product = $id ? $products->find($id) : null;
        if (!$product) {
            $error = 'That product is no longer available.';
        } elseif (!$quantity || $quantity < 1 || $quantity > (int) $product['stock']) {
            $error = 'Enter a quantity within the available stock.';
        } else {
            $cart[$id] = $quantity;
            $_SESSION['cart'] = $cart;
            redirect('checkout.php');
        }
    } elseif ($action === 'place_order') {
        $customer = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
        ];
        if ($customer['name'] === '' || strlen($customer['name']) > 150) {
            $error = 'Enter your name (up to 150 characters).';
        } elseif (!filter_var($customer['email'], FILTER_VALIDATE_EMAIL)
            || strlen($customer['email']) > 254) {
            $error = 'Enter a valid email address.';
        } elseif ($customer['address'] === '' || strlen($customer['address']) > 500) {
            $error = 'Enter your delivery address (up to 500 characters).';
        } else {
            try {
                $orderId = $products->placeOrder($customer, $cart);
                unset($_SESSION['cart']);
                redirect('checkout.php?order=' . $orderId);
            } catch (DomainException $exception) {
                $error = $exception->getMessage();
            }
        }
    }
}

$cart = $_SESSION['cart'] ?? [];
$cartProducts = $products->findMany(array_keys($cart));
$total = 0.0;
foreach ($cart as $id => $quantity) {
    if (isset($cartProducts[$id])) {
        $total += (float) $cartProducts[$id]['price'] * (int) $quantity;
    }
}
$orderId = filter_input(INPUT_GET, 'order', FILTER_VALIDATE_INT) ?: 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="header">
    <a class="logo" href="index.php">eShop</a>
    <nav><a href="index.php">Home</a><a href="product.php">Manage Products</a><a href="checkout.php">Cart (<?= cartCount() ?>)</a></nav>
  </header>
  <main class="container">
    <?php if ($orderId > 0): ?>
      <h1>Order received</h1>
      <p class="notice">Thank you. Your order number is <strong>#<?= $orderId ?></strong>.</p>
      <p><a class="button" href="index.php">Continue shopping</a></p>
    <?php else: ?>
      <h1>Checkout</h1>
      <?php if ($error !== ''): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
      <div class="checkout-layout">
        <form class="panel" action="checkout.php" method="post">
          <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
          <input type="hidden" name="action" value="place_order">
          <h2>Customer Information</h2>
          <label for="name">Name</label>
          <input id="name" name="name" maxlength="150" value="<?= e($customer['name']) ?>" required>
          <label for="email">Email</label>
          <input id="email" name="email" type="email" maxlength="254" value="<?= e($customer['email']) ?>" required>
          <label for="address">Delivery Address</label>
          <textarea id="address" name="address" maxlength="500" rows="3" required><?= e($customer['address']) ?></textarea>
          <p class="muted">This lab project records the order; it does not process payments.</p>
          <button class="button" type="submit" <?= $cart === [] ? 'disabled' : '' ?>>Place Order</button>
        </form>
        <aside class="panel">
          <h2>Your Cart</h2>
          <?php if ($cart === []): ?>
            <p>Your cart is empty. <a href="index.php">Browse products</a>.</p>
          <?php else: ?>
            <?php foreach ($cart as $id => $quantity): ?>
              <?php if (isset($cartProducts[$id])): $item = $cartProducts[$id]; ?>
                <div class="cart-item">
                  <div>
                    <strong><?= e($item['name']) ?></strong>
                    <span>$<?= number_format((float) $item['price'], 2) ?> each</span>
                  </div>
                  <form action="checkout.php" method="post" class="cart-actions">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="product_id" value="<?= (int) $id ?>">
                    <input aria-label="Quantity for <?= e($item['name']) ?>" name="quantity" type="number" min="1" max="<?= max(1, (int) $item['stock']) ?>" value="<?= (int) $quantity ?>" required>
                    <button class="small-button" name="action" value="update" type="submit">Update</button>
                    <button class="link-button" name="action" value="remove" type="submit">Remove</button>
                  </form>
                  <?php if ((int) $quantity > (int) $item['stock']): ?>
                    <p class="error">Only <?= (int) $item['stock'] ?> in stock. Update the quantity to continue.</p>
                  <?php endif; ?>
                  <strong>$<?= number_format((float) $item['price'] * (int) $quantity, 2) ?></strong>
                </div>
              <?php else: ?>
                <div class="cart-item">
                  <span>This product is no longer available.</span>
                  <form action="checkout.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="product_id" value="<?= (int) $id ?>">
                    <button class="link-button" name="action" value="remove" type="submit">Remove</button>
                  </form>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
            <hr>
            <p><strong>Total <span class="right">$<?= number_format($total, 2) ?></span></strong></p>
          <?php endif; ?>
        </aside>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
