<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$pdo = getDbConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    
    $stmt = $pdo->prepare("INSERT INTO user (email, password, name, is_admin) VALUES (?, ?, ?, 1)");
    $stmt->execute([$email, $password, $name]);
    header('Location: manageAdmins.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Add Admin</title>
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
        <h1>Add Admin</h1>
        <form action="addAdmin.php" method="POST">
            <label>Email</label>
            <input type="email" name="email" required />
            <label>Password</label>
            <input type="password" name="password" required />
            <label>Name</label>
            <input type="text" name="name" required />
            <input type="submit" value="Add Admin" />
        </form>
        <footer>
            &copy; Carbuy 2024
        </footer>
    </main>
</body>
</html>