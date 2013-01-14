<?php
/**
 * ------------------------------------------------------------------------------------------------------
 *
 * Script Name: getimg.php
 * @Rerelace: 2013/01/14
 * @About: GET方式で画像を指定すると指定した画像のタグを出力します
 * @Example: 【リクエスト例】getimg.php?img=./preview/130101/sample.png
 *
 * ------------------------------------------------------------------------------------------------------
 */

// エラー出力しない
ini_set('display_errors',0);
mb_language("Japanese");

//GET値 取得 :: htmlspecialcharsでhtmlをエスケープ、ENT_QUOTESのパラメータでシングルコーテーションもエスケープ
$imgsrc = htmlspecialchars($_GET["img"], ENT_QUOTES);
$imgsrc = mb_convert_encoding($imgsrc, "Shift_JIS","auto");

if($imgsrc == null){
	//GETに指定がなければNO IMGを読み込み
	$imgsrc = "./lib/img/notimg.png";
	echo '<img src="'.$imgsrc.'" width="260" />';
}else{
	//画像サイズ取得
	define('MAX_IMG_SIZE',320);
	list($width) = getimagesize($imgsrc);
	if( $width > MAX_IMG_SIZE){
		$new_width = "320";
	} else {
		$new_width = $width;
	}

	//出力
	$imgsrc = mb_convert_encoding($imgsrc, "UTF-8","auto");

	//画像のパスはhtmlから基準、リストの取得はphpファイル基準
	$imgsrc = str_replace("../preview/","./preview/",$imgsrc);
	echo '<img src="'.$imgsrc.'" width="'.$new_width.'" />';
}
?>