(function(book, $, undefined ) {

    book.bookId = null;

    book.gotoReviews = function(){
        book.loadReviews();
        $('.general-content').hide();
        $('.conversation-section').hide();
        $('.resenas-container').show();
    }

    book.reloadReviews = function(){
        $('.single_resena').remove();
        book.loadReviews();
    }

    book.loadReviews = function(){
        var url = Routing.generate('ajax_book_review',{
            id:book.bookId,
            offset:$('.resena_entry').length
        });

        $.ajax({
            method:'GET',
            url: url,
            success: function(reviewsHtml){
                if(reviewsHtml != ""){
                    $('#review-container').append(reviewsHtml);
                    book.attachReviewRating();

                    $('#review-container [data-rating]').each(function(ix,el){
                        el = $(el);
                        appbundle.setupRating(el,parseFloat(el.attr('data-rating')));
                    });
                }

                var newEntriesCount = (reviewsHtml.match(/single_resena/g) || []).length;
                if(newEntriesCount< 10)
                    $('#more-reviews').hide();
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    book.attachReviewRating = function(){
        _.each($('span.ion-thumbsup'),function(el){
            el = $(el);
            var binded = el.data('click.rate');
            if(!binded){
                el.data('click.rate',true);
                el.on('click.rate',function(event){
                    event.preventDefault();
                    book.rateReview($(event.target).attr('data-review'),true);
                });
            }
        });

        _.each($('span.ion-thumbsdown'),function(el){
            el = $(el);
            var binded = el.data('click.rate');
            if(!binded){
                el.data('click.rate',true);
                el.on('click.rate',function(event){
                    event.preventDefault();
                    book.rateReview($(event.target).attr('data-review'),false);
                });
            }
        });
    }

    book.rateReview = function(reviewId,up){
        if(up)
            var url = Routing.generate('review_rate_up',{id:reviewId});
        else
            var url = Routing.generate('review_rate_down',{id:reviewId});

        $.ajax({
            method:'POST',
            url: url,
            success: function(reviewRates){
                book.refreshReviewRates(reviewId,reviewRates);
            },
            error: function(){
                console.log(arguments);
            }
        });
    }

    book.refreshReviewRates = function(reviewId,rates){
        $('#review-'+reviewId+' .th_up .cantidad').text(rates.positive);
        $('#review-'+reviewId+' .th_down .cantidad').text(rates.negative);
    }

    book.attachMoreAuthorBooks = function(){
        $('.js-load-more-author').unbind('click.more');
        _.each($('.js-load-more-author'),function(el){
            var loadFunc = book.loadAuthorBooks($(el).attr('data-id'),$(el));
            $(el).on('click.more',loadFunc);
            loadFunc();
        });
    }

    book.loadAuthorBooks = function(authorId,element){
        return function(event){
            if(exists(event))
                event.preventDefault();

            var offset = $('.js-author-book-entry').length;
            var url = Routing.generate('author_books',{
                authorId:authorId,
                offset:offset,
                count:5
            });
            $.ajax({
                method:'get',
                url: url,
                success:function(html){
                    $('#all-author-books .js-book-entries').append(html);
                    follow.attachButtons();
                    if($('.js-author-book-entry').length < offset+5)
                        element.hide();
                    else
                        element.show();
                }
            })
        }
    }


    $('.goto-reviews').click(function(event){
        $('.book-tab').removeClass('activo');
        $('#goto-reviews').addClass('activo');
        book.bookId = $(event.target).attr('data-id');
        book.gotoReviews();
    });

    $('.goto-info').click(function(event){
        $('.book-tab').removeClass('activo');
        $('#goto-info').addClass('activo');
        $('.js-general-content').hide();
        $('.js-general-info').show();
    });

    $(window).on('navigate_#author-books',function(event){
        $('.book-tab').removeClass('activo');
        $('#goto-author-books').addClass('activo');
        $('.js-general-content').hide();
        book.attachMoreAuthorBooks();
        $('#all-author-books').show();
    });

    $('.goto-followers').click(function(event){
        $('.book-tab').removeClass('activo');
        $('#goto-followers').addClass('activo');
        $('.js-general-content').hide();
        $('.js-followers').show();
    });

    $(window).on('navigate_#book-conversation',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-book-conversation').addClass('activo');
        var element_id = $('#goto-book-conversation').attr('data-id');
        var type = $('#goto-book-conversation').attr('data-type');
        $('.js-general-content').hide();
        $('.conversation-section').show();
        comments.gotoComments(element_id,type);
    });

    $(window).on('navigate_#author-conversation',function(event){
        event.preventDefault();
        $('.book-tab').removeClass('activo');
        $('#goto-author-conversation').addClass('activo');
        var element_id = $('#goto-author-conversation').attr('data-id');
        var type = $('#goto-author-conversation').attr('data-type');
        $('.js-general-content').hide();
        $('.conversation-section').show();
        comments.gotoComments(element_id,type);
    });

    book.attachReviewRating();

}( window.book = window.book || {}, jQuery ));
