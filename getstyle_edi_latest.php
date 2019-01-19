<?php
include("../includes/functions.php");
global $db;

//$json_file = file_get_contents('http://localhost/shirtchamp-demo1/addon/sc_products0.json');
$json_file = file_get_contents('http://bulkapparel.com/addon/json/stylesdata.json');
$jfo = json_decode($json_file,true);							
//$api_sync_call_data=json_decode(syncnow("products/?mediatype=json"));


if(isset($jfo)) {
/*print_r($jfo);
die();*/
foreach($jfo as $key=>$item){
$slug=slugify($item['brandName']." ".$item['styleName']." ".$item['title']);
$customTitle=$item['brandName']." ".$item['styleName']." ".$item['title'];
$pagemetatitle=$customTitle;
$pagemetakeywords=$customTitle;
$pagemetadescription=$customTitle;
$brandslug=slugify($item['brandName']);
$slugCategory=slugify($item['baseCategory']);

//echo $slug."<Br>";

$data = array ("styleID" => $item['styleID'],"slug" => $slug,"partNumber" => $item['partNumber'],"brandName" => $item['brandName'],"brandslug" => $brandslug,"styleName" => $item['styleName'],"title" => $item['title'],"customTitle" => $customTitle,"pagemetatitle" => $pagemetatitle,"pagemetakeywords" => $pagemetakeywords,"pagemetadescription" => $pagemetadescription,"description" => $item['description'],"baseCategory" => $item['baseCategory'],"slugCategory" => $slugCategory,"categories" => $item['categories'],"catalogPageNumber" => $item['catalogPageNumber'],"newStyle" => $item['newStyle'],"comparableGroup" => $item['comparableGroup'],"companionGroup" => $item['companionGroup'],"brandImage" => $item['brandImage'],"styleImage" => $item['styleImage']);
	
	$cid = $db->insert ('ci_styles_23', $data);
	//echo "Last executed query was ". $db->getLastQuery();
	//die();
	if($cid) {
	echo 'Style was created. Id=' . $cid."<Br>";
	}	
}
}



?>