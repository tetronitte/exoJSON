<?php
if(!empty($_POST['json']) && !empty($_POST['pathIMG'])) {
    unlink('img.json');
    $array = scandir($_POST['pathIMG']);
    foreach($array as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        unlink('img/'.$file);
    }
}

if($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['json']) && empty($_POST['pathIMG'])) {
    //UPLOAD
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "webp" => "image/webp");
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $fileTmpName = $_FILES['image']['tmp_name'];
        // Vérifie l'extension du fichier
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if(array_key_exists($extension, $allowed)) {
            // Vérifie le type MIME du fichier
            if(in_array($filetype, $allowed)){
                $newFileName = resize_rename_upload($filename,$extension,$fileTmpName);
                addJSON($newFileName,$extension);
            } else{
                echo 'Erreur : Il y a eu un problème de téléchargement de votre fichier. Veuillez réessayer !';
            }
        }
        else {
            echo 'Erreur : Veuillez sélectionner un format de fichier valide !';
        }        
    } else{
        echo 'Erreur : '.$_FILES['image']['error'];
    }
}

function addJSON($newFileName,$extension) {
    if (!file_exists('img.json')) {
        file_put_contents('img.json','');
    }
    $json = file_get_contents('img.json');
    $array = json_decode($json,true);
    $array[] = array('filename' => $newFileName.$extension);
    $json = json_encode($array);
    file_put_contents('img.json',$json);
}

function resize_rename_upload($filename,$extension,$fileTmpName) {
    $uploadedFile = $fileTmpName; 
    $sourceProperties = getimagesize($uploadedFile);
    $newFileName = time()."_thump.";
    $dirPath = "img/";
    switch ($extension) {
        case "jpg":
        case "jpeg":
            $imageSrc = imagecreatefromjpeg($uploadedFile); 
            $tmp = imageResize($imageSrc,$sourceProperties[0],$sourceProperties[1]);
            imagejpeg($tmp,$dirPath.$newFileName.$extension);
            break;
        case "png":
            $imageSrc = imagecreatefrompng($uploadedFile); 
            $tmp = imageResize($imageSrc,$sourceProperties[0],$sourceProperties[1]);
            imagepng($tmp,$dirPath.$newFileName.$extension);
            break;
        case "gif":
            $imageSrc = imagecreatefromgif($uploadedFile); 
            $tmp = imageResize($imageSrc,$sourceProperties[0],$sourceProperties[1]);
            imagegif($tmp,$dirPath.$newFileName.$extension);
            break;
        case "webp":
            $imageSrc = imagecreatefromwebp($uploadedFile); 
            $tmp = imageResize($imageSrc,$sourceProperties[0],$sourceProperties[1]);
            imagewebp($tmp,$dirPath.$newFileName.$extension);
            break;
        default:
            echo "Image invalide";
            break;
    }
    return $newFileName;
}

function imageResize($imageSrc,$imageWidth,$imageHeight) {
    $newImageHeight = 200;
    $x = $newImageHeight*100/$imageHeight;
    $newImageWidth = $imageWidth*$x/100;
    $newImageLayer = imagecreatetruecolor($newImageWidth,$newImageHeight);
    imagecopyresampled($newImageLayer,$imageSrc,0,0,0,0,$newImageWidth,$newImageHeight,$imageWidth,$imageHeight);
    return $newImageLayer;
}

function displayIMG() {
    if (file_exists('img.json')) {
        $json = file_get_contents('img.json');
        $array = json_decode($json,true);
        echo "<div class='galery'>";
        foreach($array as $elem) {
            echo "<img class='img' src='img/".$elem['filename']."'/>";
        }
        echo '</div>';
    }
}
?>


<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="index.css">
    <body>
        <?php //pas de navbar sur w10
        // include('../../navbar.php'); 
        ?>
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="image" id="image"/>
            <button type="submit">Ajouter l'image</button>
        </form>
        <?php displayIMG(); ?>
        <button id="delete">Tout supprimer</button>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="index.js"></script>
    </body>
</html>