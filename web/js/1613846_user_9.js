(function(user, $, undefined ) {


    user.activity = function(){
        $('.tab-content').hide();
        $('div.timeline.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#activity-tab').addClass('activo');
    }

    user.follows = function(){
        $('.tab-content').hide();
        $('div.follows.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#follows-tab').addClass('activo');
    }

    user.followers = function(){
        $('.tab-content').hide();
        $('div.followers.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#followers-tab').addClass('activo');
    }

    user.lists = function(){
        $('.tab-content').hide();
        $('div.lista-listas-publicas.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#lists-tab').addClass('activo');
    }

    user.booksRead = function(){
        $('.tab-content').hide();
        $('div.libros-leidos.tab-content').show();
        $('.user-tab').removeClass('activo');
        $('#read-tab').addClass('activo');
    }

    user.load_activity = function(slug){
        var currentCount = $('.timeline ul li.entry').length;
        var url = Routing.generate('user_actions',{slug:slug,offset:currentCount});
        return user.get_new_entries(url).then(user.load_timeline_entries);
    }

    user.load_timeline = function(){
        var currentCount = $('.timeline ul li.entry').length;
        var url = Routing.generate('user_timeline',{offset:currentCount});
        return user.get_new_entries(url).then(user.load_timeline_entries).then(user.get_interview).then(function(html){follow.setupButtons(); return user.append_interview(html);});
    }

    user.get_interview = function(){
        var url = Routing.generate('timeline_interview',{offset:$('li.js-interview-entry').length});

        return new Promise(function(resolve,reject){
            $.ajax({
                url: url,
                method: 'GET',
                success: function(interview){
                    resolve(interview);
                },
                error: function(xhr){
                     reject(arguments);
                }
            });
        });
    }

    user.append_interview = function(interviewHtml){
        if($.trim(interviewHtml) == '')
            return;
        $('.timeline ul').first().append(interviewHtml);
    }

    user.get_new_entries = function(url){
        return new Promise(function(resolve,reject){
            $.ajax({
                url: url,
                method: 'GET',
                success: function(newEntries,textStatus,request){
                    resolve({entries:newEntries,total: parseInt(request.getResponseHeader('total'))});
                },
                error: function(xhr){
                     reject(arguments);
                }
            });
        });
    }

    user.load_timeline_entries = function(data){
        var newEntries = data.entries;
        var total = data.total;
        return new Promise(function(resolve,reject){
            if($.trim(newEntries) != ""){
                $('.timeline ul').first().append(newEntries);

                follow.setupButtons();
                appbundle.setupRatings();

                if($('.timeline ul li.entry').length >= total)
                    $('.load-user-timeline').hide();

                resolve(true);
            } else{
                resolve(false);
            }
        });
    }

    user.get_delete_book_relation = function(relationId){
        return function(event){
            event.preventDefault();
            var element = $(event.target);
            user.deleteBookRelation(relationId).then(element.closest('li').remove(),function(data){console.log(data)});
        }
    }

    user.deleteBookRelation = function(relationId){
        return new Promise(function(resolve,reject){
            $.ajax({
                url: Routing.generate('book_relation_delete',{id:relationId}),
                method:'delete',
                success: resolve,
                error: function(){reject(arguments)}
            });
        });
    }

    user.attachBookRelationDelete = function(){
        _.each($('.js-delete-relation'),function(el){
            el = $(el);
            el.unbind('click.delete');
            el.on('click.delete',user.get_delete_book_relation(el.attr('data-id')));
        });
    }

    user.changeToPrivate = function(){
        $('#user_profile_publicProfile').val(0);
        $('#public-profile').hide();
        $('#private-profile').show();
        $('#overlay_privacidad_perfil').hide();
    }

    user.changeToPublic = function(){
        $('#user_profile_publicProfile').val(1);
        $('#public-profile').show();
        $('#private-profile').hide();
    }

    user.loadBookEntries = function(triggerButton,container,urlName,count){
        var offset = container.find('.js-book-entry').length;
        var userId = triggerButton.attr('data-user-id');
        var url = Routing.generate(urlName,{userId:userId,count:count,offset:offset})
        $.ajax({
            url:url,
            method:'GET',
            success: function(newHtml){
                var newEntriesCount = (newHtml.match(/js-book-entry/g) || []).length;
                if(newEntriesCount < count)
                    triggerButton.hide();
                else
                    triggerButton.show();

                container.append(newHtml);
                follow.setupButtons();
                user.attachBookRelationDelete();
            },
            error: console.log
        });
    }

    user.postStatus = function(form){
        var url = Routing.generate('post_status');
        var array_data = form.serializeArray();
        var data = {};

        _.each(array_data,function(element){
            data[element.name] = element.value;
        });

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(){
                $('.timeline ul li').remove();
                form.find('input, textarea').val('');
                user.load_timeline();
            },
            error: function(xhr){
                if(xhr.status == 400){
                    if(!exists(xhr.responseJSON))
                        var jsonResponse = JSON.parse(xhr.responseText);
                    else
                        var jsonResponse = xhr.responseJSON;

                    appbundle.setErrors(jsonResponse,form,'user_status');
                }
                console.log(arguments)
            }
        });
    }

    $('#convertir_perfil_publico').on('click.list',function(event){
        event.preventDefault();
        user.changeToPublic();
    });

    $('#convertir_perfil_privado').on('click.list',function(event){
        $('#overlay_privacidad_perfil').show();
    });

    $(window).on('navigate_#lists',function(event){
        user.lists();
    });

    $(window).on('navigate_#followers',function(event){
        user.followers();
    });

    $(window).on('navigate_#follows',function(event){
        user.follows();
    });

    $(window).on('navigate_#actividad',function(event){
        user.activity();
    });

    $(window).on('navigate_#booksRead',function(event){
        user.booksRead();
    });

    $('.load-user-activity').on('click.user_activity',function(event){
        event.preventDefault();
        user.load_activity($(event.target).attr('data-slug')).then(function(){},function(){$(event.target).hide()});
    });

    $('.load-user-timeline').on('click.user_timeline',function(event){
        event.preventDefault();
        user.load_timeline().then(function(){},function(){$(event.target).hide()});
    });

    if($('#load-readed-books').length > 0){
        $('#load-readed-books').on('click.more',function(event){
            event.preventDefault();
            user.loadBookEntries($('#load-readed-books'),$('.js-readed-container'),'my_readed_books',9);
        });
        user.loadBookEntries($('#load-readed-books'),$('.js-readed-container'),'my_readed_books',9);
    }

    if($('#load-wanted-books').length > 0){
        $('#load-wanted-books').on('click.more',function(event){
            event.preventDefault();
            user.loadBookEntries($('#load-wanted-books'),$('.js-wanted-container'),'my_wanted_books',9);
        });
        user.loadBookEntries($('#load-wanted-books'),$('.js-wanted-container'),'my_wanted_books',9);
    }

    if($('#load-reading-books').length > 0){
        $('#load-reading-books').on('click.more',function(event){
            event.preventDefault();
            user.loadBookEntries($('#load-reading-books'),$('.js-reading-container'),'my_reading_books',9);
        });
        user.loadBookEntries($('#load-reading-books'),$('.js-reading-container'),'my_reading_books',9);
    }

    if($('.js-user-timeline').length > 0)
        user.load_timeline();

    user.attachBookRelationDelete();

}( window.user = window.user || {}, jQuery ));
