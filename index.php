<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link href="./output.css" rel="stylesheet">
</head>
<body class=" flex flex-col justify-center items-center ">
 <div class=" flex  flex-col items-center justify-center border shadow-md mt-14 bg-indigo-50">  
    <h2 class="text-xl font-bold underline text-gray-500 mt-2">User Registration</h2>
    <form method="post" action="index.php" class=" p-16 ">
        <label for="username" class=" font-bold">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password" class=" font-bold">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Register" class=" bg-green-600 hover:bg-green-800 rounded-md p-1">
    </form>
    <P class=" font-bold ">already a registered user?</P>
    <a href='login.php' class=" bg-gray-500 hover:bg-gray-700 rounded-md p-2 mb-3">Login here</a>
 </div>
</body>
</html>

<?php
// Database connection
$db_host = 'localhost';
$db_user = '';
$db_pass = '';
$db_name = '';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password for security

    // Check if username already exists
    $check_query = "SELECT * FROM cruzerxperson WHERE username='$username'";
    $check_result = $conn->query($check_query);
    if ($check_result->num_rows > 0) {
        
        echo '<script>alert("Username already exists. Please choose a different username.");</script>';
    } else {
        // Insert new user into database
        $insert_query = "INSERT INTO cruzerxperson (username, password) VALUES ('$username', '$password')";
        if ($conn->query($insert_query) === TRUE) {
            echo '<script>alert("user registered successfully!");</script>';
        } else {
            echo "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }
}

$conn->close();

?>
