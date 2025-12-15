<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user"])) {
    exit;
}

$user_id = $_SESSION["user"]["id"];
$status = $_GET["status"] ?? "";
$search = $_GET["q"] ?? "";

$sql = "SELECT * FROM tasks WHERE user_id=?";
$params = [$user_id];
$types = "i";

if ($status !== "") {
    $sql .= " AND status=?";
    $types .= "s";
    $params[] = $status;
}

if ($search !== "") {
    $sql .= " AND title LIKE ?";
    $types .= "s";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

function badge($status) {
    return match ($status) {
        "pending" => '<span class="badge bg-warning text-dark">Pending</span>',
        "in_progress" => '<span class="badge bg-info text-dark">In Progress</span>',
        "completed" => '<span class="badge bg-success">Completed</span>',
        default => "",
    };
}

while ($task = $result->fetch_assoc()):
?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h4><?= htmlspecialchars($task["title"]) ?></h4>
            <?= badge($task["status"]) ?>
        </div>

        <p class="text-muted">
            <?= htmlspecialchars(substr($task["description"], 0, 150)) ?>...
        </p>

        <?php if ($task["deadline"]): ?>
            <span class="badge bg-danger">
                Deadline: <?= htmlspecialchars($task["deadline"]) ?>
            </span>
        <?php endif; ?>

        <div class="mt-3">
            <a href="edit_task.php?id=<?= $task["id"] ?>" class="btn btn-sm btn-primary">Edit</a>
            <a href="delete_task.php?id=<?= $task["id"] ?>"
               onclick="return confirmDelete()"
               class="btn btn-danger btn-sm">Delete</a>
        </div>
    </div>
</div>
<?php endwhile; ?>
