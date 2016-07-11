(function(lists, $, undefined ) {

    lists.addModal = function(lists){
        var input_template = _.template($('#input-lista-template').text());
        _.each(lists,function(list){
            $('form.listas').prepend(input_template(list));
        });
    }

    lists.submit = function(form){
        var list_id = form.find(":checked").val();
        var new_list_name = null;
        var post_data = {book: $('#list-book-input').val()};

        if(list_id == null || list_id < 0){
            var new_list_name = $('#nuevalista').val();
        }

        if(list_id > 0)
            post_data['list'] = list_id;
        else if(new_list_name != '')
            post_data['newName'] = new_list_name;
        else
            return;

        $.ajax({
            method: 'post',
            url: Routing.generate('add_to_list'),
            data: post_data,
            success: function(data){
                $('#overlay_add_to_list').hide();
                $('#btn_listas_pop_up').hide();
                if(post_data['newName']){
                    var newUrl = Routing.generate('edit_list',{id:data.list});
                    window.location = newUrl;
                }
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    lists.addToList = function(bookId){
        $('.list-input').remove();
        $('#list-book-input').val(bookId);
        var lists_route = Routing.generate('my_lists');
        $.ajax({
            method: 'get',
            dataType: 'json',
            url: lists_route,
            success: lists.addModal,
            error: function(){
                console.error(arguments);
            }
        });
    }

    lists.changeToPrivate = function(){
        $('#edit_booklist_publicFlag').val(0);
        $('#public-list').hide();
        $('#private-list').show();
        $('#overlay_privacidad').hide();
    }

    lists.changeToPublic = function(){
        $('#edit_booklist_publicFlag').val(1);
        $('#public-list').show();
        $('#private-list').hide();
    }

    lists.removeBook = function(book_id,list_id){
        var prom = new Promise(function(resolve,reject){
            $.ajax({
                method: 'DELETE',
                url: Routing.generate('remove_from_list'),
                data: {list:list_id, book:book_id},
                success: resolve,
                error: reject
            });
        });
        return prom;
    }

    lists.loadBooks = function(){
        var offset = $('.js-book-list-entry').length;
        var count = 10;
        var listId = $('#all-list-books').attr('data-list-id');
        ajaxBooks(listId,count,offset).then(function(html){return loadNewBooks(html,count)});
    }

    var loadNewBooks = function(newHtml,count){
        var newEntriesCount = (newHtml.match(/js-book-list-entry/g) || []).length;
        if(newEntriesCount < count){
            $('#all-list-books').find('.js-load-more').hide();
        }
        else {
            $('#all-list-books').find('.js-load-more').show();
        }
        $('#all-list-books .js-entries-container').append(newHtml);
        lists.attachRemove($('#all-list-books'));
    }

    var ajaxBooks = function(listId,count,offset){
        return new Promise(function(resolve,reject){
            $.ajax({
                url: Routing.generate('books_in_list',{id:listId,count:count,offset:offset}),
                method: 'GET',
                success: resolve,
                error: function(){
                    reject();
                }
            });
        });

    }

    lists.setupBookSection = function(){
        if($('#all-list-books').attr('data-setup'))
            return;

        $('#all-list-books').attr('data-setup',true);
        var moreButton = $('#all-list-books').find('.js-load-more');
        moreButton.on('click.load-books',function(event){event.preventDefault();lists.loadBooks()});
        lists.loadBooks();
    }

    lists.attachRemove = function(baseEl){
        _.each(baseEl.find('.list-remove-button'),function(el){
            el = $(el);
            el.unbind('click.remove');
            var book_id = el.attr('data-id');
            var list_id = el.attr('data-list-id');
            el.on('click.remove',getRemoveClickFunc(el,book_id,list_id));
        });
    }

    var getRemoveClickFunc = function(button,book_id,list_id){
        return function(event){
            event.preventDefault();
            lists.removeBook(book_id,list_id).then(button.closest('.libro_header').remove());
        }
    }

    $('#convertir_lista_publica').on('click.list',function(event){
        event.preventDefault();
        lists.changeToPublic();
    });

    $('#convertir_lista_privada').on('click.list',function(event){
        $('#overlay_privacidad').show();
    });

    $(window).on('navigate_#list-books',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-list-books').addClass('activo');
        $('.js-general-content').hide();
        $('#all-list-books').show();
        lists.setupBookSection();
    });

    $(window).on('navigate_#list-info',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-list-info').addClass('activo');
        $('.js-general-content').hide();
        $('#list-general-content').show();
    });

    $(window).on('navigate_#list-followers',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-list-followers').addClass('activo');
        $('.js-general-content').hide();
        $('.js-followers').show();
    });

    $(window).on('navigate_#list-conversation',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-list-conversation').addClass('activo');
        $('.js-general-content').hide();
        $('.conversation-section').show();
        var element_id = $('#goto-list-conversation').attr('data-id');
        var type = $('#goto-list-conversation').attr('data-type');
        comments.gotoComments(element_id,type);

    });

}( window.lists = window.lists || {}, jQuery ));
