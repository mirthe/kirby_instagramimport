<?php $kirby = kirby();
$kirby->impersonate('kirby');

$fields = "id,media_type,media_url,thumbnail_url,timestamp,permalink,caption,children{media_url,thumbnail_url}";
$token = option('mirthe.instagram-import.token');
$limit = option('mirthe.instagram-import.limit'); // Set a number of display items

function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

$result = fetchData("https://graph.instagram.com/me/media?fields={$fields}&access_token={$token}&limit={$limit}");
$result_decode = json_decode($result, true);

if (array_key_exists("error", $result_decode)) {
  print_r($result_decode['error']['message']);
  echo "<p>Try a new token in your Kirby config, see <a href='https://www.mageplaza.com/kb/how-to-get-instagram-feed-access-token.html#6-steps-to-get-instagram-feed-access-token'>How to get Instagram Feed Access Token</a>.</p>";
  exit();
// } else {
//   print_r($result_decode);
}

function storeFile($fullpath, $subfolder) {
  // bestandsnaam eruit halen
  $imgpathmettroep = explode("?", $fullpath);
  $imgpath = $imgpathmettroep[0];
  $imgfilename = basename($imgpath);
  
  // bestand opslaan
  $ch = curl_init($fullpath);
  $savefileloc = $subfolder. '/' . $imgfilename;
  $fp = fopen($savefileloc, 'wb');
  
  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_exec($ch);
  curl_close($ch);
  fclose($fp);

  return $imgfilename;
}

foreach ($result_decode["data"] as $post):

  $caption = $post["caption"];
  $permalink = $post["permalink"];
  $media_type = $post["media_type"];

  // dateformats
  // TODO corrigeren voor timezone!!
  $pubdatumtijd = date("Y-m-d H:i", strtotime($post["timestamp"]));
  $pubdatum = date("Y-m-d", strtotime($post["timestamp"]));

 // wat te gebruiken als tijdelijke bestandsnaam
 $str=rand();
 $naam = substr(md5($str), 0, 10);

 // titel kuisen
 $titelalsstring = preg_replace('/\s+/', ' ', $naam);
 $titelalsstring = preg_replace('/\s/', '-', $titelalsstring);
 $titelalsstring = preg_replace("([^\w\s\d\-])", '', $titelalsstring);
 $titelalsstring = strtolower($titelalsstring);
 $cleantitle = preg_replace('/-+/', '-', $titelalsstring);
  
  // postfolder bepalen en aanmaken, indien nodig
  $exportdir = __DIR__ . '/temp/'; // TODO jaar dynamsich
  if (!is_dir($exportdir)) {
    mkdir($exportdir);
  }
  $folder = str_replace('-', '', $pubdatum) .'_' . $cleantitle;
  $subfolder = $exportdir . $folder;
  if (!is_dir($subfolder)) {
    mkdir($subfolder);
  }
  
  // plaatje local opslaan in de postfolder en bestandsnaam in string vasthouden voor MD
  $mediauris = "";
  if ($media_type == "CAROUSEL_ALBUM") {
    foreach ($post["children"]["data"] as $slide) {
      $imgfilename = storeFile($slide["media_url"],$subfolder);
      $mediauris .= "- " . $imgfilename . PHP_EOL;
      // of bij video iets doen met de thumbnail ipv de mp4?
      // $imgfilename = storeFile($slide["thumbnail_url"],$subfolder);
      // $mediauris .= "- " . $imgfilename . PHP_EOL;
    }
  } else {
    $imgfilename = storeFile($post["media_url"],$subfolder);
    $mediauris = "- " . $imgfilename . PHP_EOL;
  }

  // Compile the content of the file to write
  $strtowrite = "Title: " . $naam
  . PHP_EOL . "----" . PHP_EOL
  . "Intro: " . $post["caption"]
  . PHP_EOL . "----" . PHP_EOL
  . "Date: " . $pubdatumtijd
  . PHP_EOL . "----" . PHP_EOL
  . "sourcelink: " . $post["permalink"]
  . PHP_EOL . "----" . PHP_EOL
  . "Photo: " .PHP_EOL . $mediauris;
  
  // Save to file
  file_put_contents($exportdir . $folder. '/photopost.md', $strtowrite);
  
  print_r($strtowrite);
  echo "<hr>";
  
  /* TODO use native Kirby functions

  $page = Page::create([
    'parent' => page('temp'),
    'slug'     => $foldername,
    'template' => 'photopost',
    'isDraft' => false,
    'content' => [
      'title'  => $naam,
      'date' => $pubdatumtijd,  // TODO timezone corrigeren
      'photo2' => $imgarray,
      'intro' => $post["caption"],
      'permalink' => $post["permalink"]
    ]
  ]);

  of beter uitwerken met eigen functie?
  https://getkirby.com/docs/reference/objects/cms/page/create-file
    $photo = $page->createFile([
      'filename' => $imgfilename,
      'template' => 'image',
      'source'   => $media_url,
      'parent'   => $page,
      'content'  => [
          'alt' => ''
      ],
  ]);
  exit(); */

endforeach;

echo "<p>Done</p>";
exit();
