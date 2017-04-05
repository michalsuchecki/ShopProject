<?php

echo('<p class="sidemenu_label_top"> KATEGORIE </p>');

$db = new database;
$cat_mng = new categorymanager;

if(isset($_GET['catid']))
{
    $category = $_GET['catid'];
    $rescat = database::SELECT("kategoria",Array("id","kategoria_id","nazwa"),Array('id' => $category));
}
else
{
    $rescat = database::SELECT("kategoria",Array("id","kategoria_id","nazwa"),Array('kategoria_id' => NULL));
}

echo('<ul class="sidemenu_category">');
if($rescat)
{
    $is_admin = usermanager::IsAdmin();
    foreach($rescat as $row)
    {
        $subid = $row['id'];
        echo ('<li>'); 
        if($is_admin) $cat_mng->ShowDeleteCategory($row['id']);
        echo ('<a href="'.PAGE_URL.'kategoria,'.$row['id'].',1,2,1">'.$row['nazwa'].'</a></li>');
        echo('<ul class="sidemenu_subcategory">');
        $subcat = database::SELECT("kategoria",Array("id","kategoria_id","nazwa"),Array('kategoria_id' => $row['id']));
        if($subcat)
        {
            foreach($subcat AS $srow)
            {
                echo ('<li>'); 
                if($is_admin) $cat_mng->ShowDeleteCategory($srow['id']);
                echo ('<a href="'.PAGE_URL.'kategoria,'.$srow['id'].',1,2,1">'.$srow['nazwa'].'</a></li>');
            }
        }
        echo('</ul>');
    }
    if($is_admin)
    {
        echo ('<li>');  
        if(isset($_GET['catid']))
        {
            if($is_admin) $cat_mng->ShowNewCategory($_GET['catid']);
        }
        else
        {
            if($is_admin) $cat_mng->ShowNewCategory(NULL);
        } 
        echo ('</li>');   
    }
    
}

echo('</ul>');

?>