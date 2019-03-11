<?php
function checkEGOVAuth() {
if(isset($_POST['SAMLResponse'])) {
$response = base64_decode($_POST['SAMLResponse']);
//echo $response;
//die();
$dom = new DOMDocument;
loadXML($dom, $response);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
$xpath->registerNamespace('saml2', 'urn:oasis:names:tc:SAML:2.0:assertion');
// NEW
$xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');

$result = $xpath->query('/samlp:Response/samlp:Status/samlp:StatusCode/@Value');
if($result->length > 0) {
  $node = $result->item(0);
  if($node->nodeValue == 'urn:oasis:names:tc:SAML:2.0:status:Success'){
    // OK SME - ime
    $result = $xpath->query('/samlp:Response/samlp:Status/samlp:StatusMessage');
    $node = $result->item(0);
//    echo ($node->nodeValue."<br/>");
    //echo "CERT VALIDATION<br/>";
    //get cert
    $result = $xpath->query('/samlp:Response/ds:Signature/ds:KeyInfo/ds:X509Data/ds:X509Certificate');
    $certData = "-----BEGIN CERTIFICATE-----\n".$result->item(0)->nodeValue."\n-----END CERTIFICATE-----\n";
    $cert = openssl_x509_read($certData);
    $parsed_cert = openssl_x509_parse($cert);
    //var_dump($parsed_cert);
    //echo $parsed_cert["serialNumber"];
    openssl_x509_free($cert);

    $certSerNum = $parsed_cert["serialNumber"];
    $xmlCertSerNum = $xpath->query('/samlp:Response/ds:Signature/ds:KeyInfo/ds:X509Data/ds:X509IssuerSerial/ds:X509SerialNumber')->item(0)->nodeValue;

    $GLOBAL_CERT_VAR = "1433317994";
    $GLOBAL_EGOV_IP = "83.228.89.17";
//    echo $_SERVER["REMOTE_ADDR"];
//    if (($certSerNum === $xmlCertSerNum) && ($certSerNum === $GLOBAL_CERT_VAR) && $_SERVER["REMOTE_ADDR"] == $GLOBAL_EGOV_IP){
    if (($certSerNum === $xmlCertSerNum) && ($certSerNum === $GLOBAL_CERT_VAR)){
      //echo "CERT_VALID";
    } else{
      die("WRONG SERIAL!!!");
    }


    // CERT VERIFY
    $key = file_get_contents(APPROOT."conf/egov.pem");
    $publicKey = openssl_get_publickey($key);
    $signature = base64_decode($xpath->query('/samlp:Response/ds:Signature/ds:SignatureValue')->item(0)->nodeValue);

    $signedInfoNode = $xpath->query('/samlp:Response/ds:Signature/ds:SignedInfo')->item(0);

    $signedInfoNodeCanonicalized = $signedInfoNode->C14N(true, false);

    // verify the signature
    $ok = openssl_verify($signedInfoNodeCanonicalized, $signature, $publicKey);

    if ($ok ==-1){
	//while ($msg = openssl_error_string())
	//    echo $msg . "<br />\n";
	die("Signature not verified");
	
    }

    $result = $xpath->query('/samlp:Response/saml2:Assertion/saml2:AttributeStatement/saml2:Attribute[@Name="urn:egov:bg:eauth:1.0:attributes:personNamesLatin"]');
    $node = $result->item(0);
//    echo "name - {$node->nodeValue}<br/>";
    // e-mail
    $result = $xpath->query('/samlp:Response/saml2:Assertion/saml2:AttributeStatement/saml2:Attribute[@Name="urn:egov:bg:eauth:1.0:attributes:eMail"]');
    $node = $result->item(0);
    $_SESSION['EXT_USER'] = $node->nodeValue;
//    echo "mail - {$node->nodeValue}<br/>";

    // context
//    $result = $xpath->query('/samlp:Response/samlp:Extensions/egovbga:SPContext/*');
//    $node = $result->item(0);
//    echo "context - {$node->nodeValue}<br/>";
    
  } else {
      // IMA problem
      $errResult = $xpath->query('/samlp:Response/samlp:Status/samlp:StatusMessage');
      $node = $errResult->item(0);
//      echo ($node->nodeValue);
  }
}
//$xml = simplexml_load_string($dom);
//if ($xml === false) {
//    die('Error parsing XML');
//}
//now we can loop through the xml structure
//foreach ($xml->channel->item as $item) {
//print $item->title;
//}
}
//session_start();
//$_SESSION['EXT_USER'] = array_values($_SESSION['samlUserdata']['User.email'])[0];
//echo $_SESSION['EXT_USER'] . "<br>";
}

?>
