<?php 
require_once('inc/spyc.php');
require_once('inc/ramlDataObject.php');
require_once('inc/raml.php');
require_once('inc/ramlPathObject.php');
require_once('inc/markdown.php');
require_once('inc/codeSamples.php');
require_once('config.php');


if(isset($_POST['endpoind'])){

 

 $RAML = false;
if ($cacheTimeLimit && function_exists('apc_fetch')) {
	$RAML = apc_fetch('RAML' . md5($RAMLsource));
} elseif (!$cacheTimeLimit && function_exists('apc_fetch')) {
	// Remove existing cache files
	apc_delete('RAML' . md5($RAMLsource));
}

if (!$RAML) {
	$RAMLarray = spyc_load(file_get_contents($RAMLsource));
	$RAML = new RAML2HTML\RAML($RAMLactionVerbs);

	$RAML->setIncludePath(dirname($RAMLsource) . '/');
	$RAML->buildFromArray($RAMLarray);
	
	if ($cacheTimeLimit && function_exists('apc_store')) {
		apc_store('RAML' . md5($RAMLsource), $RAML, $cacheTimeLimit);
	}
}
  

   $RAML->setCurrentPath($_POST['endpoind']);
   
   $current_path =  $RAML->baseUri . $RAML->getCurrentPath();
   $desc = $RAML->path()->description;

 
   $content = "<h5 class='uk-heading-bullet'>".$current_path."</h5>";
   $content .= "<div class='uk-placeholder'>".$desc."</div>";


   $content .= '<div class="uk-tile uk-tile-muted uk-padding-remove"><h3 class="uk-heading-bullet">Available Actions</h3></div>';
   
          
          $verbs = "<ul class='uk-subnav uk-subnav-pill' uk-switcher>";
              foreach ($RAML->path()->getVerbs() as $verb) { 
               
                $verbs .= "<li><a href='#''>".$verb."</a></li>";
               
              }
          $verbs .="</ul>";


          $verb_content = "<ul class='uk-switcher uk-margin'>";
             
             foreach ($RAML->path()->getVerbs() as $verb_con) { 
               
              $description = RAML2HTML\markdown::clean($RAML->path()->get($verb_con)->get('description'));
              $verb_content .= "<li><div class='uk-placeholder'>".$description."</div>";
              
               $RAML->setCurrentAction($verb_con);


     if ($RAML->action()->get('queryParameters')->isArray()):
                    
     $verb_content .= "<table class='uk-table uk-table-striped'><caption><h3 class='uk-heading-bullet'>Query Parameters</h3></caption>";
     $verb_content .= "<thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>";
     foreach ($RAML->action()->get('queryParameters')->toArray() as $param => $details) {
     
     $required = (isset($details['required']) && $details['required'] == 1 ? 'Yes' : '');
   
     if (isset($details['example'])) {
     	$example_final = "<span style='display: block; margin: 6px 0 8px 0; border-top: 1px solid #aaa; color: #666; padding: 4px; font-size: 10px;''><b>Example:</b>".$details['example'].'</span>';
     }else { $example_final = " ";}

     if (isset($details['type'])) { $type = $details['type']; } else { $type = " "; }
     if (isset($details['description'])) { $desc_details = $details['description']; } else { $desc_details = " "; }


     $verb_content.="<tr><td>".$param."</td><td>".$type."</td><td>".$required."</td><td>{{ needs a fix }}</span>".$example_final."</td></tr>";
  }
  $verb_content .= "</table>";

     endif;

     if ($RAML->action()->get('body')->get('application/x-www-form-urlencoded')->get('formParameters')->isArray()):
                    
     $verb_content .= "<table class='uk-table uk-table-striped'><caption>Form Parameters</caption>";
     $verb_content .= "<thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>";
     foreach ($RAML->action()->get('queryParameters')->toArray() as $param => $details) {

     	$required = (isset($details['required']) && $details['required'] == 1 ? 'Yes' : '');
   
     if (isset($details['example'])) {
     	$example_final = "<span style='display: block; margin: 6px 0 8px 0; border-top: 1px solid #aaa; color: #666; padding: 4px; font-size: 10px;''><b>Example:</b>".$details['example'].'</span>';
     }else { $example_final = " ";}

     if (isset($details['type'])) { $type = $details['type']; } else { $type = " "; }
     if (isset($details['description'])) { $desc_details = $details['description']; } else { $desc_details = " "; }


     $verb_content.="<tr><td>".$param."</td><td>".$type."</td><td>".$required."</td><td>{{ needs a fix }}</span>".$example_final."</td></tr>";


     }

     $verb_content .= "</table>";

     endif;

     if ($RAML->action()->get('body')->get('application/json')->get('schema')->get('properties')->isArray()):
                    
     $verb_content .= "<table class='uk-table uk-table-striped'><caption>Form Parameters</caption>";
     $verb_content .= "<thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>";

     $tmp = $RAML->action()->get('body')->get('application/json')->get('schema')->toArray();
	if ($RAML->action()->get('body')->get('application/json')->get('schema')->isString()) {
		$tmp = json_decode($RAML->action()->get('body')->get('application/json')->get('schema')->toString(), true);
	}

	foreach ($tmp['properties'] as $param => $details) {

     $required = (isset($details['required']) && $details['required'] == 1 ? 'Yes' : '');
   
     if (isset($details['example'])) {
     	$example_final = "<span style='display: block; margin: 6px 0 8px 0; border-top: 1px solid #aaa; color: #666; padding: 4px; font-size: 10px;''><b>Example:</b>".$details['example'].'</span>';
     }else { $example_final = " ";}

     if (isset($details['type'])) { $type = $details['type']; } else { $type = " "; }
     if (isset($details['description'])) { $desc_details = $details['description']; } else { $desc_details = " "; }


     $verb_content.="<tr><td>".$param."</td><td>".$type."</td><td>".$required."</td><td>{{ needs a fix }}</span>".$example_final."</td></tr>";

     }

     $verb_content .= "</table>";

    endif;
    //** Query parameters table ends here **///  
   
   //** Form parameters table starts here **///  
    if ($RAML->action()->get('body')->get('application/x-www-form-urlencoded')->get('formParameters')->isArray()):
                    
     $verb_content .= "<table class='uk-table uk-table-striped'><caption>Form Parameters</caption>";
     $verb_content .= "<thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>";
     foreach ($RAML->action()->get('queryParameters')->toArray() as $param => $details) {

     	$required = (isset($details['required']) && $details['required'] == 1 ? 'Yes' : '');
   
     if (isset($details['example'])) {
     	$example_final = "<span style='display: block; margin: 6px 0 8px 0; border-top: 1px solid #aaa; color: #666; padding: 4px; font-size: 10px;''><b>Example:</b>".$details['example'].'</span>';
     }else { $example_final = " ";}

     if (isset($details['type'])) { $type = $details['type']; } else { $type = " "; }
     if (isset($details['description'])) { $desc_details = $details['description']; } else { $desc_details = " "; }


     $verb_content.="<tr><td>".$param."</td><td>".$type."</td><td>".$required."</td><td>{{ needs a fix }}</span>".$example_final."</td></tr>";


     }

     $verb_content .= "</table>";

     endif;
     //** Form parameters table table ends here **/// 
    
    //** JSON Parameters table starts here **/// 
    if ($RAML->action()->get('body')->get('application/json')->get('schema')->get('properties')->isArray()):
                    
     $verb_content .= "<table class='uk-table uk-table-striped'><caption>JSON Parameters</caption>";
     $verb_content .= "<thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>";

     $tmp = $RAML->action()->get('body')->get('application/json')->get('schema')->toArray();
	if ($RAML->action()->get('body')->get('application/json')->get('schema')->isString()) {
		$tmp = json_decode($RAML->action()->get('body')->get('application/json')->get('schema')->toString(), true);
	}

	foreach ($tmp['properties'] as $param => $details) {

     $required = (isset($details['required']) && $details['required'] == 1 ? 'Yes' : '');
   
     if (isset($details['example'])) {
     	$example_final = "<span style='display: block; margin: 6px 0 8px 0; border-top: 1px solid #aaa; color: #666; padding: 4px; font-size: 10px;''><b>Example:</b>".$details['example'].'</span>';
     }else { $example_final = " ";}

     if (isset($details['type'])) { $type = $details['type']; } else { $type = " "; }
     if (isset($details['description'])) { $desc_details = $details['description']; } else { $desc_details = " "; }


     $verb_content.="<tr><td>".$param."</td><td>".$type."</td><td>".$required."</td><td>{{ needs a fix }}</span>".$example_final."</td></tr>";

     }

     $verb_content .= "</table>";

    endif;
    //** JSON Parameters table ends here **///
               
    //** Code Samples starts here **///
    $codeSamples = new RAML2HTML\codeSamples($RAML);
    $verb_content .= '<div class="uk-tile uk-tile-muted uk-padding-remove"><h3 class="uk-heading-bullet" style="margin-bottom:20px !important;">Code Samples</h4></div>';
       
    $js_code = str_replace("\n", "<br />", $codeSamples->javascript());
    $php_code = str_replace("\n", "<br />", $codeSamples->php());
    $rails_code = str_replace("\n", "<br />", $codeSamples->rails());
    

    $verb_content .= "<ul class='uk-subnav uk-subnav-pill' uk-switcher>";
    $verb_content .= "<li><a href='#''> PHP </a></li>";
    $verb_content .= "<li><a href='#''> JavaScript </a></li>";
    $verb_content .= "<li><a href='#''> Rails </a></li>";
    $verb_content .= "</ul>";


    $verb_content .= "<ul class='uk-switcher uk-margin'>";
    $verb_content .= "<li><pre><code class='php hljs'>".$php_code."</code></pre></li>";
    $verb_content .= "<li><pre><code class='javascript hljs'>".$js_code."</code></pre></li>";
    $verb_content .= "<li><pre><code class='php hljs'>".$rails_code."</code></pre></li>";
    $verb_content .= "</ul>";

    //** Code Samples starts here **///
    
    //** JSON Response starts here  **///
    $verb_content .= '<div class="uk-tile uk-tile-muted uk-padding-remove"><h3 class="uk-heading-bullet" style="margin-bottom:20px !important;">Response</h3></div>';  

   //** JSON Response ends here **///  
   foreach ($RAML->path()->getResponses() as $code => $responses) 
   {
      $verb_content .= $code;
      foreach ($responses as $response) {
         if (in_array($response['type'], array('example', 'schema'))) {
				continue;
			}

	   $verb_content .= ': <span class="responseText">' . (is_array($response['type']) ? array_shift($response['type']) : $response['type']) . '</span>';

	   if (isset($response['example'])):
       
       $verb_content .= "<pre><code class='json hljs'>".print_r($response['example'])."</code></pre>";

	   	endif;



      }
   }

    $verb_content .= "</li>";
     }

          

$verb_content .="</ul>";
$verb_content .= "<div style='width:100% !important; height:60px !important; display:inline-block !important;'></div>";
$content .= $verbs;
$content .= $verb_content;

echo $content;


}


?>