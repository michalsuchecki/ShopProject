<?php

require_once("mod/mod_header.php");
echo ('<div id="main_concent" style="display:inline-block">');

if(isset($_GET['subpage']))
{
	switch($_GET['subpage'])
    {
        case 1: // Regulamin
            echo("Bardzo ale to bardzo obszerny regulamin... :D");
            break;
        case 2: // Dostawy
            echo("Informacje o dostawyach");
            break;
        case 3: // Kontakt
            echo("Kontakt do sklepu");
            break;
        default:
            header("Location: ".PAGE_URL);
            break;        
    }
}
else
{
	header("Location: ".PAGE_URL);
}

echo ('</div>');
require_once("mod/mod_footer.php");

?>