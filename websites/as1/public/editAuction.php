<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDbConnection();
$auctionId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$stmt = $pdo->prepare("SELECT * FROM auction WHERE id = ? AND userId = ?");
$stmt->execute([$auctionId, $_SESSION['user_id']]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    header('Location: index.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $pdo->prepare("DELETE FROM auction WHERE id = ?")->execute([$auctionId]);
        header('Location: index.php');
        exit;
    }
    
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $categoryId = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
    $endDate = $_POST['endDate'];
    $imagePath = $auction['image'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imagePath = 'images/auctions/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/' . $imagePath);
    }
    
    $stmt = $pdo->prepare("UPDATE auction SET title = ?, description = ?, categoryId = ?, endDate = ?, image = ? WHERE id = ?");
    $stmt->execute([$title, $description, $categoryId, $endDate, $imagePath, $auctionId]);
    header('Location: auction.php?id=' . $auctionId);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Edit Auction</title>
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
        <h1>Edit Auction</h1>
        <form action="editAuction.php?id=<?php echo $auctionId; ?>" method="POST" enctype="multipart/form-data">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($auction['title']); ?>" required />
            <label>Description</label>
            <textarea name="description" required><?php echo htmlspecialchars($auction['description']); ?></textarea>
            <label>Category</label>
            <select name="category" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $auction['categoryId'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label>End Date</label>
            <input type="datetime-local" name="endDate" value="<?php echo date('Y-m-d\TH:i', strtotime($auction['endDate'])); ?>" required />
            <label>Image</label>
            <input type="file" name="image" accept="image/*" />
            <input type="submit" value="Update Auction" />
            <input type="submit" name="delete" value="Delete Auction" onclick="return confirm('Are you sure?');" />
        </form>
        <footer>
            &copy; Carbuy 2024
        </footer>
    </main>
</body>
</html>