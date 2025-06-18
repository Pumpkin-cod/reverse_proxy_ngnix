<?php include 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = $pdo->prepare("UPDATE users SET name=?, email=? WHERE id=?");
    $stmt->execute([$name, $email, $id]);
    header("Location: index.php");
    exit();
}
?>
<form method="POST">
    <h2>Edit User</h2>
    Name: <input type="text" name="name" value="<?= $user['name'] ?>" req>
    Email: <input type="email" name="email" value="<?= $user['email'] ?>">
    <button type="submit">Update</button>
</form>

