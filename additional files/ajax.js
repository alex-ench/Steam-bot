$(document).ready(function () {
    $('#options').submit(function (event) {
        event.preventDefault();
        let infinitelyStop = 0;
        let value = $('#start-randomize option:selected').val();

        function random() {
            $.ajax({
                url: 'general.php',
                type: 'POST',
                data: $('#options').serialize(),
                beforeSend: function () {
                    $('#start-profile-random').prop('disabled', true);
                    $('.popup-fade').fadeIn();
                },
                success: function (response) {
                    $("#randomize-answer").html(response);
                    if ($(".main-info-checkbox#nickname").is(':checked')) {
                        $('.profile-item#nickname').text($(response).find('.item-output#nickname').text());
                    }
                    if ($(".main-info-checkbox#real-name").is(':checked')) {
                        $('.profile-item#real-name').text($(response).find('.item-output#real-name').text());
                    }
                    if ($(".main-info-checkbox#location").is(':checked')) {
                        $('.profile-item#location').text($(response).find('.item-output#location').text());
                    }
                    if ($(".profile-checkbox#avatar").is(':checked')) {
                        $('img#avatar-image-medium').prop('src', $(".item-output#avatar a").prop('href'));
                    }
                },
                complete: function () {
                    $('#start-profile-random').prop('disabled', false);
                    $('.popup-fade').fadeOut();
                    $("#randomize-answer")[0].scrollIntoView({behavior: "smooth", block: "start"});
                }
            });
        }

        function infinitely() {
            $('#stop-infinitely').click(function () {
                infinitelyStop = 1;
            });
            if (infinitelyStop === 1) {
                infinitelyStop = 0;
                $('#stop-infinitely').hide(500);
            } else {
                random();
                setTimeout(infinitely, 60000);
            }
        }

        if (value === 'once') {
            random();
        }
        if (value === 'infinitely') {
            $('#stop-infinitely').show(500);
            infinitely();
        }
    });
    $('#set-cookies').submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: 'additional files/cookie_api_access_handler.php',
            type: 'POST',
            data: {'set-cookies': $('#set-cookie').val()},
            success: function (response) {
                $("#answer").html(response);
                if ($(response).find('#answer').text() === 'All cookies are set!') {
                    $('.box#get-cookies').hide(500);
                }
                if ($(response).find('.start-finish-section#done').text() === 'All data are set!') {
                    setTimeout(function () {
                        window.location.reload()
                    }, 2000);
                }
            },
        });
    });
    $('#set-api-token').submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: 'additional files/cookie_api_access_handler.php',
            type: 'POST',
            data: {'set-api-token': {'set-api': $('.api-input').val(), 'set-token': $('.access-input').val()}},
            success: function (response) {
                $("#answer").html(response);
                if ($(response).find('#answer').text() === 'Api key and access token are set!') {
                    $('.box#get-api-token').hide(500);
                }
                if ($(response).find('.start-finish-section#done').text() === 'All data are set!') {
                    setTimeout(function () {
                        window.location.reload()
                    }, 2000);
                }
            },
        });
    });
    $('#parse-options').submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: 'parse_all_data.php',
            type: 'POST',
            data: $('#parse-options').serialize(),
            beforeSend: function () {
                $('#start-parse').prop('disabled', true);
                $('.popup-fade').fadeIn();
            },
            success: function (response) {
                $("#randomize-answer").html(response);
            },
            complete: function () {
                $('#start-parse').prop('disabled', false);
                $('.popup-fade').fadeOut();
                $("#randomize-answer")[0].scrollIntoView({behavior: "smooth", block: "start"});
            }
        });
    });
    $('#refresh-profile-data').click(function (event) {
        event.preventDefault();
        $.ajax({
            url: 'additional files/refresh_data.php',
            type: 'GET',
            dataType: "json",
            beforeSend: function () {
                $('.popup-fade').fadeIn();
            },
            success: function (response) {
                $("#profile-container").show(700);
                $('.profile-item#nickname').text(response['steam_personaname']);
                $('.profile-item#real-name').text(response['steam_realname']);
                $('.profile-item#location').text(response['steam_location']);
                $('a.profile-item#visibility-state').text(response['steam_communityvisibilitystate']);
                $('img.profile-item#avatar-image-medium').prop('src', response['steam_avatarmedium']);
                $('a.profile-item#profile-url').text(response['steam_profileurl']).prop('href', response['steam_profileurl']);
                $('.profile-item#persona-state').text(response['steam_personastate']);
                $('.profile-item#last-log-off').text(response['steam_lastlogoff']);
            },
            complete: function () {
                $('.popup-fade').fadeOut();
                $(".box#profile-info")[0].scrollIntoView({behavior: "smooth", block: "start"});
            }
        });
    });
    $('.parse-check-all').click(function () {
        if ($(this).prop('id') === 'btn-on') {
            $('.container#parse-customization-options input:checkbox').prop('checked', true);
            $(this).prop({"value": "Uncheck all", "id": "btn-off"});
            $('#start-parse').show(500);
        } else {
            $('.container#parse-customization-options input:checkbox').prop('checked', false);
            $(this).prop({"value": "Check all", "id": "btn-on"});
            $('#start-parse').hide(500);
        }
    });
    $('.profile-check-all').click(function () {
        if ($(this).prop('id') === 'btn-on') {
            $('#start-profile-random').show(500);
            $('select#start-randomize').show(500);
            $('#main-info-options').show(500);
            $('#showcases-options').show(500);
            $('.container#profile-options input:checkbox').prop('checked', true);
            $(this).prop({"value": "Uncheck all", "id": "btn-off"});
        } else {
            $('#start-profile-random').hide(500);
            $('select#start-randomize').hide(500);
            $('#main-info-options').hide(500);
            $('#showcases-options').hide(500);
            $('.nickname-input').val('').hide(400);
            $('.container#profile-options input:checkbox').prop('checked', false);
            $('.container#main-info-options input:checkbox').prop('checked', false);
            $('.container#showcases-options input:checkbox').prop('checked', false);
            $(this).prop({"value": "Check all", "id": "btn-on"});
            $('.main-info-check-all').prop({"value": "Check all", "id": "btn-on"});
            $('.showcases-check-all').prop({"value": "Check all", "id": "btn-on"});
        }
    });
    $('.main-info-check-all').click(function () {
        if ($(this).prop('id') === 'btn-on') {
            $('.container#main-info-options input:checkbox').prop('checked', true);
            $('.nickname-input').show(400);
            if ($('.nickname-input').val().length >= 1) {
                $('#start-profile-random').prop('disabled', false);
            } else {
                $('#start-profile-random').prop('disabled', true);
            }
            $(this).prop({"value": "Uncheck all", "id": "btn-off"});
        } else {
            $('#start-profile-random').prop('disabled', false);
            $('.container#main-info-options input:checkbox').prop('checked', false);
            $('.nickname-input').val('').hide(400);
            $(this).prop({"value": "Check all", "id": "btn-on"});
        }
    });
    $('.showcases-check-all').click(function () {
        if ($(this).prop('id') === 'btn-on') {
            $('.container#showcases-options input:checkbox').prop('checked', true);
            $(this).prop({"value": "Uncheck all", "id": "btn-off"});
        } else {
            $('.container#showcases-options input:checkbox').prop('checked', false);
            $(this).prop({"value": "Check all", "id": "btn-on"});
        }
    });
    $('.main-info-checkbox#nickname').click(function () {
        if ($(this).is(':checked')) {
            $('#start-profile-random').prop('disabled', true);
            $('.nickname-input').show(400).on('keyup', function () {
                let $this = $(this), val = $this.val();
                if (val.length >= 1) {
                    $('#start-profile-random').prop('disabled', false);
                } else {
                    $('#start-profile-random').prop('disabled', true);
                }
            });
        } else {
            $('.nickname-input').val('').hide(400);
            $('#start-profile-random').prop('disabled', false);
        }
    });
    $("input[name='profile-option[]'][id='main-info']").click(function () {
        if ($(this).is(':checked')) {
            $('.container#main-info-options').slideToggle(500);
            $('.main-info-check-all').prop({"value": "Check all", "id": "btn-on"});
        } else {
            $('.nickname-input').val('').hide(400);
            $('.container#main-info-options').slideToggle(500);
            $('.container#main-info-options input:checkbox').prop('checked', false);
            $('.main-info-check-all').prop({"value": "Uncheck all", "id": "btn-off"});
        }
    });
    $("input[name='profile-option[]'][id='showcases']").click(function () {
        if ($(this).is(':checked')) {
            $('.container#showcases-options').slideToggle(500);
            $('.showcases-check-all').prop({"value": "Check all", "id": "btn-on"});
        } else {
            $('.container#showcases-options').slideToggle(500);
            $('.container#showcases-options input:checkbox').prop('checked', false);
            $('.showcases-check-all').prop({"value": "Uncheck all", "id": "btn-off"});
        }
    });
    $('.container#parse-customization-options input:checkbox').click(function () {
        if ($('.parse-customization-checkbox:checked').length === 19) {
            $('.parse-check-all').prop({"value": "Uncheck all", "id": "btn-off"});
        } else {
            $('.parse-check-all').prop({"value": "Check all", "id": "btn-on"});
        }
    });
    $('.container#profile-options input:checkbox').click(function () {
        if ($('.profile-checkbox:checked').length === 6) {
            $('.profile-check-all').prop({"value": "Uncheck all", "id": "btn-off"});
        } else {
            $('.profile-check-all').prop({"value": "Check all", "id": "btn-on"});
        }
    });
    $('.container#main-info-options input:checkbox').click(function () {
        if ($('.main-info-checkbox:checked').length === 4) {
            $('.main-info-check-all').prop({"value": "Uncheck all", "id": "btn-off"});
        } else {
            $('.main-info-check-all').prop({"value": "Check all", "id": "btn-on"});
        }
    });
    $('.container#showcases-options input:checkbox').click(function () {
        if ($('.showcases-checkbox:checked').length === 16) {
            $('.showcases-check-all').prop({"value": "Uncheck all", "id": "btn-off"});
        } else {
            $('.showcases-check-all').prop({"value": "Check all", "id": "btn-on"});
        }
    });
    $(".profile-btn-slide").click(function () {
        $("#profile-container").slideToggle(700);
        $(".box#profile-info")[0].scrollIntoView({behavior: "smooth", block: "start"});
    });
    $(".parse-customization-btn-slide").click(function () {
        $(".box#parse-customization")[0].scrollIntoView({behavior: "smooth", block: "start"});
        $("#parse-customization-container").slideToggle(700);
    });
    $(".profile-random-btn-slide").click(function () {
        $("#profile-random-container").slideToggle(700);
        $(".box#all-options")[0].scrollIntoView({behavior: "smooth", block: "start"});
    });
    $('.put-checkbox').click(function () {
        let profile_checkboxes = $('.profile-checkbox:checked').length;
        let main_info_checkboxes = $('.main-info-checkbox:checked').length;
        let showcases_checkboxes = $('.showcases-checkbox:checked').length;
        let parse_checkboxes = $('.parse-customization-checkbox:checked').length;
        if (profile_checkboxes + main_info_checkboxes + showcases_checkboxes >= 1) {
            $('#start-profile-random').show(400);
            $('select#start-randomize').show(400);
        } else {
            $('#start-profile-random').hide(400).prop('disabled', false);
            $('select#start-randomize').hide(400);
        }
        if (parse_checkboxes >= 1) {
            $('#start-parse').show(500);
        } else {
            $('#start-parse').hide(500);
        }
    });
});