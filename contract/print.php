<?php
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

if(!isset($_GET['document']) || !$_GET['document'] || !strlen($_GET['document']) == 40)
{
    die;
}

ob_start();
$content = ob_get_clean();

$dompdf = new Dompdf();

$res = $dompdf->getOptions();

$dompdf->loadHtmlFile('/home/ali/PhpstormProjects/contract-master/contract/dompdf/toPrint/signedContracts/toPrint/' . $_GET['document'] . '.html');

$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream();

exit(0);
?>