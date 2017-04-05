<?php

require_once("mod/mod_header.php");

echo "<div id=\"main_sidemenu\" style=\"display:inline-block\">";
	include("mod/mod_catalog.php");
echo "</div>";

echo "<div id=\"main_concent\" style=\"display:inline-block\">";
	include("mod/mod_main.php");
echo "</div>";

require_once("mod/mod_footer.php");

?>