	    /*
	    * funcion para comprobar si una año es bisiesto
	    * argumento anyo > año extraido de la fecha introducida por el usuario
	    */
	    function anyoBisiesto(anyo)
	    {
	        /**
	        * si el año introducido es de dos cifras lo pasamos al periodo de 1900. Ejemplo: 25 > 1925
	        */
	        if (anyo < 100)
	            var fin = anyo + 1900;
	        else
	            var fin = anyo ;
	
	        /*
	        * primera condicion: si el resto de dividir el año entre 4 no es cero > el año no es bisiesto
	        * es decir, obtenemos año modulo 4, teniendo que cumplirse anyo mod(4)=0 para bisiesto
	        */
	        if (fin % 4 != 0)
	            return false;
	        else
	        {
	            if (fin % 100 == 0)
	            {
	                /**
	                * si el año es divisible por 4 y por 100 y divisible por 400 > es bisiesto
	                */
	                if (fin % 400 == 0)
	                {
	                    return true;
	                }
	                /**
	                * si es divisible por 4 y por 100 pero no lo es por 400 > no es bisiesto
	                */
	                else
	                {
	                    return false;
	                }
	            }
	            /**
	            * si es divisible por 4 y no es divisible por 100 > el año es bisiesto
	            */
	            else
	            {
	                return true;
	            }
	        }
	    }


	    /**
	    * funcion principal de validacion de la fecha
	    * argumento fecha > cadena de texto de la fecha introducida por el usuario
	    */
		function validar(sFec)
		{
	    	/**
	        * obtenemos la fecha introducida y la separamos en dia, mes y año
	        */
	        dia=sFec.split("/")[0];
	        mes=sFec.split("/")[1];
	 		anyo=sFec.split("/")[2];
			if( (isNaN(dia)==true) || (isNaN(mes)==true) || (isNaN(anyo)==true) )
			{
		    	alert("La fecha debe estar formada sólo por números");
		     	return false;
		       }
		       if(anyoBisiesto(anyo))
		           febrero=29;
		       else
		           febrero=28;
		       /**
		       * si el mes introducido es negativo, 0 o mayor que 12 > alertamos y detenemos ejecucion
		       */
		       if ((mes<1) || (mes>12))
		       {
		           alert("El mes introducido no es válido. Por favor, introduzca un mes correcto");
		           return false;
		       }
		       /**
		       * si el mes introducido es febrero y el dia es mayor que el correspondiente
		       * al año introducido > alertamos y detenemos ejecucion
		       */
		       if ((mes==2) && ((dia<1) || (dia>febrero)))
		       {
		           alert("El día introducido no es valido. Por favor, introduzca un día correcto");
		           return false;
		       }
		       /**
		       * si el mes introducido es de 31 dias y el dia introducido es mayor de 31 > alertamos y detenemos ejecucion
		       */
		       if (((mes==1) || (mes==3) || (mes==5) || (mes==7) || (mes==8) || (mes==10) || (mes==12)) && ((dia<1) || (dia>31)))
		       {
		           alert("El día introducido no es válido. Por favor, introduzca un día correcto");
		           return false;
		       }
		       /**
		       * si el mes introducido es de 30 dias y el dia introducido es mayor de 301 > alertamos y detenemos ejecucion
		       */
		       if (((mes==4) || (mes==6) || (mes==9) || (mes==11)) && ((dia<1) || (dia>30)))
		       {
		           alert("El día introducido no es valido. Por favor, introduzca un día correcto");
		           return;
		       }
		       /**
		       * si el mes año introducido es menor que 1900 o mayor que 2010 > alertamos y detenemos ejecucion
		       * NOTA: estos valores son a eleccion vuestra, y no constituyen por si solos fecha erronea
		       */
		       if ((anyo<1900) || (anyo>2099))
		       {
		           alert("El año introducido no es válido. Por favor, introduzca un año entre 1900 y 2099");
		       }
		       /**
		       * en caso de que todo sea correcto > enviamos los datos del formulario
		       * para ello debeis descomentar la ultima sentencia
		       */
		       else
		          return true;
		    }    

function crearXMLHttpRequest() 
{
  var xmlHttp=null;
  if (window.ActiveXObject) 
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  else 
    if (window.XMLHttpRequest) 
      xmlHttp = new XMLHttpRequest();
  return xmlHttp;
}

function replace(texto,s1,s2){
	return texto.split(s1).join(s2);
}
function soloNumeros(e){
	key=(document.all) ? e.keyCode : e.which;
	if (key < 48 || key > 57){
		return false;
	}
	return true;
}