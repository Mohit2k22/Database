<?php

require 'config.php';

$stats = [
    'total'       => (int) $pdo->query("SELECT COUNT(*) FROM Assets")->fetchColumn(),
    'operational' => (int) $pdo->query(
        "SELECT COUNT(*) FROM Assets WHERE Status = 'Operational'"
    )->fetchColumn(),
    'repair'      => (int) $pdo->query(
        "SELECT COUNT(*) FROM Assets WHERE Status = 'Under Repair'"
    )->fetchColumn(),
    'maintenance' => (int) $pdo->query(
        "SELECT COUNT(*) FROM Assets WHERE Status = 'Maintenance Required'"
    )->fetchColumn(),
];

$recent = $pdo->query("
    SELECT
        a.AssetID,
        a.SerialNo,
        a.DeviceType,
        a.Brand,
        a.Model,
        a.Status,
        u.FullName AS AssignedTo
    FROM Assets a
    LEFT JOIN Users u
        ON a.AssignedUserID = u.UserID
    ORDER BY a.UpdatedAt DESC
    LIMIT 8
")->fetchAll();

$logs = $pdo->query("
    SELECT
        m.MaintenanceDate,
        m.Technician,
        m.Cost,
        m.CompletionDate,
        a.SerialNo,
        a.DeviceType
    FROM Maintenance_Log m
    JOIN Assets a
        ON a.AssetID = m.AssetID
    ORDER BY m.MaintenanceDate DESC
    LIMIT 6
")->fetchAll();

function badge($status)
{
    $class = match ($status) {
        'Operational'           => 'badge-operational',
        'Under Repair'          => 'badge-repair',
        'Maintenance Required'  => 'badge-maintenance',
        'Retired'               => 'badge-retired',
        default                 => 'badge-retired',
    };

    return '<span class="badge ' . $class . '">'
        . htmlspecialchars($status)
        . '</span>';
}

?>

<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>IT Asset System — Overview</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <!-- ================================
         TOP NAVIGATION
    ================================= -->

    <header class="topbar">

        <div class="container nav">

            <a class="brand" href="overview.php">
                IT Asset Manager
            </a>

            <nav class="navlinks">

                <a class="active" href="overview.php">
                    Overview
                </a>

                <a href="create.php">
                    Create
                </a>

                <a href="read.php">
                    Read
                </a>

                <a href="update.php">
                    Update
                </a>

                <a href="delete.php">
                    Delete
                </a>

            </nav>

        </div>

    </header>


    <!-- ================================
         MAIN CONTENT
    ================================= -->

    <main class="container">

        <!-- PAGE HEADER -->

        <section class="hero">

            <div>

                <h1>
                    IT Asset & Inventory Log System
                </h1>

                <p class="muted">
                    Centralized asset, assignment, status and
                    maintenance overview.
                </p>

            </div>

            <a
                class="btn btn-primary"
                href="create.php"
            >
                + Register Asset
            </a>

        </section>


        <!-- ================================
             DASHBOARD STATISTICS
        ================================= -->

        <div class="grid cards">

            <!-- Total Assets -->

            <div class="card">

                <div class="metric-label">
                    Total Assets
                </div>

                <div class="metric">
                    <?= $stats['total'] ?>
                </div>

            </div>


            <!-- Operational Devices -->

            <div class="card">

                <div class="metric-label">
                    Operational Devices
                </div>

                <div class="metric">
                    <?= $stats['operational'] ?>
                </div>

            </div>


            <!-- Under Repair -->

            <div class="card">

                <div class="metric-label">
                    Under Repair
                </div>

                <div class="metric">
                    <?= $stats['repair'] ?>
                </div>

            </div>


            <!-- Maintenance Required -->

            <div class="card">

                <div class="metric-label">
                    Maintenance Required
                </div>

                <div class="metric">
                    <?= $stats['maintenance'] ?>
                </div>

            </div>

        </div>


        <!-- ================================
             RECENT ASSETS
        ================================= -->

        <section
            class="card"
            style="margin-top: 22px;"
        >

            <h2>
                Recent Assets
            </h2>

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Serial</th>
                            <th>Device</th>
                            <th>Brand / Model</th>
                            <th>Assigned To</th>
                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($recent as $r): ?>

                            <tr>

                                <td>
                                    #<?= $r['AssetID'] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($r['SerialNo']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($r['DeviceType']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $r['Brand'] . ' / ' . $r['Model']
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $r['AssignedTo'] ?? 'Unassigned'
                                    ) ?>
                                </td>

                                <td>
                                    <?= badge($r['Status']) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- ================================
             RECENT MAINTENANCE
        ================================= -->

        <section
            class="card"
            style="margin-top: 22px;"
        >

            <h2>
                Recent Maintenance
            </h2>

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>Date</th>
                            <th>Asset</th>
                            <th>Technician</th>
                            <th>Cost</th>
                            <th>Completion</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($logs as $r): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars(
                                        $r['MaintenanceDate']
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $r['SerialNo'] . ' — ' . $r['DeviceType']
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $r['Technician']
                                    ) ?>
                                </td>

                                <td>
                                    $<?= number_format(
                                        (float) $r['Cost'],
                                        2
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $r['CompletionDate'] ?? 'Pending'
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>


    <!-- ================================
         FOOTER
    ================================= -->

    <footer>

        IT Asset & Hardware Maintenance Log System
        • MR. KABIR

    </footer>

</body>

</html>
```
