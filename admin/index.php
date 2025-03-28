<?php
session_start();
error_reporting(0);

include("include/config.php");

if (isset($_POST['submit'])) {
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	$ret = mysqli_query($bd, "SELECT * FROM admin WHERE username='$username' and password='$password'");
	$num = mysqli_fetch_array($ret);
	if ($num > 0) {
		$extra = "change-password.php"; //
		$_SESSION['alogin'] = $_POST['username'];
		$_SESSION['id'] = $num['id'];
		$host = $_SERVER['HTTP_HOST'];
		$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("location:http://$host$uri/$extra");
		exit();
	} else {
		$_SESSION['errmsg'] = "Invalid username or password";
		$extra = "index.php";
		$host  = $_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("location:http://$host$uri/$extra");
		exit();
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Login</title>

	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link rel="stylesheet" href="css/admin.css">
</head>

<body>
	<!-- Navbar -->
	<nav class="navbar navbar-dark">
		<div class="container">
			<a class="navbar-brand" href="index.php">
				<i class="bi bi-shield-lock me-2"></i>Admin Panel
			</a>
			<div class="ms-auto">
				<a href="http://localhost/Complaint Management System/" class="btn btn-outline-light">
					<i class="bi bi-arrow-left me-2"></i>Back to Portal
				</a>
			</div>
		</div>
	</nav>

	<!-- Login Container -->
	<div class="login-container">
		<div class="login-wrapper">
			<div class="login-header">
				<h3><i class="bi bi-person-fill me-2"></i>Admin Sign In</h3>
			</div>

			<form method="post">
				<?php if ($_SESSION['errmsg']): ?>
					<div class="error-message">
						<?php
						echo htmlentities($_SESSION['errmsg']);
						$_SESSION['errmsg'] = "";
						?>
					</div>
				<?php endif; ?>

				<input type="text" class="form-control" id="inputEmail" name="username"
					placeholder="Username" required autofocus>

				<input type="password" class="form-control" id="inputPassword" name="password"
					placeholder="Password" required>

				<button type="submit" class="btn btn-theme btn-lg w-100" name="submit">
					<i class="bi bi-box-arrow-in-right me-2"></i>Login
				</button>
			</form>
		</div>
	</div>

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<p class="mb-0">
				<i class="bi bi-c-circle me-2"></i>2025 Routine Complaint Tracking System. All rights reserved.
			</p>
		</div>
	</footer>

	<!-- Bootstrap 5 JS and Popper.js -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>