<?php
 session_start();
 // edytowanie działa tylko w przypadku, jeżeli unikalny identyfikator ID jest w pierwszej kolumnie. Skrypt nie wykrywa automatycznie kolumny PRIMARY KEY. 
 // W związu z tym skrypt nie bedzie działał w przypadku, gdy ktoś podał unikalny numer ID w innej niz pierwszej kolumnie (w najlepszym wypadku nie zedytuje pola, które chcemy, w najgorszym - zedytuje pole inne niż chcemy) oraz gdy relacja tabeli jest "wiele do wielu" - (phpMyAdmin blokuje możliwość edytowania - ten niestety nie)
 
 // w następnej wersji problem zostanie rozwiązany. Mam nadzieje.
 


function displayArray($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre";
}
function mainFunction($DBhost, $DBname, $DBpass, $DBuser)
{
    echo "<a href=\"" . basename(__FILE__) . "  \" >Tables</a> <br>";
    echo "<a href=\"" . basename(__FILE__) . "?option=exit  \" >Exit</a> ";
    echo "<br><br>";
    if (isset($_GET['option']) && $_GET['option'] == "exit") {
        session_unset();
        session_destroy();  
            echo "<a href=\"" . basename(__FILE__) . "? \" >Exit</a> ";
    }
    else{
    $mysqlConect = new DisplayDatabaseStruct($DBhost, $DBuser, $DBpass, $DBname);
    if ($mysqlConect->isConnected()) {
        $nameTable = "";
        if (isset($_GET['nameTable'])) {
            $nameTable = $_GET['nameTable'];
            $type = "";
            if (isset($_GET['type'])) {
                $type = $_GET['type'];
            } else {
                echo "<a href=\"" . basename(__FILE__) . "?nameTable=$nameTable&type=value  \"> Display Values</a><br>";
                echo "<br>";
                echo "<a href=\"" . basename(__FILE__) . "?nameTable=$nameTable&type=struct  \"> Display struct of table</a><br>";
            }
            if ($type == "struct")
                $mysqlConect->showRecordStructure($nameTable);
            if ($type == "value") {
                if(isset($_POST['Text']) && isset($_POST['Element']))
                    {
                        $mysqlConect->updateElement($nameTable, $_POST['Text'], $_POST['Element'], $_POST['IDElement'], $_POST['NazwaID']);
                    }
                    else{
                        $mysqlConect->showRecordValue($nameTable);
                    }
                
            }
        } else {
            $mysqlConect->showTables();
        }
    } else {
        echo "Cannot connect to database. ";
    }
}
}
class DisplayDatabaseStruct
{
    private $mysql_connector;
    private $tables;
    public function __construct($DBHOST, $DBUSER, $DBPASS, $DBNAME)
    {
        $this->mysql_connector = mysqli_connect($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    }
    public function isConnected()
    {
        return $this->mysql_connector;
    }
    public function showTables()
    {
        $query = "SHOW TABLES";
        $reqult = $this->mysql_connector->query($query);
        while ($row = $reqult->fetch_array()) {
            //displayArray($row);
            $name = $row[0];
            echo "<a href=\"" . basename(__FILE__) . "?nameTable=$name\">$name</a><br>";
        }
    }
    public function showRecordStructure($nameTable)
    {
        echo "<a href=\"" . basename(__FILE__) . "?nameTable=$nameTable&type=value  \"> Display values this table</a><br>";
        $query = "DESCRIBE $nameTable";
        $reqult = $this->mysql_connector->query($query);
        while ($row = $reqult->fetch_array()) {
            displayArray($row);
        }
    }
    public function showRecordValue($nameTable)
    {
        echo "<a href=\"" . basename(__FILE__) . "?nameTable=$nameTable&type=struct  \"> Display struct</a><br>";
        $query = "SELECT * FROM $nameTable";
        $result = $this->mysql_connector->query($query);
        for ($i = 0; $i < $result->num_rows; $i++) {
            $row = $result->fetch_assoc();
            echo "<table style=\"margin-bottom: 40px; border: 1px solid black; border-collapse: collapse;\" >";
            echo "<tr style=\"border: 1px solid black; background-color: grey; color: black;\">";
            $row_temp = $row;
            while (list($klucz, $wartosc) = each($row))
                echo "<th style=\"border: 1px solid black; background-color: white; color: black;\">$klucz</th>";
            echo "</tr>";
            echo "<tr>";
            $id = 0;
            $nazwa_id = "";
            while (list($klucz, $wartosc) = each($row_temp)){

                if($klucz == 0){
                    $id = $wartosc;
                    $nazwa_id = $klucz;
                }
                echo "<td identyfikator=\"$id\" nazwa_id=\"$nazwa_id\" id=\"$klucz\" class=\"pole\" style=\"border: 1px solid black\">$wartosc</td>";
               }
            echo "";
            echo "</tr>";
            echo "</table>";
            echo "<br>";
            //displayArray($row);
        }
    }
    public function updateElement($nameTable, $text, $element, $id, $nazwa_id)
    {
        $query = "UPDATE $nameTable SET $element='$text' WHERE $nazwa_id='$id'";
        if($this->mysql_connector->query($query)){
            echo "ok";
        }
        else{
            echo "nieok";
        }
        echo $query;
    }
}
$DBhost = "";
$DBname = "";
$DBpass = "";
$DBuser = "";
if (isset($_POST['dbhost']) && isset($_POST['dbname']) && isset($_POST['dbpass']) && isset($_POST['dbuser'])) {
    $_SESSION['dbhost'] = $DBhost = $_POST['dbhost'];
    $_SESSION['dbname'] = $DBname = $_POST['dbname'];
    $_SESSION['dbpass'] = $DBpass = $_POST['dbpass'];
    $_SESSION['dbuser'] = $DBuser = $_POST['dbuser'];
    mainFunction($DBhost, $DBname, $DBpass, $DBuser);
} else if (isset($_SESSION['dbhost']) && isset($_SESSION['dbname']) && isset($_SESSION['dbpass']) && isset($_SESSION['dbuser'])) {
    $DBhost = $_SESSION['dbhost'];
    $DBname = $_SESSION['dbname'];
    $DBpass = $_SESSION['dbpass'];
    $DBuser = $_SESSION['dbuser'];
    mainFunction($DBhost, $DBname, $DBpass, $DBuser);
} else {
    echo "Go to database<br>";
    $filename = basename(__FILE__);
    echo <<< ENDOFFILE
<form method="POST" action="$filename">
    <input type="text" name="dbhost" placeholder="DBhost"><br>
    <input type="text" name="dbname" placeholder="DBname"><br>
    <input type="text" name="dbpass" placeholder="DBpass"><br>
    <input type="text" name="dbuser" placeholder="DBuser"><br>
    <input type="submit" value="Display database">
</form>
ENDOFFILE;
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 <script type="text/javascript">
    window.onload = function() {
        var elements = getElementsByClassName('pole');
        for(var i = 0; i < elements.length; i++) {
            elements[i].ondblclick = function() {
                

                var old = this;

                var input = document.createElement("textarea");
                input.type = "text";
                input.value = this.textContent;
                this.parentNode.replaceChild(input, this);
                var save = document.createElement("INPUT");
                save.type = "button";
                save.value = "Save";
                (function(old, input){
                  save.onclick = function(){
                    old.value = input.value;
                    old.textContent = input.value;
                    input.parentNode.replaceChild(old, input);
                    this.parentNode.removeChild(this);
                    cancel.parentNode.removeChild(cancel);
                    var url = window.location.href;
                    var sendData = {
                        Text: input.value,
                        IDElement: old.attributes[0].value,
                        Element: old.id,
                        NazwaID: old.attributes[1].value
                    }

                    $.ajax({
                        url: url,
                        dataType: "json",
                        data: sendData,
                        type: 'post',

                
                    });


                };
            })(old, input);
            input.parentNode.insertBefore(save, input.nextSibling);
            var cancel = document.createElement("INPUT");
            cancel.type = "button";
            cancel.value = "Cancel";
            (function(old, input){
              cancel.onclick = function(){
                input.parentNode.replaceChild(old, input);
                this.parentNode.removeChild(this);
                save.parentNode.removeChild(save);
            };
        })(old, input);
        input.parentNode.insertBefore(cancel, input.nextSibling);

    }
}(i);
}

function getElementsByClassName(className, tag, elm) {
    var testClass = new RegExp("(^|\\s)" + className + "(\\s|$)");
    var tag = tag || "*";
    var elm = elm || document;
    var elements = (tag == "*" && elm.all) ? elm.all : elm.getElementsByTagName(tag);
    var returnElements = [];
    var current;
    var length = elements.length;
    for(var i = 0; i < length; i++) {
        current = elements[i];
        if(testClass.test(current.className)) {
            returnElements.push(current);
        }
    }
    return returnElements;
}
</script>