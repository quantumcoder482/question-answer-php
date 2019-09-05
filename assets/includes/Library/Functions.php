<?php
require_once("Loader.php");

function __autoload($class_name) {
	//$class_name = strtolower($class_name);
	$path = LIBRARY.DS."{$class_name}.php";
	if (file_exists($path)) {
		require_once($path);	
	} else {
		fatal_error("Class Error!","the class '{$class_name}' could not be found ! "); /* MUST CHANGE BEFORE UPLOAD! */
	} 
}

function strip_zeros_from_date( $marked_string="" ) {
  $no_zeros = str_replace('*0', '', $marked_string);
  $cleaned_string = str_replace('*', '', $no_zeros);
  return $cleaned_string;
}

function strip_to_numbers_only($string) {
    $pattern = '/[^0-9]/';
    return preg_replace($pattern, '', $string);
}

function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
}

function redirect_to_opener( $location = NULL) {
  if ($location != NULL) {
	echo "<script type=\"text/javascript\">
		window.opener.location.href = \"{$location}\";
		self.close();
	</script>";
  }
}

function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  { //to check ip is pass from proxy
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
 	} else {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

date_default_timezone_set('Africa/Cairo');


function date_to_ar($datetime="") {
  $unixdatetime = strtotime($datetime);
 return strftime("%Y-%m-%d ، الساعة %I:%M %p", $unixdatetime);
 
}

function date_to_eng($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%Y-%m-%d at %I:%M %p", $unixdatetime);
}

function time_only($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%I:%M %p", $unixdatetime);
}

function date_only($datetime="") {
	$unixdatetime = strtotime($datetime);
	return strftime("%Y-%m-%d", $unixdatetime);
}

function date_descriptive($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%B %d, %Y", $unixdatetime);
}
function day_name($datetime="") {
  $unixdatetime = strtotime($datetime);
  return strftime("%A", $unixdatetime);
}

function SplitSQL($file, $delimiter = ';') {
    set_time_limit(0);

    if (is_file($file) === true) {
        $file = fopen($file, 'r');

        if (is_resource($file) === true) {
            $query = array();

            while (feof($file) === false) {
                $query[] = fgets($file);

                if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1) {
                    $query = trim(implode('', $query));

                    if (mysqli_query($query) === false) {
                        echo '<h3>ERROR: ' . $query . '</h3>' . "\n";
                    }

                    //else
                    //{
                       //echo '<h3>SUCCESS: ' . $query . '</h3>' . "\n";
                    //}

                    /*while (ob_get_level() > 0)
                    {
                        ob_end_flush();
                    }*/

                    flush();
                }

                if (is_string($query) === true) {
                    $query = array();
                }
            }

            return fclose($file);
        }
    }

    return false;
}


function updateSQL($file, $delimiter = ';',$con) {
    set_time_limit(0);

    if (is_file($file) === true)
    {
        $file = fopen($file, 'r');

        if (is_resource($file) === true)
        {
            $query = array();

            while (feof($file) === false)
            {
                $query[] = fgets($file);

                if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1)
                {
                    $query = trim(implode('', $query));

                    //if (mysql_query($query) === false)
                    //{
                       // echo '<h3>ERROR: ' . $query . '</h3>' . "\n";
                    //}
					try {
					
						if (mysqli_query($con,$query)) {
							throw new Exception($query ." : Column already exists!<br/>");
						}
					}
					catch (Exception $e) {
						//echo $e->getMessage();
					}
					
                    //else
                    //{
                       //echo '<h3>SUCCESS: ' . $query . '</h3>' . "\n";
                    //}

                    while (ob_get_level() > 0)
                    {
                        ob_end_flush();
                    }

                    flush();
                }

                if (is_string($query) === true)
                {
                    $query = array();
                }
            }

            return fclose($file);
        }
    }

    return false;
}


function size_as_text($size) {
	if ($size < 1024 ) {
		$size_bytes = $size . " Bytes";
		return $size_bytes;
	} elseif ($size < 1048576 ) {
		$size_kb = round($size / 1024) . " KBs";
		return $size_kb;
	} else {
		$size_mb = round($size / 1048576 , 1 ) . " MBs";
		return $size_mb;			
		
	}
}


