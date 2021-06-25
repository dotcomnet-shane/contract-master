<?php
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

ob_start();
$content = ob_get_clean();

$dompdf = new Dompdf();

$res = $dompdf->getOptions();

$dompdf->loadHtmlFile('/home/ali/PhpstormProjects/contract-master/contract/dompdf/toPrint/signedContracts/0f404e2e6efece32ea3ec20680efcc017a302ddf.html');

$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream();

exit(0);
?>