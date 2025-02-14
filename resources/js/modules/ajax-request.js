$('.btn-submit-action').on('click', function (e) {
    $("#myForm").submit();
});

window.loadModal = function (url) {
    $("#body-content").load(url);
}

window.fadeOutAndClear = function (elementId, timeout = 2000) {
    setTimeout(() => {
        $(`#${elementId}`).fadeOut('slow', function () {
            $(this).html('');
            $(this).show(); // Ensure it’s not permanently hidden if you want to reuse it.
        });
    }, timeout);
};

window.ajaxRequest = function (url, data = {}, successCallback, errorCallback, completeCallback, method = 'POST') {
    $(this).buttonLoader('start');
    let settings = {
        url: url,
        type: method,
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            // Clear previous error states
            $('form .is-invalid').removeClass('is-invalid');
            $('form .invalid-feedback').empty();

            if (successCallback && typeof successCallback === 'function') {
                successCallback(response);
            }
            console.log(response);
            if (response.alert && !response.as_ajax) {
                $("#alert").html(response.alert);
                if (response.fade_out) {
                    let timeOut = parseInt(response.fade_out_time ?? 3000, 10);
                    window.fadeOutAndClear('alert', timeOut);
                }
            }

            if (response.redirect) {
                let delay = response.redirect_delay ?? 1500;
                setTimeout(function () {
                    window.location.href = response.redirect;
                }, delay);
            }
        },
        error: function (error) {
            // Clear previous error states
            $('form .is-invalid').removeClass('is-invalid');
            $('form .invalid-feedback').empty();

            let response = error.responseJSON;
            console.log(response);
            if (response && response.data && response.data.errors && response.individual_validation_error) {
                Object.keys(response.data.errors).forEach(function (field) {
                    let inputField = $(`[name="${field}"]`);
                    inputField.addClass('is-invalid');
                    let errorMessage = response.data.errors[field][0]; // Get the first error message
                    inputField.closest('div').find('.invalid-feedback').html(errorMessage);
                });
                // Display the general error message at the top if provided
                if (response.message && !response.top_validation_error) {
                    $("#alert").html(`<div class="alert alert-danger alert-dismissible" role="alert">${response.message}</div>`);
                }
            }

            if (error && error.responseJSON && response.top_validation_error) {
                if (response.alert) {
                    $("#alert").html(response.alert);
                }
            }

            if (errorCallback && typeof errorCallback === 'function') {
                errorCallback(error);
            }

            if (response.fade_out) {
                let timeOut = parseInt(response.fade_out_time ?? 3000, 10);
                window.fadeOutAndClear('alert', timeOut);
            }

            // Scroll to top if specified
            if (response.scroll_to_top) {
                window.scrollTo(0, 0);
            }
        },
        complete: function (data) {
            $(this).buttonLoader('stop');
            if (completeCallback && typeof completeCallback === 'function') {
                completeCallback();
            }
        }
    };

    if (method !== 'GET' && data instanceof FormData) {
        settings.processData = false;
        settings.contentType = false;
    }

    $.ajax(settings);
};

window.ajaxPost = function (url, data, successCallback, errorCallback, completeCallback) {
    ajaxRequest(url, data, successCallback, errorCallback, completeCallback);
};

window.ajaxGet = function (url, data, successCallback, errorCallback, completeCallback) {
    // For a GET request, we need to append data to the URL as query parameters
    const queryParams = $.param(data); // Use jQuery's $.param to convert data object to query string
    const fullUrl = url + (queryParams ? '?' + queryParams : '');

    // Call the ajaxRequest function with the full URL including query parameters
    // Since GET requests don't have a body, we pass an empty object ({}) for the data parameter
    ajaxRequest(fullUrl, {}, successCallback, errorCallback, completeCallback, 'GET');
};

window.ajaxPut = function (url, data, successCallback, errorCallback, completeCallback) {
    data.append('_method', 'put'); // Add the _method field with value 'PATCH'
    ajaxRequest(url, data, successCallback, errorCallback, completeCallback);
};

window.ajaxPatch = function (url, data, successCallback, errorCallback, completeCallback) {
    data.append('_method', 'patch'); // Add the _method field with value 'PATCH'
    ajaxRequest(url, data, successCallback, errorCallback, completeCallback);
};

window.executeAjaxCall = (method, url, data, successCallback, errorCallback, completeCallback) => {
    // Determine the AJAX function to use based on the method
    let ajaxFunction;
    switch (method.toLowerCase()) {
        case 'get':
            ajaxFunction = window.ajaxGet;
            break;
        case 'put':
            ajaxFunction = window.ajaxPut;
            break;
        case 'patch':
            ajaxFunction = window.ajaxPatch;
            break;
        // Add more cases as needed
        default:
            ajaxFunction = window.ajaxPost;
    }

    // Call the selected AJAX function
    ajaxFunction(url, data, successCallback, errorCallback, completeCallback);
}


window.resetForm = function () {

// Reset text, email, password fields
    $('#ajax-form input[type="text"], #ajax-form input[type="email"], #ajax-form input[type="password"], #ajax-form textarea').val('');

// Reset radio buttons and checkboxes
    $('#ajax-form input[type="radio"], #ajax-form input[type="checkbox"]').prop('checked', false);

// Reset select dropdowns
    $('#ajax-form select').prop('selectedIndex', 0);

}

$(document).delegate(".ajax-submit-button", "click", function (event) {
    event.preventDefault();
    $("#alert").html("");
    const btn = $(this);

    $('[tinymce-id]').each(function () {
        const tmcIdValue = $(this).attr('tinymce-id');
        tinymceEditorsMap[tmcIdValue].triggerSave();
    });

    const form = btn.closest('form'); // Using .closest() to find the parent form
    const formData = new FormData(form[0]);

    const requestMethod = form.attr('method'); // Using attr method

    executeAjaxCall(requestMethod, $(form).attr('action'), formData, function (data) {
        console.log('Received data:', data);
        if (data.submit_button_label !== null) {
            btn.text(data.submit_button_label)
        }
        // resetForm()
    }, function (error) {
        console.error('An unexpected error occurred:', error);
    }, function () {
        console.log('Request completed');
    });
});
