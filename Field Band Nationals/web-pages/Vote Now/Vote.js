// Function to handle the vote action
function vote(bandID) {
    // Display a message while processing the vote
    var bandName = document.querySelector(`button[onclick="vote(${bandID})"]`).previousElementSibling.innerText;
    document.getElementById("successMessage").innerText = `Processing your vote for ${bandName}...`;
    document.getElementById("myModal").style.display = "flex";

    // AJAX request to send the vote to the server
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "vote.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            // Handle the server response
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                
                if (response.status === "success") {
                    // Display success message
                    document.getElementById("successMessage").innerText = `You have successfully voted for ${bandName}!`;
                } else {
                    // Display error message from the server
                    document.getElementById("successMessage").innerText = `Error: ${response.message}`;
                }
            } else {
                // Display generic error message
                document.getElementById("successMessage").innerText = `Error submitting vote. Status code: ${xhr.status}`;
            }

            // Show the modal with the message
            document.getElementById("myModal").style.display = "flex";
        }
    };

    // Send the bandID to the server
    xhr.send("bandID=" + encodeURIComponent(bandID));
}

// Function to close the modal
function closeModal() {
    document.getElementById("myModal").style.display = "none";
}

// Close the modal if the user clicks anywhere outside of it
window.onclick = function(event) {
    var modal = document.getElementById("myModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

