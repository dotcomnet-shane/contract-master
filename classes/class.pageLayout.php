<?php

class pageLayout
{
    public $js;
    public $css;
    public $jsPath;
    public $cssPath;
    public $fontsPath;
    public $customJs;

    public function __construct()
    {
        //Set path variables
        $this->jsPath = URL . 'assets/js/';
        $this->cssPath = URL . 'assets/css/';
        $this->fontsPath = URL . 'assets/fonts/';

        $this->setCSS();
        $this->setJS();
    }

    public function setJS()
    {
        $this->js = '
            <script src="assets/js/jquery-3.6.0.min.js"></script>
            <script src="https://kit.fontawesome.com/bfd9897846.js" crossorigin="anonymous"></script>
            <!-- https://github.com/brinley/jSignature/blob/master/README.md -->
            <script src="assets/js/jSignature.min.js"></script>
            <script src="assets/js/core.js"></script>
        ';
    }


    public function setCSS()
    {
        $this->css = '
            <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        ';
    }

    public function getHeader()
    {
        return '
            <!DOCTYPE html>
            <html>
                <head>
                    <title>' . PAGE_TITLE . '</title>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    ' . $this->css . '
                </head>
                <body>
        ';
    }

    public function getFooter()
    {
        return '
            ' . $this->js . '
        </body>
        </html>
        ';
    }
}