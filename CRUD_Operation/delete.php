<?php
require 'config.php';
$message=''; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id=(int)$_POST['AssetID'];
  try{
    $st=$pdo->prepare("DELETE FROM Assets WHERE AssetID=?");$st->execute([$id]);
    $message=$st->rowCount()?"Asset #$id deleted successfully.":"Asset #$id was not found.";
  }catch(PDOException $e){$error="Unable to delete asset.";}
}
$assets=$pdo->query("SELECT AssetID,SerialNo,DeviceType,Brand,Model,Status FROM Assets ORDER BY AssetID DESC")->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Delete Asset</title><link rel="stylesheet" href="style.css"></head><body>
<header class="topbar"><div class="container nav"><a class="brand" href="overview.php">IT Asset Manager</a>
<nav class="navlinks"><a href="overview.php">Overview</a><a href="create.php">Create</a><a href="read.php">Read</a><a href="update.php">Update</a><a class="active" href="delete.php">Delete</a></nav></div></header>
<main class="container"><div class="hero"><div><h1>Delete Asset</h1><p class="muted">Remove an asset record. Maintenance logs for that asset are also removed because of the database foreign-key cascade.</p></div></div>
<?php if($message): ?><div class="alert"><?=$message?></div><?php endif; ?><?php if($error): ?><div class="alert error"><?=$error?></div><?php endif; ?>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Serial</th><th>Device</th><th>Brand / Model</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach($assets as $a): ?><tr><td>#<?=$a['AssetID']?></td><td><?=htmlspecialchars($a['SerialNo'])?></td><td><?=htmlspecialchars($a['DeviceType'])?></td>
<td><?=htmlspecialchars($a['Brand'].' / '.$a['Model'])?></td><td><?=htmlspecialchars($a['Status'])?></td><td>
<form method="post" style="margin:0"><input type="hidden" name="AssetID" value="<?=$a['AssetID']?>">
<button class="btn btn-danger" data-confirm="Delete asset #<?=$a['AssetID']?>? This will also delete its maintenance logs.">Delete</button></form>
</td></tr><?php endforeach; ?>
<?php if(!$assets): ?><tr><td colspan="6" class="empty">No assets available.</td></tr><?php endif; ?></tbody></table></div>
</main><script src="script.js"></script></body></html>