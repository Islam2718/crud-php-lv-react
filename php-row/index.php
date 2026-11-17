<?php
require_once __DIR__ . '/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        http_response_code(400);
        exit('Invalid product.');
    }

    $statement = db()->prepare('DELETE FROM products WHERE id = ?');
    $statement->execute([$id]);
    redirect('product.php?message=' . urlencode('Product deleted.'));
}

$statement = db()->query('SELECT id, name, description, price, stock FROM products ORDER BY id DESC');
$products = $statement->fetchAll();
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">CRUD</a>
        </div>
    </nav>

    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 mb-0">Products</h1>
            <a class="btn btn-primary" href="product_form.php">Add New Product</a>
        </div>

        <?php if (is_string($message) && $message !== ''): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Price</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products === []): ?>
                            <tr>
                                <td class="text-center py-4" colspan="5">No products found. Add your first product.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= e($product['name']) ?></td>
                                    <td><?= e($product['description']) ?></td>
                                    <td>$<?= number_format((float) $product['price'], 2) ?></td>
                                    <td><?= (int) $product['stock'] ?></td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="product_form.php?id=<?= (int) $product['id'] ?>">Edit</a>
                                        <form class="d-inline" method="post" action="product.php" onsubmit="return confirm('Delete this product?')">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>