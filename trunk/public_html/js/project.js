/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {

        $(".reward input").val("");

        $("#back-project").click(function(){
            $("#panel-form").hide();
            $("#panel-project").show();
        })

        $("#add-propuesta").click(function(){
            if($("#closed").length==0 || $("#closed").val()==""){
                if($("#idUser").length==0 || $("#idUser").val()=="")
                    location.href="/usuario/login";
                else{
                    $( "#dialog-form-proposal" ).dialog( "open" );
                }
            }else{
                alert("Proyecto cerrado.");
            }

        });

        $(".show_comments").click(function(){
            if($(this).html()=="Ocultar"){
                $("#comentarios_concurso_"+$(this).attr("id")).fadeOut();
                $(this).html($(this).attr("lb"));
            }else{
                $(this).attr("lb",$(this).html());
                $("#comentarios_concurso_"+$(this).attr("id")).fadeIn();
                $(this).html("Ocultar");
            }
        });


        $("#descripcion").html(unescape($("#descripcion").html()));
        $(".unescape").each(function(index){
            $(this).html(unescape($(this).html()));
        });
        
        $( "#tabs" ).tabs();
        if($("#set_concurso").length>0)
            $( "#tabs" ).tabs( "select" , 2 );
        
        
        $( "#progressbar" ).progressbar({
                value: ($("#porcentaje").html()*1)
        });
        $("#recompensas .btn-red").click(function(){
            if($("#closed").length==0 || $("#closed").val()==""){
                if($("#idUser").length==0 || $("#idUser").val()=="")
                    location.href="/usuario/login";
                else{
                    var a=$(this).attr("id").split("_");
                    $("#id-recompensa").val(a[0]);
                    $("#apoyo-minimo").val(a[1]);
                    $("#amount").val($("#apoyo-minimo").val());

                    $( "#dialog-form" ).dialog( "open" );
                }
            }else{
                alert("Proyecto cerrado.");
            }
        });


        $( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 210,
			width: 350,
			modal: true,
			buttons: {
				"Apoyar": function() {
					var bValid = true;
					$("#amount").removeClass( "ui-state-error" );

					bValid=(($("#amount").val()*1)>=($("#apoyo-minimo").val()*1));

					if ( bValid ) {
                                                $("#dialog-form div").hide();
                                                $("#dialog-form").css("background","url(../img/loader.gif) center center no-repeat");
						setTimeout('location.href="/apoyo/pago/'+($("#link").val())+'/'+($("#amount").val())+'/'+$("#id-recompensa").val()+'"',1000);
						//$( this ).dialog( "close" );
					}else{
                                            $("#amount").addClass( "ui-state-error" );
                                            $(".validateTips").html("Al menos debes aportar "+$("#apoyo-minimo").val()+" € para obtener la recompensa seleccionada")
                                        }
				},
				"Cerrar": function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$("#amount").removeClass( "ui-state-error" );
			}
		});

         $( "#dialog-form-proposal" ).dialog({
                autoOpen: false,
                height: 260,
                width: 350,
                modal: true,
                buttons: {
                        "Proponer": function() {
                                var bValid = true;
                                $("#proposal").removeClass( "ui-state-error" );

                                bValid=$("#proposal").val()!="";

                                if ( bValid ) {
                                        $("#dialog-form div").hide();
                                        $("#dialog-form").css("background","url(../img/loader.gif) center center no-repeat");
                                        setTimeout('$("#do-proposal").submit()',1000);
                                        //$( this ).dialog( "close" );
                                }else{
                                    $("#proposal").addClass( "ui-state-error" );
                                }
                        },
                        "Cerrar": function() {
                                $( this ).dialog( "close" );
                        }
                },
                close: function() {
                        $("#amount").removeClass( "ui-state-error" );
                }
        });

                $("#amount").keyup(function(){
                    if ($(this).val() != '')
                        $(this).val($(this).attr('value').replace(/[^0-9]/g, ""));
                });


                $("#btn-reward").click(function(){
                    $("#reward-item").clone().appendTo(".reward");
                    var count=$(".reward-item").length;
                    $(".reward-item:nth-child("+count+") span b").html(count);
                    $(".reward-item:nth-child("+count+") input").val("");

                });
                $("#btn-reward-delete").click(function(){
                    var count=$(".reward-item").length;
                    if(count>0)
                        $(".reward-item:nth-child("+count+")").remove();

                });

         $("#up").click(function(){
            if($("#idUser").length==0 || $("#idUser").val()=="")
                location.href="/usuario/login";
            else{
                voto(1);
            }
         });
         $(".up").click(function(){
            if($("#idUser").length==0 || $("#idUser").val()=="")
                location.href="/usuario/login";
            else{
                var a=$(this).attr("id").split("-");
                votoPropuesta(1,a[1]);
            }
         });
         $("#down").click(function(){
            if($("#idUser").length==0 || $("#idUser").val()=="")
                location.href="/usuario/login";
            else{
                voto(-1);
            }
         });
         $(".down").click(function(){
            if($("#idUser").length==0 || $("#idUser").val()=="")
                location.href="/usuario/login";
            else{
                var a=$(this).attr("id").split("-");
                votoPropuesta(-1,a[1]);
            }
         });
         $("#add-comentario").click(function(){
            if($("#idUser").length==0 || $("#idUser").val()=="")
                location.href="/usuario/login";
            else{
                comentario();
            }
         });
         $(".add-comentario").click(function(){
            if($("#idUser").length==0 || $("#idUser").val()=="")
                location.href="/usuario/login";
            else{
                comentarioPropuesta($(this).attr("id"));
            }
         });
});

