<?
include("../session.php");
include("../entity.php");
include("../field.php");
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');


function generar_pass ($longitud) {

        mt_srand((double)microtime()*1000000);

        $letras=array (array ("a","e","i","o","u"),array ("b","c","d","f","g","h","j","k","l","m","n","p","q","r","s","t","v","w","x","y","z"),array ("br","ch","cr","fr","gr","pr","pl","tr"),array ("lt","lm","ln","ls","mn","st","sm"));

        // Primer caracter
        $primero=mt_rand (0,2);
        $elemento=mt_rand (0,count ($letras[$primero])-1);
        $password=$letras[$primero][$elemento];
        if ($primero!=0) $cons=true;

        while (strlen ($password)<$longitud) {
                if ($cons) {
                        $index_prob=mt_rand (0,4);
                        $password.=$letras[0][$index_prob];
                        $cons=false;
                }
                else {
                        $cons=array ("1","1","1","2","3");
                        // tiene mas probabilidades, 3/5 de salir solo una consonante
                        $index_prob=mt_rand (0,count($cons)-1);
                        $elemento=mt_rand (0,count ($letras[$cons[$index_prob]])-1);
                        $password.=$letras[$cons[$index_prob]][$elemento];
                }
        }
        return substr ($password,0,$longitud); // ya que si lo ultimo a�adido es una consonante doble puede que estemos a�adiendo un caracter de mas

}

$idEntidad=$_GET["i"];
$entities=unserialize($_SESSION[constant(CT_ENTITIES.PROJECT)]);

$pass=generar_pass(8);

//definicion
include("../../config/database.php");
foreach($entities as $entity){
	if ($entity->getIdEntity()==$idEntidad){
		$primero=true;
		$sql="update ".$entity->getTable()." ";
		$sqlSelect="select user, email from ".$entity->getTable()." ";
		foreach($entity->getFields() as $field){
			if (!$field->getIsKey()){
				if ($field->getIsPass())
					$sql.="set ".$field->getName()."='".sha1($pass)."'";
			}
			else{
				if($where=="")
					$where=" where ".$field->getName()."='".$_GET["val"]."'";
				else
					$where=" and ".$field->getName()."='".$_GET["val"]."'";

			}
			
		}
		
		$sql.=$where;
		$rs=$db->get_row($sqlSelect.$where,ARRAY_A);
		if ($rs['email']!=""){
			if ($db->query($sql)){
                            $header = "From: Kokku <kokku@kokku.es>\r\n"; //optional headerfields
                            $header .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                            if(mail($rs['email'], "Datos de acceso","Sus datos son: <br> user: ".$rs['email']."<br> pass: ".$pass."<br><br> Gracias.<br><br>Kokku.",$header))
                                echo "Generada y enviada correctamente.";
                            else
                                echo "No se pudo enviar por email.";
                        }else
                            echo "ocurrio un error generando la password";

				//if (!mail($rs['email'],"Afiliados Acea", "Sus datos son: <br> user: ".$rs['email']."<br> pass: ".$pass."<br><br> Gracias por confiar en Acea."))
					//echo $pass;
			
		}
		else
			echo "No se pudo recuperar el Email. ".$sqlSelect.$where;
		
	}
}