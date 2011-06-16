/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
        $(".txt").each(function(index){
            $(this).html(unescape($(this).html()));
        });

});