var $ = jQuery.noConflict();
$(document).ready(function () {
  $(window).load(function () {
    let currentPage = 1;
    $("#archive-morelink").on("click", function (e) {
      e.preventDefault();
      let woox_ajaxmore = $(".woox-ajaxmore");
      currentPage++;
      shortcodecat = $("#shortcode-cat").text();
      shortcodestyle = $("#shortcode-style").text();
      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: ajax_object.ajax_url,
        data: {
          action: "archive_loadmore",
          paged: currentPage,
          shortcodecat: shortcodecat,
          shortcodestyle: shortcodestyle,
        },
        beforeSend: function () {
          woox_ajaxmore.show();
          $("#archive-morelink").hide();
        },
        success: function (response) {
          woox_ajaxmore.hide();
          $("#archive-morelink").show();
          if (!response || response.error) return;
          if (response.status == "ok") {
            console.log(response.ajaxcount);
            if (response.ajaxcount < 3) {
              $("#archive-morelink").hide();
              // $(".archive-more").html("All post loaded.");
            }
            $("#woox-archive-wrap").append(response.data);
          } else {
            alert("Response else");
          }
        },
      });
    });

    let countryPage = 0;
    $("#country-morelink").on("click", function (e) {
      e.preventDefault();
      countryPage = countryPage + 3;
      let country_ajaxmore = $(".country-ajaxmore");
      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: ajax_object.ajax_url,
        data: {
          action: "country_loadmore",
          countryPage: countryPage,
        },
        beforeSend: function () {
          country_ajaxmore.show();
          $("#country-morelink").hide();
        },
        success: function (response) {
          country_ajaxmore.hide();
          $("#country-morelink").show();
          if (!response || response.error) return;
          if (response.status == "ok") {
            $(".destination-archive-wrap").append(response.data);
            console.log(response.totallocations);
            if (response.totallocations < 3) {
              $("#country-morelink").hide();
              // $(".archive-more").html("All post loaded.");
            }
          } else {
            alert("Response else");
          }
        },
      });
    });

    let givewayPage = 0;
    $("#giveway-morelink").on("click", function (e) {
      e.preventDefault();
      givewayPage = givewayPage + 3;
      let giveway_ajaxmore = $(".giveway-ajaxmore");
      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: ajax_object.ajax_url,
        data: {
          action: "giveway_loadmore",
          givewayPage: givewayPage,
        },
        beforeSend: function () {
          giveway_ajaxmore.show();
          $("#giveway-morelink").hide();
        },
        success: function (response) {
          giveway_ajaxmore.hide();
          $("#giveway-morelink").show();
          if (!response || response.error) return;
          if (response.status == "ok") {
            $("#giveways-archive-wrap").append(response.data);
            if (response.ajaxcount < 3) {
              $("#giveway-morelink").hide();
              // $(".archive-more").html("All post loaded.");
            }
          } else {
            alert("Response else");
          }
        },
      });
    });

    let campaignPage = 0;
    $("#campaign-morelink").on("click", function (e) {
      e.preventDefault();
      campaignPage = campaignPage + 3;
      let giveway_ajaxmore = $(".campaign-ajaxmore");
      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: ajax_object.ajax_url,
        data: {
          action: "campaign_loadmore",
          campaignPage: campaignPage,
        },
        beforeSend: function () {
          giveway_ajaxmore.show();
          $("#campaign-morelink").hide();
        },
        success: function (response) {
          giveway_ajaxmore.hide();
          $("#campaign-morelink").show();
          if (!response || response.error) return;
          if (response.status == "ok") {
            $("#campaign-archive-wrap").append(response.data);
            if (response.ajaxcount < 3) {
              $("#campaign-morelink").hide();
              // $(".archive-more").html("All post loaded.");
            }
          } else {
            alert("Response else");
          }
        },
      });
    });
  });
});
