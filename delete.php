  GNU nano 7.2                    delete.php                              
<?php include 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit();

