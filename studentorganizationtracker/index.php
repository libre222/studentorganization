<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$conn = getDBConnection();

/* ── Core KPIs ── */
$students   = (int)$conn->query("SELECT COUNT(*) AS n FROM students")->fetch_assoc()['n'];
$orgs       = (int)$conn->query("SELECT COUNT(*) AS n FROM organizations")->fetch_assoc()['n'];
$events     = (int)$conn->query("SELECT COUNT(*) AS n FROM events")->fetch_assoc()['n'];
$attendance = (int)$conn->query("SELECT COUNT(*) AS n FROM attendance")->fetch_assoc()['n'];

/* ── Attendance status breakdown (for doughnut chart) ── */
$att_res = $conn->query("
    SELECT status, COUNT(*) AS cnt
    FROM attendance
    GROUP BY status
");
$att_map = ['Present' => 0, 'Absent' => 0, 'Late' => 0];
while ($row = $att_res->fetch_assoc()) {
    $key = ucfirst(strtolower($row['status']));
    if (isset($att_map[$key])) $att_map[$key] = (int)$row['cnt'];
}

/* ── Members per organization (bar chart, top 6) ── */
$mem_res = $conn->query("
    SELECT o.org_name, COUNT(m.membership_id) AS total
    FROM organizations o
    LEFT JOIN memberships m ON o.org_id = m.org_id
    GROUP BY o.org_id, o.org_name
    ORDER BY total DESC
    LIMIT 6
");
$mem_labels = [];
$mem_data   = [];
while ($row = $mem_res->fetch_assoc()) {
    $mem_labels[] = strlen($row['org_name']) > 18
        ? substr($row['org_name'], 0, 16) . '…'
        : $row['org_name'];
    $mem_data[] = (int)$row['total'];
}

/* ── Events per month — last 6 months (line chart) ── */
$evt_res = $conn->query("
    SELECT DATE_FORMAT(event_date, '%b') AS mo,
           DATE_FORMAT(event_date, '%Y-%m') AS ym,
           COUNT(*) AS cnt
    FROM events
    WHERE event_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY ym, mo
    ORDER BY ym ASC
    LIMIT 6
");
$evt_labels = [];
$evt_data   = [];
while ($row = $evt_res->fetch_assoc()) {
    $evt_labels[] = $row['mo'];
    $evt_data[]   = (int)$row['cnt'];
}

/* ── Recent students (last 5) ── */
$recent_res = $conn->query("SELECT * FROM students ORDER BY student_id DESC LIMIT 5");
$recent_stu = $recent_res ? $recent_res->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();

/* Membership % per attendance status */
$total_att = max(1, array_sum($att_map));
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ╔══════════════════════════════════════╗ -->
<!-- ║          DASHBOARD HERO STRIP        ║ -->
<!-- ╚══════════════════════════════════════╝ -->
<div class="page-hd">
  <div>
    <div class="page-hd-title">Dashboard</div>
    <div class="page-hd-sub">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> — here's your system snapshot.</div>
  </div>
  <div class="page-hd-actions no-print">
    <span style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:5px;">
      <span class="material-symbols-rounded" style="font-size:15px;">calendar_today</span>
      <?php echo date('F j, Y'); ?>
    </span>
  </div>
</div>

<!-- Hero Strip: Welcome Card + KPI Stack -->
<div class="dash-hero">

  <!-- Welcome banner -->
  <div class="welcome-card">
    <div class="welcome-text">
      <div class="greeting">Good <?php
        $h = (int)date('H');
        echo $h < 12 ? 'morning' : ($h < 18 ? 'afternoon' : 'evening');
      ?></div>
      <h2><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
      <p>Your student organizations are active. You have
        <strong style="color:white;"><?php echo $events; ?> events</strong> on record and
        <strong style="color:white;"><?php echo $students; ?> students</strong> enrolled.
      </p>
      <div class="welcome-date-badge">
        <span class="material-symbols-rounded">calendar_month</span>
        <?php echo date('l, F j, Y'); ?>
      </div>
    </div>
    <div class="welcome-graphic">
      <div class="graphic-circle">
        <span class="material-symbols-rounded">school</span>
      </div>
    </div>
  </div>

  <!-- KPI stack (right column) -->
  <div class="kpi-stack">

    <a href="students/index.php" class="kpi-card" style="text-decoration:none;">
      <div class="kpi-icon indigo">
        <span class="material-symbols-rounded">group</span>
      </div>
      <div class="kpi-text">
        <div class="kpi-val"><?php echo number_format($students); ?></div>
        <div class="kpi-lbl">Total Students</div>
      </div>
      <span class="material-symbols-rounded kpi-arrow">chevron_right</span>
    </a>

    <a href="organizations/index.php" class="kpi-card" style="text-decoration:none;">
      <div class="kpi-icon teal">
        <span class="material-symbols-rounded">account_balance</span>
      </div>
      <div class="kpi-text">
        <div class="kpi-val"><?php echo number_format($orgs); ?></div>
        <div class="kpi-lbl">Organizations</div>
      </div>
      <span class="material-symbols-rounded kpi-arrow">chevron_right</span>
    </a>

    <a href="events/index.php" class="kpi-card" style="text-decoration:none;">
      <div class="kpi-icon amber">
        <span class="material-symbols-rounded">event</span>
      </div>
      <div class="kpi-text">
        <div class="kpi-val"><?php echo number_format($events); ?></div>
        <div class="kpi-lbl">Total Events</div>
      </div>
      <span class="material-symbols-rounded kpi-arrow">chevron_right</span>
    </a>

    <a href="attendance/index.php" class="kpi-card" style="text-decoration:none;">
      <div class="kpi-icon rose">
        <span class="material-symbols-rounded">fact_check</span>
      </div>
      <div class="kpi-text">
        <div class="kpi-val"><?php echo number_format($attendance); ?></div>
        <div class="kpi-lbl">Attendance Records</div>
      </div>
      <span class="material-symbols-rounded kpi-arrow">chevron_right</span>
    </a>

  </div>
</div><!-- /dash-hero -->


<!-- ╔══════════════════════════════════════╗ -->
<!-- ║              CHARTS ROW             ║ -->
<!-- ╚══════════════════════════════════════╝ -->
<div class="charts-row">

  <!-- Bar chart: Members per Organization -->
  <div class="card">
    <div class="card-hd">
      <div>
        <h3>Members per Organization</h3>
        <div class="card-sub">Membership count across all orgs</div>
      </div>
      <a href="memberships/index.php" class="btn btn-secondary btn-sm no-print">
        View All <span class="material-symbols-rounded" style="font-size:14px;">arrow_forward</span>
      </a>
    </div>
    <div class="card-body">
      <div class="chart-area">
        <canvas id="chartMembers"></canvas>
      </div>
    </div>
  </div>

  <!-- Doughnut: Attendance status breakdown -->
  <div class="card">
    <div class="card-hd">
      <div>
        <h3>Attendance Status</h3>
        <div class="card-sub">Overall breakdown</div>
      </div>
    </div>
    <div class="card-body">
      <div class="chart-area" style="height:180px;">
        <canvas id="chartAttendance"></canvas>
      </div>
      <!-- Legend -->
      <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px;">
        <?php foreach ([
          ['Present', $att_map['Present'], '#10b981'],
          ['Absent',  $att_map['Absent'],  '#f43f5e'],
          ['Late',    $att_map['Late'],    '#f59e0b'],
        ] as [$label, $val, $col]):
          $pct = $total_att > 0 ? round($val / $total_att * 100) : 0;
        ?>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
          <div style="display:flex;align-items:center;gap:8px;">
            <span style="width:10px;height:10px;border-radius:50%;background:<?php echo $col; ?>;flex-shrink:0;"></span>
            <span style="font-size:12.5px;color:var(--text-body);"><?php echo $label; ?></span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;">
            <div class="prog-bar" style="width:60px;">
              <div class="prog-fill" style="width:<?php echo $pct; ?>%;background:<?php echo $col; ?>;"></div>
            </div>
            <span style="font-size:12px;color:var(--text-muted);width:30px;text-align:right;"><?php echo $pct; ?>%</span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div><!-- /charts-row -->


<!-- ╔══════════════════════════════════════╗ -->
<!-- ║           BOTTOM ROW                ║ -->
<!-- ╚══════════════════════════════════════╝ -->
<div class="bottom-row">

  <!-- Quick Actions -->
  <div class="card">
    <div class="card-hd">
      <h3>Quick Actions</h3>
    </div>
    <div class="card-body">
      <div class="quick-grid">

        <div class="quick-card">
          <div class="quick-icon"><span class="material-symbols-rounded">person_add</span></div>
          <h5>Add Student</h5>
          <p>Register a new student into the system.</p>
          <a href="students/add.php" class="btn btn-primary btn-sm">
            <span class="material-symbols-rounded" style="font-size:14px;">add</span> Add
          </a>
        </div>

        <div class="quick-card">
          <div class="quick-icon"><span class="material-symbols-rounded">add_business</span></div>
          <h5>Add Organization</h5>
          <p>Create a new student organization.</p>
          <a href="organizations/add.php" class="btn btn-primary btn-sm">
            <span class="material-symbols-rounded" style="font-size:14px;">add</span> Add
          </a>
        </div>

        <div class="quick-card">
          <div class="quick-icon"><span class="material-symbols-rounded">event_available</span></div>
          <h5>Add Event</h5>
          <p>Schedule a new organization event.</p>
          <a href="events/add.php" class="btn btn-primary btn-sm">
            <span class="material-symbols-rounded" style="font-size:14px;">add</span> Add
          </a>
        </div>

        <div class="quick-card">
          <div class="quick-icon"><span class="material-symbols-rounded">bar_chart_4_bars</span></div>
          <h5>View Reports</h5>
          <p>Analyze participation and attendance.</p>
          <a href="reports/index.php" class="btn btn-secondary btn-sm">
            Reports <span class="material-symbols-rounded" style="font-size:14px;">arrow_forward</span>
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Recent Students -->
  <div class="card" style="overflow:hidden;">
    <div class="recent-header">
      <h3>Recent Students</h3>
      <a href="students/index.php" class="btn btn-secondary btn-sm no-print">
        View All <span class="material-symbols-rounded" style="font-size:14px;">arrow_forward</span>
      </a>
    </div>
    <?php if (empty($recent_stu)): ?>
      <div class="card-body">
        <div class="empty-state">
          <div class="e-icon"><span class="material-symbols-rounded">group</span></div>
          <h4>No Students Yet</h4>
          <p>Start by adding your first student.</p>
          <a href="students/add.php" class="btn btn-primary btn-sm">Add Student</a>
        </div>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Course</th>
              <th>Year</th>
              <th class="no-print">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_stu as $s): ?>
            <tr>
              <td>
                <div class="cell-name"><?php echo htmlspecialchars($s['student_name'] ?? '—'); ?></div>
                <div style="font-size:11.5px;color:var(--text-faint);"><?php echo htmlspecialchars($s['email']); ?></div>
              </td>
              <td style="color:var(--text-muted);"><?php echo htmlspecialchars($s['course'] ?? '—'); ?></td>
              <td><span class="badge badge-indigo"><?php echo htmlspecialchars($s['year_level'] ?? '—'); ?></span></td>
              <td class="no-print">
                <a href="students/edit.php?id=<?php echo $s['student_id']; ?>" class="btn btn-secondary btn-sm">
                  <span class="material-symbols-rounded" style="font-size:14px;">edit</span>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</div><!-- /bottom-row -->


<!-- ╔══════════════════════════════════════╗ -->
<!-- ║         LINE CHART: Events/Month     ║ -->
<!-- ╚══════════════════════════════════════╝ -->
<?php if (!empty($evt_labels)): ?>
<div class="card" style="margin-top:20px;">
  <div class="card-hd">
    <div>
      <h3>Events Over Time</h3>
      <div class="card-sub">Scheduled events in the last 6 months</div>
    </div>
  </div>
  <div class="card-body">
    <div class="chart-area" style="height:200px;">
      <canvas id="chartEvents"></canvas>
    </div>
  </div>
</div>
<?php endif; ?>


<!-- ======================================== -->
<!--  Chart.js                               -->
<!-- ======================================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
/* ── Shared defaults ── */
Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
Chart.defaults.color       = '#64748b';

const INDIGO = '#4f46e5';
const TEAL   = '#14b8a6';
const AMBER  = '#f59e0b';
const ROSE   = '#f43f5e';
const EMER   = '#10b981';

/* ─────────────────────────────────────────
   1. BAR CHART — Members per Organization
   ───────────────────────────────────────── */
(function() {
  const ctx = document.getElementById('chartMembers');
  if (!ctx) return;

  const labels = <?php echo json_encode($mem_labels ?: ['No orgs yet']); ?>;
  const data   = <?php echo json_encode($mem_data   ?: [0]); ?>;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Members',
        data,
        backgroundColor: labels.map((_, i) =>
          ['rgba(79,70,229,0.85)', 'rgba(20,184,166,0.85)',
           'rgba(245,158,11,0.85)', 'rgba(244,63,94,0.85)',
           'rgba(16,185,129,0.85)', 'rgba(99,102,241,0.85)'][i % 6]
        ),
        borderRadius: 8,
        borderSkipped: false,
        maxBarThickness: 48,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0f172a',
          titleFont: { weight: '600', size: 13 },
          bodyFont:  { size: 12 },
          padding:   10,
          cornerRadius: 8,
          callbacks: {
            label: ctx => ` ${ctx.parsed.y} member${ctx.parsed.y !== 1 ? 's' : ''}`
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: { size: 12 } }
        },
        y: {
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: { stepSize: 1, font: { size: 12 } },
          beginAtZero: true,
        }
      }
    }
  });
})();

