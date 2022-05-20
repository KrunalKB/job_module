// (function ($) {
//   $("tr").slice(3).hide();
//   $("#loadMore").on("click", function (e) {
//     e.preventDefault();
//     $("tr:hidden").slice(0, 2).slideDown();
//     if ($("tr:hidden").length == 0) {
//       $("#loadMore").fadeOut("slow");
//       $("#msg").html("No more data available to read!");
//     }
//   });
// })(jQuery);

// (function ($) {
//   $(".jobData").slice(2).hide();
//   $("#loadMore").on("click", function (e) {
//     e.preventDefault();
//     $(".jobData:hidden").slice(0,2).slideDown();
//     if ($(".jobData:hidden").length == 0) {
//       $("#loadMore").fadeOut("slow");
//       $("#msg").html("No more data available to read!");
//     }
//   });
// })(jQuery);

(function ($) {
  $(document).ready(function () {
    $.ajax({
      url: myVar.ajax_url,
      type: "POST",
      data: {
        action: "job_listing_hook",
      },
      success: function (response) {
        if (response) {
          $("#response").append(response);
        }
      },
    });
  });
  // var page = 1;
  // $("#loadMore").on("click", function () {
  //   $.ajax({
  //     url: myVar.ajax_url,
  //     type: "POST",
  //     data: {
  //       action: "job_listing_hook",
  //       offset: page * 2,
  //     },
  //     success: function (response) {
  //       if (response) {
  //         page++;
  //         $("#response").append(response);
  //       } else {
  //         $("#loadMore").fadeOut("slow");
  //         $("#msg").html("No more data available to read!");
  //       }
  //     },
  //   });
  // });
})(jQuery);
