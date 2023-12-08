$(document).ready(function () {
  // Function to handle filtering based on user full name
  function filterUsers() {
    var userName = $("#userNameFilter").val(); // Assuming you have an input with id 'userNameFilter'

    // Make an AJAX request to fetch filtered data
    $.ajax({
      url: "filter_users.php", // Create a new PHP file for handling the AJAX request
      method: "POST",
      data: { userName: userName },
      success: function (data) {
        // Update the table body with the filtered data
        $("tbody").html(data);
      },
      error: function (xhr, status, error) {
        console.error("AJAX error: " + status, error);
      },
    });
  }

  // Bind the filterUsers function to the input change event
  $("#userNameFilter").on("input", function () {
    filterUsers();
  });
});
