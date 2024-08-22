<?php
$conn = mysqli_connect('localhost','root','','project_law',3307);

if(!$conn){
    die('connection error'.mysqli_connect_error());
}

?>