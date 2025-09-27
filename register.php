<?php
include "includes/db.php";
include "includes/header.php";

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $msg = "❌ Email already exists.";
    } else {
        $sql = "INSERT INTO users (name,email,password) VALUES ('$name','$email','$pass')";
        if ($conn->query($sql)) {
            $msg = "✅ Registration successful. <a href='login.php'>Login</a>";
        } else {
            $msg = "❌ Error: " . $conn->error;
        }
    }
}
?>
<h2>Register</h2>
<p style="color:red;"><?php echo $msg; ?></p>
<form method="post">
  <input type="text" name="name" placeholder="Full Name" required><br>
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Register</button>
</form>
<?php include "includes/footer.php"; ?>
