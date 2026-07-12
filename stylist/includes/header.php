<?php
$sid = intval($_SESSION['bpmsstid']);
$sn = mysqli_query($con, "SELECT StylistName FROM tblstylists WHERE ID='$sid'");
$srow = mysqli_fetch_array($sn);
$sname = $srow ? $srow['StylistName'] : 'Stylist';
$cur = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>MSMS Stylist</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg stylist-nav">
  <div class="container">
    <a class="brand" href="dashboard.php"><i class="bi bi-person-badge"></i> Stylist Panel</a>
    <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link <?php echo $cur=='dashboard.php'?'active':''; ?>" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $cur=='my-appointments.php'?'active':''; ?>" href="my-appointments.php">My Schedule</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $cur=='profile.php'?'active':''; ?>" href="profile.php">Profile</a></li>
        <li class="nav-item ms-lg-2"><span class="text-white-50 small d-none d-lg-inline">Hi, <?php echo htmlspecialchars($sname); ?></span></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<main class="page-wrap">
<div class="container">
