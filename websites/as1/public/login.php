<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Login</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
    <header>
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>
        <div class="user-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 
                <a href="addAuction.php">Add Auction</a> | 
                <a href="logout.php">Logout</a></p>
                <?php if ($_SESSION['is_admin']): ?>
                    <p><a href="adminCategories.php">Manage Categories</a> | 
                    <a href="manageAdmins.php">Manage Admins</a></p>
                <?php endif; ?>
            <?php else: ?>
                <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
            <?php endif; ?>
        </div>
        <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Search for a car" />
            <input type="submit" name="submit" value="Search" />
        </form>
    </header>
    <nav>
        <ul>
            <?php
            $pdo = getDbConnection();
            $stmt = $pdo->query("SELECT * FROM category");
            while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li><a class='categoryLink' href='category.php?id={$category['id']}'>{$category['name']}</a></li>";
            }
            ?>
        </ul>
    </nav>
    <img src="banners/1.jpg" alt="Banner" />
    <main>
        <h1>Login</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label>Email</label>
            <input type="email" name="email" required />
            <label>Password</label>
            <input type="password" name="password" required />
            <input type="submit" name="submit" value="Login" />
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        <footer>
            © Carbuy 2024
        </footer>
    </main>
</body>
</html>