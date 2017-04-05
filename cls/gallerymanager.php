<?php

require_once("/../config/config.php");

class gallerymanager
{
    /* Sprawdza czy plik istnieje */
    static private function FileExist($NAME)
    {
        return file_exists($NAME);
    }
    /* Zmienia nazwę podanego pliku na inną */
    static private function ChangeName($FILE,$NEWNAME)
    {
        if(self::FileExist(SHOP_DIR.FOLDER_GALLERY.$FILE))
        {
            return rename(SHOP_DIR.FOLDER_GALLERY.$FILES,SHOP_DIR.FOLDER_GALLERY.$NEWNAME);
        }
        else
        {
            return false;
        }
    }
    /* Usuwa obrazk z danego katalogu */
    static private function DeleteImage($FOLDER,$FILE)
    {
        unlink($FOLDER.$FILE);
    }
    /* Sprawdza czy katalogi istnieją */
    static private function FolderExist()
    {
        // Poprawić warningi lol :/
        if(!file_exists(SHOP_DIR.FOLDER_TEMP));
            @mkdir(SHOP_DIR.FOLDER_TEMP);
        if(!file_exists(SHOP_DIR.FOLDER_THUMB));
            @mkdir(SHOP_DIR.FOLDER_THUMB);   
        if(!file_exists(SHOP_DIR.FOLDER_GALLERY ));
            @mkdir(SHOP_DIR.FOLDER_GALLERY );   
    }
    /* Zapisuje dowolny format jako JPG */
    private function SaveAsJPG($SOURCE,$DEST,$FILENAME)
    {
        $image_info = $image_info = getimagesize($SOURCE.$FILENAME);
        $image_type = $image_info[2]; 
        if($image_type == IMAGETYPE_JPEG)
        {
            $image = imagecreatefromjpeg($SOURCE.$FILENAME);
        }
        elseif($image_type == IMAGETYPE_PNG)
        {
            $image = imagecreatefrompng($SOURCE.$FILENAME);
        }
        else
        {
            $image = imagecreatefromwbmp($SOURCE.$FILENAME);            
        }
        
        $image_width = imagesx($image);
        $image_height = imagesy($image); 
        $new_image = imagecreatetruecolor($image_width , $image_height);
        imagecopyresampled($new_image,$image,0,0,0,0,$image_width,$image_height,$image_width,$image_height);
        imagejpeg($new_image,$DEST.$FILENAME,JPG_QTY);
    }
    /* Tworzy miniaturki */
    private function Resize($SOURCE,$DEST,$FILE,$WIDTH,$HEIGHT) 
    {
        $image_info = $image_info = getimagesize($SOURCE.$FILE);
        $image_type = $image_info[2];
        
        if($image_type == IMAGETYPE_JPEG)
        {
            $image = imagecreatefromjpeg($SOURCE.$FILE);
        }
        elseif($image_type == IMAGETYPE_PNG)
        {
            $image = imagecreatefrompng($SOURCE.$FILE);
        }
        elseif($image_type == IMAGETYPE_BMP)
        {
            $image = imagecreatefromwbmp($SOURCE.$FILE);            
        }
        
        $image_width= imagesx($image);
        $image_height= imagesy($image);
        
        $new_image = imagecreatetruecolor($WIDTH, $HEIGHT);
        imagecopyresampled($new_image,$image,0,0,0,0,$WIDTH,$HEIGHT,$image_width,$image_height);
        imagejpeg($new_image,$DEST.$FILE,JPG_QTY);
    }
    /* Ładuję listę obrazków na serwer i zapisuje do katalogów oraz bazy danych */   
    public function UploadImage($FILES,$ID)
    {
        $this->FolderExist();
        $error = false;
        $image_count = count($FILES['pliki']['name']);

        // Sprawdzenie typu plików
        foreach($FILES['pliki']['type'] as $type)
        {
            if(!($type == "image/jpeg" || $type == "image/pjpeg" || $type == "image/bmp" || $type == "image/png")) $error = true;
        }

        // Sprawdzenie rozmiaru plików
        foreach($FILES['pliki']['type'] as $size)
        {
            if(($size > MAX_IMAGE_SIZE)) $error = true;
        }

        if(!$error)
        {
            $i = 0;
            foreach($FILES['pliki']['tmp_name'] as $tmp)
            {
                $filename = $ID."_".$i.".jpg";
                $i++;
                move_uploaded_file($tmp,SHOP_DIR.FOLDER_TEMP.$filename);
                $this->SaveAsJPG(SHOP_DIR.FOLDER_TEMP,SHOP_DIR.FOLDER_GALLERY,$filename);
                $this->Resize(SHOP_DIR.FOLDER_TEMP,SHOP_DIR.FOLDER_THUMB,$filename,100,100);
                $this->DeleteImage(SHOP_DIR.FOLDER_TEMP,$filename);
                
                // Sprawdzić czy istnieje plik w bazie, jeśli nie to dodajemy
                $result = database::SELECT('galeria',Array('plik'),Array('plik' => $filename));
                if(!$result) database::INSERT('galeria',Array('produkt_id' => $ID, 'plik' => $filename));
            }
        }  
        return !$error;            
    }
    /* Usuwa obrazek z bazy wraz z plikami */
    public function RemoveImage($ID)
    {
        // Usuwanie plików z dysku
        $result = database::SELECT('galeria',Array('plik'),Array('produkt_id' => $ID));
        if($result)
        {
            foreach($result as $file)
            {
                unlink(SHOP_DIR.FOLDER_GALLERY.$file['plik']);
                unlink(SHOP_DIR.FOLDER_THUMB.$file['plik']);
            }
        }
        
        // Usuwanie plików z bazy danych
        return database::DELETE('galeria',Array('produkt_id' => $ID));
    }
    /* Zwraca nazwe obrazka dla danego ID */
    public function GetImages($ID)
    {
        return database::SELECT('galeria',Array('plik'),Array('produkt_id' => $ID));
    }
    /* Zwraca miniaturkę */
    public function GetProductThumb($ID)
    {
        $result = $this->GetImages($ID);
        if($result)
        {
            return $result[0]['plik'];
        }
        else
        {
            return "noimage.jpg";   // wstawic adres obrazka "brak obrazka" 
        }
    }
}
?>
