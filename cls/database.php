<?php

require_once("/../config/config.php");

class database
{
    static protected function getConnection()
    {
        $connection = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
        if(mysqli_connect_errno())
        {
            exit();
        }
        else
        {
            $connection->query("SET NAMES 'utf8'");
            return $connection;
        };        
    }
    static public function SELECT($TABLE, $COLUMNS = array("*"), $WHERE = array(), $LOGIC_OP = "=", $OPERATOR = "AND", $ORDERYBY ="", $ORDERTYPE = "ASC", $LIMIT = 0)
    {
        $connection = self::getConnection();
        $SQL = "SELECT ";
        $NULL_VAL = false;
        
        foreach($COLUMNS as $column)
        {
            $SQL .= $column.",";
        }
        $SQL = rtrim($SQL,',');
        
        $SQL.= " FROM {$TABLE}";
        
        if(count($WHERE)>0)
        {
            $SQL.= " WHERE ";
            //echo(count($WHERE)."<br/>");
            foreach($WHERE as $key => $val)
            {
                //echo("KEY ".$key." VAL ".$val);
                if($val == NULL)
                {
                    $SQL .= $key." IS NULL";
                    $NULL_VAL = true;
                }
                else
                {
                    $SQL .= $key.$LOGIC_OP."'".$val."' ".$OPERATOR." ";
                }
            }
            if($NULL_VAL == false)
            {
                $SQL = substr($SQL,0,strlen($SQL)-(strlen($OPERATOR)+2));
            }
        }
        
        if(strlen($ORDERYBY)>0)
        {
            $SQL.= " ORDER BY ".$ORDERYBY;
            $SQL.= " ".$ORDERTYPE;
        }
        
        if($LIMIT >0)
        {
            $SQL.= " LIMIT ".$LIMIT;
        }
        
        //echo($SQL."<br/>");
        $result = $connection->query($SQL);
        
        if(!$result)
        {
            return false;  
        }
        else
        {
            $resultarray = Array();
            while(($row = $result->fetch_array(MYSQLI_ASSOC)) !== NULL)
            {
                $resultarray[] = $row;
            }            
        }
        if(count($resultarray) > 0 )
        {
            return $resultarray;
        }
        else
        {
            return false;
        }
        mysqli_close($connection);        
    }
    
    static public function SELECTFROMTO($TABLE, $COLUMNS = array("*"), $WHERE = array(), $FROM = 0, $TO = 0, $WHAT, $ORDERYBY ="", $ORDERTYPE = "ASC")
    {
        $connection = self::getConnection();
        $SQL = "SELECT ";
        $NULL_VAL = false;
        
        foreach($COLUMNS as $column)
        {
            $SQL .= $column.",";
        }
        $SQL = rtrim($SQL,',');
        
        $SQL.= " FROM {$TABLE}";
        
        if(count($WHERE)>0)
        {
            $SQL.= " WHERE ";
            foreach($WHERE as $key => $val)
            {
                if($val == NULL)
                {
                    $SQL .= $key." IS NULL";
                    $NULL_VAL = true;
                }
                else
                {
                    $SQL .= $key."="."'".$val."' "."AND"." ";
                }
            }
            if($NULL_VAL == false)
            {
                $SQL = substr($SQL,0,strlen($SQL)-(strlen("AND")+2));
            }
        }
        //SELECT * FROM produkt WHERE kategoria_id='75' AND cena > 500 AND cena < 2000
        if($FROM >0)
        {
            $SQL.= " AND ".$WHAT." >= ".$FROM;
        }
        if($TO >0)
        {
            $SQL.= " AND ".$WHAT." <= ".$TO;
        }
        
        if(strlen($ORDERYBY)>0)
        {
            $SQL.= " ORDER BY ".$ORDERYBY;
            $SQL.= " ".$ORDERTYPE;
        }
                     
        //echo($SQL);   

        $result = $connection->query($SQL);
        
        if(!$result)
        {
            return false;  
        }
        else
        {
            $resultarray = Array();
            while(($row = $result->fetch_array(MYSQLI_ASSOC)) !== NULL)
            {
                $resultarray[] = $row;
            }            
        }
        if(count($resultarray) > 0 )
        {
            return $resultarray;
        }
        else
        {
            return false;
        }
        
        mysqli_close($connection);         
    }
    
    static public function UPDATE($TABLE, $SET, $WHERE = Array(), $OPERATOR = "AND")
    {
        $connection = self::getConnection();
        $SQL = "UPDATE {$TABLE} SET "; 
        
        foreach($SET as $key => $val)
        {
            $SQL .= $key."='".$val."',";
        }
        $SQL = rtrim($SQL,',');
        
        if(count($WHERE)>0)
        {
            $SQL.= " WHERE ";
            foreach($WHERE as $key => $val)
            {
                $SQL .= $key."='".$val."' ".$OPERATOR." ";
            }
            $SQL = substr($SQL,0,strlen($SQL)-(strlen($OPERATOR)+2));
        }        
        
        //echo($SQL."<br>");
        
        $result = $connection->query($SQL);
        
        if($result)
        {
            return true;  
        }
        else
        {
            return false;
        }
        mysqli_close($connection);                
    }
    static public function DELETE($TABLE, $WHERE = Array(), $OPERATOR = "AND")
    {
        $connection = self::getConnection();
        $SQL = "DELETE FROM {$TABLE}";
        
        if(count($WHERE)>0)
        {
            $SQL.= " WHERE ";
            foreach($WHERE as $key => $val)
            {
                $SQL .= $key."='".$val."' ".$OPERATOR." ";
            }
            $SQL = substr($SQL,0,strlen($SQL)-(strlen($OPERATOR)+2));
        }
        //echo($SQL."<br>");
        
        $result = $connection->query($SQL);
        
        if($result)
        {
            return true;    // Udało się
        }
        else
        {
            return false;   // Nie udało się
        }
        mysqli_close($connection);        
    }
    static public function INSERT($TABLE, $DATA)
    {
        $connection = self::getConnection();
        $SQL = "INSERT INTO {$TABLE} (";
     
        foreach($DATA as $key => $val)
        {
            $SQL.= $key.",";
        }
        
        $SQL = rtrim($SQL,",");
        $SQL.= ") VALUES (";
        
        foreach($DATA as $key => $val)
        {
            $SQL.= "'".$val."',";
        }      
        $SQL = rtrim($SQL,",");
        $SQL.= ")";
        //echo($SQL."<br>");
        
        $result = $connection->query($SQL);
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
        mysqli_close($connection);
    }
    static public function QUERY($SQL)
    {
        $connection = self::getConnection();
        $result = $connection->query($SQL);
        
        if(!$result)
        {
            return false;  
        }
        else
        {
            $resultarray = Array();
            while(($row = $result->fetch_array(MYSQLI_ASSOC)) !== NULL)
            {
                $resultarray[] = $row;
            }            
        }
                
        if(count($resultarray) > 0 )
        {
            return $resultarray;
        }
        else
        {
            return false;
        }
    }
    //
}

?>