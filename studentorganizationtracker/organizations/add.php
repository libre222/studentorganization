<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['org_name']);
    $desc = trim($_POST['description']);
    $date = $_POST['date_created'];

    if (empty($name) || empty($date)) {
        $error = 'Organization name and date are required.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO organizations (org_name, description, date_created) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $desc, $date);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Organization added successfully!';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Failed to add organization.';
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Add Organization</h2>
        <p>Create a new student organization.</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <span class="material-symbols-outlined"></span> Back to Organizations
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
                    <input type="text" name="org_name"
                           placeholder="e.g. Computer Science Society"
                           value="<?php echo htmlspecialchars($_POST['org_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" placeholder="Brief description of the organization..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Date Created <span class="req">*</span></label>
                    <input type="date" name="date_created"
                           value="<?php echo htmlspecialchars($_POST['date_created'] ?? date('Y-m-d')); ?>" required>
                </div>

            </div>

            <div class="form-actions" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined"></span> Add Organization
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
