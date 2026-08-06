<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "it_asset_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$message = "";

// Handle Delete Request
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM assets WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "<p style='color: #166534; font-weight: bold;'>Asset removed successfully.</p>";
    }
    $stmt->close();
}

// Handle Form Submission (Insert)
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update_asset'])) {
    $asset_tag     = trim($_POST['asset_tag']);
    $device_type   = trim($_POST['device_type']);
    $brand_model   = trim($_POST['brand_model']);
    $department    = trim($_POST['department']);
    $assigned_user = !empty($_POST['assigned_user']) ? trim($_POST['assigned_user']) : 'Unassigned';
    $status        = trim($_POST['status']);
    $purchase_date = $_POST['purchase_date'];

    $stmt = $conn->prepare("INSERT INTO assets (asset_tag, device_type, brand_model, department, assigned_user, status, purchase_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $asset_tag, $device_type, $brand_model, $department, $assigned_user, $status, $purchase_date);

    if ($stmt->execute()) {
        $message = "<p style='color: #166534; font-weight: bold;'>Asset registered successfully!</p>";
    } else {
        $message = "<p style='color: #991b1b; font-weight: bold;'>Error: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// edit reqst
// =========================
// EDIT & UPDATE
// =========================

$editMode = false;
$editData = [
    'id' => '',
    'asset_tag' => '',
    'device_type' => 'Laptop',
    'brand_model' => '',
    'department' => '',
    'assigned_user' => '',
    'status' => 'In Stock',
    'purchase_date' => ''
];

// Load data for editing
if (isset($_GET['edit'])) {

    $editMode = true;
    $edit_id = intval($_GET['edit']);

    $stmt = $conn->prepare("SELECT * FROM assets WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();

    $resultEdit = $stmt->get_result();

    if ($resultEdit->num_rows > 0) {
        $editData = $resultEdit->fetch_assoc();
    }

    $stmt->close();
}

// Update asset
if (isset($_POST['update_asset'])) {

    $id = intval($_POST['id']);

    $asset_tag     = trim($_POST['asset_tag']);
    $device_type   = trim($_POST['device_type']);
    $brand_model   = trim($_POST['brand_model']);
    $department    = trim($_POST['department']);
    $assigned_user = !empty($_POST['assigned_user']) ? trim($_POST['assigned_user']) : "Unassigned";
    $status        = trim($_POST['status']);
    $purchase_date = $_POST['purchase_date'];

    $stmt = $conn->prepare("
        UPDATE assets
        SET asset_tag=?,
            device_type=?,
            brand_model=?,
            department=?,
            assigned_user=?,
            status=?,
            purchase_date=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sssssssi",
        $asset_tag,
        $device_type,
        $brand_model,
        $department,
        $assigned_user,
        $status,
        $purchase_date,
        $id
    );

    if ($stmt->execute()) {

        $message = "<p style='color:green;font-weight:bold;'>Asset updated successfully!</p>";

        $editMode = false;

        $editData = [
            'id'=>'',
            'asset_tag'=>'',
            'device_type'=>'Laptop',
            'brand_model'=>'',
            'department'=>'',
            'assigned_user'=>'',
            'status'=>'In Stock',
            'purchase_date'=>''
        ];

    } else {

        $message = "<p style='color:red;'>".$conn->error."</p>";

    }

    $stmt->close();
}

// Fetch Assets
$result = $conn->query("SELECT * FROM assets ORDER BY id DESC");

// Fetch Assets
$result = $conn->query("SELECT * FROM assets ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Asset Inventory System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="sidebar.css">
</head>
<body>

<div class="container">
    <h1>IT Asset Inventory System</h1>
    <!-- ===========================
            Sidebar
    ============================ -->

    <aside class="sidebar">
        

        <div class="logo">

            <i class="fa-solid fa-server"></i>

            <h3>IT Asset</h3>

        </div>

        <ul class="menu">

            <li class="active">
                <a href="index.html">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Add Asset</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa-solid fa-laptop"></i>
                    <span>Homepage</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa-solid fa-square-plus"></i>
                    <span>Assets</span>
                </a>
            </li>

            

            <li>
                <a href="reports.html">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Reports</span>
                </a>
            </li>

            <!-- <li>
                <a href="profile.html">
                    <i class="fa-solid fa-user"></i>
                    <span>Profile</span>
                </a>
            </li> -->

        </ul>

        <div class="logout">

            <a href="login.html">

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>Logout</span>

            </a>

        </div>

    </aside>
   
<!-- ---------------------------------- -->

    <!-- Form Section -->
    <div class="card">
        <h2>Register Hardware Asset</h2>
        <?php echo $message; ?>
        <form action="" method="POST" class="form-grid" >
            <?php if($editMode): ?>
<input type="hidden" name="id"
value="<?php echo $editData['id']; ?>">
<?php endif; ?>
            <div class="form-group">
                <label>Asset Tag / Serial No.</label>
                <input
type="text"
name="asset_tag"
required
placeholder="e.g. AST-1004"
value="<?php echo htmlspecialchars($editData['asset_tag']); ?>">
            </div>

            <div class="form-group">
                <label>Device Type</label>
                <select name="device_type">

<option value="Laptop"
<?php if($editData['device_type']=="Laptop") echo "selected"; ?>>
Laptop
</option>

<option value="Desktop"
<?php if($editData['device_type']=="Desktop") echo "selected"; ?>>
Desktop
</option>

<option value="Monitor"
<?php if($editData['device_type']=="Monitor") echo "selected"; ?>>
Monitor
</option>

<option value="Printer"
<?php if($editData['device_type']=="Printer") echo "selected"; ?>>
Printer
</option>

<option value="Switch/Router"
<?php if($editData['device_type']=="Switch/Router") echo "selected"; ?>>
Switch/Router
</option>

</select>
            </div>

            <div class="form-group">
                <label>Brand & Model</label>
                <input
type="text"
name="brand_model"
required
value="<?php echo htmlspecialchars($editData['brand_model']); ?>">
            </div>

            <div class="form-group">
                <label>Department</label>
                <input
type="text"
name="department"
required
value="<?php echo htmlspecialchars($editData['department']); ?>">
            </div>

            <div class="form-group">
                <label>Assigned User</label>
                <input
type="text"
name="assigned_user"
value="<?php echo htmlspecialchars($editData['assigned_user']); ?>">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">

<option value="In Stock"
<?php if($editData['status']=="In Stock") echo "selected"; ?>>
In Stock
</option>

<option value="Assigned"
<?php if($editData['status']=="Assigned") echo "selected"; ?>>
Assigned
</option>

<option value="Disposed"
<?php if($editData['status']=="Disposed") echo "selected"; ?>>
Disposed
</option>

</select>
            </div>

            <div class="form-group">
                <label>Purchase Date</label>
                <input
type="date"
name="purchase_date"
required
value="<?php echo htmlspecialchars($editData['purchase_date']); ?>">
            </div>

            <?php if($editMode): ?>

<button
type="submit"
name="update_asset"
class="btn-submit">
Update Asset
</button>

<a href="index.php" class="edit-link">
Cancel
</a>

<?php else: ?>

<button
type="submit"
class="btn-submit">
Add Asset to Inventory
</button>

<?php endif; 
?>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <h2>Asset Inventory List</h2>
        <table>
            <thead>
                <tr>
                    <th>Asset Tag</th>
                    <th>Device</th>
                    <th>Brand/Model</th>
                    <th>Department</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Purchase Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php $badge_class = str_replace(' ', '-', $row['status']); ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['asset_tag']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['device_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['brand_model']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td><?php echo htmlspecialchars($row['assigned_user']); ?></td>
                            <td><span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
                            <td>
                                 
                            <a href="index.php?edit=<?php echo $row['id']; ?>" class="edit-link">
                                Edit
                            </a>

                        <a href="index.php?delete=<?php echo $row['id']; ?>"
                        class="action-link"
                        onclick="return confirm('Delete this asset?');">
                            Delete
                        </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No assets currently registered.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>