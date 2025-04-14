<?php
session_start();
require_once 'db_connect.php';

$pdo = getDbConnection();
$auctionId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$stmt = $pdo->prepare("SELECT a.*, c.name AS category_name, u.name AS user_name FROM auction a JOIN category c ON a.categoryId = c.id JOIN user u ON a.userId = u.id WHERE a.id = ?");
$stmt->execute([$auctionId]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    header('Location: index.php');
    exit;
}

$bidStmt = $pdo->prepare("SELECT MAX(amount) as max_bid FROM bid WHERE auctionId = ?");
$bidStmt->execute([$auctionId]);
$maxBid = $bidStmt->fetch(PDO::FETCH_ASSOC)['max_bid'];

$reviewStmt = $pdo->prepare("SELECT r.*, u.name AS reviewer_name FROM review r JOIN user u ON r.reviewerId = u.id WHERE r.userId = ?");
$reviewStmt->execute([$auction['userId']]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $amount = filter_input(INPUT_POST, 'bid', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    if ($amount > ($maxBid ?: 0)) {
        $stmt = $pdo->prepare("INSERT INTO bid (amount, auctionId, userId) VALUES (?, ?, ?)");
        $stmt->execute([$amount, $auctionId, $_SESSION['user_id']]);
        header("Location: auction.php?id=$auctionId");
        exit;
    } else {
        $bidError = "Bid must be higher than current bid.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewText'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $reviewText = filter_input(INPUT_POST, 'reviewText', FILTER_SANITIZE_STRING);
    $stmt = $pdo->prepare("INSERT INTO review (reviewText, userId, reviewerId) VALUES (?, ?, ?)");
    $stmt->execute([$reviewText, $auction['userId'], $_SESSION['user_id']]);
    header("Location: auction.php?id=$auctionId");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - <?php echo htmlspecialchars($auction['title']); ?></title>
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
        <h1>Car Page</h1>
        <article class="car">
            <img src="<?php echo htmlspecialchars($auction['image'] ?: 'car.png'); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>">
            <section class="details">
                <h2><?php echo htmlspecialchars($auction['title']); ?></h2>
                <h3><?php echo htmlspecialchars($auction['category_name']); ?></h3>
                <p>Auction created by <a href="userReviews.php?id=<?php echo $auction['userId']; ?>"><?php echo htmlspecialchars($auction['user_name']); ?></a></p>
                <p class="price">Current bid: £<?php echo number_format($maxBid ?: 0, 2); ?></p>
                <?php
                $endDate = new DateTime($auction['endDate']);
                $now = new DateTime();
                $interval = $now->diff($endDate);
                $timeLeft = $interval->format('%h hours %i minutes');
                if ($interval->invert) {
                    $timeLeft = "Auction ended";
                }
                ?>
                <time>Time left: <?php echo $timeLeft; ?></time>
                <?php if (!$interval->invert): ?>
                    <form action="auction.php?id=<?php echo $auctionId; ?>" class="bid" method="POST">
                        <input type="text" name="bid" placeholder="Enter bid amount" />
                        <input type="submit" value="Place bid" />
                        <?php if (isset($bidError)): ?>
                            <p style="color: red;"><?php echo htmlspecialchars($bidError); ?></p>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $auction['userId']): ?>
                    <p><a href="editAuction.php?id=<?php echo $auctionId; ?>">Edit Auction</a></p>
                <?php endif; ?>
            </section>
            <section class="description">
                <p><?php echo htmlspecialchars($auction['description']); ?></p>
            </section>
            <section class="reviews">
                <h2>Reviews of <?php echo htmlspecialchars($auction['user_name']); ?></h2>
                <ul>
                    <?php foreach ($reviews as $review): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($review['reviewer_name']); ?> said </strong>
                            <?php echo htmlspecialchars($review['reviewText']); ?>
                            <em><?php echo date('d/m/Y', strtotime($review['createdAt'])); ?></em>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="auction.php?id=<?php echo $auctionId; ?>" method="POST">
                        <label>Add your review</label>
                        <textarea name="reviewText" required></textarea>
                        <input type="submit" value="Add Review" />
                    </form>
                <?php endif; ?>
            </section>
        </article>
        <footer>
            &copy; Carbuy 2024
        </footer>
    </main>
</body>
</html>