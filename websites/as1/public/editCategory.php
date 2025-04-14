<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: adminLogin.php');
    exit;
}

$pdo = getDbConnection();
$category = null;
$error = null;

// Fetch the category to edit
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $error = "Invalid category ID.";
} else {
    $categoryId = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM category WHERE id = ?");
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$category) {
        $error = "Category not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    
    if (!$name || trim($name) === '') {
        $error = "Category name is required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE category SET name = ? WHERE id = ?");
            $stmt->execute([$name, $categoryId]);
            header('Location: manageCategory.php');
            exit;
        } catch (PDOException $e) {
            $error = "Failed to update category: " . $e->getMessage();
        }
    }
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Edit Category</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
    <header>
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>
        <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Search for a car" />
            <input type="submit" name="submit" value="Search" />
        </form>
    </header>
    <nav>
        <ul>
            <?php
            $insertPosition = -1;
            foreach ($categories as $index => $category) {
                if (strtolower($category['name']) === '4x4') {
                    $insertPosition = $index;
                    break;
                }
            }
            foreach ($categories as $index => $category) {
                if ($insertPosition !== -1 && $index === $insertPosition) {
                    echo '<li><a href="addAuction.php">Add Auction</a></li>';
                    echo '<li><a href="manageCategory.php">Manage Categories</a></li>';
                    echo '<li><a href="manageAdmins.php">Manage Admins</a></li>';
                    echo '<li><a href="logout.php">Logout</a></li>';
                }
                echo "<li><a class='categoryLink' href='category.php?id={$category['id']}'>{$category['name']}</a></li>";
            }
            if ($insertPosition === -1) {
                echo '<li><a href="addAuction.php">Add Auction</a></li>';
                echo '<li><a href="manageCategory.php">Manage Categories</a></li>';
                echo '<li><a href="manageAdmins.php">Manage Admins</a></li>';
                echo '<li><a href="logout.php">Logout</a></li>';
            }
            ?>
        </ul>
    </nav>
    <img src="banners/1.jpg" alt="Banner" />
    <main>
        <h1>Edit Category</h1>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (!$category): ?>
            <p>Category not found.</p>
        <?php else: ?>
            <form action="editCategory.php?id=<?php echo $category['id']; ?>" method="POST">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required />
                <input type="submit" value="Update Category" />
            </form>
        <?php endif; ?>
        <p><a href="manageCategory.php">Back to Manage Categories</a></p>
        <footer>
            © Carbuy 2024
        </footer>
    </main>
</body>
</html>