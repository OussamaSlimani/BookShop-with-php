$(document).ready(function () {
  // Function to handle filtering based on book name
  function filterBooks() {
    var bookName = $("#bookNameFilter").val();

    // Make an AJAX request to fetch filtered data
    $.ajax({
      url: "filter_books.php",
      method: "POST",
      data: { bookName: bookName },
      success: function (data) {
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
