<html>
<head>
<title>AlchemyAPI サンプル</title>
</head>
<body>
<?php
$trans1 = 0;
$trans2 = 0;
if( isset( $_GET['url'] ) ){
  $apikey = 'b896a8c6090c1ec2eee62a54e5eb77979caefbea';

  $url = $_GET['url'];
  $alchemyurl = 'http://access.alchemyapi.com/calls/url/';
?>
  <img src='<?php echo $url; ?>'/>
  <p/>
<?php
  $apiurl1 = $alchemyurl . 'URLGetRankedImageKeywords?apikey=' . $apikey . '&outputMode=json&url=' . urlencode( $url );
  $text1 = file_get_contents( $apiurl1 );
  $json1 = json_decode( $text1 );

  $trans1 = $json1->totalTransactions;
  $imageKeywords = $json1->imageKeywords;
  if( count( $imageKeywords ) ){
?>
  <h2>Keywords</h2>
  <table border='1'>
    <tr><th>text</th><th>score</th></tr>
<?php
    for( $i = 0; $i < count( $imageKeywords ); $i ++ ){
      $imageKeyword = $imageKeywords[$i];
      $text = $imageKeyword->text;
      $score = $imageKeyword->score;
?>
    <tr><td><?php echo $text; ?></td><td><?php echo $score; ?></td></tr>
<?php
    }
?>
  </table>
  <p/>
<?php
  }

  $apiurl2 = $alchemyurl . 'URLGetRankedImageFaceTags?apikey=' . $apikey . '&outputMode=json&knowledgeGraph=1&url=' . urlencode( $url );
  $text2 = file_get_contents( $apiurl2 );
  $json2 = json_decode( $text2 );

  $trans2 = $json2->totalTransactions;
  $imageFaces = $json2->imageFaces;
  if( count( $imageFaces ) ){
?>
  <h2>FaceTags</h2>
  <table border='1'>
    <tr><th>attr</th><th>value</th><th>score</th></tr>
<?php
    for( $i = 0; $i < count( $imageFaces ); $i ++ ){
      $imageFace = $imageFaces[$i];
      $positionX = $imageFace->positionX;
      $positionY = $imageFace->positionY;
      $width = $imageFace->width;
      $height = $imageFace->height;
      $ageO = $imageFace->age;
      $ageRange = $ageO->ageRange;
      $ageScore = $ageO->score;
      $genderO = $imageFace->gender;
      $gender = $genderO->gender;
      $genderScore = $genderO->score;
?>
    <tr><th colspan='3'><?php echo $i; ?></th></tr>
    <tr><td>positionX</td><td><?php echo $positionX; ?></td><td>&nbsp;</td></tr>
    <tr><td>positionY</td><td><?php echo $positionY; ?></td><td>&nbsp;</td></tr>
    <tr><td>width</td><td><?php echo $width; ?></td><td>&nbsp;</td></tr>
    <tr><td>height</td><td><?php echo $height; ?></td><td>&nbsp;</td></tr>
    <tr><td>age</td><td><?php echo $ageRange; ?></td><td><?php echo $ageScore; ?></td></tr>
    <tr><td>gender</td><td><?php echo $gender; ?></td><td><?php echo $genderScore; ?></td></tr>
<?php
      $identityO = $imageFace->identity;
      if( $identityO ){
        $name = $identityO->name;
        $nameScore = $identityO->score;
?>
    <tr><td>name</td><td><?php echo $name; ?></td><td><?php echo $nameScore; ?></td></tr>
<?php
      }
?>
<?php
    }
?>
  </table>
  <p/>
<?php
  }
?>
  <div align='right'>
  使ったトランザクション数：<?php echo $trans1; ?> + <?php echo $trans2; ?> = <?php echo ($trans1 + $trans2); ?>
  </div>
<?php
}
?>
</body>
</html>

