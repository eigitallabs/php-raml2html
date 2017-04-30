<?php

// Source of Your RAML file (local or http)


$ch = curl_init();
$source = "https://anypoint.mulesoft.com/apiplatform/repository/v2/organizations/94693e32-03c1-4adc-8cdb-deab2cc71db7/public/apis/54089/versions/56071/files/root";
curl_setopt($ch, CURLOPT_URL, $source);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec ($ch);
curl_close ($ch);

$destination = "raml/epaisa.raml";
$file = fopen($destination, "w+");
fputs($file, $data);
fclose($file);

$RAMLsource = 'https://anypoint.mulesoft.com/apiplatform/repository/v2/organizations/94693e32-03c1-4adc-8cdb-deab2cc71db7/public/apis/54089/versions/56071/files/root';

// Types of Action Verbs Allowed
$RAMLactionVerbs = array('get', 'post', 'put', 'patch', 'delete', 'connect', 'trace');

// APC Cache Time Limit - set to "0" to disable
$cacheTimeLimit = '3600';

// Path to the theme file for the docs
$docsTheme = 'templates/epaisa/index.phtml';

// Enable Try It (alpha)
$tryIt = false;

// Enable Disqus
$disqus = false;

// Disqus Short Name for Site (see Disqus admin)
$disqus_shortname = '';

?>
