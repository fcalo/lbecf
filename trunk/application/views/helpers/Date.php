<?php
class View_Helper_Date extends Zend_View_Helper_Abstract
{
    public function Date2DateComment($date){
        $today = strtotime(date(DATE_ATOM));
        $commentDate = strtotime($date);
        $minutos=round(abs($today-$commentDate)/60);
        if($minutos<1)
            return "Hace algunos segundos";
        if ($minutos<2)
            return "Hace ".$minutos." minuto";
        if ($minutos<60)
            return "Hace ".$minutos." minutos";
        if ($minutos<360)
            return "Hace ".round($minutos/60,0)." hora".(($horas<120)?"":"s")." y ".($minutos%60)." minutos";

        if ($minutos<1440)
            return "Hace ".round($minutos/60,0)." horas";

        $da=explode(" ", $date);
        $d=explode("-", $da[0]);
        return "El ".$d[2]." del ".$d[1]." del ".$d[0];

        
    }

    
}


?>
