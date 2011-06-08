<?php
	include ("entity.php");
	include ("field.php");
	include ("mXmlParser.php");
	
	function XMLEntities($string)
    {
        $string = preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '_privateXMLEntities("$0")', $string);
        return $string;
    }

    function _privateXMLEntities($num)
    {
    $chars = array(
        128 => '&#8364;',
        130 => '&#8218;',
        131 => '&#402;',
        132 => '&#8222;',
        133 => '&#8230;',
        134 => '&#8224;',
        135 => '&#8225;',
        136 => '&#710;',
        137 => '&#8240;',
        138 => '&#352;',
        139 => '&#8249;',
        140 => '&#338;',
        142 => '&#381;',
        145 => '&#8216;',
        146 => '&#8217;',
        147 => '&#8220;',
        148 => '&#8221;',
        149 => '&#8226;',
        150 => '&#8211;',
        151 => '&#8212;',
        152 => '&#732;',
        153 => '&#8482;',
        154 => '&#353;',
        155 => '&#8250;',
        156 => '&#339;',
        158 => '&#382;',
        159 => '&#376;');
        $num = ord($num);
        return (($num > 127 && $num < 160) ? $chars[$num] : "&#".$num.";" );
    } 
	
	
	$parser=new mXmlParser();

	$parser->parse(XMLEntities(utf8_decode(file_get_contents("config/config.xml"))));
	
	$entities=$parser->getEntities();
	$_SESSION[constant(CT_ENTITIES.PROJECT)]=serialize($entities);
	$_SESSION[CT_LANGUAGES]=$parser->getHasLanguages();

?>