################################################
function calc_difference($newer_str, $older_str) {
	$older = new DateTime($older_str);
	$newer = new DateTime($newer_str);

  $Y1 = $older->format('Y'); 
  $Y2 = $newer->format('Y'); 
  $Y = $Y2 - $Y1; 

  $m1 = $older->format('m'); 
  $m2 = $newer->format('m'); 
  $m = $m2 - $m1; 

  $d1 = $older->format('d'); 
  $d2 = $newer->format('d'); 
  $d = $d2 - $d1; 

  $H1 = $older->format('H'); 
  $H2 = $newer->format('H'); 
  $H = $H2 - $H1; 

  $i1 = $older->format('i'); 
  $i2 = $newer->format('i'); 
  $i = $i2 - $i1; 

  $s1 = $older->format('s'); 
  $s2 = $newer->format('s'); 
  $s = $s2 - $s1; 

  if($s < 0) { 
    $i = $i -1; 
    $s = $s + 60; 
  } 
  if($i < 0) { 
    $H = $H - 1; 
    $i = $i + 60; 
  } 
  if($H < 0) { 
    $d = $d - 1; 
    $H = $H + 24; 
  } 
  if($d < 0) { 
    $m = $m - 1; 
    $d = $d + get_days_for_previous_month($m2, $Y2); 
  } 
  if($m < 0) { 
    $Y = $Y - 1; 
    $m = $m + 12; 
  } 
  $timespan_string = create_timespan_string($Y, $m, $d, $H, $i, $s); 
  return $timespan_string; 
} 

function get_days_for_previous_month($current_month, $current_year) { 
  $previous_month = $current_month - 1; 
  if($current_month == 1) { 
    $current_year = $current_year - 1; //going from January to previous December 
    $previous_month = 12; 
  } 
  if($previous_month == 11 || $previous_month == 9 || $previous_month == 6 || $previous_month == 4) { 
    return 30; 
  } 
  else if($previous_month == 2) { 
    if(($current_year % 4) == 0) { //remainder 0 for leap years 
      return 29; 
    } else { 
      return 28; 
    } 
  } else { 
    return 31; 
  } 
} 

function create_timespan_string($Y, $m, $d, $H, $i, $s) { 
  $timespan_string = array(); 
  //$found_first_diff = false; 
  $found_first_diff = true; 
  if($Y >= 1) {
    $found_first_diff = true; 
    $timespan_string['years']= $Y; 
  } else {
	$timespan_string['years']= 0; 
  }
  if($m >= 1 || $found_first_diff) { 
    $found_first_diff = true; 
    $timespan_string['months']= $m; 
  } 
  if($d >= 1 || $found_first_diff) { 
    $found_first_diff = true; 
    $timespan_string['days'] = $d; 
  } 
  if($H >= 1 || $found_first_diff) { 
    $found_first_diff = true; 
    $timespan_string['hours'] = $H; 
  } 
  if($i >= 1 || $found_first_diff) { 
    $found_first_diff = true; 
    $timespan_string['minuts'] = $i; 
  } 
  
  $timespan_string['seconds']= $s;
  return $timespan_string; 
} 
################################################



function calc_difference_months($date2,$date1) {

	$diff = abs(strtotime($date2) - strtotime($date1)); 
	$result = array();
	$result['months']  = floor($diff / (30*60*60*24));
	$result['months']  = floor($diff / 86400 / 30 );
	return $result;
}

####################################
function calc_difference_days($newer_str,$older_str) {

	$older = new DateTime($older_str);
	$newer = new DateTime($newer_str);

	$Y1 = $older->format('Y'); 
	$Y2 = $newer->format('Y');
	
	$z1 = $older->format('z'); 
	$z2 = $newer->format('z'); 
	$z = $z2 - $z1; 
	
	$mnth1= $older->format('m');
	$mnth2= $newer->format('m');
	
	if($mnth1 == "02" && $mnth2 > $mnth1) {
		$z+= 3;
	}
	
	if ($Y2 != $Y1) {
		$Y = $Y2 - $Y1;
		for ($i = 1 ; $i <= $Y ; $i++ ) {
			$z += 365;
		}
	}
	
	$result = array();
	
	//$result['days'] = $z + 1;
	$result['days'] = $z;
	return $result;
}	
####################################

function get_random($length=0) {
    $characters = str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) -1)];
    }
    return $string;
}

function get_random_num($length=0) {
    $characters = str_shuffle('0123456789');
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) -1)];
    }
    return $string;
}

###################################

