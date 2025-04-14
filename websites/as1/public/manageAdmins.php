<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$pdo = getDbConnection();
$admins = $pdo->query("SELECT * FROM user WHERE is_admin = 1")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    $pdo->prepare("DELETE FROM user WHERE id = ? AND is_admin = 1")->execute([$deleteId]);
    header('Location: manageAdmins.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Manage Admins</title>
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
            $categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($categories as $category): ?>
                <li><a class="categoryLink" href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <img src="banners/1.jpg" alt="Banner" />
    <main>
        <h1>Manage Admins</h1>
        <p><a href="addAdmin.php">Add New Admin</a></p>
        <ul>
            <?php foreach ($admins as $admin): ?>
                <li>
                    <?php echo htmlspecialchars($admin['name']); ?> (<?php echo htmlspecialchars($admin['email']); ?>)
                    <a href="editAdmin.php?id=<?php echo $admin['id']; ?>">Edit</a>
                    <form action="manageAdmins.php" method="POST" style="display: inline;">
                        <input type="hidden" name="delete_id" value="<?php echo $admin['id']; ?>">
                        <input type="submit" value="Delete" onclick="return confirm('Are you sure?');">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        <footer>
            &copy; Carbuy 2024
        </footer>
    </main>
</body>
</html>