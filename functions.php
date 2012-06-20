<?php

function validateUSAZip($zip_code)
{
  if(preg_match("/^([0-9]{5})(-[0-9]{4})?$/i",$zip_code))
    return true;
  else
    return false;
}

function partyWriteOut($party)
{
	if(strcmp($party,'D') == 0) {
		return 'democrat';
	} else if(strcmp($party,'R') == 0) {
		return 'republican';
	} else if(strcmp($party,'I') == 0) {
		return 'independent';
	} else {
		return 'member';
	}
}

function billTypeWriteOut($type)
{
	if(strcmp($type,'hr')==0) {
		return '<acronym title="House bill">H.R.</acronym>';
	} else if(strcmp($type,'hres')==0) {
		return '<acronym title="House resolution">H.Res</acronym>';
	} else if(strcmp($type,'hjres')==0) {
		return '<acronym title="House joint resolution">H.J.Res</acronym>';
	} else if(strcmp($type,'hcres')==0) {
		return '<acronym title="House concurrent resolution">H.C.Res</acronym>';
	} else if(strcmp($type,'s')==0) {
		return '<acronym title="Senate bill">S.</acronym>';
	} else if(strcmp($type,'sres')==0) {
		return '<acronym title="Senate resolution">S.Res</acronym>';
	} else if(strcmp($type,'sjres')==0) {
		return '<acronym title="Senate joint resolution">S.J.Res</acronym>';
	} else if(strcmp($type,'scres')==0) {
		return '<acronym title="Senate concurrent resolution">S.C.Res</acronym>';
	} else {
		return '';
	}
}

function addGovtrack($line, $session)
{
	$matches = array();
	preg_match("([HS][ \\.]?[RrEeSsJjCc\\. ]*[0-9]{1,})", $line, $matches);
	$line = str_replace($matches[0], "<a href=\"http://www.govtrack.us/congress/bills/".$session."/".strtolower(preg_replace('/[\\. ]/',"",$matches[0]))."\">".$matches[0]."</a>", $line);
	//$line = preg_replace("([HS][ \\.]?[RrEeSsJjCc\\. ]*[0-9]{1,})","<a href=\"http://www.govtrack.us/congress/bills/111/".preg_replace(" ","","$2")."\">Bill$2</a>",$line,1);
	return $line;
}

function stripBillType($search)
{
	$search = str_replace('H.R.','',$search);
	//Rest are probably unnecessary
	/*$search = str_replace('H.Res','',$search);
	$search = str_replace('H.J.Res','',$search);
	$search = str_replace('H.C.Res','',$search);
	$search = str_replace('S.Res',''$search);*/
	return $search;
}

function getXmlObj($url)
{
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_HEADER, false);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);

	$response = curl_exec($ch);
	
	curl_close($ch);
	
	$obj = simplexml_load_string( $response );
	
	return $obj;
}

function getJsonObj($url)
{
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_HEADER, false);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);

	$response = curl_exec($ch);
	
	curl_close($ch);
	
	$obj = json_decode( $response );
	
	return $obj;
}

function shortenString($string, $chars)
{
	if (strlen($string)>$chars) {
		return substr($string,0,$chars).'...';
	} else {
		return $string;
	}
}

?>