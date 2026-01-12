<?php

//$fn_in = "names.txt";

$fn_in = "anki_in.txt";
$fn_out = "anki_out.txt";

$fc = file_get_contents($fn_in);
$fa = explode("\n", $fc);
//echo "<pre>" . count($fa) . "| "; print_r($fa);

$fc_new = "";

function synonyms(string $str)
{
	
	$pattern = "#(<span style='color: navy'>.*?</span>)(.*)#s";
	$syn_pure = preg_match($pattern, $str, $matches);
	$syn_pure = synonyms_pure($matches[2]);
	//echo "<pre>"; print_r($matches);
	
	$str = preg_replace($pattern, "$2<br>$1", $str);
	$str = preg_replace("%<span style='color: navy'>%", '<span class="al-w-allmeans">', $str);
	//$str = str_ireplace("</li>", '', $str);
	//$str = str_ireplace("|", '<br>', $str);
	$arr["syn_pure"] = $syn_pure; 
	$arr["syn"] = $str;
	//echo "<pre>"; print_r($arr);
	return $arr;
	
}

function synonyms_pure($str)
{
	
	$pattern = "%<span style='color: #777'>(.*)</span>|\+%i";
	$str = preg_replace($pattern, "", $str);
	//echo "<pre>"; print_r($str);
	$arr = preg_match_all("%<b>(.*?)</b>%i", $str, $matches);
	//echo "<pre>"; print_r($matches);
	$syn_pure = $matches[1];
	$syn_pure = implode(", ", $syn_pure);
	return $syn_pure;
	
}

foreach ( $fa as $l ) {
	$arr = str_getcsv($l, "\t");
	//echo "<pre>"; print_r($arr);
	
	$front = $arr[0];
	$front_pure = trim($arr[0]);
	$front_pure = preg_replace("%1$|2$|3$|4$|5$%i", "", $front_pure);
	$front_pure = preg_replace("% n$| v$| v/n$| v/adj$| v/n/excl$| n/adj$| n/adv$| n/adj/adv$| n/excl$| v/n/adj$| v/n/adj/adv$| v/adj/adv$| adv/excl$| v/n/adv$| adj/adv$| adj$| adv$| phr$| conj$| pron$| prep$| excl$%i", "", trim($front_pure), -1, $tmp_c);
	//echo "<pre>"; print_r($front_pure);
	if ( $tmp_c < 1 ) {
		echo $front . "<br />";
	}
	
	if ( isset($arr[6]) ) {
		$back_pure = trim($arr[6]);
		$back_pure = preg_replace("%<li>|<br>|<br/>|<br />%i", "===", $back_pure);
		$back_pure = strip_tags($back_pure, "");
		$back_pure = preg_replace("%syn:|[a-zA-Z]%i", "", $back_pure);
		$back_pure = str_ireplace("===", "<br>", $back_pure);
		$back_pure = preg_replace("%^<br>|\.$|,$| ,| \.%i", "", trim($back_pure));
	} else { 
		$back_pure = "";
	}
	
	if ( isset($arr[10]) ) {
		$syn = trim($arr[10]);
		$syn_arr = synonyms($syn); 
	}
	//echo "<pre>"; print_r($syn_arr);
	
	unset($arr);
	
	$arr_new[$front]["front_pure"] = $front_pure;
	$arr_new[$front]["back_pure"] = $back_pure;
	$arr_new[$front]["syn_pure"] = $syn_arr["syn_pure"];
	$arr_new[$front]["syn"] = $syn_arr["syn"];
	
	$fc_new .= $front . "\t" . $front_pure . "\t" . $back_pure . "\t" . $syn_arr["syn_pure"] . "\t" . $syn_arr["syn"] . "\r\n";
}
//echo "<pre>"; print_r($arr_new);

file_put_contents($fn_out, $fc_new);


?>