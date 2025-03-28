<?php

include('includes/config.php');
error_reporting(0);

if (isset($_POST['submit'])) {

	// Validate form inputs
	if (empty($_POST['fullname']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['contactno'])) {
		$errormsg = "Please fill in all fields.";
	} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$errormsg = "Invalid email format.";
	} elseif (strlen($_POST['password']) < 6) {
		$errormsg = "Password must be at least 6 characters.";
	} elseif (strlen($_POST['contactno']) != 10) {
		$errormsg = "Contact number must be 10 digits.";
	} elseif (!preg_match('/^[0-9]+$/', $_POST['contactno'])) {
		$errormsg = "Contact number must be numeric.";
	} else {
		$fullname = $_POST['fullname'];
		$email = $_POST['email'];
		$password = md5($_POST['password']);
		$contactno = $_POST['contactno'];
		$status = 1;
		$query = mysqli_query($bd, "insert into users(fullName,userEmail,password,contactNo,status) values('$fullname','$email','$password','$contactno','$status')");
		$msg = "Registration successfull. Now You can login !";
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CMS | User Registration</title>

	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link rel="stylesheet" href="assets/css/registration.css">
</head>

<body>
	<div class="container">
		<div class="registration-container">
			<button class="back-button" type="button" onclick="window.location.href='../index.html'">
				<i class="bi bi-arrow-left"></i>
			</button>

			<div class="registration-header">
				<h2><i class="bi bi-person-plus-fill me-2"></i>User Registration</h2>
			</div>

			<form method="post" autocomplete="off">
				<?php if ($msg): ?>
					<div class="alert alert-success mb-3">
						<?php echo htmlentities($msg); ?>
					</div>
				<?php endif; ?>

				<?php if ($errormsg): ?>
					<div class="alert alert-danger">
						<?php echo htmlentities($errormsg); ?>
					</div>
				<?php endif; ?>

				<input type="text" class="form-control" placeholder="Full Name" name="fullname" required autofocus
					autocomplete="off">

				<input type="email" class="form-control" placeholder="Email" id="email" onBlur="userAvailability()"
					name="email" required autocomplete="off">
				<span id="user-availability-status1"></span>

				<input type="password" class="form-control" placeholder="Password" required name="password"
					maxlength="6" autocomplete="off">

				<input type="text" class="form-control" maxlength="10" name="contactno" placeholder="Contact No"
					required autocomplete="off">

				<button class="btn btn-theme btn-lg w-100 mb-3" type="submit" name="submit" id="submit">
					<i class="bi bi-person-plus me-2"></i> Register
				</button>

				<div class="login-link">
					Already Registered?
					<a href="index.php" class="text-primary">login</a>
				</div>
			</form>
		</div>
	</div>

	<!-- Bootstrap 5 JS and Popper.js -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<script>
		function userAvailability() {
			$("#loaderIcon").show();
			$.ajax({
				url: "check_availability.php",
				data: 'email=' + $("#email").val(),
				type: "POST",
				success: function (data) {
					$("#user-availability-status1").html(data);
					$("#loaderIcon").hide();
				},
				error: function () {
					$("#user-availability-status1").html("Error checking availability");
					$("#loaderIcon").hide();
				}
			});
		}
	</script>
</body>

</html>