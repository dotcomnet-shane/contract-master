<?php

require_once("config/config.php");
require_once 'libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

if(!isset($_GET['document']) || !$_GET['document'] || !strlen($_GET['document']) == 40)
{
    die;
}

ob_start();
$content = ob_get_clean();

$dompdf = new Dompdf();

$res = $dompdf->getOptions();

try
{
    $dompdf->loadHtmlFile(ROOT_PATH . '\libs\dompdf\toPrint\signedContracts\toPrint\\' . $_GET['document'] . '.html');
}
catch (\Dompdf\Exception $e)
{
    var_dump($e);
}

$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream();

exit(0);