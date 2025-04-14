<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: adminLogin.php'); // Updated from login.php
    exit;
}

// Prevent caching
header('Cache-Control: no-cache, must-revalidate');

$db = getDbConnection();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Updated: Replace deprecated FILTER_SANITIZE_STRING with FILTER_SANITIZE_SPECIAL_CHARS
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Validate the input
    if (!$name || trim($name) === '') {
        $error = "Category name is required.";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO category (name) VALUES (?)");
            $stmt->execute([$name]);
            header('Location: manageCategory.php'); // Updated from adminCategories.php
            exit;
        } catch (PDOException $e) {
            $error = "Failed to add category: " . $e->getMessage();
        }
    }
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Add Category</title>
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
        <h1>Add Category</h1>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="addCategory.php" method="POST">
            <label>Name</label>
            <input type="text" name="name" required />
            <input type="submit" value="Add Category" />
        </form>
        <footer>
            &copy; Carbuy 2024
        </footer>
    </main>
</body>
</html>