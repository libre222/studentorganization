<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'];

$conn = getDBConnection();

$orgs = $conn->query("SELECT * FROM organizations");

$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $org_id = $_POST['org_id'];
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("UPDATE events SET org_id=?, event_name=?, event_date=?, location=? WHERE event_id=?");
    $stmt->bind_param("isssi", $org_id, $name, $date, $location, $id);
    $stmt->execute();

    header('Location: index.php');
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2>Edit Event</h2>

    <form method="POST">
        <label>Organization:</label><br>
        <select name="org_id">
            <?php while($o = $orgs->fetch_assoc()): ?>
                <option value="<?php echo $o['org_id']; ?>"
                    <?php if ($o['org_id'] == $data['org_id']) echo 'selected'; ?>>
                    <?php echo $o['org_name']; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <input type="text" name="event_name" value="<?php echo $data['event_name']; ?>" required><br><br>
        <input type="date" name="event_date" value="<?php echo $data['event_date']; ?>" required><br><br>
        <input type="text" name="location" value="<?php echo $data['location']; ?>"><br><br>

        <button type="submit">Update</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
