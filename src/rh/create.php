<?php include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $salary = $_POST['salary'];

    $sql = "INSERT INTO employees (first_name, last_name, email, position, department, salary)
            VALUES ('$first_name', '$last_name', '$email', '$position', '$department', '$salary')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Employee - RH Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3>Add New Employee</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="mb-3">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Position</label>
                                <input type="text" name="position" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Department</label>
                                <select name="department" class="form-control">
                                    <option>IT</option>
                                    <option>HR</option>
                                    <option>Finance</option>
                                    <option>Marketing</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Salary</label>
                                <input type="number" step="0.01" name="salary" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Save Employee</button>
                            <a href="index.php" class="btn btn-secondary w-100 mt-2">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
