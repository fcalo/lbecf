/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
        $( "#tabs" ).tabs();
        $( "#progressbar" ).progressbar({
                value: $("#porcentaje").html()
        });
        $("#recompensas .btn-red").click(function(){
            if($("#idUser").length==0 || $("#idUser").val()=="")
                location.href="/usuario/login";
            else{
                var a=$(this).attr("id").split("_");
                $("#id-recompensa").val(a[0]);
                $("#apoyo-minimo").val(a[1]);
                $("#amount").val($("#apoyo-minimo").val());

                $( "#dialog-form" ).dialog( "open" );
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

                $("#amount").keyup(function(){
                    if ($(this).val() != '')
                        $(this).val($(this).attr('value').replace(/[^0-9]/g, ""));
                });


                $("#btn-reward").click(function(){
                    $("#reward-item").clone().appendTo(".reward");
                    var count=$(".reward-item").length;
                    $(".reward-item:nth-child("+count+") span b").html(count);

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
         $("#down").click(function(){
            if($("#idUser").length==0 || $("#idUser").val()=="")
                location.href="/usuario/login";
            else{
                voto(-1);
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