<?php

require_once("mod/mod_header.php");

echo '<div id="main_sidemenu" style="display:inline-block">';
	include("mod/mod_catalog.php");
echo '</div>';
echo '<div id="category_list" style="display:inline-block">';
echo '<div id="category_menu" style="display:inline-block">';
echo '<div id="category_nav" style="display:inline-block">';

$category_id = (isset($_GET['catid'])) ? $_GET['catid'] : NULL;

$cat_mng = new categorymanager;

if(isset($_GET['action']) && usermanager::IsAdmin())
{
    $action = $_GET['action'];
    $name = (isset($_POST['newcat'])) ? $_POST['newcat'] : NULL;
    switch($action)
    {
    case "dodaj":
        $cat_mng->AddCategory($name,$category_id);
        break;
    case "usun":
        $cat_mng->DeleteCategory($category_id);
        break;
    case "edytuj":
        break;
    default:
    }
    if(isset($_SERVER['HTTP_REFERER']))
    {
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }
    else
    {
        header("Location: ".PAGE_URL);
    }
}
else
{
    $tree = $cat_mng->GetCategoryNav($category_id);
    $navig = '<a href="start">Główna</a> > ';
    for ($i = count($tree)-2; $i>=0; --$i)
    {
        $navig.= '<a href="kategoria,'.$tree[$i]['id'].',1,2,1">'.$tree[$i]['nazwa']."</a> > ";
    }
    $navig = substr($navig,0,strlen($navig)-(strlen($navig)+3));
    echo $navig;
    
    echo '</div>';
    
    if(usermanager::IsAdmin())
    {
        echo '<div id="category_admin" style="display:inline-block"><a href="produkt,nowy,'.$category_id.'">[+] Dodaj produkt</a></div>';
    }
    
    echo "</div>";
    // -----------------------------------------------------------------------------------------------
    // Menu filtrowania
    
    $current_page = (isset($_GET['page'])) ? $_GET['page'] : 1; // Aktualna strona, jeśli nie podaliśmy to ustawiamy pierwszą
    if($current_page <1 ) $current_page = 1;

    $filter_show = (isset($_GET['show'])) ? $_GET['show'] : 2;  // Ile wyświetlić na stronie
    if(isset($_POST['filter_count'])) $filter_show = $_POST['filter_count'];    // Nadpisać jeśli filtrowaliśmy 
    
    $filter_orderby = (isset($_GET['order'])) ? $_GET['order'] : 1;  // Ile wyświetlić na stronie
    if(isset($_POST['filter_sortby'])) $filter_orderby = $_POST['filter_sortby'];   // Sortowanie po... jeśli nie podaliśmy używamy pierwszej opcji
    $filter_orderby = (int) $filter_orderby;
    if($filter_orderby == 0 ) $filter_orderby = 1;
    if($filter_orderby > 4) $filter_orderby = 1;
    
    $filter_min = (isset($_POST['filter_min'])) ? (float)$_POST['filter_min'] : NULL;   // Minimalna kwota
    $filter_max = (isset($_POST['filter_max'])) ? (float)$_POST['filter_max'] : NULL;   // Maksymalna kwota
    
    // Sprawdzenie wartości minimalnej i max
    if(!is_null($filter_max) && !is_null($filter_min))
    {
        if( $filter_min == 0 && $filter_max == 0)
        {
            $filter_min = NULL;
            $filter_max = NULL;
        }
        else
        {
            if($filter_min != 0 && $filter_max !=0)
            {
                if($filter_min > $filter_max)
                {
                    $change =  $filter_max;
                    $filter_max = $filter_min;
                    $filter_min = $change;   
                }
            }
            $filter_min = sprintf("%.2f",$filter_min);
            $filter_max = sprintf("%.2f",$filter_max);        
        }
    }
    
    echo ('<div class="filtering">
    <form action="'.PAGE_URL.'kategoria,'.$_GET['catid'].',1,'.$filter_show.','.$filter_orderby.'" method="post">
        <label class="filtering_input">Wyświetl: </label>
        <select name="filter_count" class="filtering_select">');
        
        foreach($show_count as $key => $val)
        {
            if($filter_show == $key)
            {
                echo('<option value="'.$key.'" selected>'.$val.'</options>');
            }
            else
            {
                echo('<option value="'.$key.'">'.$val.'</options>');            
            }
        }
    
        echo('
        </select>
        <label class="filtering_input">Cena od: </label>
        <input type="text" size="8" name="filter_min" value="'.$filter_min.'"/>
        <label class="filtering_input">Cena do: </label>
        <input type="text" size="8" name="filter_max" value="'.$filter_max.'"/>
        <label class="filtering_input">Sortowanie:</label>
        <select name="filter_sortby" class="filtering_select">');
    
        $sort_names = Array('nazwa rosnąco', 'nazwa malejąco', 'cena rosnąco', 'cena malejąco');
        
        for($i = 0; $i < count($sort_names) ; $i++)
        {
            $index = ($i+1);
            if($filter_orderby == $index)
            {
                echo('<option value="'.$index.'" selected>'.$sort_names[$i].'</options>');
            }
            else
            {
                echo('<option value="'.$index.'">'.$sort_names[$i].'</options>');
            }
        }
    
    echo('
        </select>    
        <input class="filtering_input" type="submit" value="Filtruj"/>
    </form>
    </div>
    ');
    
    // -----------------------------------------------------------------------------------------------
    // Lista produktów  w danej kategorii
    
    $cat = $cat_mng->GetAllSubCategory($category_id);
    $products = Array();
    
    foreach($cat as $cat_id)
    {
        switch($filter_orderby)
        {
            case 1:
                $result = database::SELECTFROMTO('produkt',Array('*'),Array('kategoria_id' => $cat_id),$filter_min,$filter_max,'cena','nazwa','ASC');
                break;
            case 2:
                $result = database::SELECTFROMTO('produkt',Array('*'),Array('kategoria_id' => $cat_id),$filter_min,$filter_max,'cena','nazwa','DESC');
                break;
            case 3:
                $result = database::SELECTFROMTO('produkt',Array('*'),Array('kategoria_id' => $cat_id),$filter_min,$filter_max,'cena','cena','ASC');
                break;
            case 4:
                $result = database::SELECTFROMTO('produkt',Array('*'),Array('kategoria_id' => $cat_id),$filter_min,$filter_max,'cena','cena','DESC');
                break;
        }
    
        if($result)
        {
            foreach($result as $p)
            {
                $products[] = $p;
            }
        }
    }
    
    $all_pages = count($products)/$filter_show;
    $all_pages = (int)round($all_pages);
    $product_count = count($products);
    
    // -----------------------------------------------------------------------------------------------
    // Strona: Poprzednia 1/15 Następna
    
    echo('<div class="page_number">');
    if($current_page > 1)
    {
        $nextpage=$current_page-1;
        echo('<a href="'.PAGE_URL.'kategoria,'.$_GET['catid'].','.$nextpage.','.$filter_show.','.$filter_orderby.'">Poprzednia </a>');
    }
    else
    {
        echo('Poprzednia '); // Nieaktywne
    }
    
    echo('<strong>'.$current_page.' / '.$all_pages.'</strong>');
    
    if($current_page < $all_pages)
    {
        $nextpage=$current_page+1;
        echo(' <a href="'.PAGE_URL.'kategoria,'.$_GET['catid'].','.$nextpage.','.$filter_show.','.$filter_orderby.'">Następna</a>');
    }
    else
    {
        echo(' Następna'); // Nieaktywne
    }
    
    $from = ($current_page-1)*$filter_show;
    $to = $from+$filter_show;
    if($to >= $product_count)
    {
        $to = $product_count;
    }
    
    echo(' ( '.($from+1).' - '.($to).' z '.$product_count.' )');
    echo ('</div>');
    
    // -----------------------------------------------------------------------------------------------
    $prod_mng = new productmanager;
    for($i = $from; $i < $to; $i++)
    {
        $prod_mng->ShowCatalogProduct($products[$i]);    
    }
}

require_once("mod/mod_footer.php");

?>