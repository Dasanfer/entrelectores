(function(comments, $, undefined ) {

    comments.element_id = null;
    comments.element_type = null;

    comments.getElementComments = function(){
        var offset = $('#comments-list-'+comments.element_id+' li.conversation-entry').length;
        var url = Routing.generate(
           'element_conversation',
           {id: comments.element_id,type:comments.element_type,offset:offset});

        $.ajax({
            method: 'GET',
            url: url,
            success: function(html,textStatus,request){
                $('#comments-list-'+comments.element_id).append(html);
                $('a.js-abre-form-respuesta').each(function(ix,el){comments.attachNewCommentEvent($(el))});
                if(html.length == 0 || parseInt(request.getResponseHeader('total')) <= $('#comments-list-'+comments.element_id+' li.js-element-comment').length){
                    $('#load-more-comments-'+comments.element_id).hide();
                }
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    comments.submitForm = function(form,parentId){
        var url = Routing.generate('post_comment');
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
                $('#comments-list-'+comments.element_id+' li.conversation-entry').remove();
                comments.getElementComments();
                comments.cancelComment(form.closest('.form-holder'));
                console.log(arguments)
            },
            error: function(xhr){
                if(xhr.status == 400)
                    appbundle.setErrors(xhr.responseJSON.errors,form);
            }
        });
    }

    comments.newComment=function(element,parentId){
        var form_holder = element.parent().find('.form-holder');
        form_holder.html($('#comment-form-'+comments.element_id).text());

        form_holder.find('form').on('submit',function(event){
            event.preventDefault();
            comments.submitForm($(this),parentId);
        });

        element.off('click.comment');
        element.removeClass('no_activo');
        element.data('data-prev-text',element.text());
        element.text('cancelar');

        element.addClass('activo').on('click.cancel',
            function(event){
                event.preventDefault();
                comments.cancelComment(form_holder);
            }
        );
        form_holder.show(300);
    }

    comments.cancelComment = function(form_holder){
        var button = form_holder.parent().find('.js-abre-form-comentario');
        button.off('click.cancel');
        button.removeClass('activo');
        button.addClass('no_activo');
        button.text(button.data('data-prev-text'));
        form_holder.hide(300);
        comments.attachNewCommentEvent(button);
    }

    comments.gotoComments = function(element_id,type){

        if($('#comments-list-'+element_id+' li.conversation-entry').length > 0 )
            return;

        comments.element_id = element_id;
        comments.element_type = type;
        comments.getElementComments();

        $('#load-more-comments-'+element_id).on('click.comments',function(event){
            event.preventDefault();
            comments.getElementComments();
        });
    }

    comments.attachNewCommentEvent = function(element){
        element.on('click.comment',
            function(event){
                event.preventDefault();
                comments.newComment(element,element.attr('data-parent-comment'));
            }
        );
    }

    $('a.js-abre-form-comentario').each(function(ix,el){comments.attachNewCommentEvent($(el))});

}( window.comments = window.comments || {}, jQuery ));
