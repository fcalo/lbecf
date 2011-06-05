<?
function getWidthImageFromHeight($path, $height)
{
	if (file_exists($path) && is_file($path))
	{
	   // get original images width and height
	   list($or_w, $or_h, $or_t) = getimagesize($path);
	
	   // obtain the image's ratio
	   $ratio = ($or_w / $or_h);
	   
	   return $height*$ratio;
	}
	else 
		return $height;
}
function getHeightImageFromWidth($path, $width)
{
	if (file_exists($path) && is_file($path))
	{
	   // get original images width and height
	   list($or_w, $or_h, $or_t) = getimagesize($path);
	
	   // obtain the image's ratio
	   $ratio = ($or_h / $or_w);
	   
	   return $width*$ratio;
	}
	else
		return $width;
	
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


function imagecreatefrombmp($file)
{
global  $CurrentBit, $echoMode;

$f=fopen($file,"r");
$Header=fread($f,2);

if($Header=="BM")
{
 $Size=freaddword($f);
 $Reserved1=freadword($f);
 $Reserved2=freadword($f);
 $FirstByteOfImage=freaddword($f);

 $SizeBITMAPINFOHEADER=freaddword($f);
 $Width=freaddword($f);
 $Height=freaddword($f);
 $biPlanes=freadword($f);
 $biBitCount=freadword($f);
 $RLECompression=freaddword($f);
 $WidthxHeight=freaddword($f);
 $biXPelsPerMeter=freaddword($f);
 $biYPelsPerMeter=freaddword($f);
 $NumberOfPalettesUsed=freaddword($f);
 $NumberOfImportantColors=freaddword($f);

if($biBitCount<24)
 {
  $img=imagecreate($Width,$Height);
  $Colors=pow(2,$biBitCount);
  for($p=0;$p<$Colors;$p++)
   {
    $B=freadbyte($f);
    $G=freadbyte($f);
    $R=freadbyte($f);
    $Reserved=freadbyte($f);
    $Palette[]=imagecolorallocate($img,$R,$G,$B);
   };




if($RLECompression==0)
{
   $Zbytek=(4-ceil(($Width/(8/$biBitCount)))%4)%4;

for($y=$Height-1;$y>=0;$y--)
    {
     $CurrentBit=0;
     for($x=0;$x<$Width;$x++)
      {
         $C=freadbits($f,$biBitCount);
       imagesetpixel($img,$x,$y,$Palette[$C]);
      };
    if($CurrentBit!=0) {freadbyte($f);};
    for($g=0;$g<$Zbytek;$g++)
     freadbyte($f);
     };

 };
};


if($RLECompression==1) //$BI_RLE8
{
$y=$Height;

$pocetb=0;

while(true)
{
$y--;
$prefix=freadbyte($f);
$suffix=freadbyte($f);
$pocetb+=2;

$echoit=false;

if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
if(($prefix==0)and($suffix==1)) break;
if(feof($f)) break;

while(!(($prefix==0)and($suffix==0)))
{
 if($prefix==0)
  {
   $pocet=$suffix;
   $Data.=fread($f,$pocet);
   $pocetb+=$pocet;
   if($pocetb%2==1) {freadbyte($f); $pocetb++;};
  };
 if($prefix>0)
  {
   $pocet=$prefix;
   for($r=0;$r<$pocet;$r++)
    $Data.=chr($suffix);
  };
 $prefix=freadbyte($f);
 $suffix=freadbyte($f);
 $pocetb+=2;
 if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
};

for($x=0;$x<strlen($Data);$x++)
 {
  imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
 };
$Data="";

};

};


if($RLECompression==2) //$BI_RLE4
{
$y=$Height;
$pocetb=0;

/*while(!feof($f))
 echo freadbyte($f)."_".freadbyte($f)."<BR>";*/
while(true)
{
//break;
$y--;
$prefix=freadbyte($f);
$suffix=freadbyte($f);
$pocetb+=2;

$echoit=false;

if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
if(($prefix==0)and($suffix==1)) break;
if(feof($f)) break;

while(!(($prefix==0)and($suffix==0)))
{
 if($prefix==0)
  {
   $pocet=$suffix;

   $CurrentBit=0;
   for($h=0;$h<$pocet;$h++)
    $Data.=chr(freadbits($f,4));
   if($CurrentBit!=0) freadbits($f,4);
   $pocetb+=ceil(($pocet/2));
   if($pocetb%2==1) {freadbyte($f); $pocetb++;};
  };
 if($prefix>0)
  {
   $pocet=$prefix;
   $i=0;
   for($r=0;$r<$pocet;$r++)
    {
    if($i%2==0)
     {
      $Data.=chr($suffix%16);
     }
     else
     {
      $Data.=chr(floor($suffix/16));
     };
    $i++;
    };
  };
 $prefix=freadbyte($f);
 $suffix=freadbyte($f);
 $pocetb+=2;
 if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
};

for($x=0;$x<strlen($Data);$x++)
 {
  imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
 };
$Data="";

};

};


 if($biBitCount==24)
{
 $img=imagecreatetruecolor($Width,$Height);
 $Zbytek=$Width%4;

   for($y=$Height-1;$y>=0;$y--)
    {
     for($x=0;$x<$Width;$x++)
      {
       $B=freadbyte($f);
       $G=freadbyte($f);
       $R=freadbyte($f);
       $color=imagecolorexact($img,$R,$G,$B);
       if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
       imagesetpixel($img,$x,$y,$color);
      }
    for($z=0;$z<$Zbytek;$z++)
     freadbyte($f);
   };
};
return $img;

};


fclose($f);


}

function freaddword($f)
{
 $b1=freadword($f);
 $b2=freadword($f);
 return $b2*65536+$b1;
}

function freadword($f)
{
 $b1=freadbyte($f);
 $b2=freadbyte($f);
 return $b2*256+$b1;
};

function freadbyte($f)
{
 return ord(fread($f,1));
};

function getThumb($path,$width, $height){
	$tokens=@split("/",$path);
	$num=sizeof($tokens);
	$p="";
	for ($i=0;$i<$num-1;$i++)
		$p.=$tokens[$i]."/";
	
	$p.=$width."x".$height."/thumb_".$tokens[$num-1];
	return $p;
}

?>