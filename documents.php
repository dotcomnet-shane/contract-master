<?php
require_once("config/config.php");

$elements = new elements();
$pageLayout = new pageLayout();

$params = array(
    'title' => 'My Documents',
    'icon'  => 'fas fa-file-alt',
    'color' => '#5f6368',
);

function getDocuments()
{
    $signed = true;
    $html = '<div class="documents">';

    if($signed) {
        $signedIcon = '
            <div class="signed" title="Signed On: 23/08/2021">
                <i class="fas fa-check-circle"></i>
            </div>
        ';
    }
    else
    {
        $signedIcon = '
            <div class="signed" title="Please Sign the Document">
                <i style="color: #ed1c24" class="fas fa-times-circle"></i>
            </div>
        ';
    }

    // Get the files from the _template directory
    $documents = functions::dirToArray('_templates');

    foreach ($documents as $document)
    {
        // Fix the titles and strip the extension
        $document_title = preg_replace('/\\.[^.\\s]{3,4}$/', '', $document);

        $html .= '
            <a href="documentSigner.php?doc='.$document_title.'" style="text-decoration: none; color: #232323">
                <div class="doc">
                    <figure style="position: relative; margin: 0; ">
                        '.$signedIcon.'
                        <img style="max-height: 150px; max-width: 220px; text-align: center; border-top-left-radius: 4px; border-top-right-radius: 4px;" alt="pdf" src="https://www.propertycoza.com/images/pdf.png">
                    </figure>
                    <div style="height: 75px; background-color: #f2f2f2 !important;">
                        <div class="document_title">
                            <h3 class="doc_name">'.$document_title.'</h3>
                        </div>
                    </div>
                </div>
            </a>
        ';
    }

    $html .= '</div>';

    return $html;
}

$html  = $pageLayout->getHeader();
$html .= $elements->navBar($params);
$html .= $elements->accountPanel();
$html .= '<div class="page-wrapper" id="content" style="background-color: snow !important; overflow: hidden; overflow-y: scroll">';
$html .= getDocuments();
$html .= $pageLayout->getFooter();

echo $html;