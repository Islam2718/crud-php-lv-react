<?php
// db connection 
$conn = mysqli_connect("localhost", "root", "", "shop_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<?php
// get all records 
$result = $conn->query("SELECT * FROM products");
?>

<?php
// save or update record using the same form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);

    if (!empty($_POST['id'])) {
        $id = (int) $_POST['id'];
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
        $stmt->bind_param("sdi", $name, $price, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
        $stmt->bind_param("sd", $name, $price);
    }

    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>

<?php
// delete record 
if (isset($_GET['deleteId'])) {
    $id = $_GET['deleteId'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>
<?php
// get record for edit 
if (isset($_GET['editId'])) {
    $id = $_GET['editId'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editResult = $stmt->get_result();
    $editableRow = $editResult->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <main>
        <h1 style="color: #024;">Simple Crud Application</h1>
        <!-- <hr/> -->

        <form action="" method="post">
            <input type="text" name="name" placeholder="Product Name" value="<?php echo isset($editableRow['name']) ? htmlspecialchars($editableRow['name']) : ''; ?>" />
            <input type="text" name="price" placeholder="Product Price" value="<?php echo isset($editableRow['price']) ? htmlspecialchars($editableRow['price']) : ''; ?>" />
            <input type="hidden" name="id" value="<?php echo isset($editableRow['id']) ? (int) $editableRow['id'] : ''; ?>" />
            <input type="submit" value="<?php echo isset($editableRow) ? 'Update' : 'Save'; ?>" />
        </form>
        <!-- product table  -->
        <table>
            <tr>
                <th>SL</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td style="width: 200px;">
                        <a href="?editId=<?php echo $row['id']; ?>">Edit</a> &nbsp;
                        <a href="?deleteId=<?php echo $row['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </main>
</body>

</html>