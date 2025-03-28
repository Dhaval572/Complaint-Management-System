<?php
session_start();
error_reporting(0);

include("includes/config.php");

// Common validation functions
function validateInput($email, $password)
{
	if (empty($email) || empty($password)) {
		return "Please fill in all fields.";
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return "Invalid email format.";
	}
	if (strlen($password) < 6) {
		return "Password must be at least 6 characters.";
	}
	return null;
}

// Prepare and execute queries safely
function executeQuery($bd, $query, $params)
{
	$stmt = mysqli_prepare($bd, $query);
	if ($stmt) {
		mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
		mysqli_stmt_execute($stmt);
		return mysqli_stmt_get_result($stmt);
	}
	return false;
}

// Handle login
if (isset($_POST['submit'])) {
	$email = $_POST['userEmail'];
	$password = $_POST['password'];

	if ($error = validateInput($email, $password)) {
		$errormsg = $error;
	} else {
		$query = "SELECT * FROM users WHERE userEmail = ? AND password = ?";
		$result = executeQuery($bd, $query, [$email, md5($password)]);

		if ($num = mysqli_fetch_array($result)) {
			$_SESSION['login'] = $email;
			$_SESSION['id'] = $num['id'];

			// Log successful login
			$query = "INSERT INTO userlog (uid, username, userip, status) VALUES (?, ?, ?, ?)";
			executeQuery($bd, $query, [$_SESSION['id'], $email, $_SERVER['REMOTE_ADDR'], 1]);

			header("location: dashboard.php");
			exit();
		} else {
			// Log failed login
			$query = "INSERT INTO userlog (username, userip, status) VALUES (?, ?, ?)";
			executeQuery($bd, $query, [$email, $_SERVER['REMOTE_ADDR'], 0]);

			$errormsg = "Invalid Email or password";
		}
	}
}

// Handle password reset
if (isset($_POST['change'])) {
	$email = $_POST['email'];
	$contact = $_POST['contact'];
	$password = $_POST['password'];
	$confirmpass = $_POST['confirmpassword'];

	if (empty($email) || empty($contact) || empty($password) || empty($confirmpass)) {
		$errormsg = "Please fill in all fields.";
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errormsg = "Invalid email format.";
	} elseif ($password !== $confirmpass) {
		$errormsg = "Passwords do not match.";
	} else {
		$query = "SELECT * FROM users WHERE userEmail = ? AND contactNo = ?";
		$result = executeQuery($bd, $query, [$email, $contact]);

		if (mysqli_fetch_array($result)) {
			$query = "UPDATE users SET password = ? WHERE userEmail = ? AND contactNo = ?";
			executeQuery($bd, $query, [md5($password), $email, $contact]);
			$msg = "Password Changed Successfully";
		} else {
			$errormsg = "Invalid email or contact number";
		}
	}
}
?>

<!-- TODO: Add styling the change-password modal -->

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>CMS | User Login</title>

	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
	<div class="container">
		<div class="login-container">
			<button class="back-button" type="button" onclick="window.location.href='../index.html'">
				<i class="bi bi-arrow-left"></i>
			</button>

			<div class="login-header">
				<h2><i class="bi bi-lock-fill me-2"></i>Login</h2>
			</div>

			<form name="login" method="post" autocomplete="off">
				<!-- PHP Error/Success Message Placeholders -->
				<div id="message-container">
					<?php if ($errormsg): ?>
						<div class="alert alert-danger"><?php echo htmlentities($errormsg); ?></div>
					<?php endif; ?>

					<?php if ($msg): ?>
						<div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
					<?php endif; ?>
				</div>

				<div class="mb-3">
					<input type="text" class="form-control" name="userEmail" placeholder="Email" required autofocus
						autocomplete="off">
				</div>

				<div class="mb-3">
					<input type="password" class="form-control" name="password" required placeholder="Password"
						maxlength="6" autocomplete="off">
				</div>

				<div class="forgot-password mb-3">
					<a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal"
						class="text-primary text-decoration-none">
						Forgot Password?
					</a>
				</div>

				<button class="btn btn-theme btn-lg w-100 mb-3" name="submit" type="submit">
					<i class="bi bi-box-arrow-in-right me-2"></i> SIGN IN
				</button>

				<div class="registration">
					Don't have an account?
					<a href="registration.php" class="text-primary">Create an account</a>
				</div>
			</form>
		</div>
	</div>

	<!-- Forgot Password Modal -->
	<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Forgot Password</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form name="forgot" method="post" onsubmit="return valid();" autocomplete="off">
					<div class="modal-body">
						<p>Enter your details below to reset your password.</p>
						<div class="mb-3">
							<input type="email" name="email" placeholder="Email" class="form-control" required
								autocomplete="off">
						</div>
						<div class="mb-3">
							<input type="text" name="contact" placeholder="Contact No" class="form-control" required
								autocomplete="off">
						</div>
						<div class="mb-3">
							<input type="password" class="form-control" placeholder="New Password" id="password"
								name="password" required maxlength="6" autocomplete="off">
						</div>
						<div class="mb-3">
							<input type="password" class="form-control" placeholder="Confirm Password"
								id="confirmpassword" name="confirmpassword" required maxlength="6" autocomplete="off">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary" name="change">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Bootstrap 5 JS and Popper.js -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

	<script>
		function valid() {
			if (document.forgot.password.value != document.forgot.confirmpassword.value) {
				alert("Password and Confirm Password do not match!");
				document.forgot.confirmpassword.focus();
				return false;
			}
			return true;
		}
	</script>
</body>

</html>