function voto(valor){
    $.ajax({
        url: '/proyecto/voto/'+$("#link").val(),
        data: "valor="+valor,
        type: 'post',
        cache: false,
        dataType: 'json',
        success: function (data) {
                $("#lup").html("("+data['positivos']+")");
                $("#ldown").html("("+data['negativos']+")");
        }

    });
}
function votoPropuesta(valor, idPropuesta){
    $.ajax({
        url: '/proyecto/voto-propuesta/'+$("#link").val(),
        data: "valor="+valor+"&propuesta="+idPropuesta,
        type: 'post',
        cache: false,
        dataType: 'json',
        success: function (data) {
                $("#lup-"+idPropuesta).html("("+data['positivos']+")");
                $("#ldown-"+idPropuesta).html("("+data['negativos']+")");
        }

    });
}
function comentario(){
    if($("#txt-comentario").val()=="")
        return false;
    $.ajax({
        url: '/proyecto/comentario/'+$("#link").val(),
        data: "comentario="+$("#txt-comentario").val(),
        type: 'post',
        cache: false,
        dataType: 'json',
        success: function (data) {
            $("#comentarios").append('<div class="comentario"><img src="'+data['imagen']+'"><div><span>'+data['username']+'</span>\n'+data['txt']+'</div><div class="fecha">hace unos segundos</div></div>');
            $("#txt-comentario").val();
            $("#txt-comentario").html();
        }

    });
}
function comentarioPropuesta(idPropuesta){
    if($("#txt-comentario"+idPropuesta).val()=="")
        return false;
    $.ajax({
        url: '/proyecto/comentario-propuesta/'+$("#link").val(),
        data: "comentario="+$("#txt-comentario-"+idPropuesta).val()+"&propuesta="+idPropuesta,
        type: 'post',
        cache: false,
        dataType: 'json',
        success: function (data) {
            $("#comentarios_"+data['propuesta']).append('<div class="comentario-concurso"><img src="'+data['imagen']+'"><div><span>'+data['username']+'</span>\n'+data['txt']+'</div><div class="fecha">hace unos segundos</div></div>');
            $("#txt-comentario-"+data['propuesta']).val();
            $("#txt-comentario-"+data['propuesta']).html();
        }

    });
}