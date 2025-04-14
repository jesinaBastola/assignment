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
        if ($user['is_admin']) {
            header('Location: adminCategories.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

$pdo = getDbConnection();
$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - User Login</title>
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
                    if (isset($_SESSION['user_id'])) {
                        echo '<li><a href="addAuction.php">Add Auction</a></li>';
                        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                            echo '<li><a href="adminCategories.php">Manage Categories</a></li>';
                            echo '<li><a href="manageAdmins.php">Manage Admins</a></li>';
                        }
                        echo '<li><a href="logout.php">Logout</a></li>';
                    } else {
                        echo '<li><a href="userLogin.php">User Login</a></li>';
                        echo '<li><a href="adminLogin.php">Admin Login</a></li>';
                        echo '<li><a href="register.php">Register</a></li>';
                    }
                }
                echo "<li><a class='categoryLink' href='category.php?id={$category['id']}'>{$category['name']}</a></li>";
            }
            if ($insertPosition === -1) {
                if (isset($_SESSION['user_id'])) {
                    echo '<li><a href="addAuction.php">Add Auction</a></li>';
                    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                        echo '<li><a href="adminCategories.php">Manage Categories</a></li>';
                        echo '<li><a href="manageAdmins.php">Manage Admins</a></li>';
                    }
                    echo '<li><a href="logout.php">Logout</a></li>';
                } else {
                    echo '<li><a href="userLogin.php">User Login</a></li>';
                    echo '<li><a href="adminLogin.php">Admin Login</a></li>';
                    echo '<li><a href="register.php">Register</a></li>';
                }
            }
            ?>
        </ul>
    </nav>
    <img src="banners/1.jpg" alt="Banner" />
    <main>
        <h1>User Login</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="userLogin.php" method="POST">
            <label>Email</label>
            <input type="email" name="email" required />
            <label>Password</label>
            <input type="password" name="password" required />
            <input type="submit" name="submit" value="Login" />
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        <p>Are you an admin? <a href="adminLogin.php">Login as Admin</a>.</p>
        <footer>
            © Carbuy 2024
        </footer>
    </main>
</body>
</html>