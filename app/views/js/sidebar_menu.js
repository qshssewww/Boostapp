 
$(document).ready(function() {
  $("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
  });
 $("#menu-toggle-2").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled-2");
    $(".navbar").toggleClass("toggled-2");
    $('#menu ul').hide();
  });
  $(".sidebar-item").click(function(e) {
  var navItem = $(this);
  e.preventDefault();
  $(".sidebar-item").each(function() {
    $(this).removeClass("active");
  })
  navItem.addClass("active");
  });

 function initMenu() {
  $('#menu ul').hide();
  $('#menu ul').children('.current').parent().show();
  //$('#menu ul:first').show();
  $('#menu li a').click(
    function() {
      var checkElement = $(this).next();
      if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
        checkElement.slideUp('normal');
        return false;
        }
      if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
        $('#menu ul:visible').slideUp('normal');
        checkElement.slideDown('normal');
        return false;
        }
      }
    );
  }
  initMenu();

}); 
