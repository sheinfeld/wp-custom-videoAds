$(document).ready(function () {
    if (Cookies.get('videoAdv-'+$('#videoAdv button').attr('data-value')+'-'+$('#videoAdv button').attr('data-id')) != "false") {
        $('#videoAdv').show();
    }

    $('#videoAdvCls').on('click', function () {
        $('#videoAdv').hide();
        Cookies.set('videoAdv-'+$(this).attr('data-value')+'-'+$(this).attr('data-id'), 'false', {expires: 0.5});
    });
});