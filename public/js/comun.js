/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
        $( "#logo" ).click(function(){
            location.href="/";
        });
        $( ".fb" ).click(function(){
            location.href="/usuario/oauth/login";
        });

});