<?php
session_start();
require_once("settings.php");

$conn = @mysqli_connect($host, $user, $pswd, $dbmn) or die('Failed to connect to server');

if (!$conn) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

$errors = [];

// Function to validate input fields
function validateInput($input, $type, $maxLength, $conn, &$errors) {
    if (isset($_POST[$input])) {
        $value = trim($_POST[$input]);
        if (!empty($value) && strlen($value) <= $maxLength) {
            if ($type == 'email') {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$input] = "Your email must match the correct format.";
                } else {
                    $query = "SELECT * FROM friends WHERE friend_email = '$value'"; 
                    $result = mysqli_query($conn, $query); 
                    if (mysqli_num_rows($result) <= 0) { 
                        $errors[$input] = "This email doesn't exist."; 
                    }
                }
            } elseif ($type == 'password') {
                if (!ctype_alnum($value)) {
                    $errors[$input] = "Password must contain only letters and numbers.";
                } else {
                    $email = mysqli_real_escape_string($conn, $_POST['email']);
                    $query = "SELECT * FROM friends WHERE friend_email = '$email'"; 
                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        if ($value != $row['password']) {
                            $errors[$input] = "Incorrect password."; 
                        }
                    } else {
                        $errors[$input] = "Failed to retrieve password.";
                    }
                }
            }
        } else {
            $errors[$input] = "Your $type can't be empty and must be <= $maxLength characters.";
        }
    } else {
        $errors[$input] = "Please enter your $type.";
    }
}

// Handle form submission BEFORE outputting any HTML
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateInput("email", "email", 50, $conn, $errors);
    validateInput("password", "password", 20, $conn, $errors);

    if (empty($errors)) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $query = "SELECT friend_id FROM friends WHERE friend_email = '$email'";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['friend_id'] = $row['friend_id'];
            mysqli_close($conn);
            header("Location: friendlist.php"); // Redirect before HTML starts
            exit();
        } else {
            echo "<p>Failed to retrieve friend_id.</p>";
        }
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="description" content="Web application development" />
    <meta name="keywords" content="PHP" />
    <meta name="author" content="Huy Vu Tran" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Sign In Page</title>
</head>
<body>
    <div class="container-fluid p-5 text-black ">
        <form action="login.php" method="post">
            <h2>My Friend System Login Page</h2><br />
            <div class="mb-3 mt-3">
                <label for="email">Email</label>
                <input type="text" name="email" class="form-control" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"><br />
                <?php if (isset($errors['email'])) echo "<p>" . htmlspecialchars($errors['email']) . "</p>"; ?>
            </div>
            <div class="mb-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password"><br />
                <?php if (isset($errors['password'])) echo "<p>" . htmlspecialchars($errors['password']) . "</p>"; ?>
            </div>
            <input type="submit" value="Login" name="submit" class="btn btn-primary mb-3">
            <input type="reset" value="Clear" class="btn btn-secondary mb-3">
        </form>
        <div class="mt-3">
            <a href="index.php" class="text-decoration-none btn btn-success">Home</a>
        </div>
    </div>
</body>
</html>
