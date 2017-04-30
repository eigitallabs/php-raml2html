<?php
/**
 * RAML2HTML for PHP -- A Simple API Docs Script for RAML & PHP
 * @version 1.3beta
 * @author Mike Stowe <me@mikestowe.com>
 * @link https://github.com/mikestowe/php-raml2html
 * @link http://www.mikestowe.com/2014/05/raml-2-html.php
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */
use phpFastCache\CacheManager;

require __DIR__ . '/vendor/autoload.php';

$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$InstanceCache = CacheManager::getInstance('files');
$CachedString = $InstanceCache->getItem('HTML' . md5($url));
if (is_null($CachedString->get())) {
    require_once('inc/spyc.php');
    require_once('inc/ramlDataObject.php');
    require_once('inc/raml.php');
    require_once('inc/ramlPathObject.php');
    require_once('inc/markdown.php');
    require_once('inc/codeSamples.php');
    require_once('config.php');


// Dangling Function
    function formatResponse($text)
    {
        return str_replace([" ", "\n"], ["&nbsp;", "<br />"], htmlspecialchars($text, ENT_QUOTES));
    }

// Handle Caching and Build
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

    if (isset($_GET['path'])) {
        $RAML->setCurrentPath($_GET['path']);
        unset($_GET['path']);
    }

    if (isset($_GET['action']) && $RAML->isActionValid($_GET['action'])) {
        $RAML->setCurrentAction($_GET['action']);
        unset($_GET['action']);
    }
    ob_start();
    require_once($docsTheme);
    $html = ob_get_contents();
    ob_end_clean();
    $CachedString->set($html)->expiresAfter($cacheTimeLimit);
    $InstanceCache->save($CachedString);
    echo $CachedString->get();
} else {
    echo $CachedString->get();
}


?>