function strtotimefix($val,$timestamp=0) {
	if($timestamp == 0) { $timestamp = time(); } 
	//$nval = $val * 31;
	//$strtotime = strtotime("{$nval} Days",$timestamp);
	
	$d = strftime("%d", $timestamp);
	$m = strftime("%m" , $timestamp);
	$y = strftime("%Y" , $timestamp);
	
	if ($m + $val >= 1 && $m + $val <= 12) {
		$new_m = $m + $val;
		$new_y = $y;
	} else {
		if($m + $val > 12) {
			$new_m = ($m + $val) - 12;
			$new_y = $y + 1;
		} elseif ($m + $val <= 1) {
			$new_m = ($m + $val) + 12;
			$new_y = $y - 1;
			
			if($new_m > 12) {
				$new_m -= 12;
				$new_y = $y;
			}
			
		} else {
			$new_m = $m;
			$new_y = $y;
		}	
	}
		
		if($d > "28") {
			$new_d = "28";
		} else {
			$new_d = $d;
		}
		
		$final_str = $new_d . "-" . $new_m . "-" . $new_y;
		
		$strtotime = strtotime($final_str);
		
	return $strtotime;
}


function convert_to_k($value) {
	global $cur_lang;
	if(isset($cur_lang) && $cur_lang == 'ar' ) { $ident = ' آلاف'; $ident2 = ' ملايين'; } else {$ident = 'k'; $ident2 = 'm';	}
	if ($value > 999 && $value <= 999999) {
		$result = floor($value / 1000) . $ident;
	} elseif ($value > 999999) {
		$result = floor($value / 1000000) . $ident2;
	} else {
		$result = $value;
	}
	
	return $result;
}



function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}


/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source https://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
$url = 'https://www.gravatar.com/avatar/';
$url .= md5( strtolower( trim( $email ) ) );
$url .= "?s=$s&d=$d&r=$r";
if( $img ) {
$url = '<img src="' . $url . '"';
foreach ( $atts as $key => $val )
$url .= ' ' . $key . '="' . $val . '"';
$url .= ' />';
}
return $url;
}

function slugify($text){
	// replace non letter or digits by -
	$text = preg_replace('~[^\pL\d]+~u', '-', $text);
	
	if (!preg_match('/[^A-Za-z0-9]/', $text)) {
		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);
		// trim
		$text = trim($text, '-');
		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);
		// lowercase
		$text = strtolower($text);		
	} else {
		// trim
		$text = trim($text, '-');
		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);
	}
	
	if (empty($text)) {
	return 'n-a';
	}
	return $text;
}

function date_ago($date) {
	$arr = calc_difference( strftime("%Y-%m-%d %H:%M:%S" , time()) , $date);
	$str = '';
	if($arr['years']) {
		$str .= $arr['years'] . " Year";
		if($arr['years'] > 1) { $str.= "s"; } $str .= ",";
	}
	if($arr['months']) {
		$str .= $arr['months'] . " Month";
		if($arr['months'] > 1) { $str.= "s"; } $str .= ",";
	}
	if($arr['days']) {
		$str .= $arr['days'] . " Day";
		if($arr['days'] > 1) { $str.= "s"; } $str .= ",";
	}
	if($arr['hours']) {
		$str .= $arr['hours'] . " Hour";
		if($arr['hours'] > 1) { $str.= "s"; }
	} elseif($arr['minuts']) {
		$str .= $arr['minuts'] . " Minute";
		if($arr['minuts'] > 1) { $str.= "s"; }
	}
	
	if($str) {
		$str = $str . " ago";
	} else {
		$str = 'Right Now';
	}
	
	if( $arr['days'] || $arr['months'] || $arr['years'] ) {
		$str = strftime("%d %b. %Y" , strtotime($date));
	}
	
	return $str;
}

function profanity_filter($str) {
	$filter = MiscFunction::get_function("profanity_filter");
    $bad= explode(",", $filter->value);
    $good= array('*****');
	$piece=explode(" ", $str);
    /*for($i=0;$i < sizeof($bad); $i++) {
        for($j=0;$j<sizeof($piece);$j++) {
            if(strtolower(trim($bad[$i])) == strtolower(trim($piece[$j]))) {
                $piece[$j]=" ***** ";
            }
        }
    }
    return implode(" ",$piece);*/
	
	return preg_replace_callback(
        '/(^|\b|\s)('.implode('|', $bad).')(\b|\s|$)/i',
        function ($matches) use ($good){
            return $matches[1].'*****'.$matches[3];
        },
        $str
	 );
}

/*function headers_for_page_cache($cache_length=600){
    $cache_expire_date = gmdate("D, d M Y H:i:s", time() + $cache_length);
    header("Expires: {$cache_expire_date}");
    header("Pragma: cache");
    header("Cache-Control: max-age={$cache_length}");
    header("User-Cache-Control: max-age={$cache_length}");
}

headers_for_page_cache();*/

?>