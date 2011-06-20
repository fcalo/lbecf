$(function() {
    $("#baja").click(function(){
          $( "#dialog-form" ).dialog( "open" );
    })
    $("#image").click(function(){
          $( "#dialog-form-image" ).dialog( "open" );
    })
    $("#new-project").click(function(){
          location.href="/proyecto/crear/";
    })
    $( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 150,
			width: 350,
			modal: true,
			buttons: {
				"Aceptar": function() {
                                        $("#dialog-form div").hide();
                                        $("#dialog-form").css("background","url(../img/loader.gif) center center no-repeat");
                                        setTimeout('location.href="/usuario/baja/"',1000);
				},
				"Cerrar": function() {
					$( this ).dialog( "close" );
				}
			}
		});
    $( "#dialog-form-image" ).dialog({
			autoOpen: false,
			height: 150,
			width: 350,
			modal: true,
			buttons: {
				"Aceptar": function() {
                                        $("#dialog-form div").hide();
                                        $("#dialog-form").css("background","url(../img/loader.gif) center center no-repeat");
                                        setTimeout('$("#frm-image").submit();',1000);
				},
				"Cerrar": function() {
					$( this ).dialog( "close" );
				}
			}
		});
});