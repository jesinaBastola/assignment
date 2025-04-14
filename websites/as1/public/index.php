<?php
session_start();
require_once 'db_connect.php';

// Debug: Log session data
error_log("Session Data in index.php: " . print_r($_SESSION, true));

// Prevent caching
header('Cache-Control: no-cache, must-revalidate');

$pdo = getDbConnection();
$stmt = $pdo->query("SELECT a.*, c.name AS category_name FROM auction a JOIN category c ON a.categoryId = c.id WHERE a.endDate > NOW() ORDER BY a.endDate ASC LIMIT 10");
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categoriesStmt = $pdo->query("SELECT * FROM category");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
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
                            echo '<li><a href="manageCategory.php">Manage Categories</a></li>';
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
                        echo '<li><a href="manageCategory.php">Manage Categories</a></li>';
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
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <h1>Latest Car Listings</h1>
        <ul class="carList">
            <?php foreach ($auctions as $auction): ?>
                <li>
                    <img src="<?php echo htmlspecialchars($auction['image'] ?: 'car.png'); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>">
                    <article>
                        <h1><?php echo htmlspecialchars($auction['title']); ?></h1>
                        <h3><?php echo htmlspecialchars($auction['category_name']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($auction['description'], 0, 200)) . '...'; ?></p>
                        <?php
                        $bidStmt = $pdo->prepare("SELECT MAX(amount) as max_bid FROM bid WHERE auctionId = ?");
                        $bidStmt->execute([$auction['id']]);
                        $maxBid = $bidStmt->fetch(PDO::FETCH_ASSOC)['max_bid'];
                        ?>
                        <p class="price">Current bid: £<?php echo number_format($maxBid ?: 0, 2); ?></p>
                        <a href="auction.php?id=<?php echo $auction['id']; ?>" class="more auctionLink">More >></a>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
        <footer>
            © Carbuy 2024
        </footer>
    </main>
</body>
</html>