(function ($) {
  $("#searchresult").hide();
  function job_form() {
    var client = $("#client").val();
    var contractor = $("#contractor").val();
    var jobname = $("#jobname").val();
    var jobdesc = $("#jobdesc").val();
    var price = $("#price").val();
    var image = $("#image").val();
    $.ajax({
      url: myVar.ajax_url,
      type: "POST",
      data: {
        action: "jobform_hook",
        nonce: myVar.nonce,
        client: client,
        contractor: contractor,
        jobname: jobname,
        jobdesc: jobdesc,
        price: price,
        image: image,
      },
      success: function (response) {
        alert("Submitted successfully");
      },
    });
  }
  $(".job_registerbtn").on("click", function (e) {
    e.preventDefault();
    job_form();
  });
})(jQuery);

(function ($) {
  $("#client").keyup(function (e) {
    e.preventDefault;
    var search = $(this).val();
    if (search != "") {
      $.ajax({
        url: myVar.ajax_url,
        type: "POST",
        data: {
          action: "search_user_hook",
          search: search,
        },
        success: function (response) {
          $("#searchresult").fadeIn("fast").html(response);
        },
      });
    } else {
      $("#searchresult").fadeOut();
    }
  });
  $("#searchresult").on("click", function () {
    $("#client").val($(this).text());
    $("#searchresult").fadeOut("fast");
  });
})(jQuery);
