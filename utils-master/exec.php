<?php // exec.php
$cmd = "wkhtmltopdf /var/www/html/contract/Steve.html /var/www/html/contract/Steve.pdf";   // Windows
// $cmd = "ls"; // Linux, Unix & Mac

exec(escapeshellcmd($cmd), $output, $status);

if ($status) echo "Exec command failed";
else
{
    echo "<pre>";
    foreach($output as $line) echo "$line\n";
}
?>