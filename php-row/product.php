<?php

require_once __DIR__ . '/connection.php';

$products = new Product(database());
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        http_response_code(400);
        exit('Invalid product.');
    }

    $products->delete($id);
    redirect('product.php?message=' . urlencode('Product deleted.'));
}

$items = $products->all();
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Products</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="header">
    <a class="logo" href="index.php">eShop</a>
    <nav><a href="index.php">Store</a><a href="product.php">Manage Products</a><a href="checkout.php">Cart (<?= cartCount() ?>)</a></nav>
  </header>
  <main class="container">
    <div class="page-heading"><h1>Products</h1><a class="button" href="product_form.php">Add New Product</a></div>
    <?php if ($message !== ''): ?><p class="notice"><?= e($message) ?></p><?php endif; ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Description</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if ($items === []): ?>
            <tr><td colspan="5">No products yet.</td></tr>
          <?php else: ?>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= e($item['name']) ?></td>
                <td><?= e($item['description']) ?></td>
                <td>$<?= number_format((float) $item['price'], 2) ?></td>
                <td><?= (int) $item['stock'] ?></td>
                <td class="actions">
                  <a href="product_form.php?id=<?= (int) $item['id'] ?>">Edit</a>
                  <form action="product.php" method="post" onsubmit="return confirm('Delete this product?')">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                    <button class="link-button" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
