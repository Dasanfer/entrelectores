var exists = function(x) { return x != null && typeof x != 'undefined'; };

var truthy = function(x) {
  return (x !== false) && exists(x);
};

(function(appbundle, $, undefined ) {


    appbundle.setupRating = function(element, rate){

        if(typeof element == 'string')
            element = $(element);

        var stars = element.find('li');
        stars.each(function(ix,el){$(el).removeClass('half whole').addClass('empty');});
        for(var i = 0; rate > 0; rate = rate - 1){
            if(i % 2 == 0){
                $(stars[i/2]).removeClass('empty');
                $(stars[i/2]).addClass('half');
            } else {
                $(stars[(i-1)/2]).removeClass('half');
                $(stars[(i-1)/2]).addClass('whole');
            }
            i = i + 1;
        }
    }

    appbundle.starHover = function(el){
        el.closest('ul').find('li').removeClass('rover halfrover');

        if(el.hasClass('r'))
            el.parent().addClass('rover');
        else
            el.parent().addClass('halfrover');

        var prev = el.parent().prev();
        while(prev.is('li')){
            prev.removeClass('rover halfrover');
            prev.addClass('rover');
            prev = prev.prev();
        }
    }

    appbundle.ratingHoverOut = function(el){
        el.find('li').each(function(ix,el){$(el).removeClass('halfrover rover')})
    }

    appbundle.sendRating = function(element,value,bookId){
        var route = Routing.generate('rate_book',{id: bookId});
        $.post(
            route,
            {value:value},
            function(){
                element.attr('data-rating',value);
                appbundle.setupRating(element,value);
            }
        );
    }

    appbundle.sendReview = function(form){
        var data = form.serializeArray();
        form.find('.form_errors').remove();

        if(!_.some(data,function(val){return val['name'] == 'review_public[spoiler]';}))
            data['review_public[spoiler]'] = false;

        var route = Routing.generate('post_review');
        $.ajax(
            {
                method:'POST',
                url: route,
                data: data,
                error: function(xhr,txtstatus,error){
                    $.each(xhr.responseJSON.form.children,function(name,val){
                        if(exists(val.errors)){
                            var list = $('<ul class="form_errors">');

                            $.each(val.errors,function(ix,val){
                                list.append('<li>'+val+'</li>');
                            });

                            form.find('[name="review_public['+name+']"]').before(list);
                        }
                    });
                },
                success: function(){
                    form.hide(200);
                    book.reloadReviews();
                }
            }
        );
    }

    appbundle.addBookRelation = function(relation,bookId,button){
        var url = Routing.generate('user_add_'+relation);
        $.ajax(
            {
                url: url,
                method:'POST',
                data: {book:bookId},
                error: function(){
                    console.log(arguments);
                },
                success: function(){
                    button.parent().find('a').removeClass('boton_celeste');
                    button.addClass('boton_celeste');
                    if(relation == 'read'){
                        $('.book-tab').removeClass('activo');
                        $('#goto-reviews').addClass('activo');
                        book.bookId = bookId;
                        book.gotoReviews();
                        $('html, body').animate({
                            scrollTop: $("#publicar-resena").offset().top
                        }, 1000);
                    }
                }
            }
        );
    }


    appbundle.checkBookRelation = function(){
        if(user_id == 'anon')
            return;

        if($('.js-reading-buttons').length <= 0)
            return;

        var bookId = $('.js-reading-buttons').attr('data-book-id');
        var url = Routing.generate('user_book_relation',{id:bookId});
        $.ajax({
            url: url,
            method: 'GET',
            error: console.log,
            success: function(data){
                if(data.read)
                    $('#js-set-read').addClass('boton_celeste');
                if(data.reading)
                    $('#js-set-reading').addClass('boton_celeste');
                if(data.wants)
                    $('#js-set-want').addClass('boton_celeste');
            }
        });

    }

    appbundle.poll_vote = function(element){
        var selected = element.closest('form').find('input:checked');
        if(selected.length > 0){
            var value = selected.val();
            console.log('poll option',value);
            $.ajax({
                url: Routing.generate('poll_vote'),
                data:{option: value},
                method: 'POST',
                success: function(data){
                    element.closest('.single_poll').html(data);
                },
                error: console.log
            });
        }
    }

    appbundle.showRegisterModal = function(){
        $('#register-modal').show();
    }


    appbundle.setErrors = function (errors, container,formName) {
        $('span.validation-error').remove();
        var fields = errors.children;
        _.each(fields,function(error,name){
            _.each(error.errors,function(error){
                container.find('[name="'+formName+'['+name+']"]').after('<span class="validation-error">'+error+'</span>');}
            );
        });
    }

    appbundle.setupRatings = function(){
        $('[data-rating]').each(function(ix,el){
            el = $(el);

            if($(el).attr('setup'))
                return;

            $(el).attr('setup',true);

            appbundle.setupRating(el,parseFloat(el.attr('data-rating')));

            if(!el.parent().hasClass('disabled')){
                el.find('span').hover(
                    function(){
                        appbundle.starHover($(this));
                    }
                );
                el.find('span').click(
                    function(event){
                        event.preventDefault();
                        event.stopPropagation();
                        var value = $(this).attr('data-value');
                        var bookId = $(this).closest('ul').attr('data-id');
                        appbundle.sendRating($(el),value,bookId);

                        $('ul[data-id="'+bookId+'"]').each(function(ix,el){
                            if(!$(el).hasClass('disabled')){
                                $(el).attr('data-rating',value);
                            }
                        });
                    }
                );
                el.hover(
                    function(){},
                    function(){
                    appbundle.ratingHoverOut(el);
                    }
                );
            }

        });
    }

    $( document ).ready(function() {
        appbundle.setupRatings();
        appbundle.checkBookRelation();
        $('.js-trigger-panel').click( function(event){
            event.preventDefault();
            var its_id = $(this).attr('id');
            $('.js-panel').each( function() {
                if ( $(this).attr('id') != 'panel-' + its_id ) {
                    $(this).slideUp();
                }
            });
            $('#panel-' + its_id ).slideToggle();
        });

        if(exists(location.hash))
            $(window).trigger('navigate_'+location.hash)
    });

    $( document ).ajaxError(function(event,xhr,settings,error) {
        if(xhr.status == 401 || xhr.status == 403){
            appbundle.showRegisterModal();
        }
    });

    $(window).on('hashchange', function(event) {
        $(window).trigger('navigate_'+location.hash)
    });

    appbundle.mostrado = function() {
        $.cookie("visto_143015", true, { expires: 10000, path: '/' } );
        $('.overlay.pop_up').fadeOut();
    };

    $('html').on('click.panel',function(event){
        if($(event.target).closest('.js-trigger-panel').length == 0)
            $('.js-panel').hide(100);
    })
}( window.appbundle = window.appbundle || {}, jQuery ));


