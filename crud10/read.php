
<?php
require 'config.php';

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';

$sql = "SELECT a.*,u.FullName AS AssignedTo,u.Department
        FROM Assets a LEFT JOIN Users u ON a.AssignedUserID=u.UserID
        WHERE 1=1";

$params = [];

if ($q !== '') {
    $sql .= " AND (a.SerialNo LIKE ? OR a.DeviceType LIKE ? OR a.Brand LIKE ? OR a.Model LIKE ? OR u.FullName LIKE ?)";

    $like = "%$q%";
    $params = [$like, $like, $like, $like, $like];
}

if (in_array($status, ['Operational', 'Maintenance Required', 'Under Repair', 'Retired'], true)) {
    $sql .= " AND a.Status=?";
    $params[] = $status;
}

$sql .= " ORDER BY a.AssetID DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$assets = $stmt->fetchAll();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>Read Assets</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="topbar">
    <div class="container nav">

        <a class="brand" href="overview.php">
            IT Asset Manager
        </a>

        <nav class="navlinks">
            <a href="overview.php">Overview</a>
            <a href="create.php">Create</a>
            <a class="active" href="read.php">Read</a>
            <a href="update.php">Update</a>
            <a href="delete.php">Delete</a>
        </nav>

    </div>
</header>

<main class="container">

    <div class="hero">
        <div>
            <h1>Asset Records</h1>
            <p class="muted">
                Search and inspect all registered hardware.
            </p>
        </div>

        <a class="btn btn-primary" href="create.php">
            + Create
        </a>
    </div>

    <div class="toolbar">
        <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">

            <input
                class="search"
                name="q"
                value="<?=htmlspecialchars($q)?>"
                placeholder="Search serial, device, brand, model, user"
            >

            <select name="status">
                <option value="">All statuses</option>

                <?php foreach (['Operational', 'Maintenance Required', 'Under Repair', 'Retired'] as $s): ?>
                    <option <?=$status === $s ? 'selected' : ''?>>
                        <?=$s?>
                    </option>
                <?php endforeach; ?>

            </select>

            <button class="btn btn-secondary">
                Filter
            </button>

        </form>
    </div>

    <div class="table-wrap">

        <table data-search-table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Serial</th>
                    <th>Device</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Assigned User</th>
                    <th>Status</th>
                    <th>Warranty</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($assets as $a): ?>
                    <tr>

                        <td>#<?=$a['AssetID']?></td>

                        <td>
                            <?=htmlspecialchars($a['SerialNo'])?>
                        </td>

                        <td>
                            <?=htmlspecialchars($a['DeviceType'])?>
                        </td>

                        <td>
                            <?=htmlspecialchars($a['Brand'])?>
                        </td>

                        <td>
                            <?=htmlspecialchars($a['Model'])?>
                        </td>

                        <td>
                            <?=htmlspecialchars($a['AssignedTo'] ?? 'Unassigned')?>
                        </td>

                        <td>
                            <?=htmlspecialchars($a['Status'])?>
                        </td>

                        <td>
                            <?=htmlspecialchars($a['WarrantyExpiry'] ?? '—')?>
                        </td>

                    </tr>
                <?php endforeach; ?>

                <?php if (!$assets): ?>
                    <tr>
                        <td colspan="8" class="empty">
                            No assets found.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>
<footer>

        IT Asset & Hardware Maintenance Log System
        • Backbencher-Association

    </footer>
<script src="script.js"></script>

</body>
</html>