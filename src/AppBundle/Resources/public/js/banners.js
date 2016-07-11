(function(banners, $, undefined ) {

  $('a.banner-link').on('click', function(event){
    var bannerZone = $(this).attr('data-zone');
    _gaq.push(['_trackEvent', 'banner-click', bannerZone]);
  });

  if($('.js-banner-adsense').length > 0) {
    $.getScript('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',function(){
      $('.js-banner-adsense').show();
      (adsbygoogle = window.adsbygoogle || []).push({})
    });
  }

}( window.banners = window.banners || {}, jQuery ));
