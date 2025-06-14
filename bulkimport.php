<?php foreach ($decoded_json as $v) {
    $pubdatumtijd = date("Y-m-d H:i", $v['media'][0]['creation_timestamp']);
    $pubdatum = date("Y-m-d", $v['media'][0]['creation_timestamp']);

    if (count($v['media']) > 1) {
        $title = $v['title'];
    } else {
        $title = $v['media'][0]['title'];
    }

    // wat te gebruiken als bestandsnaam
    $str=rand();
    $naam = substr(md5($str), 0, 10);

    $titelalsstring = preg_replace('/\s+/', ' ', $naam);
    $titelalsstring = preg_replace('/\s/', '-', $titelalsstring);
    $titelalsstring = preg_replace("([^\w\s\d\-])", '', $titelalsstring);
    $titelalsstring = strtolower($titelalsstring);
    $cleantitle = preg_replace('/-+/', '-', $titelalsstring);

    // wegschrijven!
    $folder = str_replace('-', '', $pubdatum) .'_' . $cleantitle;

    if (!is_dir($exportdir . $folder)) {
        mkdir($exportdir . $folder);
    }

    $mediauris = "";
    for ($mediateller = 0; $mediateller < count($v['media']); $mediateller++) {
        $bestand = $v['media'][$mediateller]['uri'];
        $bestandarray = explode('/', $bestand) ;
        $bestandsnaam = end($bestandarray);

        $mediauris .= "- " . $bestandsnaam . PHP_EOL;

        try {
            copy(__DIR__ . "/". str_replace('media/', '', $bestand), $exportdir . $folder . "/" . $bestandsnaam);
        } catch (Exception $e) {
            echo '<span style="color: red">Caught exception: ',  $e->getMessage(), "</span>\n";
        }
    }

    // $title = mb_convert_encoding($title, 'HTML-ENTITIES', "UTF-8");
    $title = htmlspecialchars_decode(htmlentities($title));

    // Compile the content of the file to write
    $strtowrite =
      "Title: " . $naam
    . PHP_EOL . "----" . PHP_EOL
    . "Intro: " . $title
    . PHP_EOL . "----" . PHP_EOL
    . "Date: " . $pubdatumtijd
    . PHP_EOL . "----" . PHP_EOL
    . "Photo: " .PHP_EOL . $mediauris;

    // Save to file
    file_put_contents($exportdir . $folder. '/photopost.md', $strtowrite);
    
    print_r($strtowrite);
    echo "<hr>";
}

echo "<p>Done</p>";
exit();
