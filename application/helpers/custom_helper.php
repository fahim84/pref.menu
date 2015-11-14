<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function my_var_dump($string)
{		
	if(is_array($string) or is_object($string))
	{
		echo "<pre>";
		print_r($string);
		echo "</pre>";
	}
	elseif(is_string($string))
	{
		echo $string."<br>\n";
	}
	else
	{
		echo "<pre>";
		var_dump($string);
		echo "</pre>";
	}
}


function delete_file($path_and_filename)
{
	if(file_exists($path_and_filename))
	{
		if(is_file($path_and_filename))
		{
			if(unlink($path_and_filename))
			{
				return true;
			}
			else return false;
		}else return false;
	}else return false;
}

function isValidEmail($email)
{
	//return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email);
	
	if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) 
	{
    	//$msg = 'email is not valid';
		return false;
	}
	else
	{
		return true;
	}
}

function isValidURL($url)
{
	return preg_match('|^http(s)?://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}
function addhttp($url) 
{
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}
function my_redirect($url,$target='')
{
	echo "<script>window.parent.location=\"".$url."\"</script>";
}

function display_success_message()
{
	if(isset($_SESSION['msg_success']))
	{
		$errors	=	array();
		$numarray	=	array();
		$strarray	=	array();
		$string ="";
		$string2 ="";
		if(is_array($_SESSION['msg_success']))
		{
			foreach($_SESSION['msg_success'] as $msgvalue)
			{
					$strarray[]	=	$msgvalue;
			}
			$string	.=	implode("<br>",$strarray);
		}
		else
		{
			$string	.=	$_SESSION['msg_success'];
		}

		unset($_SESSION['msg_success']);
		return "$string";
	}
	else
	{
		return "";
	}	
}
function display_error()
{
	if(isset($_SESSION['msg_error']))
	{
		$errors	=	array();
		$numarray	=	array();
		$strarray	=	array();
		$string ="";
		$string2 ="";
		if(is_array($_SESSION['msg_error']))
		{
			foreach($_SESSION['msg_error'] as $msgvalue)
			{
					$strarray[]	=	$msgvalue;
			}
			$string	.=	implode("<br>",$strarray);
		}
		else
		{
			$string	.=	$_SESSION['msg_error'];
		}

		unset($_SESSION['msg_error']);
		return "$string";
	}
	else
	{
		return "";
	}	
}

function make_table($data,$columns,$table_class = 'make_table',$tr_class = 'make_table_tr', $td_class = 'make_table_td',$default_value='&nbsp;')
{
	#################### REQUIRED INPUT ####################
	/*
	1. REQUIRED
	$data should be an array, and array key must be 
	an integer starting with 0 and must contain 
	further iteration in sequence. For example
	
	$data[0] = "any value";
	$data[1] = "any value";
	$data[2] = "any value";
	$data[3] = "any value";
	
	2. REQUIRED
	$columns must be a variable
	$columns must have integer value greater than 0
	*/
	#################### REQUIRED INPUT ####################

	$no_of_cells = count($data);
	$no_of_rows = ceil($no_of_cells/$columns);
	$no_of_total_cells = $columns*$no_of_rows;
	$extra_cells = $no_of_total_cells-$no_of_cells;
	
	#################### SUMMARY FOR DEBUGGING ####################
	#	echo "Number of columns: $columns<br>";
	#	echo "Number of rows: $no_of_rows<br>";
	#	echo "Number of data Cells: $no_of_cells<br>";
	#	echo "Number of Extra Cells: $extra_cells<br>";
	#	echo "Number of Total Cells: $no_of_total_cells<br>";
	#################### SUMMARY FOR DEBUGGING ####################
	
	$key = 0;	# THIS VARIABLE WILL BE INCREMENTED ON EARCH CELL

	$HTML = "<table class=\"$table_class\" >";
	for($i=0;$i<$no_of_rows;$i++)	# THIS LOOP WILL GENERATE TABLE ROWS
	{
		$HTML .= "<tr class=\"$tr_class\">";	# START TABLE ROW
		
		for($j=0;$j<$columns;$j++)	# THIS LOOP WILL GENERATE TABLE CELLS
		{
			if(isset($data[$key]))	# IF DATA CELL EXISTS
			{
				$HTML .= "<td class=\"$td_class\">";	# START TABLE CELL
				$HTML .= $data[$key];
				$HTML .= "</td>";	# END TABLE CELL
			}
			else
			{
				$HTML .= "<td class=\"$td_class\">";	# START TABLE CELL
				$HTML .= $default_value;	# $data[$key];
				$HTML .= "</td>";	# END TABLE CELL			
			}
			$key++;
		}
		
		$HTML .= "</tr>";	# END TABLE ROW
	}
	$HTML .= "</table>";
	
	
	#echo $HTML;
	return $HTML;
}

function handle_post_request_from_angularjs()
{
	if(isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
		$_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
	}
}

function get_business_types()
{
	$business_type[1] = "Casual Dining";
	$business_type[2] = "Quick Bites";
	$business_type[3] = "Cafe";
	$business_type[4] = "Fine Dining";
	$business_type[5] = "Dessert Parlor";
	$business_type[6] = "Cafeteria";
	$business_type[7] = "Bar";
	$business_type[8] = "Lounge";
	$business_type[9] = "Bakery";
	$business_type[10] = "Food Court";
	$business_type[11] = "Beverage Shop";
	$business_type[12] = "Pub";
	$business_type[13] = "Club";
	return $business_type;
}

function get_tables()
{
	$table[1] = "Table One";
	$table[2] = "Table Two";
	$table[3] = "Table Three";
	$table[4] = "Table Four";
	$table[5] = "Table Five";
	$table[6] = "Table Six";
	$table[7] = "Table Seven";
	$table[8] = "Table Eight";
	$table[9] = "Table Nine";
	$table[10] = "Table Ten";
	$table[11] = "Table Eleven";
	$table[12] = "Table Twelve";
	$table[13] = "Table Thirteen";
	$table[14] = "Table Fourteen";
	$table[15] = "Table Fifteen";
	$table[16] = "Table Sixteen";
	$table[17] = "Table Seventeen";
	$table[18] = "Table Eighteen";
	$table[19] = "Table Nineteen";
	$table[20] = "Table Twenty";
	$table[21] = "Table Twenty One";
	$table[22] = "Table Twenty Two";
	$table[23] = "Table Twenty Three";
	$table[24] = "Table Twenty Four";
	$table[25] = "Table Twenty Five";
	$table[26] = "Table Twenty Six";
	$table[27] = "Table Twenty Seven";
	$table[28] = "Table Twenty Eight";
	$table[29] = "Table Twenty Nine";
	$table[30] = "Table Thirty";
	$table[31] = "Table Thirty One";
	$table[32] = "Table Thirty Two";
	$table[33] = "Table Thirty Three";
	$table[34] = "Table Thirty Four";
	$table[35] = "Table Thirty Five";
	$table[36] = "Table Thirty Six";
	$table[37] = "Table Thirty Seven";
	$table[38] = "Table Thirty Eight";
	$table[39] = "Table Thirty Nine";
	$table[40] = "Table Fourty";
	$table[41] = "Table Fourty One";
	$table[42] = "Table Fourty Two";
	$table[43] = "Table Fourty Three";
	$table[44] = "Table Fourty Four";
	$table[45] = "Table Fourty Five";
	$table[46] = "Table Fourty Six";
	$table[47] = "Table Fourty Seven";
	$table[48] = "Table Fourty Eight";
	$table[49] = "Table Fourty Nine";
	$table[50] = "Table Fifty";
	return $table;
}

function get_come_again_options()
{
	$come_again_option[1] = 'Yes';
	$come_again_option[2] = 'No';
	$come_again_option[3] = 'May be';
	return $come_again_option;
}

function get_regions()
{
	$regions[1] = 'I am a tourist';
	$regions[2] = 'Abu Hail';
	$regions[3] = 'Acacia Avenues';
	$regions[4] = 'Al Barari';
	$regions[5] = 'Al Barsha';
	$regions[6] = 'Al Furjan';
	$regions[7] = 'Al Jadaf';
	$regions[8] = 'Al Jafliya';
	$regions[9] = 'Al Khwaneej';
	$regions[10] = 'Al Mamzar';
	$regions[11] = 'Al Mezhar';
	$regions[12] = 'Al Muhaisna';
	$regions[13] = 'Al Nahda';
	$regions[14] = 'Al Quoz';
	$regions[15] = 'Al Qusias';
	$regions[16] = 'Arabian Ranches';
	$regions[17] = 'Bur Dubai';
	$regions[18] = 'Burj Khalifa';
	$regions[19] = 'Business Bay';
	$regions[20] = 'City of Arabia';
	$regions[21] = 'Culture Village';
	$regions[22] = 'Deira';
	$regions[23] = 'DIFC';
	$regions[24] = 'Discovery Garden';
	$regions[25] = 'Downtown Dubai';
	$regions[26] = 'Downtown Jebel Ali';
	$regions[27] = 'Dubai Biotech';
	$regions[28] = 'Dubai Festival City';
	$regions[29] = 'Dubai Industrial City';
	$regions[30] = 'Dubai Investment Park';
	$regions[31] = 'Dubai Lagoons';
	$regions[32] = 'Dubai Land';
	$regions[33] = 'Dubai Marina';
	$regions[34] = 'Dubai Media City';
	$regions[35] = 'Dubai Promenade';
	$regions[36] = 'Dubai Silicon Oasis';
	$regions[37] = 'Dubai Sports City';
	$regions[38] = 'Dubai Waterfront';
	$regions[39] = 'Dubai World Central';
	$regions[40] = 'Emirates Hills';
	$regions[41] = 'Emirates Towers';
	$regions[42] = 'Falcon City of Wonders';
	$regions[43] = 'Garhoud';
	$regions[44] = 'Green Community';
	$regions[45] = 'Greens';
	$regions[46] = 'IMPZ';
	$regions[47] = 'International City';
	$regions[48] = 'Internet City';
	$regions[49] = 'JBR Jumeirah Beach Residence';
	$regions[50] = 'Jebel Ali';
	$regions[51] = 'JLT Jumeirah Lakes Tower';
	$regions[52] = 'Jumeirah';
	$regions[53] = 'Jumeirah Golf Estate';
	$regions[54] = 'Jumeirah Heights';
	$regions[55] = 'Jumeirah Island';
	$regions[56] = 'Jumeirah Park';
	$regions[57] = 'Jumeirah Village';
	$regions[58] = 'JVC Jumeirah Village Circle';
	$regions[59] = 'JVS Jumierah Village South';
	$regions[60] = 'JVT Jumeirah Village Triangle';
	$regions[61] = 'Karama';
	$regions[62] = 'Mankhool';
	$regions[63] = 'Maritime City';
	$regions[64] = 'Meadows';
	$regions[65] = 'Meydan City';
	$regions[66] = 'Motor City';
	$regions[67] = 'Mushrif Park';
	$regions[68] = 'Old Town';
	$regions[69] = 'Oud Metha';
	$regions[70] = 'Palm Jebel Ali';
	$regions[71] = 'Palm Jumeirah';
	$regions[72] = 'Sheikh Zayed Road';
	$regions[73] = 'Tecom';
	$regions[74] = 'The Lagoons';
	$regions[75] = 'The Lakes';
	$regions[76] = 'The Lakes';
	$regions[77] = 'The Springs';
	$regions[78] = 'The Views';
	$regions[79] = 'Umm Suqeim';
	$regions[80] = 'Victory Heights';
	$regions[81] = 'World Trade Center';
	return $regions;
}

function references()
{
	$references[1] = 'Walking Past';
	$references[2] = 'Friend';
	$references[3] = 'Internet';
	$references[4] = 'Flyer';
	$references[5] = 'Newspaper';
	$references[6] = 'TV';
	return $references;
}
function get_email_message_with_wrapper($message)
{
	$MailHTML = '<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Notification Email</title>
		<style>
		* {
			margin:0px;
			padding:0px;
		}
		body {
			font-family:Arial, Helvetica, sans-serif;
			font-size:12px;
			color:#000;
		}
		a, a:hover {
			color:#000000;
		}
		td {
			padding:5px;
		}
		</style>
		</head>
		<body>
		<div>'.$message.'<br /><br /><br /><strong>'.EMAIL_NOTIFICATION_FOOTER.'</strong></div>
		</body>
		</html>';
		
		return $MailHTML;
}

function resize_image2($url, $newWidth='', $newHeight='', $Base='')
{
	list($iw, $ih, $imageType) = getimagesize($url);
	$imageType = image_type_to_mime_type($imageType);
	
	switch($imageType)
	{
		case "image/gif":
			$image = imagecreatefromgif($url);
		break;
		
		case "image/pjpeg":
			$image = imagecreatefromjpeg($url);
		break;
		
		case "image/jpeg":
			$image = imagecreatefromjpeg($url);
		break;
		
		case "image/jpg":
			$image = imagecreatefromjpeg($url);
		break;
		
		case "image/png":
			$image = imagecreatefrompng($url);
		break;
		
		case "image/x-png":
			$image = imagecreatefrompng($url);
		break;
	}
	
	$orig_width = imagesx($image);
	$orig_height = imagesy($image);
	
	if($Base=='W')
	{
		$width = $newWidth;
		$height = (($orig_height * $newWidth) / $orig_width);
		$new_image = imagecreatetruecolor($newWidth, $height);
	}
	else if($Base=='H')
	{
		$width = (($orig_width * $newHeight) / $orig_height);
		$height = $newHeight;
		$new_image = imagecreatetruecolor($width, $newHeight);
	}
	
	imagecopyresized($new_image, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
	
	switch($imageType)
	{
		case "image/gif":
			imagegif($new_image, $url);
		break;
		
		case "image/pjpeg":
			imagejpeg($new_image, $url, 100);
		break;
		
		case "image/jpeg":
			imagejpeg($new_image, $url, 100);
		break;
		
		case "image/jpg":
			imagejpeg($new_image, $url, 100); 
		break;
		
		case "image/png":
			imagepng($new_image, $url);
		break;
		
		case "image/x-png":
			imagepng($new_image, $url);
		break;
	}
		
		
	
}

//You do not need to alter these functions
function get_height($image)
{
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
}

//You do not need to alter these functions
function get_width($image)
{
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
}

function generate_password($length)
{
	$password = "";
	$possible = "0123456789abcdfghijklmnopqrstuvwxyz";
	$i = 0;
	while($i < $length)
	{
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		if (!strstr($password, $char))
		{
			$password .= $char;
			$i++;
		}
	}
	return $password;
}

function reset_survey()
{
	unset($_SESSION[SURVEY_COUNT_TABLE]);
	unset($_SESSION[CURRENT_SURVEY]);
	unset($_SESSION[SURVEY_ORDER]);
	unset($_SESSION[SURVEY_LOOP]);
	unset($_SESSION[SURVEY_TABLE]);
	unset($_SESSION[SURVEY_ITEMS]);
}

function client_logo()
{
	$Logo = $_SESSION[USER_LOGIN]["logo"];
	if($Logo!='')
	{
		$Path = base_url().UPLOADS."/".$Logo;
		$Image = base_url()."thumb.php?src=".$Path."&w=100&h=100";
		echo '<a href="'.$Path.'" data-lightbox="roadtrip" data-title="'.$Logo.'">
		<img class="menu-dish menu-dish-flexible" src="'.$Image.'" alt="'.$Logo.'" /></a>';
	}
}

function truncate_string($Str, $limit, $Dots='...')
{
	if(strlen($Str)>$limit)
	{
		$return = substr($Str, 0, $limit).$Dots;
		return $return;
	}
	else
	{
		return $Str;
	}
}