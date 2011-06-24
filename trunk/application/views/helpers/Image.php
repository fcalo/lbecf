<?php
class View_Helper_Image extends Zend_View_Helper_Abstract
{
    function image($idUser, $imagen)
    {
        if(strlen($imagen)<10)
            return STATIC_PATH."/img/user.jpg";

        $urlImagen=STATIC_PATH."/admin/".str_replace("/".$idUser."/","/".$idUser."/50x/thumb_",$imagen);
        $pathImagen=$_SERVER['DOCUMENT_ROOT']."/admin/".str_replace("/".$idUser."/","/".$idUser."/50x/thumb_",$imagen);
        
        if (file_exists($pathImagen))
            return $urlImagen;
        else
            return STATIC_PATH."/img/user.jpg";
    }
    function ensurePath($path){
	//return true;
	return $this->ensurePath_($path);
	/*$rt=true;
	if (!file_exists($path)){
		if (!mkdir($path,0777,true))
			die("No se pudo crear ".$path);
	}
	return $rt;
	*/
    }

    function ensurePath_($path){
            $rt=true;
            $a=explode("/",$path);
            $c=sizeof($a);
            $r="";
            for ($i=0;$i<$c;$i++){
                    $r.=$a[$i]."/";
                    //echo $r;
                    //echo "<br>";
                    if (!file_exists($r)){
                            //echo "no existe";
                            if (!mkdir($r))
                                    die("No se pudo crear ".$r);
                    }

                    if(substr_count($r,"uploads")>0){
                        //echo $r;
                        @chmod($r,0777);
                    }

            }
            return $rt;

    }

    /**********************************************************
     * function resizeImage:
     *
     *  = creates a resized image based on the max width
     *    specified as well as generates a thumbnail from
     *    a rectangle cut from the middle of the image.
     *
     *    @dir    = directory image is stored in
     *    @newdir = directory new image will be stored in
     *    @img    = the image name
     *    @max_w  = the max width of the resized image
     *    @max_h  = the max height of the resized image
     *    @th_w  = the width of the thumbnail
     *    @th_h  = the height of the thumbnail
     *
     **********************************************************/

    //function resizeImage($dir, $newdir, $img, $max_w, $max_h, $th_w, $th_h)
    function resizeImage($dir, $newdir, $img, $max_w, $max_h)
    {
       // set destination directory
       if (!$newdir) $newdir = $dir;

       // get original images width and height
       list($or_w, $or_h, $or_t) = getimagesize($dir.$img);

       // obtain the image's ratio
       $ratio = ($or_h / $or_w);

       // original image
       switch($or_t)
       {
                    case 1:$or_image = imagecreatefromgif($dir.$img);break;
                    case 2:$or_image = imagecreatefromjpeg($dir.$img);break;
                    case 3:$or_image = imagecreatefrompng($dir.$img);break;
                    case 6:$or_image = imagecreatefrombmp($dir.$img);break;
                    case 15:$or_image = imagecreatefromwbmp($dir.$img);break;
       }

       if (or_image!="")
       {
               // resize image?
               if ($or_w > $max_w || $or_h > $max_h) {

                   // resize by height, then width (height dominant)
                   if ($max_h < $max_w) {
                       $rs_h = $max_h;
                       $rs_w = $rs_h / $ratio;
                   }
                   // resize by width, then height (width dominant)
                   else {
                       $rs_w = $max_w;
                       $rs_h = $ratio * $rs_w;
                   }

                   // copy old image to new image
                   $rs_image = imagecreatetruecolor($rs_w, $rs_h);
                   imagecopyresampled($rs_image, $or_image, 0, 0, 0, 0, $rs_w, $rs_h, $or_w, $or_h);
               }
               // image requires no resizing
               else {
                   $rs_w = $or_w;
                   $rs_h = $or_h;

                   $rs_image = $or_image;
               }
                    /*
               // generate resized image
               //imagejpeg($rs_image, $newdir.$img, 100);

               $th_image = imagecreatetruecolor($th_w, $th_h);

               // cut out a rectangle from the resized image and store in thumbnail
               $new_w = (($rs_w / 2) - ($th_w / 2));
               $new_h = (($rs_h / 2) - ($th_h / 2));

               imagecopyresized($th_image, $rs_image, 0, 0, $new_w, $new_h, $rs_w, $rs_h, $rs_w, $rs_h);

               // generate thumbnail

               imagejpeg($th_image, $newdir.'thumb_'.$img, 100);
               */
               imagejpeg($rs_image, $newdir.'thumb_'.$img, 100);
               chmod($newdir.'thumb_'.$img,0777);
               return true;
       }
       else
       {
                    $rt=copy($dir.$img,$newdir.'thumb_'.$img);
                    chmod($newdir.'thumb_'.$img,0777);
               return $rt;
       }


    }

}


?>
