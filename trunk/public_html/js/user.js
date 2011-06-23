$(function() {
    $(".cancel-preaproval").click(function(){
        $("#preapproval").val($(this).attr("id"));
        $( "#dialog-form-cancel" ).dialog( "open" );
    });


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
    $( "#dialog-form-cancel" ).dialog({
			autoOpen: false,
			height: 150,
			width: 350,
			modal: true,
			buttons: {
				"Aceptar": function() {
                                        $("#dialog-form-cancel div").hide();
                                        $("#dialog-form-cancel").css("background","url(../img/loader.gif) center center no-repeat");
                                        $.ajax({
                                            url: '/apoyo/cancel/'+$("#preapproval").val(),
                                            type: 'post',
                                            cache: false,
                                            dataType: 'html',
                                            success: function (data) {
                                                if(data=="ok"){
                                                    $("#"+$("#preapproval").val()).parent().parent().remove();
                                                    alert("Cancelado correctamente");
                                                }else
                                                    alert("No se pudo cancelar. Pongase en contacto con el servicio técnico");
                                                $( "#dialog-form-cancel" ).dialog( "close" );
                                                $("#dialog-form-cancel div").show();
                                                $("#dialog-form-cancel").css("background","");
                                            }
                                        });
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