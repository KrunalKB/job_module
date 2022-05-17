(function ($) {
  $("tr").slice(3).hide();
  $("#loadMore").on("click", function (e) {
    e.preventDefault();
    $("tr:hidden").slice(0, 2).slideDown();
    if ($("tr:hidden").length == 0) {
      $("#loadMore").fadeOut("slow");
      $("#msg").html("No more data available to read!");
    }
  });
})(jQuery);
