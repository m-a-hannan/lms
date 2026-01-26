<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Register – Booklore</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap 5.3 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

	<style>
* {
  margin: 0;
  padding: 0;
  font-family: "Trebuchet MS", "Lucida Sans Unicode", "Lucida Grande",
    "Lucida Sans", Arial, sans-serif;
}

html, body {
  min-height: 100%;
}

body {
  background: url("https://images.unsplash.com/photo-1529148482759-b35b25c5f217") no-repeat center center fixed;
  background-size: cover;
}

body::before {
  content: "";
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
}

section {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100%;
}

.form-box {
  position: relative;
  width: 420px;
  padding: 36px 28px;
	margin: 25px 0;
  background: transparent;
  border: none;
	backdrop-filter: blur(15px) brightness(80%);
  border-radius: 20px;
  backdrop-filter: blur(16px);
  display: flex;
  justify-content: center;
  align-items: center;
}

h2 {
  font-size: 2em;
  color: #111827;
  text-align: center;
}

.inputbox {
  position: relative;
  margin: 24px 0;
  width: 310px;
  border-bottom: 2px solid rgba(17, 24, 39, 0.4);
}

.inputbox label {
  position: absolute;
  top: 50%;
  left: 5px;
  transform: translateY(-50%);
  color: #111827;
  font-size: 1em;
  pointer-events: none;
  transition: 0.5s;
}

input:focus ~ label,
input:valid ~ label {
  top: -5px;
}

.inputbox input {
  width: 100%;
  height: 50px;
  background: transparent;
  border: none;
  outline: none;
  font-size: 1em;
  padding: 0 35px 0 5px;
  color: #111827;
}

.inputbox .input-icon {
  position: absolute;
  right: 8px;
  color: #1a1c20;
  font-size: 1.2em;
  top: 20px;
}

button {
  width: 100%;
  height: 42px;
  border-radius: 40px;
  background-color: #111827;
  border: none;
  outline: none;
  cursor: pointer;
  font-size: 1em;
  font-weight: 600;
  color: #fff;
}

.register {
  font-size: 0.9em;
  color: #374151;
  text-align: center;
  margin: 18px 0 0;
}

.register a {
  text-decoration: none;
  color: #111827;
  font-weight: 600;
}

.register a:hover {
  text-decoration: underline;
}

@media screen and (max-width: 480px) {
  .form-box {
    width: calc(100% - 32px);
    border-radius: 16px;
  }
}

	</style>
</head>

<body>
	<section>
		<div class="form-box">
			<div class="form-value">
				<form>
					<h2>Register</h2>

					<div class="inputbox">
						<i class="bi bi-person input-icon"></i>
						<input type="text" name="username" required>
						<label>Username</label>
					</div>

					<div class="inputbox">
						<i class="bi bi-envelope input-icon"></i>
						<input type="email" name="email" required>
						<label>Email</label>
					</div>

					<div class="inputbox">
						<i class="bi bi-lock input-icon"></i>
						<input type="password" name="password" required>
						<label>Password</label>
					</div>

					<button type="submit">Create Account</button>

					<div class="register">
						<p>Already have an account? <a href="login.php">Login</a></p>
					</div>
				</form>
			</div>
		</div>
	</section>
	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
