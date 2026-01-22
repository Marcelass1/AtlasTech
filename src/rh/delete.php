session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize ID
    $sql = "DELETE FROM employees WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=deleted");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    header("Location: index.php");
}

$conn->close();

