$(function () {
    
    $('.ui.checkbox').checkbox({
        onChange: function() {
            var entity = $(this).data('entity'),
                action = $(this).data('action'),
                id = $(this).data('id');

            sendAjaxRequest(entity, action, id)
        }
    });


    function sendAjaxRequest(entity, action, id) {

        // TODO
        // Ajax Loading: Add "active" class to .ui.dimmer.

        console.log(entity + ' - ' + action + ' - ' + id);

        // $(this).parent().parent().parent().parent().parent().parent().find('.ui.dimmer').addClass("active")

        // $(this).parent().parent().parent().parent().parent().parent().find('.ui.dimmer').addClass("active")
        // console.log($(this).parent().parent().parent().parent().parent().parent());

        $.ajax({
            // url: '/zohartours/web/app.php/backend/ajax/test/' + entity + '/' + action + '/' + id,
            url: '/projects/zohartours/app_dev.php/backend/ajax/test/' + entity + '/' + action + '/' + id,
            type: 'POST',
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
        })
        .done(function(data) {
            console.log("success");
            console.log(data);
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    }

    function stopSellAjaxRequest(room, date) {

        // TODO
        // Ajax Loading: Add "active" class to .ui.dimmer.

        console.log(room + ' - ' + date);

        // $(this).parent().parent().parent().parent().parent().parent().find('.ui.dimmer').addClass("active")

        // $(this).parent().parent().parent().parent().parent().parent().find('.ui.dimmer').addClass("active")
        // console.log($(this).parent().parent().parent().parent().parent().parent());

        $.ajax({
            // url: '/zohartours/web/app.php/backend/ajax/test/' + entity + '/' + action + '/' + id,
            url: '/projects/zohartours/app_dev.php/backend/ajax/addstopsell/',
            type: 'POST',
            data: {'room': room, 'date': date}
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
        })
        .done(function(data) {
            console.log("success");
            console.log(data);
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    }



})