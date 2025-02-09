<?php
session_start();
require 'connect.php';

// Fetch pending specialist sign-up requests
$query = "SELECT id, name, certificate, status FROM specialist";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error fetching data: " . mysqli_error($conn);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="custom.css" />
    </head>

    <body>
        <header class="bg-dark text-white text-center py-4">
            <h1>Admin Dashboard</h1>
        </header>
        <main class="container my-5">
            <h2 class="mb-4 text-center">Pending Specialist Certificates</h2>

            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Certificate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>
                                <a href="uploads/<?php echo htmlspecialchars($row['certificate']); ?>" target="_blank"
                                    class="btn btn-info btn-sm">View Certificate</a>
                            </td>
                            <td>
                                <?php echo $row['status'] ?> 
                                <?php if(isset($row['status']) && $row['status'] == "pending"): ?>
                                <button onclick="acceptHandler('<?php echo $row['id']; ?>')"
                                    class="btn btn-success btn-sm me-2">Accept</button>
                                <button onclick="declineHandler('<?php echo $row['id']; ?>')"
                                    class="btn btn-danger btn-sm">Decline</button>
                                <?php elseif(isset($row['status']) && $row['status'] == "approved"): ?>
                                <span class="badge bg-success">Certificate Approved</span>
                                <?php elseif(isset($row['status']) && $row['status'] == "decliend"): ?>
                                <span class="badge bg-danger">Certificate Declined</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-center">No pending specialist requests.</p>
            <?php endif; ?>
        </main>

        <footer class="bg-light text-center py-3">
            <p>&copy; 2024 My Website</p>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        function acceptHandler(planId) {
            fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'approved',
                        id: planId,
                        who: "admin",
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status == "approved") {
                        window.location.reload();
                    } else {
                        alert('Error updating the status');
                    }
                });
        }

        function declineHandler(planId) {
            fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'decliend',
                        id: planId,
                        who: "admin"
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.status == "decliend") {
                        window.location.reload();
                    } else {
                        alert('Error updating the status');
                    }
                })
                .catch(e => {
                    console.debug(e);
                });
        }
        </script>
    </body>

</html>