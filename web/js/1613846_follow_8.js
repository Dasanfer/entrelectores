(function(follow, $, undefined ) {

    var user_follows_data = {};
    var store_url = user_id+'follows_sotre';

    var attachFollow = function(button){
        var type = button.attr('data-type');
        var id = button.attr('data-id');
        button.unbind('click.follow');
        button.on('click.follow',function(event){
            event.preventDefault();
            follow.setFollow(type,id,true).then(function(){
                follow.recoverUserFollows().then();
                $('.follow-button[data-id="'+id+'"][data-type="'+type+'"]').hide();
                $('.unfollow-button[data-id="'+id+'"][data-type="'+type+'"]').show();
            });
        });
    }

    var attachUnfollow = function(button){
        var type = button.attr('data-type');
        var id = button.attr('data-id');
        button.unbind('click.unfollow');
        button.on('click.unfollow',function(event){
            event.preventDefault();
            follow.setFollow(type,id,false).then(function(){
                follow.recoverUserFollows().then();
                $('.follow-button[data-id="'+id+'"][data-type="'+type+'"]').show();
                $('.unfollow-button[data-id="'+id+'"][data-type="'+type+'"]').hide();
            });
        });
    }

    follow.attachButtons = function(){
        $('.follow-button').each(function(ix,el){attachFollow($(el))});
        $('.unfollow-button').each(function(ix,el){attachUnfollow($(el))});
    }

    follow.setFollow = function(type,id,follow){
        return new Promise(function(resolve,reject){
            if(follow)
                var url = Routing.generate('follow_element',{type:type,id:id});
            else
                var url = Routing.generate('unfollow_element',{type:type,id:id});

            $.ajax({
                url: url,
                method: 'POST',
                success: function(){
                    resolve(true);
                    console.log(arguments)
                },
                error: function(){console.log(arguments)}
            });
        });
    }

    follow.setupButtons = function(){
        _.each($('span.js-follow'),function(el){
            el = $(el);
            if(el.attr('data-type')  != 'user' || el.attr('data-id') != user_id)
                follow.doesUserFollow(el.attr('data-type'),el.attr('data-id'))
                    .then(function(data){
                        if(data){
                            el.find('.unfollow-button').show();
                        } else {
                            el.find('.follow-button').show();
                        }
                    })
        });

        follow.attachButtons();
    }

    follow.doesUserFollow = function(type,id){
        var object_list = user_follows_data[type+'s_followed'];
        return new Promise(
            function(resolve,reject){
                resolve(_.some(object_list,function(el){
                    return el.id == id;
                }));
            }
        );
    }

    follow.getUserFollows = function(){
        return new Promise(
            function(resolve,reject){
                var url = Routing.generate('user_follow_objects');
                $.ajax({
                    url:url,
                    method: 'GET',
                    success: function(data){
                        resolve(data);
                    },
                    error: function(){
                        resolve({});
                    }
                });
            }
        );
    }

    follow.recoverUserFollows = function(resolve,reject){
        return new Promise(function(resolve,rejec){
                follow.getUserFollows().then(function(data){
                user_follows_data = data;
                window.localStorage.setItem(store_url,JSON.stringify(data));
                resolve();
            });
        })
    }

    follow.checkUserFollows = function(resolve,reject){
        var data_string = window.localStorage.getItem(store_url);
        if(!exists(data_string)){
            return follow.recoverUserFollows();
        }
        else{
            return new Promise(function(resolve,reject){
                user_follows_data = JSON.parse(data_string);
                resolve();
            });
        }
    }

    follow.checkUserFollows().then(follow.setupButtons);


}( window.follow = window.follow || {}, jQuery ));
