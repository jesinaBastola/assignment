<?php
session_start();
require_once 'db_connect.php';

// Debug: Log session data
error_log("Session Data in manageCategory.php: " . print_r($_SESSION, true));

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    error_log("Redirecting to adminLogin.php - user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set') . ", is_admin: " . (isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : 'not set'));
    header('Location: adminLogin.php');
    exit;
}

// Prevent caching
header('Cache-Control: no-cache, must-revalidate');

$pdo = getDbConnection();
$error = null;

// Fetch all categories
try {
    $categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
    error_log("Number of categories fetched in manageCategory.php: " . count($categories));
} catch (PDOException $e) {
    $error = "Failed to load categories: " . $e->getMessage();
    $categories = [];
    error_log("Database error in manageCategory.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Manage Categories</title>
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
                    echo '<li><a href="manageAdmins.php">Manage Admins</a></li>';
                    echo '<li><a href="logout.php">Logout</a></li>';
                }
                echo "<li><a class='categoryLink' href='category.php?id={$category['id']}'>{$category['name']}</a></li>";
            }
            if ($insertPosition === -1) {
                echo '<li><a href="addAuction.php">Add Auction</a></li>';
                echo '<li><a href="manageAdmins.php">Manage Admins</a></li>';
                echo '<li><a href="logout.php">Logout</a></li>';
            }
            ?>
        </ul>
    </nav>
    <img src="banners/1.jpg" alt="Banner" />
    <main>
        <h1>Manage Categories</h1>
        <p><a href="addCategory.php">Add New Category</a></p>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (empty($categories)): ?>
            <p>No categories found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td>
                                <a href="editCategory.php?id=<?php echo $category['id']; ?>">Edit</a> |
                                <a href="deleteCategory.php?id=<?php echo $category['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <footer>
            © Carbuy 2024
        </footer>
    </main>
</body>
</html>