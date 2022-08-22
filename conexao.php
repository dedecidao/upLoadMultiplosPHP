<?php
    mysqli_connect("localhost", "root", "", "db_upload");

    if(mysqli_connect_errno()){
        echo "Erro ao conectar com o banco de dados: " . mysqli_connect_error();
    } else {
        echo "Conexão realizada com sucesso!";
    }
?>