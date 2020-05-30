$(document).ready(function() {


    $('#country_code_view').val('+' + $('#input_country_code').val());
    $('#input_country_code').change(function() {
        $('#country_code_view').val('+' + $('#input_country_code').val())
    });

    var phone_select = 0;
    var user_list = [];
    //Auto complete to mobile number
    $("#mobile_number").autocomplete({
        source: function(request, response) {
            $.ajax({
                type: 'POST',
                url: REQUEST_URL + '/search_phone',
                data: {
                    type: 'merchant',
                    text: $("#mobile_number").val(),
                    country_code: $("#input_country_code").val()
                },
                dataType: "json",
                success: function(data) {
                    //console.log(data);
                    var users = [];
                    user_list = [];
                    for (var i = 0; i < data.length; i++) {
                        if (data[i].user_type == 'Merchant')
                            user_list[data[i].mobile_number] = users[i] = { value: data[i].mobile_number, first_name: data[i].first_name, last_name: data[i].last_name, email: data[i].email, address_line1: data[i].address_line1, address_line2: data[i].address_line2, city: data[i].city, state: data[i].state, postal_code: data[i].postal_code, used_referral_code: data[i].used_referral_code }
                    }
                    response(users);
                },
                error: function(err) {
                    console.log(err);
                    console.log('error');
                }
            });
        },
        select: function(event, ui) {
            $('#input_first_name').val(ui.item.first_name);
            $('#input_last_name').val(ui.item.last_name);
            $('#input_email').val(ui.item.email);
            $('#address_line1').val(ui.item.address_line1);
            $('#address_line2').val(ui.item.address_line2);
            $('#city').val(ui.item.city);
            $('#state').val(ui.item.state);
            $('#postal_code').val(ui.item.postal_code);
            $('#input_first_name').prop('readonly', true);
            $('#input_last_name').prop('readonly', true);
            $('#input_email').prop('readonly', true);
            $('#address_line1').prop('readonly', true);
            $('#address_line2').prop('readonly', true);
            $('#city').prop('readonly', true);
            $('#state').prop('readonly', true);
            $('#postal_code').prop('readonly', true);

            var select = $("#input-tags3").selectize();
            var selectize = select[0].selectize;
            if (v = ui.item.used_referral_code) {
                selectize.setValue(v, false);
                $('#referrer_id').val(ui.item.used_referral_code);
            }
            selectize.disable();

            phone_select = 1;
        }
    })
    $("#mobile_number").keyup(function() {
        if (typeof user_list[$(this).val()] !== 'undefined') {
            $('#input_first_name').val(user_list[$(this).val()].first_name);
            $('#input_last_name').val(user_list[$(this).val()].last_name);
            $('#input_email').val(user_list[$(this).val()].email);
            $('#address_line1').val(user_list[$(this).val()].address_line1);
            $('#address_line2').val(user_list[$(this).val()].address_line2);
            $('#city').val(user_list[$(this).val()].city);
            $('#state').val(user_list[$(this).val()].state);
            $('#postal_code').val(user_list[$(this).val()].postal_code);
            $('#input_first_name').prop('readonly', true);
            $('#input_last_name').prop('readonly', true);
            $('#input_email').prop('readonly', true);
            $('#address_line1').prop('readonly', true);
            $('#address_line2').prop('readonly', true);
            $('#city').prop('readonly', true);
            $('#state').prop('readonly', true);
            $('#postal_code').prop('readonly', true);

            var select = $("#input-tags3").selectize();
            var selectize = select[0].selectize;
            if (v = user_list[$(this).val()].used_referral_code) {
                selectize.setValue(v, false);
                $('#referrer_id').val(ui.item.used_referral_code);
            }
            selectize.disable();
        } else {
            $('#input_first_name').prop('readonly', false);
            $('#input_last_name').prop('readonly', false);
            $('#input_email').prop('readonly', false);
            $('#address_line1').prop('readonly', false);
            $('#address_line2').prop('readonly', false);
            $('#city').prop('readonly', false);
            $('#state').prop('readonly', false);
            $('#postal_code').prop('readonly', false);
            $('#input_first_name').val('');
            $('#input_last_name').val('');
            $('#input_email').val('');
            $('#address_line1').val('');
            $('#address_line2').val('');
            $('#city').val('');
            $('#state').val('');
            $('#postal_code').val('');

            var select = $("#input-tags3").selectize();
            var selectize = select[0].selectize;
            selectize.setValue(0, false);
            $('#referrer_id').val('0');
            selectize.enable();
        }
    });
});