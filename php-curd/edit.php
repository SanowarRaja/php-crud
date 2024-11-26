<?php
include 'db.php';

if (!isset($_GET['id'])) {
    header('Location: view.php'); // Redirect if no ID is provided
    exit();
}

$id = $_GET['id'];

// Fetch the current user details
$sql = "SELECT * FROM users WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
    header('Location: view.php'); // Redirect if user not found
    exit();
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $profile_image = $user['profile_image']; // Keep the existing image by default

    // Handle new image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // Upload the new image
        $profile_image = 'uploads/' . time() . '_' . $_FILES['profile_image']['name'];
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image);

        // Optionally, delete the old image if needed
        if (!empty($user['profile_image']) && file_exists($user['profile_image'])) {
            unlink($user['profile_image']); // Delete old image file
        }
    }

    // Update user details in the database
    $sql = "UPDATE users SET username = '$username', email = '$email', profile_image = '$profile_image' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header('Location: view.php'); // Redirect to view page after update
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <!-- Include Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-header text-center">
            <h2>Edit User</h2>
        </div>
        <div class="card-body">
            <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="profile_image" class="form-label">Profile Image</label><br>
                    <?php if (!empty($user['profile_image'])) { ?>
                        <img src="<?php echo $user['profile_image']; ?>" alt="Current Image" style="width: 100px; height: 100px; border-radius: 50%; margin-bottom: 10px;">
                    <?php } else { ?>
                        <span>No image uploaded</span><br>
                    <?php } ?>
                    <input type="file" class="form-control" id="profile_image" name="profile_image">
                </div>
                <button type="submit" class="btn btn-primary w-100">Update User</button>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="view.php" class="btn btn-secondary">Back to User List</a>
        </div>
    </div>
</div>
</body>
</html>
