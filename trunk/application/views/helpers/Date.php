<?php
class View_Helper_Date extends Zend_View_Helper_Abstract
{
    public function Date2DateComment($date){
        $today = strtotime(date(DATE_ATOM));
        $today+=3600;
        $commentDate = strtotime($date);
        $minutos=round(abs($today-$commentDate)/60);
        if($minutos<1)
            return "a few seconds";
        if ($minutos<2)
            return $minutos." minute ago";
        if ($minutos<60)
            return $minutos." minutes ago";
        if ($minutos<360)
            return round($minutos/60,0)." hour".(($horas<120)?"":"s")." and ".($minutos%60)." minute ago";

        if ($minutos<1440)
            return "".round($minutos/60,0)." hours ago";

        $da=explode(" ", $date);
        $d=explode("-", $da[0]);
        return $d[2]."/".$d[1]."/".$d[0];

        
    }

    
}


?>
