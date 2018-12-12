jQuery(document).ready(function($) {

  // Determine trigger for touch/click events
  var clickortap;
  if ($('html').hasClass('touch')) {
    clickortap = 'touchend';
  } else {
    clickortap = 'click';
  }

  // Add special class to default WP embeds
  $('iframe.wp-embedded-content').not('[src*="/data-viz/"]').closest('.entry-content-asset').addClass('wp-embed');

  // Make sure WordPress embeds have correct permissions
  $('iframe.wp-embedded-content').attr('sandbox', 'allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox');

  // Add external-link icon to EdNC map embeds
  $('iframe[src*="//www.ednc.org"]').each(function() {
    var href = $(this).attr('src');
    $(this).after('<a class="icon-external-link" href="' + href + '" target="_blank"></a>');
  });

  // If 'print' URL parameter exists, open print dialog on page load
  if (window.location.search === '?print') {
    window.onload = function() { window.print(); };
  }


  /**
   * Smooth scroll to anchor on same page
   */
  $('a[href*="#"]:not([href="#"]):not(.collapsed)').on(clickortap, function() {
    if (location.pathname.replace(/^\//,'') === this.pathname.replace(/^\//,'') && location.hostname === this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });


  /**
   * FitText for big fancy numbers
   */
  $('.fancy-number').each(function() {
    $(this).fitText();
  });


  /**
   * Bootstrap Popover
   */
  $('[data-toggle="popover"]').popover();


  /**
   * Bootstrap Affix
   */
  $(window).on('load', function() {
    $('#data-dash-nav').affix({
      offset: {
        top: function() {
          return (this.top = $('#data-dash-nav').offset().top - 20);
        },
        bottom: function () {
          return (this.bottom = $('footer.content-info').outerHeight(true) + $('.above-footer').outerHeight(true) + 96);
        }
      }
    }).on('affix.bs.affix', function() {
      // Set width of element on affix
      width = $(this).width();
      $(this).width(width);
    }).on('affix-top.bs.affix', function() {
      // Remove width of element when at top
      $(this).removeAttr('style');
    }).on('affix-bottom.bs.affix', function() {
      // Set width of element when at bottom
      $(this).width('auto');
    });
    $('body').scrollspy({
      target: '#data-dash-nav',
      offset: 40
    });
  });


  /**
   * Gallery lightboxes
   */
  $('.gallery').each(function() { // the containers for all your galleries
    $(this).magnificPopup({
      delegate: 'a', // the selector for gallery item
      type: 'image',
      gallery: {
        enabled:true
      },
      midClick: true,
      mainClass: 'mfp-with-zoom',
      zoom: {
        enabled: true,
        duration: 300,
        easing: 'ease-in-out',
        opener: function(openerElement) {
          return openerElement.is('img') ? openerElement : openerElement.find('img');
        }
      },
      image: {
        cursor: 'mfp-zoom-out-cur',
        verticalFit: true,
        titleSrc: function(item) {
          return $(item.el).children('img').attr('alt');
        }
      }
    });
  });


  /**
   * Owl Carousel 2
   */
  $(window).on('load', function() {
    $('.g-carousel').each(function() {
      $(this).owlCarousel({
        items: 1,
        loop: true,
        autoHeight: true,
        animateOut: 'fadeOut',
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true
      });
    });
  });

  // Manual carousel nav
  $('.fc-nav .fc-next').on(clickortap, function() {
    owl.trigger('next.owl.carousel');
  });

  $('.fc-nav .fc-prev').on(clickortap, function() {
    owl.trigger('prev.owl.carousel');
  });

});
