<?php
require 'config.php';

$message = '';
$error = '';

$users = $pdo->query(
    "SELECT UserID,FullName,Department FROM Users ORDER BY FullName"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "INSERT INTO Assets
            (SerialNo,DeviceType,Brand,Model,PurchaseDate,WarrantyExpiry,AssignedUserID,Status)
            VALUES (?,?,?,?,?,?,?,?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            trim($_POST['SerialNo']),
            trim($_POST['DeviceType']),
            trim($_POST['Brand']),
            trim($_POST['Model']),
            $_POST['PurchaseDate'] ?: null,
            $_POST['WarrantyExpiry'] ?: null,
            $_POST['AssignedUserID'] !== ''
                ? (int)$_POST['AssignedUserID']
                : null,
            $_POST['Status']
        ]);

        $message = "Asset created successfully. Asset ID: " . $pdo->lastInsertId();

    } catch (PDOException $e) {
        $error = $e->errorInfo[1] == 1062
            ? "Serial number already exists."
            : "Unable to save asset.";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width,initial-scale=1"
    >

    <title>Create Asset</title>

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
            <a class="active" href="create.php">Create</a>
            <a href="read.php">Read</a>
            <a href="update.php">Update</a>
            <a href="delete.php">Delete</a>
        </nav>

    </div>

</header>

<main class="container">

    <div class="hero">

        <div>

            <h1>Create Asset</h1>

            <p class="muted">
                Register a new hardware asset.
            </p>

        </div>

    </div>

    <?php if ($message): ?>

        <div class="alert">
            <?=$message?>
        </div>

    <?php endif; ?>

    <?php if ($error): ?>

        <div class="alert error">
            <?=$error?>
        </div>

    <?php endif; ?>

    <form method="post" class="card form-card">

        <div class="form-grid">

            <div class="field">

                <label>Serial Number *</label>

                <input
                    name="SerialNo"
                    required
                    maxlength="100"
                >

            </div>

            <div class="field">

                <label>Device Type *</label>

                <select name="DeviceType" required>

                    <?php foreach (
                        [
                            'Desktop computer',
                            'Laptop',
                            'Printer',
                            'Router',
                            'Switch',
                            'Monitor',
                            'UPS device',
                            'Projector',
                            'Network device',
                            'Other IT hardware'
                        ] as $v
                    ): ?>

                        <option>
                            <?=htmlspecialchars($v)?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="field">

                <label>Brand *</label>

                <input
                    name="Brand"
                    required
                    maxlength="50"
                >

            </div>

            <div class="field">

                <label>Model *</label>

                <input
                    name="Model"
                    required
                    maxlength="50"
                >

            </div>

            <div class="field">

                <label>Purchase Date</label>

                <input
                    type="date"
                    name="PurchaseDate"
                >

            </div>

            <div class="field">

                <label>Warranty Expiry</label>

                <input
                    type="date"
                    name="WarrantyExpiry"
                >

            </div>

            <div class="field">

                <label>Assigned User</label>

                <select name="AssignedUserID">

                    <option value="">
                        Unassigned
                    </option>

                    <?php foreach ($users as $u): ?>

                        <option value="<?=$u['UserID']?>">
                            <?=htmlspecialchars($u['FullName'] . ' — ' . $u['Department'])?>
                        </option>
                        <!-- if you need to update user then go to php database - user table
                         UPDATE `users` SET `FullName` = 'MR.KABIR' WHERE `users`.`UserID` = 1;
                          UPDATE `users` SET `Email` = 'kabir@example.com' WHERE `users`.`UserID` = 1;
                         =========Add new user?
                         INSERT INTO users (FullName, Department, Email)
                        VALUES ('username', 'new Department', 'user@example.com');-->

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="field">

                <label>Status *</label>

                <select name="Status">

                    <?php foreach (
                        [
                            'Operational',
                            'Maintenance Required',
                            'Under Repair',
                            'nostw hoye geche'
                        ] as $s
                    ): ?>

                        <option>
                            <?=$s?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>

        <div class="actions">

            <button class="btn btn-primary">
                Create Asset
            </button>

            <a
                class="btn btn-secondary"
                href="read.php"
            >
                View Assets
            </a>

        </div>

    </form>

</main>
<footer>

        IT Asset & Hardware Maintenance Log System
        • Backbencher-Association

    </footer>
<script src="script.js"></script>

</body>
</html>