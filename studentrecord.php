<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

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

// Check if the user has submitted a student record
$username = $_SESSION['username'];
$check_query = "SELECT * FROM studentrecord WHERE username=?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$hasRecord = $result->num_rows > 0;
$stmt->close();

// Handle form submission (Add Record, Update Record, Delete Record)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $class = $_POST['class'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];

        $add_query = "INSERT INTO studentrecord (name, class, gender, dob, username) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($add_query);
        $stmt->bind_param("sssss", $name, $class, $gender, $dob, $username);
        $stmt->execute();
        $stmt->close();

        $_SESSION['add_success'] = true; // Set success session variable

        // Redirect back to studentrecord.php after adding the record
        header("Location: studentrecord.php");
        exit();
    } elseif (isset($_POST['update'])) {
        $studentrecordID = $_POST['id'];
        $name = $_POST['name'];
        $class = $_POST['class'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];

        $update_query = "UPDATE studentrecord SET name=?, class=?, gender=?, dob=? WHERE studentrecordID=? AND username=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssis", $name, $class, $gender, $dob, $studentrecordID, $username);
        $stmt->execute();
        $stmt->close();

        $_SESSION['update_success'] = true; // Set success session variable

        // Redirect back to studentrecord.php after updating the record
        header("Location: studentrecord.php");
        exit();
    } elseif (isset($_POST['delete'])) {
        $studentrecordID = $_POST['id'];

        $delete_query = "DELETE FROM studentrecord WHERE studentrecordID=? AND username=?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("is", $studentrecordID, $username);
        $stmt->execute();
        $stmt->close();

        $_SESSION['delete_success'] = true; // Set success session variable

        // Redirect back to studentrecord.php after deleting the record
        header("Location: studentrecord.php");
        exit();
    }
}

// Fetch user's records
$fetch_query = "SELECT * FROM studentrecord WHERE username=?";
$stmt = $conn->prepare($fetch_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Record</title>
    <link href="./output.css" rel="stylesheet">
</head>
<body class=" flex flex-col justify-center items-center ">
 <div class=" flex flex-col items-center justify-center border shadow-md bg-indigo-50"> 
    <h2 class="text-xl font-bold underline text-gray-500 mt-2">Welcome <?php echo $_SESSION['username']; ?> to the Student Record Page</h2>
    <p class=" font-bold text-gray-500">Logged in as: <?php echo $_SESSION['username']; ?></p>

    <!-- Display alert if add, update, or delete was successful -->
    <?php
    if (isset($_SESSION['add_success'])) {
        echo '<script>alert("Record added successfully!");</script>';
        unset($_SESSION['add_success']); // Unset the success session variable
    } elseif (isset($_SESSION['update_success'])) {
        echo '<script>alert("Record updated successfully!");</script>';
        unset($_SESSION['update_success']); // Unset the success session variable
    } elseif (isset($_SESSION['delete_success'])) {
        echo '<script>alert("Record deleted successfully!");</script>';
        unset($_SESSION['delete_success']); // Unset the success session variable
    }
    ?>

    <!-- Add Record Form (only if user has no record) -->
    <?php if (!$hasRecord) { ?>
    <h3 class="text-xl font-bold underline text-gray-500">Add Record</h3>
    <form method="post" class=" p-16 flex flex-col space-y-1">
        <label class=" font-bold">Name:</label>
        <input type="text" name="name" required><br>
        <label class=" font-bold">Class:</label>
        <input type="text" name="class" required><br>
        <label class=" font-bold">Gender:</label>
        <input type="text" name="gender" required><br>
        <label class=" font-bold">Date of Birth:</label>
        <input type="date" name="dob" required><br>
        <input type="submit" name="add" value="Add Record" class=" border bg-green-600 hover:bg-green-800 rounded-md p-1">
    </form>
    <?php } ?>

    <!-- Display User's Records (if any) -->
    <?php if ($result->num_rows > 0) { ?>
    <h3 class="text-xl font-bold underline text-gray-500">Your Record</h3>
    <table border="">
        <tr>
            
            <th>Name</th>
            <th>Class</th>
            <th>Gender</th>
            <th>Date of Birth</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['class']; ?></td>
                <td><?php echo $row['gender']; ?></td>
                <td><?php echo $row['dob']; ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="id" value="<?php echo $row['studentrecordID']; ?>">
                        <input type="text" name="name" value="<?php echo $row['name']; ?>" required>
                        <input type="text" name="class" value="<?php echo $row['class']; ?>" required>
                        <input type="text" name="gender" value="<?php echo $row['gender']; ?>" required>
                        <input type="date" name="dob" value="<?php echo $row['dob']; ?>" required>
                        <input type="submit" name="update" value="Update" class=" bg-green-600 hover:bg-green-800 rounded-md p-1">
                        <input type="submit" name="delete" value="Delete" class=" bg-red-600 hover:bg-red-800 rounded-md p-1">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php } ?>
 </div>
</body>
</html>

<?php
$conn->close();
?>
