$(document).ready(function () {
  // Function to handle filtering based on book name
  function filterBooks() {
    var bookName = $("#bookNameFilter").val(); // Assuming you have an input with id 'bookNameFilter'

    // Make an AJAX request to fetch filtered data
    $.ajax({
      url: "filter_books.php", // Create a new PHP file for handling the AJAX request
      method: "POST",
      data: { bookName: bookName },
      success: function (data) {
        // Update the table body with the filtered data
        $("tbody").html(data);
      },
      error: function (xhr, status, error) {
        console.error("AJAX error: " + status, error);
      },
    });
  }

  // Bind the filterBooks function to the input change event
  $("#bookNameFilter").on("input", function () {
    filterBooks();
  });
});
