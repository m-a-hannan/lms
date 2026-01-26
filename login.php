<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Login – Booklore</title>
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
  background: transparent;
}

section {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100%;
  background: transparent;
}

.form-box {
  position: relative;

  width: 400px;

  height: 450px;

  background: transparent;

  border: none;

  border-radius: 20px;

  backdrop-filter: blur(15px) brightness(80%);

  display: flex;

  justify-content: center;

  align-items: center;
}

h2 {
  font-size: 2em;

  color: #fff;

  text-align: center;
}

.inputbox {
  position: relative;

  margin: 30px 0;

  width: 310px;

  border-bottom: 2px solid #fff;
}

.inputbox label {
  position: absolute;

  top: 50%;

  left: 5px;

  transform: translateY(-50%);

  color: #fff;

  font-size: 1em;

  pointer-events: none;

  transition: 0.5s;
}

/* animations: start */

input:focus ~ label,
input:valid ~ label {
  top: -5px;
}

/* animation:end */

.inputbox input {
  width: 100%;

  height: 50px;

  background: transparent;

  border: none;

  outline: none;

  font-size: 1em;

  padding: 0 35px 0 5px;

  color: #fff;
}

.inputbox i {
  position: absolute;

  right: 8px;

  color: #fff;

  font-size: 1.2em;

  top: 20px;
}

.forget {
  margin: -10px 0 17px;

  font-size: 0.9em;

  color: #fff;

  display: flex;

  justify-content: space-between;
}

.forget label input {
  margin-right: 3px;
}

.forget a {
  color: #fff;

  text-decoration: none;
}

.forget a:hover {
  text-decoration: underline;
}

button {
  width: 100%;

  height: 40px;

  border-radius: 40px;

  background-color: #fff;

  border: none;

  outline: none;

  cursor: pointer;

  font-size: 1em;

  font-weight: 600;
}

.register {
  font-size: 0.9em;

  color: #fff;

  text-align: center;

  margin: 25px 0 10px;
}

.register p a {
  text-decoration: none;

  color: #fff;

  font-weight: 600;
}

.register p a:hover {
  text-decoration: underline;
}

/* Responsiveness:Start */
@media screen and (max-width: 480px) {
  .form-box {
    width: 100%;
    border-radius: 0px;
  }
}
/* Responsiveness:End */

	</style>
</head>

<body>

	<section>

		<div class="form-box">

			<div class="form-value">

				<form>

					<h2>Login</h2>

					<div class="inputbox">

					<i class="bi bi-envelope-at"></i>

						<input type="email" required>

						<label>Email</label>

					</div>

					<div class="inputbox">

					<i class="bi bi-lock"></i>

						<input type="password" required>

						<label>Password</label>

					</div>

					<div class="forget">

						<label><input type="checkbox">Remember Me</label>

						<a href="#">Forgot Password</a>

					</div>

					<button>Log In</button>

					<div class="register">

						<p>Don't have an account? <a href="register.php" target="_blank">Sign Up</a></p>

					</div>

				</form>

			</div>

		</div>

	</section>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
