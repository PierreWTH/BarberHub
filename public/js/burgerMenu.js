$('.burger-menu, .burger-overlay').click(function(){
    $('.burger-menu').toggleClass('clicked');
    $('.burger-overlay').toggleClass('show');
    $('#responsive-nav').toggleClass('show');
    $('body').toggleClass('overflow');
  });
