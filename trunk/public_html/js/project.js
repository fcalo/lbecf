/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
        $( "#tabs" ).tabs();
        $( "#progressbar" ).progressbar({
                value: $("#porcentaje").html()
        });
});