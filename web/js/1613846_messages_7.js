(function(messages, $, undefined ) {

    messages.user_id = null;

    messages.attachNewMessageEvent = function(element){
        element.on('click.comment',
            function(event){
                event.preventDefault();
                messages.newMessage(element,element.attr('data-parent-comment'));
            }
        );
    }



    messages.getUserMessages = function(){
        var offset = $('li.js-message-entry').length;
        var url = Routing.generate(
           'user_messages',
           {user_id: messages.user_id, offset:offset});

        $.ajax({
            method: 'GET',
            url: url,
            success: function(html){
                if(html.length == 0){
                    $('#js-more-messages-'+messages.user_id).hide();
                }
                $('#message-list-'+messages.user_id).append(html);
                $('a.js-abre-form-mensaje').each(function(ix,el){messages.attachNewMessageEvent($(el))});
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    messages.submitForm = function(form, parentId){
        var url = Routing.generate('post_message');
        var array_data = form.serializeArray();
        var data = {};

        _.each(array_data,function(element){
            data[element.name] = element.value;
        });

        if(typeof parentId != 'undefined')
            data['parent'] = parentId;

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(){
                $('.js-message-entry').remove();
                messages.getUserMessages();
                messages.cancelMessage(form.closest('.form-holder'));
            },
            error: function(){
                console.log(arguments)
            }
        });
    }

    messages.newMessage=function(element, parentId){
        var form_holder = element.parent().find('.form-holder');
        form_holder.html($('#comment-form').text());

        form_holder.find('form').on('submit',function(event){
            event.preventDefault();
            messages.submitForm($(this),parentId);
        });

        element.off('click.comment');
        element.removeClass('no_activo');
        element.data('data-prev-text',element.text());
        element.text('cancelar');

        element.addClass('activo').on('click.cancel',
            function(event){
                event.preventDefault();
                messages.cancelMessage(form_holder);
            }
        );
        form_holder.show(300);
    }

    messages.cancelMessage = function(form_holder){
        var button = form_holder.parent().find('.js-abre-form-mensaje');
        button.off('click.cancel');
        button.removeClass('activo');
        button.addClass('no_activo');
        button.text(button.data('data-prev-text'));
        form_holder.hide(300);
        messages.attachNewMessageEvent(button);
    }

    $('a.js-abre-form-mensaje').each(function(ix,el){messages.attachNewMessageEvent($(el))});

    if($('#conversation-user-id').length > 0)
    {
        messages.user_id = $('#conversation-user-id').attr('userid');
        $('#js-more-messages-'+messages.user_id).on('click.more',messages.getUserMessages);
    }


}( window.messages = window.messages || {}, jQuery ));
