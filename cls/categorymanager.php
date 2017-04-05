<?php

require_once("/../config/config.php");

class categorymanager
{
    var $subtree = Array();
    function ShowNewCategory($ID)
    {
            echo('<form action="kategoria,'.$ID.',dodaj" method="post">
                    <label>[+] </label><input type="text" id="newcat" name="newcat"/>
                </form>');
    }
    /* Dodaje kategorie/podkategoriê */
    function AddCategory($NAME,$CATID)
    {
        if(is_null($CATID))
        {
            $result = database::SELECT('kategoria',Array('nazwa'),Array('nazwa' => $NAME));
        }  
        else
        {
            $result = database::SELECT('kategoria',Array('nazwa'),Array('nazwa' => $NAME, 'kategoria_id' => $CATID));
        }
        
        if(!$result && preg_match("/^([a-zA-Z0-9¹œæêó³ñ¿Ÿ¥ŒÆÊÓ£Ñ¯]{3,20}[ ]{0,1}){1,3}$/",$NAME))
        {
            if($CATID != NULL )
            {
                database::INSERT('kategoria',Array('kategoria_id' => $CATID, 'nazwa' => $NAME));
            }
            else
            {
                database::INSERT('kategoria',Array('nazwa' => $NAME));
            }            
        }
        var_dump($result); 
    }
    /* Wyœwietla znacznik */
    function ShowDeleteCategory($ID)
    {
        echo ('<a href="kategoria,'.$ID.',usun"> [-] </a>');
    }
    function DeleteCategory($ID)
    {
        $subcat = $this->GetAllSubCategory($ID);
        $prod_mng = new productmanager;
        if($subcat)
        {
            foreach($subcat as $c)
            {
                $prod = database::SELECT('produkt',Array('id,nazwa'),Array('kategoria_id 	' => $c));
                if($prod)
                {
                    foreach($prod as $p)
                    {
                        $prod_mng->RemoveProduct($p['id']);
                    }
                }
                database::DELETE('kategoria',Array('id' => $c));
            }
        }
    }
    function RenameCategory($ID,$NAME)
    {
        //TODO:... Ale jak...
    }
    function GetCategoryName($ID)
    {
        $result = database::SELECT('kategoria',Array('nazwa'),Array('id' => $ID));
        return $result[0]['nazwa'];
    }
    function GetCategoryID($ID)
    {
        return database::SELECT('kategoria',Array('id','kategoria_id','nazwa'),Array('id' => $ID));
    }    
    function GetCategoryNav($ID)
    {
        $loop = true;
        $search = $ID;
        $tree = Array();
        do
        {
            $result = $this->GetCategoryID($search);
            if(!$result)
                $loop = false;
            $tree[] = $result[0];
            $search =$result[0]['kategoria_id'];
        } while ($loop);
        
        return $tree;
    }
    private function GetSubTree($ID)
    {
        $result = database::SELECT('kategoria',Array('id','kategoria_id'),Array('kategoria_id' => $ID));
        if($result)
        {
            foreach($result as $row)
            {
                $this->subtree[] = $row['id'];
                $this->GetSubTree($row['id']);
            }           
        }
    }
    function GetAllSubCategory($ID)
    {
        $this->subtree[] = $ID;
        $this->GetSubTree($ID);
        return $this->subtree;
    }
}

?>
