<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZIP FILE AND DOWNLOAD</title>
</head>
<body>
<h1>ZIP FILE AND DOWNLOAD</h1>
<form name="file" action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file[]" multiple>
    <button type="submit">Enviar</button>
</form>

<?php
/**
 *
 * @author Hoheckell <hoheckell.info@gmail.com>
 *
 * As referẽncias abaixo em desenvolvimento sao como se o website estivesse em localhost/zipfile
 *
 */
if (!empty($_FILES['file'])) {
    try {

        for ($i = 0; $i < count($_FILES["file"]["tmp_name"]); $i++) {

            $tmp_name = $_FILES["file"]["tmp_name"][$i];
            if (!$tmp_name) die('TMP NAME ERROR!');

            $name = basename($_FILES["file"]["name"][$i]);
            if (move_uploaded_file($tmp_name, "./" . $name)) {
                echo "Enviando arquivo '" . $name . "'.<br/>\n";
                $zip = new ZipArchive;
                $filename = "./ZFAD_" . date("d_m_Y") . ".zip";
                $c=true;
                $t=1;
                /*
                 * Verifica a existência do arquivo ZIP com o mesmo nome edefine outro nome adicionando numero sequencial
                 */
                while($c){
                    if(file_exists($filename)){
                        $filename = "/ZFAD_" . date("d_m_Y") . "_".$c.".zip";
                        $c=true;
                        $t++;
                    }else{
                        $c=false;
                    }
                }

                if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
                    exit("Não foi possível criar <$filename>\n");
                }

                $zip->addFile($_FILES['file']["name"][$i], $name);
                echo "Arquivo " . $zip->numFiles . " ";
                echo " " . ($zip->status === 0) ? "Enviado" : " Falhou";
                echo "<br/>\n";
                $zip->close();
                /*
                 * Exclui arquivos que foram compactados
                 */
                unlink($_FILES['file']["name"][$i]);

            } else {
                die('CANT UPLOAD!');

            }
        }

        echo '<a href="' . $filename . '">Baixar arquivo</a><br>';
        echo '<a href="/zipfile">Compactar novo arquivo</a>';

        /*
         * Toda vez que compacta arquivos apaga arquivos do dia anterior baseado no nome do arquivo zip
         *
         */
        if ($handle = opendir('.')) {
            $data = date('d_m_Y');
            $d = explode("_", $data);
            while (false !== ($arquivo = readdir($handle))) {
                if ($arquivo != "." && $arquivo != ".." && strtolower(substr($arquivo, strrpos($arquivo, '.') + 1)) == 'zip') {
                    $arqname = explode('_', $arquivo);
                    if ($arqname[1] < $d[0]) {
                        unlink($arquivo);
                    }
                }
            }
            closedir($handle);
        }

    } catch (Exception $e) {
        echo $e->getMessage;
    }
}
?>
</body>
</html>
