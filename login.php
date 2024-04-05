<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <link href="./output.css" rel="stylesheet">
</head>
<body class=" flex flex-col justify-center items-center ">
    <div class=" flex  flex-col items-center justify-center border shadow-md mt-14 bg-indigo-50"> 
        <h2 class="text-xl font-bold underline text-gray-500 mt-2">Login</h2>
            <form method="post" action="login.php" class=" p-16 ">
             <label for="username" class=" font-bold">Username:</label>
             <input type="text" id="username" name="username" required><br><br>
             <label for="password" class=" font-bold">Password:</label>
             <input type="password" id="password" name="password" required><br><br>
             <input type="submit" value="Login" class=" bg-green-600 hover:bg-green-800 rounded-md p-1">
            </form>
    </div>
</body>
</html>
<?php
session_start(); // Start session for error messages

// Database connection details
$db_host = '';
$db_user = '';
$db_pass = '';
$db_name = '';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve user from database
    $retrieve_query = "SELECT * FROM cruzerxperson WHERE username=?";
    $stmt = $conn->prepare($retrieve_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Successful login, redirect to studentrecord.php
            $_SESSION['username'] = $username;
            header("Location: studentrecord.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password. Please try again.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Username not found. Please register first.";
        header("Location: index.php");
        exit();
    }
}

$conn->close();
?>
