<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$conn = getDBConnection();

$stmt = $conn->prepare("SELECT * FROM organizations WHERE org_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    $_SESSION['error'] = 'Organization not found.';
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['org_name']);
    $desc = trim($_POST['description']);
    $date = $_POST['date_created'];

    $stmt = $conn->prepare("UPDATE organizations SET org_name=?, description=?, date_created=? WHERE org_id=?");
    $stmt->bind_param("sssi", $name, $desc, $date, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Organization updated successfully!';
        header('Location: index.php');
        exit;
    } else {
        $error = 'Update failed.';
    }
    $stmt->close();
}
$conn->close();
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Edit Organization</h2>
        <p>Update organization details below.</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <span class="material-symbols-outlined">arrow_back</span> Back to Organizations
    </a>
</div>

<?php if ($error): ?>
    <div class="alert error"><span class="material-symbols-outlined">error</span> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card" style="max-width: 680px;">
    <div class="card-header">
        <h3>Organization Details</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-grid">

                <div class="form-group full-width">
                    <label>Organization Name <span class="req">*</span></label>
                    <input type="text" name="org_name" value="<?php echo htmlspecialchars($data['org_name']); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Date Created <span class="req">*</span></label>
                    <input type="date" name="date_created" value="<?php echo htmlspecialchars($data['date_created']); ?>" required>
                </div>

            </div>

            <div class="form-actions" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined">save</span> Save Changes
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
