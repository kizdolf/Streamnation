$( ".mainbase" ).on( "click", "button", function() {
  $(this).addClass('invisible');
})

$( ".fullmenu" ).on( "click", ".handle", function() {
  $(".fullmenu").toggleClass('movemenu');
  $(".fullmenu .handle").toggleClass('movehandle')
  $(".fullmenu .handle span").toggleClass('glyphicon-remove')
  							.toggleClass('glyphicon-list');
  $("div.overlay").toggleClass("hidden");
})
