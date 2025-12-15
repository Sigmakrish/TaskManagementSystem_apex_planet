<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role_id"] != 1) {
    header("Location: ../login.php");
    exit;
}

/* ===== Pagination ===== */
$limit = 10;
$page = isset($_GET["page"]) ? max(1, intval($_GET["page"])) : 1;
$offset = ($page - 1) * $limit;

/* ===== Count tasks ===== */
$total = $mysqli->query("SELECT COUNT(*) AS c FROM tasks")->fetch_assoc()['c'];
$total_pages = ceil($total / $limit);

/* ===== Fetch tasks ===== */
$stmt = $mysqli->prepare("
    SELECT 
        t.id, t.title, t.description, t.deadline, t.status, t.created_at,
        u.username, u.name
    FROM tasks t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

/* ===== STATUS BADGE (SINGLE SOURCE OF TRUTH) ===== */
function badge($status) {
    return match ($status) {
        "pending" =>
            '<span class="badge bg-warning text-dark">Pending</span>',

        "in_progress" =>
            '<span class="badge bg-info text-dark">In Progress</span>',

        "completed" =>
            '<span class="badge bg-success">Completed</span>',

        default =>
            '<span class="badge bg-secondary">Unknown</span>',
    };
}
?>

<!doctype html>
<html>
<head>
<title>Admin - All Tasks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h2>Admin: All Tasks</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card shadow p-3">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Task Title</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Created</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($t = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $t["id"] ?></td>

                        <td>
                            <strong><?= htmlspecialchars($t["username"]) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($t["name"]) ?></small>
                        </td>

                        <td>
                            <strong><?= htmlspecialchars($t["title"]) ?></strong><br>
                            <small class="text-muted">
                                <?= htmlspecialchars(substr($t["description"], 0, 70)) ?>...
                            </small>
                        </td>

                        <td><?= badge($t["status"]) ?></td>

                        <td>
                            <?php if ($t["deadline"]): ?>
                                <span class="badge bg-danger"><?= htmlspecialchars($t["deadline"]) ?></span>
                            <?php else: ?>
                                <span class="text-muted">No deadline</span>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($t["created_at"]) ?></td>

                        <td>
                            <a href="edit_task.php?id=<?= $t["id"] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete_task.php?id=<?= $t["id"] ?>" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php if ($total_pages > 1): ?>
<nav>
  <ul class="pagination justify-content-center mt-4">

    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
      <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
    </li>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>

    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
      <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
    </li>

  </ul>
</nav>
<?php endif; ?>
</body>
</html>

