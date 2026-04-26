<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn = getDBConnection();
$orgs = $conn->query("SELECT * FROM organizations");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $org_id = $_POST['org_id'];
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO events (org_id, event_name, event_date, location) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $org_id, $name, $date, $location);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2>Add Event</h2>

    <form method="POST">
        <label>Organization:</label><br>
        <select name="org_id" required>
            <?php while($o = $orgs->fetch_assoc()): ?>
                <option value="<?php echo $o['org_id']; ?>">
                    <?php echo $o['org_name']; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <input type="text" name="event_name" placeholder="Event Name" required><br><br>
        <input type="date" name="event_date" required><br><br>
        <input type="text" name="location" placeholder="Location"><br><br>

        <button type="submit">Add Event</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
