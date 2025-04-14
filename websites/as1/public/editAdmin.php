<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$pdo = getDbConnection();
$adminId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$stmt = $pdo->prepare("SELECT * FROM user WHERE id = ? AND is_admin = 1");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    header('Location: manageAdmins.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $admin['password'];
    
    $stmt = $pdo->prepare("UPDATE user SET email = ?, name = ?, password = ? WHERE id = ?");
    $stmt->execute([$email, $name, $password, $adminId]);
    header('Location: manageAdmins.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Edit Admin</title>
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
            <?php foreach ($categories as $category): ?>
                <li><a class="categoryLink" href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <img src="banners/1.jpg" alt="Banner" />
    <main>
        <h1>Edit Admin</h1>
        <form action="editAdmin.php?id=<?php echo $adminId; ?>" method="POST">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required />
            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password" />
            <label>Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required />
            <input type="submit" value="Update Admin" />
        </form>
        <footer>
            &copy; Carbuy 2024
        </footer>
    </main>
</body>
</html>