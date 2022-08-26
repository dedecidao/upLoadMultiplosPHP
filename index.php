<?php

include("conexao.php");
//Pode ser feito uma validacao de session aqui
//Validar  (tipo de arquivo) contra injeção de código php
// Pegar o arquivo e copiar o conteudo para o servidor e salva o caminho no banco
if (isset($_FILES['arquivo']) && count($_FILES['arquivo']) > 0) {
    $arquivos = $_FILES['arquivo'];
    // ATENÇÃO ao foreach abaixo q usa um array interno pra poder saber quantas vezes o loop vai abrir 2 arquivos = 2 vezes
    foreach ($arquivos['name'] as $index => $arq) {
        $result = upload($arquivos["error"][intval($index)], 
        $arquivos['size'][intval($index)], 
        $arquivos['name'][intval($index)], 
        $arquivos['tmp_name'][intval($index)]);
        if ($result) 
            echo "Arquivo <strong> || ". $arquivos['name'][intval($index)] ."</strong> || enviado com sucesso! <br>";
        else
            echo "Erro ao enviar o arquivo <strong> || ". $arquivos['name'][intval($index)]  ."|| </strong>!<br><br>";
    }
    
}

function upload($error, $size, $name, $tmp_name){
    include("conexao.php");

    $pasta = "arquivos/";
    $novoNomeArquivo = geraPathName($pasta, $name);

    verificaErro($error);
    verificaTamanho($size);
    verificaExtensao($novoNomeArquivo);
    //moxe o arquivo da temp para pasta logica com o pathname
    move_uploaded_file($tmp_name, $novoNomeArquivo);
        $mysqli->query("INSERT INTO arquivos (path, data_upload, nome_original) 
                        VALUES ('{$novoNomeArquivo}', NOW(), '{$name}')");
    return true;   
    }

function verificaErro($erro){
    if ($erro){
        return "Houve um erro ao enviar o arquivo";
        die();
    }
}
function verificaTamanho($size){
    //verifica tamanho do arquivo 2 * 1024 * 1024 = 2MB
    if ($size > 7152)
        echo "Arquivo maior que 2MB!";
        die();
}
function verificaExtensao($novoNomeArquivo){
    $extensao = strtolower(pathinfo($novoNomeArquivo, PATHINFO_EXTENSION));
    if ($extensao != 'jpg' && $extensao != 'png' && $extensao != 'gif') {
        echo "- Extensão inválida -";
        die();
    }
}    
function geraPathName($pasta, $name){
    return $pasta . uniqid() . "-" . $name;
}

?>
<!-- Lista arquivos -->


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de arquivo</title>
</head>

<body>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="arquivo">Selecione o arquivo:</label>
        <br>
        <input multiple type="file" name="arquivo[]">
        <br>
        <input type="submit" value="Enviar">
    </form>
    <h3>Lista de Arquivos:</h3>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Preview</th>
                <th>Arquivo Original</th>
                <th>Data Upload</th>
                <th>path</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $mysqli->query("SELECT * FROM arquivos");
            while ($arquivo = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><img height='50' target='_blank' src='{$arquivo['path']}'</img></td>";
                echo "<td><a target='_blank' href='{$arquivo['path']}'>{$arquivo['nome_original']}</a></td>";
                echo "<td>" . date("d/m/Y H:i", strtotime($arquivo['data_upload'])) . "</td>";
                echo "<td>{$arquivo['path']}</td>";
                echo "</tr>";
            }
            ?>
    </table>
    <?php
    $mysqli->close();
    ?>
    

</body>

</html>