<?php
function displayError(array $error){
    echo "<pre>";
    print_r($error);
    echo "</pre";
}
class NewUserWordpress
{

    private $DBHOST = "localhost";
    private $DBNAME = "test";
    private $DBPASS = "Qwerty12#";
    private $DBUSER = "test";

    private $PREFIX = "wp_";

    private $NEWLOGIN = "marcin";
    private $NEWPASS = "marcin666";
    private $NEWNICKNAME = "marcin";
    private $NEWEMAIL = "marcin@w.w";
    private $NEWNAMEDISPLAY = "marcin";
    private $connect;

    public function __construct()
    {
        if($this->connect = mysqli_connect($this->DBHOST, $this->DBUSER, $this->DBPASS, $this->DBNAME)){
        }

    }

    public function isConnect()
    {
        return $this->connect;
    }
    public function addNewUser()
    {
        $tabela = $this->PREFIX . "users";
        $query1 = "INSERT INTO $tabela (user_login, user_pass, user_nicename, user_email, user_registered, user_status, display_name) VALUES ('$this->NEWLOGIN', MD5('$this->NEWPASS'), '$this->NEWNICKNAME', '$this->NEWEMAIL', NOW(), 0, '$this->NEWNAMEDISPLAY')";
        return $this->connect->query($query1);
    }
    public function setPrivilegeNewUser($ID){
        $table_capabilitities = $this->PREFIX . "capabilities";
        $table_userlevel = $this->PREFIX . "user_level";
        $table_usermeta = $this->PREFIX . "usermeta";
        $query = "INSERT INTO $table_usermeta (`user_id`, `meta_key`, `meta_value`) VALUES ($ID,'$table_capabilitities', 'a:1:{s:13:\"administrator\";s:1:\"1\";}'), ($ID, '$table_userlevel', '10')";
        //$query = "INSERT INTO $table_usermeta (`user_id`, `meta_key`, `meta_value`) VALUES ($ID,$table_capabilitities, 'a:1:{s:13:\"administrator\";s:1:\"1\";}'), ($ID, $table_userlevel, '10')";
        //$q = $this->connect->prepare($query);

        return $this->connect->query($query);

    }
    public function getIDNewUser(){
        $tabela = $this->PREFIX . "users";

        $query2 = "SELECT ID FROM ". $tabela . " WHERE user_login='$this->NEWLOGIN' AND user_email='$this->NEWEMAIL'";


        $result= $this->connect->query($query2);
        $id = "";
        if($result){
            while($row = $result->fetch_assoc()){
                $id = $row['ID'];

            }
            return $id;
        }
        else{
            echo "cos nie tak z zapytaniem<br>";
            displayError($this->getErrorMySQL());
        }
    }
    public function getErrorMySQL(){
        return $this->connect->error_list;
    }
}

$newUser = new NewUserWordpress();

if ($newUser->isConnect()) {

    echo "polaczenie z baza mysql nawiazano<br>";


    if ($newUser->addNewUser() === true) {

        echo "poprawnie dodano nowego uzytkownika<br>";

        $IDNewUser = $newUser->getIDNewUser();
        echo "ID nowego uzytkownika:  $IDNewUser<br>";
        if($newUser->setPrivilegeNewUser($IDNewUser)){
            echo "poprawnie nadano uprawnienia<br>";
        }
        else{
            echo "nie dodano uprawnien";
            displayError($newUser->getErrorMySQL());
        }



} else {
        echo "<br>cos jest nie tak<br>";
        displayError($newUser->getErrorMySQL());
    }

} else {
    echo "Polaczenie z baza danych nie zostało poprawnie nawiazane<br>";
    echo "<pre>";
    echo $newUser->getErrorMySQL();
    echo "</pre>";
}







