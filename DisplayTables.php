<?php
session_start();
function displayArray($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre";
}

function mainFunction($DBhost, $DBname, $DBpass, $DBuser)
{

    echo "<a href=\"" . basename(__FILE__) . "?option=exit  \" >Exit</a> ";
    echo "<br><br>";

    if (isset($_GET['option']) && $_GET['option'] == "exit") {
        session_destroy();
        $file = basename(__FILE__);
        header("Location: $file");
    }


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
                $mysqlConect->showRecordValue($nameTable);
            }
        } else {
            $mysqlConect->showTables();
        }

    } else {
        echo "Cannot connect to database. ";
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

            while (list($klucz, $wartosc) = each($row_temp))
                echo "<td style=\"border: 1px solid black\">$wartosc</td>";

            echo "";
            echo "</tr>";
            echo "</table>";

            echo "<br>";
            //displayArray($row);


        }

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
    echo <<< ENDOFFILE


<form method="POST" action="index.php">
    <input type="text" name="dbhost" placeholder="DBhost"><br>
    <input type="text" name="dbname" placeholder="DBname"><br>
    <input type="text" name="dbpass" placeholder="DBpass"><br>
    <input type="text" name="dbuser" placeholder="DBuser"><br>
    <input type="submit" value="Display database">
</form>

ENDOFFILE;

}
?>
