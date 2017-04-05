<?php

require_once("mod/mod_header.php");

echo "<div id=\"main_sidemenu\" style=\"display:inline-block\">";
	include("mod/mod_catalog.php");
echo "</div>";

echo "<div id=\"product_contener\" style=\"display:inline-block\">";

if(isset($_GET['action']))
{ 
    if($_GET['action'] == "comment")
    {
        $comm_mng = new commentmanager;
        $prod_id = isset($_GET['prodid']) ? $_GET['prodid'] : false;
        $title = isset($_POST['title']) ? $_POST['title'] : "";
        $text  = isset($_POST['comment']) ? $_POST['comment'] : "";
        $rate  = isset($_POST['rating']) ? $_POST['rating'] : 1;
        
        $id = (isset($_SESSION['clientid'])) ? $_SESSION['clientid'] : NULL;
        
        if($id)
        {
            $result = $comm_mng->AddCommment($prod_id,$id,$title,$text,$rate); 
        }
        else
        {
            // przekierowanie
        }
    }
    else
    {
        if(usermanager::IsAdmin())
        {
            $catmng = new categorymanager;
            $category = (isset($_GET['catid'])) ? $_GET['catid'] : NULL;
            $category_name = $catmng->GetCategoryName($category);
            $prod_mng = new productmanager; 
            switch($_GET['action'])
            {
            case "add": // Formularz
                echo('
                <div id="product_div">
                    <div class="div_label">Dodawanie nowego produktu</div>
                    <form method="post" action="produkt,dodaj,'.$category.'" enctype="multipart/form-data">
                    <div id="product_catdiv"><h1 id="product_catlabel">Opis Ogólny</h1></div>
                    <div class="product_row"><label class="label_product">Nazwa produktu</label><input id="prod_input" name="nazwa" type="text" maxlength="100"/></div>
                    <div class="product_row"><label class="label_product">Kod towaru</label><input id="prod_input" name="kod" type="text" maxlength="10"/></div>
                    <div class="product_row"><label class="label_product">Kategoria</label><input id="prod_input" name="kod" type="text" disabled="true" value="'.$category_name.'"/></div>
                    <div class="product_row"><label class="label_product">Krótki opis</label><textarea id="prod_text" name="krotki_opis" maxlength="255"></textarea></div>
                    <div class="product_row"><label class="label_product">Opis</label><textarea id="prod_text" name="opis" maxlength="1000"></textarea></div>
                    <div id="product_catdiv"><h1 id="product_catlabel">Cena</h1></div>
                    <div class="product_row"><label class="label_product">Cena brutto</label><input id="prod_input" name="cena" type="text" /></div>
                    <div id="product_catdiv"><h1 id="product_catlabel">Galeria</h1></div>
                    <div class="product_row"><label class="label_product">Pliki:</label><input id="prod_input" name="pliki[]" type="file" multiple="multiple" /></div>
                    <input type="hidden" name="referer" value="'.$_SERVER['HTTP_REFERER'].'"> 
                    <div class="product_row"> <input type="submit" name="submit" value="Dodaj produkt"/></div>
                    </form>
                </div>
                ');    
                break;
            case "addproduct":
                $prod_mng->AddProduct($_POST,$category,$_FILES);
                if(isset($_POST['referer']))
                {
                    header("Location: ".$_POST['referer']); // Zmienić na PAGE_URL.$_POST['referer'] ?
                }
                else
                {
                    header("Location: ".PAGE_URL);
                }
                break;
            case "delete":
                $prod_mng->RemoveProduct($_GET['prodid']);
                header("Location: ".PAGE_URL);
                break;
            case "change":
                $prod_id = $_GET['prodid'];
                $prod_mng->ModifyProduct($prod_id,$_POST);
                header("Location: ".PAGE_URL.'produkt,'.$category.','.$prod_id);
                break;
            default:
                header("Location: ".PAGE_URL);
                break;
            }
        }
        else
        {
            echo "Nie masz uprawnień!";
        }
    }
    
}
else
{
    $gallery_mng = new gallerymanager;
    $id = (isset($_GET['prodid'])) ? $_GET['prodid'] : false;
    if($id)
    {
        update_lastviewed($id);
        $product = database::SELECT('produkt',Array('*'),Array('id' => $id));
        $product = $product[0];
        $gallery = $gallery_mng->GetImages($id); // foreach($gallery as $image) $image['plik']
        $front = $gallery_mng->GetProductThumb($id);
        $oldprice = $product['poprzednia_cena'];
        $oldprice = sprintf("%.2f",$oldprice);
        $price = $product['cena'];
        $price = sprintf("%.2f",$price);

        if(usermanager::IsAdmin())
        {
            echo('<form method="post" action="'.PAGE_URL.'produkt,zmien,'.$product['id'].'">');
            echo('<div class="label_header"><input name="title" class="input_title" type="text" size="50" value="'.$product['nazwa'].'"/>');
            echo('<div class="admin_panel"><input type="submit" name="submit" value="Modyfikuj" /></div>');
        }
        else
        {
            echo('<div class="label_header"><div class="label2">'.$product['nazwa'].'</div>');
        }
        echo('</div>');
        
        if(usermanager::IsAdmin())
        {
            echo('
                <div class="product_upperdiv">
                    <div class="image_container"><img src="'.PAGE_URL.FOLDER_GALLERY.$front.'" alt="" /></div>
                    <div class="admin_panel">
                        <div><label>Aktualna cena: </label><input name="stara_cena" class="input_price" type="text" size="20" value="'.$price.'" disabled/></div>
                        <div><label>Nowa cena: </label><input name="cena" class="input_price" type="text" size="20" value=""/></div>
                        <div><label>Promocja: </label><input type="checkbox" name="promocja" value="1"/></div> 
                    </div>                     
                </div> ');           
        }
        else
        {
            echo('
                <div class="product_upperdiv">
                    <div class="image_container"><img src="'.PAGE_URL.FOLDER_GALLERY.$front.'" alt="" /></div>
                    
                    <div class="productPriceBox"> 
                    <div class="productOldPrice">');
            if($oldprice > 0)
            {
                       echo($oldprice.' zł');
            }//http://localhost/sklep/koszyk,dodaj,100011
            echo('</div>
                        <div class="productPrice">'.$price.' zł</div>
                                            <div class="ButtonNext cart_font"><a href="'.PAGE_URL.'koszyk,dodaj,'.$id.'">Do koszyka</a></div>
                    </div>   
                </div>        
            ');
        }
        
        echo('<div class="productGallery">');
        foreach($gallery as $image)
        {
            echo("<img src=\"".PAGE_URL.FOLDER_THUMB.$image['plik']."\" onclick=\"set_image('".PAGE_URL.FOLDER_GALLERY.$image['plik']."')\"/>");
        }
        echo('</div>');
        
        // ================ OPIS ================
        
        echo('<div><div class="label">Opis</div>');
        
        if(usermanager::IsAdmin())
        {      
            echo('<textarea name="opis" cols="70" rows="20" maxlength="1000">'.$product['opis'].'</textarea>');
        }
        else
        {
            echo('<div class="product_desc">'.$product['opis'].'</div>');
        }
        echo('</div>');
        
        if(usermanager::IsAdmin())
        {
            echo('<input type="hidden" name="referer" value="'.$_SERVER['HTTP_REFERER'].'"></form>');
        }
        
        // ================ KOMENTARZE ================
        
        echo('<div><div class="label">Komentarze</div>');
        
        $comm_mng = new commentmanager;
        $comments = $comm_mng->GetComments($id);
        $no_comment = false;
        
        if($comments)
        {
            foreach($comments as $c)
            {
                $user = database::SELECT('uzytkownicy',Array('id','imie'),Array('id' => $c['uzytkownicy_id']));
                $user = $user[0];
                if(isset($_SESSION['clientid']))
                {
                    if($user['id'] == $_SESSION['clientid']) $no_comment = true;
                }
                
                $tytul = $c['tytul'];
                echo('
                    <div class="product_comment">
                        <div class="comment_whodiv">
                            <dl>
                                <dt> <strong>Kto: </strong>'.$user['imie'].'</dt>
                                <dt> <strong>Data: </strong>'.$c['czas'].'</dt>
                                <dt> <strong>Ocena: </strong>'.$c['ocena'].'/5</dt>
                            </dl>
                        </div>  
                        <div class="comment_main">
                            <div class="comment_title">'.$c['tytul'].'</div>
                            <div class="comment_text">'.$c['tresc'].'</div> 
                        </div>    
                   </div>             
                ');
            }
        }
        
        $is_logged = usermanager::IsLogged();
        
        if($is_logged && !$no_comment)
        {
            echo('
                <div class="product_newcomment">
                    <form action="'.PAGE_URL.'komentuj,'.$product['id'].'" method="post">
                        <div class="row"><label>Tytuł</label><input name="title" type="text" size="50"/></div>
                        <div class="row"><label>Ocena 1</label><input type="radio" name="rating" value="1" checked><input type="radio" name="rating" value="2"><input type="radio" name="rating" value="3"><input type="radio" name="rating" value="4"><input type="radio" name="rating" value="5"><label> 5</label></div>
                        <div class="row"><label>Opis</label><textarea name="comment" cols="60" rows="10"></textarea></div>
                        <div class="row"><input type="submit" name="submit" value="Dodaj komentarz" /></div>     
                    </form>
                </div>               
            ');
        }
        else
        {
            if(!$is_logged)
            {
                echo "Musisz się zalgować by móc dodać komentarz";
            }
            elseif ($no_comment)
            {
                echo "Już komentowałeś !";
            }
            
        }
            
        echo('</div>');
    }
    else
    {
        //redirect
    }
}
    
echo "</div>";
require_once("mod/mod_footer.php");
?>