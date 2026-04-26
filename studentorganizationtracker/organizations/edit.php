<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'];

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT * FROM organizations WHERE org_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['org_name'];
    $desc = $_POST['description'];
    $date = $_POST['date_created'];

    $stmt = $conn->prepare("UPDATE organizations SET org_name=?, description=?, date_created=? WHERE org_id=?");
    $stmt->bind_param("sssi", $name, $desc, $date, $id);
    $stmt->execute();

    header('Location: index.php');
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2>Edit Organization</h2>

    <form method="POST">
        <input type="text" name="org_name" value="<?php echo $data['org_name']; ?>" required><br><br>
        <textarea name="description"><?php echo $data['description']; ?></textarea><br><br>
        <input type="date" name="date_created" value="<?php echo $data['date_created']; ?>" required><br><br>

        <button type="submit">Update</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
