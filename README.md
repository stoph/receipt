Receipt
=====================

Get confirmation when a file is viewed.

    RewriteRule ^(.*)$ index.php?file=$1 [L,QSA]
