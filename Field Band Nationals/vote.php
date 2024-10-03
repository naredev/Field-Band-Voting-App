<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get the team name from the POST request
    $team = isset($_POST['team']) ? $_POST['team'] : '';


    // Sanitize the team name to prevent SQL injection
    $team = htmlspecialchars($team);

    // Azure SQL Database connection details
    $servername = "fb.database.windows.net";
    $username = "username";
    $password = "password";
    $dbname = "Field band";

    // Create a connection to the Azure SQL Database using PDO
    try {
        $conn = new PDO("sqlsrv:server=$servername;Database=$dbname", $username, $password);
        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Return an error message if the connection fails
        http_response_code(500);
        echo "Connection failed: " . $e->getMessage();
        exit;
    }

    // Update the votes for the selected team
    try {
        // First, check if the team exists in the Teams table
        $stmt = $conn->prepare("SELECT Votes FROM Teams WHERE TeamName = :teamName");
        $stmt->bindParam(':teamName', $team);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Team found, update the vote count
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $currentVotes = $row['Votes'];
            $newVotes = $currentVotes + 1;

            // Update the vote count in the Teams table
            $updateStmt = $conn->prepare("UPDATE Teams SET Votes = :newVotes WHERE TeamName = :teamName");
            $updateStmt->bindParam(':newVotes', $newVotes);
            $updateStmt->bindParam(':teamName', $team);

            if ($updateStmt->execute()) {
                // Return success message
                http_response_code(200);
                echo "Vote submitted successfully!";
            } else {
                // Return error message if the update fails
                http_response_code(500);
                echo "Error updating vote count!";
            }
        } else {
            // Team not found
            http_response_code(404);
            echo "Team not found!";
        }
    } catch (PDOException $e) {
        // Return an error if there's an issue with the query
        http_response_code(500);
        echo "Error: " . $e->getMessage();
    }

    // Close the connection
    $conn = null;

} else {
    // Return error if the request method is not POST
    http_response_code(405); // 405 Method Not Allowed
    echo "Invalid request method!";
}
?>