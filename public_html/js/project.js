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
            else
                location.href="/apoyo/pago/"+($("#link").val())+"/"+($(this).attr("id"));
        });


});