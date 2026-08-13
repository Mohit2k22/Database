<?php
require 'config.php';

$error = '';
$message = '';

$users = $pdo->query(
    "SELECT UserID,FullName,Department FROM Users ORDER BY FullName"
)->fetchAll();

$id = (int)($_GET['id'] ?? $_POST['AssetID'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare(
            "UPDATE Assets SET SerialNo=?,DeviceType=?,Brand=?,Model=?,PurchaseDate=?,WarrantyExpiry=?,AssignedUserID=?,Status=? WHERE AssetID=?"
        );

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
            $_POST['Status'],
            $id
        ]);

        $message = "Asset #$id updated successfully.";

    } catch (PDOException $e) {
        $error = $e->errorInfo[1] == 1062
            ? "Serial number already exists."
            : "Unable to update asset.";
    }
}

$asset = null;

if ($id) {
    $st = $pdo->prepare(
        "SELECT * FROM Assets WHERE AssetID=?"
    );

    $st->execute([$id]);
    $asset = $st->fetch();
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

    <title>Update Asset</title>

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
            <a href="read.php">Read</a>
            <a class="active" href="update.php">Update</a>
            <a href="delete.php">Delete</a>
        </nav>

    </div>
</header>

<main class="container">

    <h1>Update Asset</h1>

    <p class="muted">
        Enter an Asset ID to edit the record.
    </p>

    <form
        method="get"
        class="card"
        style="max-width:500px;margin:18px 0"
    >

        <div class="field">

            <label>Asset ID</label>

            <input
                type="number"
                name="id"
                min="1"
                value="<?=$id?>"
                required
            >

        </div>

        <div class="actions">

            <button class="btn btn-secondary">
                Load Asset
            </button>

        </div>

    </form>

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

    <?php if ($asset): ?>

        <form method="post" class="card form-card">

            <input
                type="hidden"
                name="AssetID"
                value="<?=$asset['AssetID']?>"
            >

            <div class="form-grid">

                <div class="field">

                    <label>Serial Number *</label>

                    <input
                        name="SerialNo"
                        value="<?=htmlspecialchars($asset['SerialNo'])?>"
                        required
                    >

                </div>

                <div class="field">

                    <label>Device Type *</label>

                    <input
                        name="DeviceType"
                        value="<?=htmlspecialchars($asset['DeviceType'])?>"
                        required
                    >

                </div>

                <div class="field">

                    <label>Brand *</label>

                    <input
                        name="Brand"
                        value="<?=htmlspecialchars($asset['Brand'])?>"
                        required
                    >

                </div>

                <div class="field">

                    <label>Model *</label>

                    <input
                        name="Model"
                        value="<?=htmlspecialchars($asset['Model'])?>"
                        required
                    >

                </div>

                <div class="field">

                    <label>Purchase Date</label>

                    <input
                        type="date"
                        name="PurchaseDate"
                        value="<?=htmlspecialchars($asset['PurchaseDate'] ?? '')?>"
                    >

                </div>

                <div class="field">

                    <label>Warranty Expiry</label>

                    <input
                        type="date"
                        name="WarrantyExpiry"
                        value="<?=htmlspecialchars($asset['WarrantyExpiry'] ?? '')?>"
                    >

                </div>

                <div class="field">

                    <label>Assigned User</label>

                    <select name="AssignedUserID">

                        <option value="">
                            Unassigned
                        </option>

                        <?php foreach ($users as $u): ?>

                            <option
                                value="<?=$u['UserID']?>"
                                <?=$asset['AssignedUserID'] == $u['UserID'] ? 'selected' : ''?>
                            >
                                <?=htmlspecialchars($u['FullName'] . ' — ' . $u['Department'])?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="field">

                    <label>Status</label>

                    <select name="Status">

                        <?php foreach (['Operational', 'Maintenance Required', 'Under Repair', 'Retired'] as $s): ?>

                            <option
                                <?=$asset['Status'] === $s ? 'selected' : ''?>
                            >
                                <?=$s?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

            <div class="actions">

                <button class="btn btn-primary">
                    Save Changes
                </button>

            </div>

        </form>

    <?php elseif ($id): ?>

        <div class="alert error">
            Asset #<?=$id?> was not found.
        </div>

    <?php endif; ?>

</main>
<footer>

        IT Asset & Hardware Maintenance Log System
        • Backbencher-Association

    </footer>
</body>
</html>