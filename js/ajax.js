function openbox(id) {
    display = document.getElementById(id).style.display;
    if (display == 'none') {
        document.getElementById(id).style.display = 'block';
    } else {
        document.getElementById(id).style.display = 'none';
    }
}

function call() {
    var msg = $('#formx').serialize();
    $.ajax({
        type: 'POST',
        data: msg,
        success: function (data) {
            $('#results').html(data);
        },
        error: function (xhr, str) {
            alert('Возникла ошибка: ' + xhr.responseCode);
        }
    });
}
