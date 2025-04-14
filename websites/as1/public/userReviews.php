<?php
session_start();
require_once 'db_connect.php';

$pdo = getDbConnection();
$userId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$stmt = $pdo->prepare("SELECT name FROM user WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: index.php');
    exit;
}

$reviewStmt = $pdo->prepare("SELECT r.*, u.name AS reviewer_name FROM review r JOIN user u ON r.reviewerId = u.id WHERE r.userId = ?");
$reviewStmt->execute([$userId]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Reviews for <?php echo htmlspecialchars($user['name']); ?></title>
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
        <h1>Reviews for <?php echo htmlspecialchars($user['name']); ?></h1>
        <ul class="reviews">
            <?php foreach ($reviews as $review): ?>
                <li>
                    <strong><?php echo htmlspecialchars($review['reviewer_name']); ?> said </strong>
                    <?php echo htmlspecialchars($review['reviewText']); ?>
                    <em><?php echo date('d/m/Y', strtotime($review['createdAt'])); ?></em>
                </li>
            <?php endforeach; ?>
        </ul>
        <footer>
            &copy; Carbuy 2024
        </footer>
    </main>
</body>
</html>