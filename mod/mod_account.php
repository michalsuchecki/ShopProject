<?php

if(usermanager::IsAdmin())
{
    echo('<p class="sidemenu_label_top">PANEL ADMINISTRATORA</p>');
    $menu = Array( 1 => 'Nowe zamówienia', 2 => 'W trakcje realizacji', 3 => 'Oczekujące na dostawę', 4 => 'Wysłane zamówienia');
}
else
{
    echo('<p class="sidemenu_label_top">PANEL UŻYTKOWNIKA</p>');
    $menu = Array( 1 => 'Zmiana danych', 2=> 'Zmiana hasła', 3 => 'Aktualne Zamówienia', 4=> 'Archiwum zamówień');
}

echo('<ul class="sidemenu_category">');
foreach($menu as $key => $val)
{
    echo('<li class="ul_panel"><a href="'.PAGE_URL.'konto,'.$key.'">'.$val.'</a></li>');
}
echo('</ul>');

?>