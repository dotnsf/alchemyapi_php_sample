<html>
<head>
<title>AlchemyAPI サンプル</title>
<link rel="stylesheet" type="text/css" href="./jqcloud.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js"></script>
<script type="text/javascript" src="./jqcloud-1.0.4.js"></script>
<script type="text/javascript">
var arr = new Array();
function addMeta( xywh, arange, ascore, ggender, gscore, itype, iname, iscore ){
  var o = new Array( xywh, arange, ascore, ggender, gscore, itype, iname, iscore );
  arr.push( o );
}

function drawMeta(){
  var canvas = document.getElementById( 'img' );
  var ctx = canvas.getContext( '2d' );
  while( arr.length > 0 ){
    var o = arr.shift();
    var xywh = o[0];
    var arange = decodeURI( o[1] );
    var ascore = o[2];
    var ggender = o[3];
    var gscore = o[4];
    var itype = decodeURI( o[5] );
    var iname = decodeURI( o[6] );
    var iscore = o[7];

    topos = xywh.split( "," );
    ctx.strokeStyle = "rgb( 200, 200, 200 )";
    if( ggender == 'MALE' ){
      ctx.strokeStyle = "rgb( 200, 200, 255 )";
    }else if( ggender == 'FEMALE' ){
      ctx.strokeStyle = "rgb( 255, 200, 200 )";
    }
    ctx.lineWidth = 10.0;
    ctx.strokeRect( topos[0], topos[1], topos[2], topos[3] );

    ctx.fillText( ggender, topos[0], parseInt(topos[1]) + 5, topos[2] );
    console.log( ggender + "(" + gscore + ")" );
    ctx.fillText( arange, topos[0], parseInt(topos[1]) + parseInt(topos[3]) + 5, topos[2] );
    console.log( arange + "(" + ascore + ")" );
    if( iname != '' ){
      ctx.strokeRect( topos[0], parseInt(topos[1]) + parseInt(topos[3]) + 10, topos[2], 1 );
      ctx.fillText( iname, topos[0], parseInt(topos[1]) + parseInt(topos[3]) + 15, topos[2] );
      console.log( iname + "(" + iscore + ") : " + itype );
    }
  }
}

function roundRect(ctx, x, y, w, h, r) {
  ctx.moveTo(x, y + r);
  ctx.lineTo(x, y + h - r);
  ctx.quadraticCurveTo(x, y + h, x + r, y + h);
  ctx.lineTo(x + w - r, y + h);
  ctx.quadraticCurveTo(x + w, y + h, x + w, y + h - r);
  ctx.lineTo(x + w, y + r);
  ctx.quadraticCurveTo(x + w, y, x + w - r, y);
  ctx.lineTo(x + r, y);
  ctx.quadraticCurveTo(x, y, x, y + r);
}

</script>
</head>
<body>
<?php
$trans1 = 0;
$trans2 = 0;
if( isset( $_GET['url'] ) ){
  $apikey = '(Your API Key)';

  $url = $_GET['url'];
  $alchemyurl = 'http://access.alchemyapi.com/calls/url/';

  $apiurl1 = $alchemyurl . 'URLGetRankedImageKeywords?apikey=' . $apikey . '&outputMode=json&url=' . urlencode( $url );
  $text1 = file_get_contents( $apiurl1 );
?>
<!-- $text1
<?php echo $text1; ?>
-->
<?php
  $json1 = json_decode( $text1 );

  $trans1 = $json1->totalTransactions;
  $imageKeywords = $json1->imageKeywords;
  if( count( $imageKeywords ) ){
?>
<script type='text/javascript'>
var word_list = [
<?php
    for( $i = 0; $i < count( $imageKeywords ); $i ++ ){
      $imageKeyword = $imageKeywords[$i];
      $text = $imageKeyword->text;
      $score = ( int )( floatval( $imageKeyword->score ) * 20 );
      if( $i > 0 ){ echo ','; }
?>
  { text: '<?php echo $text; ?>', weight: <?php echo $score; ?> }
<?php
    }
?>
];
$(function(){
  var canvas = document.getElementById( 'img' );
  var ctx = canvas.getContext( '2d' );
  var img = new Image();
  img.src = '<?php echo $url; ?>';
  img.addEventListener( 'load', function(){
    $("#img").attr( 'width', img.width );
    $("#img").attr( 'height', img.height );
    ctx.drawImage( img, 0, 0, img.width, img.height );
    setTimeout( 'drawMeta()', 1000 );
  }, false );

  $("#tagcloud").jQCloud( word_list, {
    width: 450, height: 200
  });
});
</script>
<?php
  }

  $apiurl2 = $alchemyurl . 'URLGetRankedImageFaceTags?apikey=' . $apikey . '&outputMode=json&knowledgeGraph=1&url=' . urlencode( $url );
  $text2 = file_get_contents( $apiurl2 );
?>
<!-- $text2
<?php echo $text2; ?>
-->
<?php
  $json2 = json_decode( $text2 );

  $trans2 = $json2->totalTransactions;
  $imageFaces = $json2->imageFaces;
  if( count( $imageFaces ) ){
?>
<script type='text/javascript'>
<?php
    for( $i = 0; $i < count( $imageFaces ); $i ++ ){
      $imageFace = $imageFaces[$i];
      $positionX = $imageFace->positionX;
      $positionY = $imageFace->positionY;
      $width = $imageFace->width;
      $height = $imageFace->height;
      $xywh = $positionX . ',' . $positionY . ',' . $width . ',' . $height;
      $ageO = $imageFace->age;
      $ageRange = $ageO->ageRange;
      $ageScore = floatval( $ageO->score );
      $genderO = $imageFace->gender;
      $gender = $genderO->gender;
      $genderScore = floatval( $genderO->score );
      $name = '';
      $nameScore = 0;
      $identityO = $imageFace->identity;
      if( $identityO ){
        $name = $identityO->name;
        $nameScore = floatval( $identityO->score );
      }
?>
addMeta( '<?php echo $xywh; ?>', '<?php echo $ageRange; ?>', <?php echo $ageScore; ?>, '<?php echo $gender; ?>', <?php echo $genderScore; ?>, '', '<?php echo $name; ?>', <?php echo $nameScore; ?> );
<?php
    }
?>
</script>
<?php
  }
?>
  <!-- Tag Cloud -->
  <div id="tagcloud"></div>

  <!-- Canvas -->
  <canvas id='img' width='800' height='800'></canvas>

  <div align='right'>
  使ったトランザクション数：<?php echo $trans1; ?> + <?php echo $trans2; ?> = <?php echo ($trans1 + $trans2); ?>
  </div>
<?php
}
?>
</body>
</html>

