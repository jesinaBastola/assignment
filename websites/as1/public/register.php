<?php
session_start();
require_once 'db_connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    // Replace FILTER_SANITIZE_STRING with FILTER_SANITIZE_SPECIAL_CHARS
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validate inputs
    if (!$email || !$name || !$password) {
        $error = "Please fill in all fields correctly.";
    } else {
        $pdo = getDbConnection();
        try {
            $stmt = $pdo->prepare("INSERT INTO user (email, password, name) VALUES (?, ?, ?)");
            $stmt->execute([$email, $password, $name]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['is_admin'] = 0; // Default to non-admin
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}

// Fetch categories for the navigation bar
$pdo = getDbConnection();
$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Register</title>
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
                        if ($_SESSION['is_admin']) {
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
                    if ($_SESSION['is_admin']) {
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
        <h1>Register</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <label>Email</label>
            <input type="email" name="email" required />
            <label>Password</label>
            <input type="password" name="password" required />
            <label>Name</label>
            <input type="text" name="name" required />
            <input type="submit" value="Register" />
        </form>
        <p>Already have an account? <a href="userLogin.php">Login here</a>.</p>
        <footer>
            © Carbuy 2024
        </footer>
    </main>
</body>
</html>