<?php
require_once("config/config.php");

$signatureClient    = null;
$signatureDev       = 'data:image/png;base64, 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';
$signatureDev       = '<img id="dev_signature" src="' . $signatureDev . '" >';
$signatoryName      = 'Trevor Belomont';

/** Functionality to self delete as required for test purposes */
$selfDelete = 0;

$path       = 'libs/dompdf/toPrint/signedContracts/toPrint/';
$fileName   = sha1($_SESSION['timeStart']);
$htmlName   = $fileName . '.html';
$pdfName    = $fileName . '.pdf';

$footerContent = null;

$current_document = $_GET['doc'];

function setHtmlContent()
{
    $current_document = $_GET['doc'];
    $doc_path = '/_templates/' . $current_document . '.html';
    return file_get_contents(ROOT_PATH . $doc_path);
}


/** Check if client has signed */
//Assign default value to signature

//Set signature variable, from $_POST, if client signed
if(isset($_POST['client_signature']))
{
    $signatureClient = $_POST['client_signature'];

    //Make sure the signature is a base64 encoded png and there is a minimum signature size
    if(substr($signatureClient, 0, 22) === 'data:image/png;base64,' && strlen($signatureClient) > 200)
    {
        $signatureClient = '<img id="hk" src="' . htmlspecialchars($signatureClient) . '" >';
    }
}

$elements = new elements();
$pageLayout = new pageLayout();

$params = array(
    'title' => $current_document,
    'icon'  => 'fas fa-times-circle',
    'color' => '#ed1c24',
);

//fas fa-check-circle

if($signatureClient)
{
    $params['icon'] = 'fas fa-check-circle';
    $params['color'] = '#2bb673';

    $_SESSION['timeEnd'] = microtime(true);
    $browserName = functions::getBrowser();
    $signedOn = date('j F Y g:i a');
    $clientIP = functions::get_client_ip_env();

    $footerContent .= '<div>';
        $footerContent .= $signatureDev;
        $footerContent .= $signatureClient;
    $footerContent .= '</div>';

    $footerContent .= '
            <div class="date-ip" id="date-ip">
                <strong>Signed on:</strong> ' . $signedOn .' <br>
                <strong>IP address:</strong> ' . $clientIP .'<br>
                <strong>Browser:</strong> ' . $browserName .'<br>
                <strong>PC Name:</strong> ' . gethostname() .'<br>
                <strong>Operating System:</strong> ' . PHP_OS .'<br>
                <strong>Time Spent:</strong> '. substr((($_SESSION["timeEnd"] - $_SESSION["timeStart"]) / 60) ,0,4) . ' Minutes<br>
            </div>
            
            <div class="noprint" id="print-pdf">
                <button id="print" type="button" class="btn-primary" onclick="printContract()">
                    Print contract
                </button>
                <button id="pdf" type="button" class="btn-primary" onclick="generatePdf()">
                    Download as PDF
                </button>
            </div>
      </div> <!-- #doc_container -->
        
    </div> <!--#content-->
    <script>
        function printContract() {
          window.print();
        }
        function generatePdf() {
            window.location.href = "print.php?document=' . $fileName . '";
        }
    </script>
    ';
}
else
{
    $footerContent = '
        <form method="post" class="noprint" id="signature_form">
            <div id="signature">
              <!-- Client Signature Canvas -->
            </div>
            <div id="signatory">' . $signatoryName . '</div>
                
            <div class="buttons">
              <button class="btn-primary" id="reset" type="button">Reset</button>
              <button class="btn-primary" id="submit" type="submit">Done <i class="fas fa-arrow-right"></i></button>
              <p> '.date('d-m-Y h:m').'</p>
            </div>
                
            <input type="hidden" id="client_signature" name="client_signature" />
        </form>
      </div> <!-- #doc_container -->
            
    </div> <!-- #content -->
    ';
}

$html  = $pageLayout->getHeader();
$html .= $elements->navBar($params);
$html .= $elements->accountPanel();
$html .= '<div class="page-wrapper" id="content">';
$html .= setHtmlContent();
$html .= $footerContent;
$html .= $pageLayout->getFooter();

echo $html;

function saveHtml()
{
    global $path, $htmlName, $html_content, $signatureClient, $signatureDev;

    $pageLayout = new pageLayout();

    $html = '<div class="page-wrapper" id="content">';
    $htmlFileLocation = $path . $htmlName;
    file_put_contents($htmlFileLocation, $html);

    return $htmlFileLocation;
}

if($signatureClient == null)
{
    if($selfDelete && file_exists($htmlName))
    {
        header('Location: ' . $htmlName . '#hk');
        die();
    }
}
else
{
    /** Contract was just signed: put $signatureClient and the other parts in the .html file **/
    $htmlFileLocation = saveHTML();

    functions::emailMembers();

    if($selfDelete)
    {
        unlink(__FILE__);
    }

    /**
     * TODO:
     * Fix: Cannot modify header information - headers already sent by
     */
    session_destroy();
    //header('location: ' . $htmlFileLocation . '#hk');

    die();
}