<?php
require_once __DIR__ . '/connection.php';

$id = 0;
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        http_response_code(400);
        exit('Invalid product.');
    }
}

$product = null;
if ($id > 0) {
    $statement = db()->prepare('SELECT id, name, description, price, stock FROM products WHERE id = ?');
    $statement->execute([$id]);
    $product = $statement->fetch();
    if (!$product) {
        http_response_code(404);
        exit('Product not found.');
    }
}

$name = (string) ($product['name'] ?? '');
$description = (string) ($product['description'] ?? '');
$price = (string) ($product['price'] ?? '');
$stock = (string) ($product['stock'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

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
            $statement = db()->prepare(
                'UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?'
            );
            $statement->execute([$name, $description, $validPrice, $validStock, $id]);
            $message = 'Product updated.';
        } else {
            $statement = db()->prepare(
                'INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)'
            );
            $statement->execute([$name, $description, $validPrice, $validStock]);
            $message = 'Product added.';
        }

        redirect('product.php?message=' . urlencode($message));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $id > 0 ? 'Edit' : 'Add' ?> Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="product.php">Product Admin</a>
        </div>
    </nav>

    <main class="container py-4">
        <div class="card shadow-sm mx-auto" style="max-width: 700px;">
            <div class="card-body p-4">
                <h1 class="h2 mb-4"><?= $id > 0 ? 'Edit' : 'Add' ?> Product</h1>

                <?php if ($errors !== []): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="product_form.php<?= $id > 0 ? '?id=' . $id : '' ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                    <div class="mb-3">
                        <label class="form-label" for="name">Product name</label>
                        <input class="form-control" id="name" name="name" maxlength="150" value="<?= e($name) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= e($description) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="price">Price</label>
                        <input class="form-control" id="price" name="price" type="number" min="0" max="99999999.99" step="0.01" value="<?= e($price) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="stock">Stock</label>
                        <input class="form-control" id="stock" name="stock" type="number" min="0" step="1" value="<?= e($stock) ?>" required>
                    </div>

                    <button class="btn btn-primary" type="submit">Save Product</button>
                    <a class="btn btn-outline-secondary" href="product.php">Cancel</a>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
