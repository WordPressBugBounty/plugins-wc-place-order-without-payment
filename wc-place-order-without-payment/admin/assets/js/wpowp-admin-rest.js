const api_name_space = 'wpowp-api/action';

(function ($) {

  $('#wpowp-settings-form').on('submit', function () {

    $.ajax({
      url: wpApiSettings.root + api_name_space + '/save-settings',
      method: 'POST',
      data: { 'settings': JSON.stringify($('#wpowp-settings-form').serializeArray()) },
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
      },
      success: function (data) {
        toastr.success(data.message, '', { "positionClass": "toast-bottom-right", });
      }
    }, function (data, status) {
      console.log(data);
    });

    return false;
  })

  $('#wpowp-settings-reset').on('click', function () {

    const isConfirmed = window.confirm(wpowp_admin_rest.confirm_reset_text);

    // Check if the user confirmed
    if (isConfirmed) {

      // Perform the reset action or call the appropriate function

      $.ajax({
        url: wpApiSettings.root + api_name_space + '/reset-settings',
        method: 'POST',
        beforeSend: function (xhr) {
          xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
        },
        success: function (data) {
          toastr.success(data.message, '', { "positionClass": "toast-bottom-right", });
          window.location.reload();
        }
      }, function (data, status) {
      });

    }

  })


})(jQuery)

