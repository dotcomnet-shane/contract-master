<?php
namespace Dompdf;
require_once './contract/dompdf/autoload.inc.php';

ob_start();
include('./contract/test.php');
$content = ob_get_clean();

if(isset($_GET['submit_val']))
{
    $dompdf = new Dompdf();
    $dompdf-> file_get_contents($_GET["$htmlName"]);
;
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("",array("Attachment" => false));
    exit(0);
}
?>