/* ─────────────────────────────────────────
   2. DOUGHNUT — Attendance Status
   ───────────────────────────────────────── */
(function() {
  const ctx = document.getElementById('chartAttendance');
  if (!ctx) return;

  const present = <?php echo $att_map['Present']; ?>;
  const absent  = <?php echo $att_map['Absent']; ?>;
  const late    = <?php echo $att_map['Late']; ?>;
  const total   = present + absent + late;

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Present', 'Absent', 'Late'],
      datasets: [{
        data: [present, absent, late],
        backgroundColor: [EMER, ROSE, AMBER],
        borderWidth: 0,
        hoverOffset: 6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0f172a',
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            label: ctx => {
              const pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
              return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
            }
          }
        }
      }
    },
    plugins: [{
      id: 'centerLabel',
      afterDraw(chart) {
        const { ctx: c, chartArea: { top, bottom, left, right } } = chart;
        const cx = (left + right) / 2;
        const cy = (top + bottom) / 2;
        c.save();
        c.font = 'bold 22px Lexend, sans-serif';
        c.fillStyle = '#0f172a';
        c.textAlign = 'center';
        c.textBaseline = 'middle';
        c.fillText(total, cx, cy - 8);
        c.font = '11px Plus Jakarta Sans, sans-serif';
        c.fillStyle = '#94a3b8';
        c.fillText('records', cx, cy + 12);
        c.restore();
      }
    }]
  });
})();

/* ─────────────────────────────────────────
   3. LINE CHART — Events per Month
   ───────────────────────────────────────── */
(function() {
  const ctx = document.getElementById('chartEvents');
  if (!ctx) return;

  const labels = <?php echo json_encode($evt_labels ?: []); ?>;
  const data   = <?php echo json_encode($evt_data   ?: []); ?>;

  new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Events',
        data,
        borderColor: INDIGO,
        backgroundColor: 'rgba(79,70,229,0.08)',
        borderWidth: 2.5,
        tension: 0.4,
        fill: true,
        pointBackgroundColor: INDIGO,
        pointRadius: 5,
        pointHoverRadius: 7,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0f172a',
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            label: ctx => ` ${ctx.parsed.y} event${ctx.parsed.y !== 1 ? 's' : ''}`
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: { size: 12 } }
        },
        y: {
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: { stepSize: 1, font: { size: 12 } },
          beginAtZero: true,
        }
      }
    }
  });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
