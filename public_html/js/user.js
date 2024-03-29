$(function() {
    
    $(".show_mail").click(function(){
        id=$(this).attr("id").replace("ver_","");
        $("#mensaje_"+id).fadeIn();
        $("#ver_"+id).hide();
        $("#ocultar_"+id).show();
    });
    $(".hide_mail").click(function(){
        id=$(this).attr("id").replace("ocultar_","");
        $("#mensaje_"+id).fadeOut();
        $("#ver_"+id).show();
        $("#ocultar_"+id).hide();
    });

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
    $("#mail").click(function(){
          $( "#dialog-form-mail" ).dialog( "open" );
    })
    $(".update").click(function(){
          $("#update_id").val($(this).attr("id"));
          $( "#dialog-form-update" ).dialog( "open" );
    })
    $(".remove_update").click(function(){
          $("#input_remove_update").val($(this).attr("id"));
          $("#frm_remove_update").submit();
    })
    $("#new-project").click(function(){
          location.href="/event/crear/";
    })
    $( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 150,
			width: 350,
			modal: true,
			buttons: {
				"Ok": function() {
                                        $("#dialog-form div").hide();
                                        $("#dialog-form").css("background","url(../img/loader.gif) center center no-repeat");
                                        setTimeout('location.href="/user/baja/"',1000);
				},
				"Close": function() {
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
				"Ok": function() {
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
				"Close": function() {
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
				"Ok": function() {
                                        $("#dialog-form div").hide();
                                        $("#dialog-form").css("background","url(../img/loader.gif) center center no-repeat");
                                        setTimeout('$("#frm-image").submit();',1000);
				},
				"Close": function() {
					$( this ).dialog( "close" );
				}
			}
		});
    $( "#dialog-form-mail" ).dialog({
			autoOpen: false,
			height: 240,
			width: 350,
			modal: true,
			buttons: {
				"Ok": function() {
                                        $("#dialog-form div").hide();
                                        $("#dialog-form").css("background","url(../img/loader.gif) center center no-repeat");
                                        setTimeout('$("#frm-mail").submit();',1000);
				},
				"Close": function() {
					$( this ).dialog( "close" );
				}
			}
		});
    $( "#dialog-form-update" ).dialog({
			autoOpen: false,
			height: 210,
			width: 300,
			modal: true,
			buttons: {
				"Ok": function() {
                                        if($("#update").val()!=""){
                                            $("#dialog-form-update").css("background","url(../img/loader.gif) center center no-repeat");
                                            setTimeout('$("#frm-update").submit();',1000);
                                        }
				},
				"Close": function() {
					$( this ).dialog( "close" );
				}
			}
		});
         if($("#dialog-send").length>0){
             $( "#dialog-send" ).dialog({
			autoOpen: true,
			height: 150,
			width: 350,
			modal: true,
			buttons: {
				"Close": function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$( this ).dialog( "close" );
			}
		});

         }
});