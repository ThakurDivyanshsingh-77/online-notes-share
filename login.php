<?php
include "includes/db.php";
include "includes/header.php";

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit;
        } else {
            $msg = "❌ Invalid password.";
        }
    } else {
        $msg = "❌ User not found.";
    }
}
?>
<h2>Login</h2>
<p style="color:red;"><?php echo $msg; ?></p>
<form method="post">
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Login</button>
</form>
<?php include "includes/footer.php"; ?>
