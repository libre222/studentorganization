<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn     = getDBConnection();
$students = $conn->query("SELECT student_id, student_name FROM students ORDER BY student_name ASC");
$orgs     = $conn->query("SELECT org_id, org_name FROM organizations ORDER BY org_name ASC");

$student_list = $students ? $students->fetch_all(MYSQLI_ASSOC) : [];
$org_list     = $orgs     ? $orgs->fetch_all(MYSQLI_ASSOC)     : [];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id  = $_POST['student_id'];
    $org_id      = $_POST['org_id'];
    $role        = trim($_POST['role']);
    $date_joined = $_POST['date_joined'];

    if (empty($student_id) || empty($org_id)) {
        $error = 'Please select both a student and an organization.';
    } else {
        // Check for duplicate
        $check = $conn->prepare("SELECT membership_id FROM memberships WHERE student_id = ? AND org_id = ?");
        $check->bind_param("ii", $student_id, $org_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $error = 'This student is already a member of that organization.';
        } else {
            $stmt = $conn->prepare("INSERT INTO memberships (student_id, org_id, role, date_joined) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $student_id, $org_id, $role, $date_joined);

            if ($stmt->execute()) {
                $_SESSION['success'] = 'Membership added successfully!';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Failed to add membership.';
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>
<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Add Membership</h2>
        <p>Enroll a student into an organization.</p>
    </div>
    <a href="index.php" class="btn btn-secondary">← Back to Memberships</a>
</div>

<?php if ($error): ?>
    <div class="alert error">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if (empty($student_list) || empty($org_list)): ?>
    <div class="alert warning">
        ⚠️ You need at least one <a href="../students/add.php" style="font-weight:600;color:inherit;text-decoration:underline;">student</a>
        and one <a href="../organizations/add.php" style="font-weight:600;color:inherit;text-decoration:underline;">organization</a> before adding memberships.
    </div>
<?php else: ?>

<div class="card" style="max-width:680px;">
    <div class="card-header">
        <h3>🤝 Membership Details</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-grid">

                <div class="form-group full-width">
                    <label>Student <span class="req">*</span></label>
                    <select name="student_id" required>
                        <option value="">— Select Student —</option>
                        <?php foreach ($student_list as $s): ?>
                            <option value="<?php echo $s['student_id']; ?>"
                                <?php echo (($_POST['student_id'] ?? '') == $s['student_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['student_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Organization <span class="req">*</span></label>
                    <select name="org_id" required>
                        <option value="">— Select Organization —</option>
                        <?php foreach ($org_list as $o): ?>
                            <option value="<?php echo $o['org_id']; ?>"
                                <?php echo (($_POST['org_id'] ?? '') == $o['org_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($o['org_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Role / Position</label>
                    <select name="role">
                        <?php
                        $roles = ['Member', 'Officer', 'Secretary', 'Treasurer', 'President'];
                        foreach ($roles as $r):
                        ?>
                            <option value="<?php echo $r; ?>"
                                <?php echo (($_POST['role'] ?? 'Member') === $r) ? 'selected' : ''; ?>>
                                <?php echo $r; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date Joined</label>
                    <input type="date" name="date_joined"
                           value="<?php echo htmlspecialchars($_POST['date_joined'] ?? date('Y-m-d')); ?>">
                </div>

            </div>

            <div class="form-actions" style="margin-top:20px;">
                <button type="submit" class="btn btn-primary">✅ Add Membership</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>
