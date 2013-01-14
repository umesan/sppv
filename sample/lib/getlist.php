<?php
/**
 * ------------------------------------------------------------------------------------------------------
 *
 * Script Name: getlist.php
 * @Rerelace: 2013/01/14
 * @About: GET方式でディレクトリを指定すると指定したディレクトリ以下のファイル一覧を取得します
 * @Example: 【リクエスト例】getlist.php?dir=./preview/130101/
 *
 * ------------------------------------------------------------------------------------------------------
 */

// エラー出力しない
ini_set('display_errors',0);
mb_language("Japanese");

//GET値 取得 :: htmlspecialcharsでhtmlをエスケープ、ENT_QUOTESのパラメータでシングルコーテーションもエスケープ
if(isset($_GET["dir"]) && $_GET["dir"]!=""){
	$dir = htmlspecialchars($_GET["dir"], ENT_QUOTES);
	$dir = mb_convert_encoding($dir, "Shift_JIS","auto");
}else{
	//GETに指定がなければルートディレクトリを読み込み
	$dir = "../preview";
}

$tree = getdirtree($dir);


/**
 * -------------------------------------------------------------------------------
 * 
 * [getdirtree 指定したディレクトリ以下のファイル一覧を取得]
 * @param  [type] $path [ディレクトリを示す文字列]
 * @return [array] ファイル一覧を格納した配列
 * @reference [url]http://www.sound-uz.jp/php/note/dirTree
 *
 * -------------------------------------------------------------------------------
 */
function getdirtree ( $path ){

	// ディレクトリでなければ false を返す
	if (!is_dir($path)) {
		return false;
	}

	// 戻り値用の配列
	$dir = array();

	if ($handle = opendir($path)) {

		while (false !== ($file = readdir($handle))) {

			//$file = mb_convert_encoding($file, "SJIS","auto");

			// 自分自身と上位階層のディレクトリを除外
			if ('.' == $file || '..' == $file || 'Thumbs.db' == $file) {
				continue;
			}

			if (is_dir($path.'/'.$file)) {

				// ディレクトリならば自分自身を呼び出し
				//$dir[$file] = getdirtree($path.'/'.$file);
				//$dir[$file] = $path.'/'.$file.'/';
				$dir[$file] = $path.'/'.$file;

			} elseif (is_file($path.'/'.$file)) {
				// ファイルならばパスを格納
				$dir[$file] = $path.'/'.$file;
			}
		}
		closedir($handle);
	}
	return $dir;
}

//連想配列キーソート(昇順)
ksort($tree);
//print_r($tree);

$imgcount = count($tree);
$allviewlist = array();
$num = 0;

//echo $imgcount;
//printf(key($tree));

while (current($tree)) {

	//パス取得
	$imgsrc = $tree[key($tree)];

	//表示用
	$imgsrc2 = key($tree);
	$imgsrc2 = mb_convert_encoding($imgsrc2, "UTF-8","auto");

	//拡張子取得
	$ext = substr($imgsrc, strrpos($imgsrc, '.') + 1);
	if($ext == "jpg" || $ext == "gif" || $ext == "png" || $ext == "JPG" || $ext == "GIF" || $ext == "PNG" || $ext == "jpeg"){

		//画像サイズ取得
		$get = file_get_contents($imgsrc);
		$size = ceil(strlen($get)/1024);

		//更新日取得
		$mod = filemtime($imgsrc);

		if( !defined('MAX_IMG_SIZE') ){
			define('MAX_IMG_SIZE',320);
		}

		//define('MAX_IMG_SIZE',320);

		list($width,$height) = getimagesize($imgsrc);
		if( $width > MAX_IMG_SIZE){
			$new_width = "320";
		} else {
			$new_width = $width;
		}

		$imgsrc = mb_convert_encoding($imgsrc, "UTF-8","auto");
		echo '<li class= "img"><a href="'.$imgsrc.'" title="'.$new_width.'">'.$imgsrc2.'<span>'.$size.'KB - '.date("Y/m/d H:i",$mod).' - '.$width.'×'.$height.'</span></a></li>'."\n";

	}
	elseif($ext == "txt" || $ext == "html" || $ext == "htm"){
		//ファイルサイズ取得
		$get = file_get_contents($imgsrc);
		$size = ceil(strlen($get)/1024);
		//更新日取得
		$mod = filemtime($imgsrc);
		$imgsrc = mb_convert_encoding($imgsrc, "UTF-8","auto");
		echo '<li class= "html"><a href="'.$imgsrc.'">'.$imgsrc2.'<span>'.$size.'KB, '.date("Y/m/d H:i",$mod).'</span></a></li>'."\n";
	}
	elseif($ext == "pdf"){
		//ファイルサイズ取得
		$get = file_get_contents($imgsrc);
		$size = ceil(strlen($get)/1024);
		//更新日取得
		$mod = filemtime($imgsrc);
		$imgsrc = mb_convert_encoding($imgsrc, "UTF-8","auto");
		$imgsrc = str_replace("../preview/","./preview/",$imgsrc);
		echo '<li class= "'.$ext.'"><a href="'.$imgsrc.'" target="_blank">'.$imgsrc2.'<span>'.$size.'KB, '.date("Y/m/d H:i",$mod).'</span></a></li>'."\n";
	}
	else{
		$imgsrc = mb_convert_encoding($imgsrc, "UTF-8","auto");
		echo '<li class="dir"><a href="'.$imgsrc.'">'.$imgsrc2.'</a></li>'."\n";
	}

	$num++;
	next($tree);
}

?>