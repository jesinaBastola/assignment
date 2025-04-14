<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = getDbConnection();
$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $categoryId = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
    $endDate = $_POST['endDate'];
    $imagePath = null;
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imagePath = 'images/auctions/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/' . $imagePath);
    }
    
    $stmt = $pdo->prepare("INSERT INTO auction (title, description, categoryId, endDate, userId, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $categoryId, $endDate, $_SESSION['user_id'], $imagePath]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions - Add Auction</title>
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
        <h1>Add Auction</h1>
        <form action="addAuction.php" method="POST" enctype="multipart/form-data">
            <label>Title</label>
            <input type="text" name="title" required />
            <label>Description</label>
            <textarea name="description" required></textarea>
            <label>Category</label>
            <select name="category" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <label>End Date</label>
            <input type="datetime-local" name="endDate" required />
            <label>Image</label>
            <input type="file" name="image" accept="image/*" />
            <input type="submit" value="Add Auction" />
        </form>
        <footer>
            &copy; Carbuy 2024
        </footer>
    </main>
</body>
</html>