<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: adminLogin.php');
    exit;
}

$pdo = getDbConnection();
$error = null;

// Delete the category
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manageCategory.php?error=' . urlencode('Invalid category ID.'));
    exit;
}

$categoryId = (int)$_GET['id'];
try {
    // Check if the category has associated auctions
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM auction WHERE categoryId = ?");
    $stmt->execute([$categoryId]);
    $auctionCount = $stmt->fetchColumn();

    if ($auctionCount > 0) {
        header('Location: manageCategory.php?error=' . urlencode('Cannot delete category because it has associated auctions.'));
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM category WHERE id = ?");
    $stmt->execute([$categoryId]);
    header('Location: manageCategory.php');
    exit;
} catch (PDOException $e) {
    header('Location: manageCategory.php?error=' . urlencode('Failed to delete category: ' . $e->getMessage()));
    exit;
}
?>