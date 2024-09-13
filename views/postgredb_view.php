<?php
// PostgreSQL Configuration
$host = 'ldb.tha.kz'; // Host from your configuration
$port = '5432';      // Port from your configuration
$dbname = 'dev_census'; // Database name from your configuration
$user = 'dev_census'; // User from your configuration
$password = 'Y0xC!A68^Iwi'; // Password from your configuration

// Create a connection to the database
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check the connection
if (!$conn) {
    die("Error connecting to the database: " . pg_last_error());
}

// Initialize variables for search
$phone = '';
$resultsHtml = '';

// Check if the form has been submitted
if (isset($_GET['search'])) {
    // Get the phone number from the request
    $phone = isset($_GET['phone']) ? pg_escape_string($_GET['phone']) : '';

    // Perform the query
    $query = "SELECT * FROM external_users WHERE phone = '$phone'";
    $result = pg_query($conn, $query);

    // Check for errors in the query
    if (!$result) {
        $resultsHtml = "Error executing query: " . pg_last_error();
    } else {
        // Output results as HTML table
        $resultsHtml .= "<table border='1'>";
        $resultsHtml .= "<tr><th>ID</th><th>Phone</th><th>Other Columns</th></tr>";

        while ($row = pg_fetch_assoc($result)) {
            $resultsHtml .= "<tr>";
            $resultsHtml .= "<td>" . htmlspecialchars($row['id']) . "</td>";
            $resultsHtml .= "<td>" . htmlspecialchars($row['phone']) . "</td>";
            // Add other columns here if necessary
            $resultsHtml .= "</tr>";
        }

        if (pg_num_rows($result) === 0) {
            $resultsHtml .= "<tr><td colspan='3'>No data found</td></tr>";
        }

        $resultsHtml .= "</table>";
    }
}

// Close the database connection
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postgres Search</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <h1>Search PostgreSQL Database</h1>

    <form method="get" action="">
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
        <input type="submit" name="search" value="Search">
    </form>

    <div class="results">
        <?php echo $resultsHtml; ?>
    </div>
</div>
</body>
</html>
