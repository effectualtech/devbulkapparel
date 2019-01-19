<?php 
define('PRODUCT_IMAGES','http://bulkapparel.com/',true);
define('BASE_PATH','http://bulkapparel.com/',true);
define('SYMBOL','$',true);
function base_url($params){
	return PRODUCT_IMAGES.($params!=''?$params:'');
}


function getTopmenu(){
	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
	$sql="select * from `ci_category_local` where `status`='1' order by `displayOrder` ASC";
	$result = mysqli_query($con,$sql);
	$row_cnt = mysqli_num_rows($result); 
	// Fetch all
	$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
	// Free result set
	mysqli_free_result($result);
 	foreach($arr as $k=>$val) {
			 $sql2="select * from `ci_category_local_map` where `mappedSubStatus`='1' and `mappedSubCatLiveParentId`='".$val['catEDIid']."'";
			$result1 = mysqli_query($con,$sql2);
 			$row_cnt1 = mysqli_num_rows($result1); 
			// Fetch all
			$arr1=mysqli_fetch_all($result1,MYSQLI_ASSOC);
 			foreach($arr1 as $k1=>$val1) {
		 		$arr[$k]['subcat'][]=$val1;
			}
	} 
	return $arr;	    
}


function createHtml(){
	$html="";
	foreach(getTopmenu() as $k=>$val){
		$html.='<li><a href="http://bulkapparel.com/customCode/index.php?cat='.$val['catEDIid'].'">'.$val['name'].'</a>';
		if(!empty($val['subcat'])){
			$html.='<span><img src="http://bulkapparel.com/public/front/images/subnav-indicator.png" alt="submenu"></span><ul>';
			foreach($val['subcat'] as $key1=>$val1){
				$html.='<li><a href="http://bulkapparel.com/customCode/index.php?cat='.$val1['mappedSubCatId'].'_'.$val1['mappedSubCatLiveParentId'].'">'.$val1['mappedSubCatName'].'</a>';		
			}
			$html.='</ul>';
		} 
		$html.='</li>';   
	}
return $html;
}

 
function showallbrands(){
	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
	$sql="select * from `ci_styles` group by brandName";
	$result = mysqli_query($con,$sql);
	$row_cnt = mysqli_num_rows($result); 
	$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
	$res=array();
	foreach($arr as $k1=>$val) 
	{
		$res[$val['styleID']]=$val['brandName'];	
	}
return  $res;
}
 
function ci_get_userdata($item=''){
	session_start();
	if($item!=''):
		return $_SESSION[$item];		
	else:
		return $_SESSION;		
	endif;
}	



function fetchSingleCat($ids){
	 
	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
	$sql="select * from `ci_category`  where `categoryID`='".$ids."'";
	$result = mysqli_query($con,$sql);
	$row_cnt = mysqli_num_rows($result); 
	if($row_cnt>0){
		$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
			foreach($arr as $k1=>$val) 
			{
				return $val['name'];
			}
		} else {
			return false;
	}
}		
	
	
function get_weight_catId($id){
	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
	$sql="select * from `ci_category`  where `categoryID`='".$id."'";
	$result = mysqli_query($con,$sql);
	$row_cnt = mysqli_num_rows($result); 
	if($row_cnt>0){
		$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
		foreach($arr as $k1=>$val) 
			{
				return $val['name'];
			}
	 
		}  else {
			return false;	
		} 
	}		
	
	
	
function get_attach_categoriesSort(){
 
	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
	$sql="select * from `ci_category_local`  where `showOnHome`='1' order by homepageLeftsideDisporder ASC";  
	$result = mysqli_query($con,$sql);
	$row_cnt = mysqli_num_rows($result); 
	if($row_cnt>0){
		$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
		return $arr;		
	} else {
		return false; 
	}
}
	
	
	
		
	
	//@get all category total from DB
	function get_category_total($categoryID,$where1=false){
			$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
			if($where1!=false){
			$whereConditions=$where1;
			if(strrpos($whereConditions,"FIND_IN_SET(".$categoryID.", st.categories)")==false){
				$whereConditions.=" and FIND_IN_SET(".$categoryID.", st.categories)";
			}  
   		 	$sql="SELECT count(*) as total FROM `ci_styles` as st inner join ci_products as pd on pd.styleID=st.styleID ".$whereConditions." group by pd.styleID order by st.styleID ASC";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			} else {
				return 0;	
			}
		} else {
			$sql="SELECT count(*) as total FROM `ci_styles` WHERE FIND_IN_SET(".$categoryID.",categories)";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
				} else {
				return 0;	
			} 
		}
	}
	
	
	
function filtersOrders(){
	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
 	$sql="SELECT * FROM `ci_site_filter_order` order by `FilterOrder` ASC";
		$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
		$arr=array();
		if($row_cnt>0){
			$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
			return $arr;
		} else {
			return 0;
		}
}
 
 function get_shopfor(){
	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
	$sql="SELECT shop.*,cat.name,cat.categoryID FROM `ci_category` as cat inner join  ci_shop_for_filters as shop on shop.eid=cat.categoryID";
	$result = mysqli_query($con,$sql);
	$row_cnt = mysqli_num_rows($result); 
	if($row_cnt>0){
		$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
		 return $arr;
	} else {
		return false;	
	}
}


//@get all shopfor total from DB
	function get_shopfor_total($titlename,$where1=false){
	 	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");

		if($where1!=false)
		{
 			$whereConditions=$where1;
			if(strpos($whereConditions,"FIND_IN_SET($titlename, st.categories)")==false){
				$whereConditions.=" and FIND_IN_SET($titlename, st.categories)";
			} 
	 
			 
			$sql="SELECT count(*) as total FROM `ci_styles` as st inner join ci_products as pd on pd.styleID=st.styleID ".$whereConditions." group by pd.styleID order by st.styleID ASC";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			} else {
				return 0;	
			}
		} 
		else 
		{
		 	$sql="SELECT count(*) as total FROM `ci_styles` WHERE FIND_IN_SET(".$titlename.",categories)";
			$result = mysqli_query($con,$sql);
			 $row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
			 
				foreach($arr as $k1=>$val) 
				{
					 
					return $val['total'];
				}
				} else {
				return 0;	
			} 
		}
 	}
	
	
		//@get all brands from DB
	function get_all_brands(){
			$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
			$sql="SELECT count(*) as total,brandName FROM ci_styles  group by brandName";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				 return $arr;
			} else {
				return false;
			}	
	}
	
	
	//@get all brand total from DB
	function get_brand_total($barnds,$where1=false){
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
			if($where1!=false){
			$whereConditions=$where1;
			if(strrpos($whereConditions,"st.brandName like '%".$barnds."%'")==false){
				$whereConditions.=" and st.brandName like '%".$barnds."%'";
			} 
			$sql="SELECT count(*) as total FROM `ci_styles` as st inner join ci_products as pd on pd.styleID=st.styleID ".$whereConditions." group by pd.styleID order by st.styleID ASC";
			
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			} else {
				return 0;	
			}
		} else {
			$sql="SELECT count(*) as total FROM `ci_styles` WHERE brandName like '%".$barnds."%'";
		 	$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			} else {
				return 0;	
			} 
		}
	}
	
		function load_all_fabrics(){
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
			$sql="SELECT * FROM ci_category  where name IN ('Bamboo','Blends','Burnouts','Canvas','Cashmere','Chenille','Corduroy','Cotton - 100%','Cotton - Combed','Cotton - Organic','Cotton - Over 50%','Cotton - Polyester (50/50)','Cotton - Ringspun','Denim','Dobby','Down','Eco-Friendly','Flannels','French Terry','Gingham','Jersey','Lycra','Mesh','Micro Fleece','Non Woven','Nylon','Organic','Performance','Pique','Plaid','Polyester','Polyester - 50%','Polyester - Over 50%','Poplin','PVC','Rayon','Recycled','Ribbed','Ripstop','Sherpa','Slub','Spandex','Stripes','Thermals','Triblends','Tricot','Viscose')";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				 return $arr;
			} else {
				return false;
			}	
	}



function get_fabric_total($fabricid,$where1=false){
			$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
	if($where1!=false){
			$whereConditions=$where1;
			if(strrpos($whereConditions,"FIND_IN_SET($fabricid, st.categories)")==false){
				$whereConditions.=" and FIND_IN_SET($fabricid, st.categories)";
			} 
			$sql="SELECT count(*) as total FROM `ci_styles` as st inner join ci_products as pd on pd.styleID=st.styleID ".$whereConditions." group by pd.styleID order by st.styleID ASC";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}else {
				return 0;	
			}
		} else {
			$sql="SELECT count(*) as total FROM `ci_styles` WHERE FIND_IN_SET($fabricid, categories)"; 
				$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	
			} 
		}

	} 
	
	
	 function get_product_color(){
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
		$sql="select color1,colorFamily from ci_products group by colorFamily";
		$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
 		if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				 return $arr;
		} else {		
			return false;	
		}
	}
	
	
	function get_color_total($color,$where1=false){
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
		$whereConditions=$where1;
		if($where1!=false){
			if(strrpos($whereConditions,"pd.colorFamily like '".$color."'")==false){
 				$whereConditions.=" and pd.colorFamily like '".$color."'";
			}  
			$sql="SELECT count(*) as total FROM `ci_products` as pd inner join ci_styles as st on st.styleID=pd.styleID ".$whereConditions." group by pd.styleID order by st.styleID ASC";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}else {
				return 0;	
			}
		} else {
 			$sql="SELECT count(*) as total FROM `ci_products` WHERE colorFamily like '".$color."'";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	
			} 
		}
	}
	
	
	
	//@get all sizes from DB
	function get_product_sizes(){
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
	 	$sql="select sizeName from ci_products where sizeName IN('Adjustable','One Size','XXS','Youth','XS','S','M','F59L','XL','2XL','3XL','4XL','5XL','6XL','XS/S','S/M','M/L','L/XL','2X/','2XL/3XL','XL/2XL','4XL/5XL','LT','XLT','2XLT','3XLT','4XLT','10H','14H','18H','30','30W or 50W','NB','32','34','36','2T','38','38 or 58','40','42','44','46','48','50','2','52','54','56','2T/3T','3T','4','3/6','4T','5','5 - XS','5/6','5T','6','6 - S','6/12','6M','6T','7 - S','7','8','8 - M','10','12','12 - L','12/18','12M ,14','14.5','15','15.5','16','16 - XL','18/24','18M','16.5','17','17.5','18','18 - XXL','18.5','19.5','20','20 - XXL','20.5','22','24','24M','28W','30W','32W','34W','36W','38W','40W','42W','44W','46W','48W','50W','1 - 14/16','2 - 18/20','3 - 22/24','4 - 26/28','5 - 30/32') group by sizeName";
		$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
 		if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				 return $arr;
		} else {
			return 0;	
		}
	}
	
	
	//@get all sizes total from DB	
	function get_size_total($sizes,$where1=false){
	 
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
		if($where1!=false){
			$whereConditions=$where1;
			if(strrpos($whereConditions,"pd.sizeName = '".$sizes."'")==false){
				$whereConditions.=" and pd.sizeName = '".$sizes."'";
			} 
			$sql="SELECT count(*) as total FROM `ci_products` as pd inner join ci_styles as st on st.styleID=pd.styleID ".$whereConditions." group by pd.styleID order by st.styleID ASC";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	
			}
		} else {
			  $sql="SELECT count(*) as total FROM `ci_products` WHERE sizeName = '".$sizes."'";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	
			} 
		}
	}
	
	function get_allcustomstyles(){
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
		$sql="select * from ci_category WHERE name IN('Adjustable','Adult','Cropped','Fitted','Flowy','Girls','High Profiles','Infants / Toddlers','Juniors','Low Profiles','Mens','Mid Profiles','Missy','One Size','Relaxed','Side Seams,Talls','Tubular,Unisex','Womens,Youth') order by name ASC";
		$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
 		if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				 return $arr;
		} else {
			return false;	
		}
	}
	
	
	
		//@get all styles total from DB
	function get_customstyle_total($categoryID,$where1=false){
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
		if($where1!=false){
			$whereConditions=$where1;
			if(strrpos($whereConditions,"FIND_IN_SET($categoryID, st.categories)")==false){
				$whereConditions.=" and FIND_IN_SET($categoryID, st.categories)";
			} 
			$sql="SELECT count(*) as total FROM `ci_styles` as st inner join ci_products as pd on pd.styleID=st.styleID ".$whereConditions." group by pd.styleID order by st.styleID ASC";
			$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	 
			}
		} else {
			$sql="SELECT count(*) as total FROM `ci_styles`  WHERE FIND_IN_SET($categoryID, categories)"; 
				$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	
			} 
		}
	}
	
		function get_weight_cat(){ 
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
		  $sql="select * from ci_category where name like '%oz)%'";
		$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
 		if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				 return $arr;
		} else {
			return false;	
		}
	} 
	
	
	//@get all sizes total from DB	
	function get_weight_total($weight,$where1=false){
				$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
if($where1!=false){
			$whereConditions=$where1;
			if(strrpos($whereConditions,"FIND_IN_SET($weight, st.categories)")==false){
				$whereConditions.=" and FIND_IN_SET($weight, st.categories)";
			} 
			$sql="SELECT count(*) as total FROM `ci_styles` as st inner join ci_products as pd on pd.styleID=st.styleID ".$whereConditions." group by pd.styleID order by st.styleID ASC";
				$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	
			}
		} else {
			$sql="SELECT count(*) as total FROM `ci_styles` WHERE FIND_IN_SET($weight, categories)"; 
					$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	
			} 
		}
	} 
	
	
	 function load_all_fit(){
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
		$sql="select * from ci_category where name in('3/4 Sleeve','Accessories','Advantage','American Made','Aprons','Athletics','Backpacks','Bags','Beanies','Bibs','Blankets','Bowling Shirts','Bras','Bucket','Camisoles','Camouflage','Capris','Chairs','Chambray','Cinch','Clear','Coolers','Cozy / Coozies','Cuffed / Cuffs','Digital','Dog Wear','Drawstrings','Dress Shirts','Dresses','Duffels','Five-Panel','Flat Bills','Fleece','Full-Zips','Gloves','Golf','Gusset','Headwear','Henley','High Visibility','Hooded / Hoods','Jackets','Jumpers','Kissing Zippers','Knit','Kryptek','Leggings','Long Sleeves','Loungewear','Media Pocket','Mesh Back','Messengers','Mossy Oak','Muddy Girl','Neons','Oilfield','Onesies','Open Backs','Open Bottoms','Outerwear','Packables','Pants','Plackets','Pockets','Polos','Ponchos','Pre-Curved Visor','Puffers','Pullovers','Quarter-Zips','Raglans','Realtree','Rollers / Luggage','Safety','Scarf / Scarves','School','Short Sleeves','Shorts','Six-Panel','Sleeveless','Spiritwear','Sport Shirts','Structured','Sweaters','Sweatpants','Sweatshirts','Swimwear','T-Shirts','Tank Tops','Thumbholes','Tie Dyed','Totes','Towels','Truckers','Underwear','Uniforms','Union Made','Unstructured','USA Made','Vests','Visor','Warm-ups','Workwear','Wovens') order by name asc";
		$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
 		if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				 return $arr;
		} else {			 
		 return false;	
			} 
	}
	
	function get_fit_total($fabricid,$where1=false){
				$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");

				if($where1!=false){
			$whereConditions=$where1;
			if(strrpos($whereConditions,"FIND_IN_SET($fabricid, st.categories)")==false){
				$whereConditions.=" and FIND_IN_SET($fabricid, st.categories)";
			} 
			$sql="SELECT count(*) as total FROM `ci_styles` as st inner join ci_products as pd on pd.styleID=st.styleID ".$whereConditions." group by pd.styleID order by st.styleID ASC";
				$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	
			}
		} else {
			$sql="SELECT count(*) as total FROM `ci_styles` WHERE FIND_IN_SET($fabricid, categories)"; 
					$result = mysqli_query($con,$sql);
			$row_cnt = mysqli_num_rows($result); 
 			if($row_cnt>0){
				$arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					return $val['total'];
				}
			}  else {
				return 0;	
			} 
		}

	} 
	
	
	function set_userdata($arrayitems){
		session_start();
		foreach($arrayitems as $key=>$val){
			$_SESSION[$key]=$val;	
		}
		return true;
	}
	
	
	function countcolor($styleID){
		$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");

		$sql="select  colorName,colorCode,colorPriceCodeName,colorGroup,colorGroupName,colorFamilyID,colorFamily,colorSwatchImage,colorSwatchTextColor,colorFrontImage,colorSideImage,colorBackImage,color1,color2 from ci_products where styleID='".$styleID."' group by colorName asc";
	 
		$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
 		if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				 return $arr;
		} else {
		return false;
	}
} 
	
	
	
function get_lowest_style_product_price($styleid){
	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");

	$arr=array();
	$sql="select  piecePrice,salePrice from  ci_products_custom  where styleID='".$styleid."'  order by piecePrice,salePrice asc";
	$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
 		if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
					if($val['piecePrice']==''){
						return  $val['salePrice'];		
					} else {
						return $val['piecePrice'];
					}
				}
		} else {
	 $sql="select piecePrice,salePrice from ci_products where styleID='".$styleid."' order by piecePrice,salePrice asc";
	 	$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
 		if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				foreach($arr as $k1=>$val) 
				{
				if($val['piecePrice']==''){
					return  $val['salePrice'];		
				} else {
					return $val['piecePrice'];
				}
				}
		} 
		}


	 
	 
}



function removeSess($sess){
	session_start();
	unset($_SESSION[$sess]); 
	return true;
}
	
	
	
	 
function totalCartItems(){
 	 $total_items="0";
	 if(count(OrderPriceCalc())>0){
		 foreach(OrderPriceCalc() as $k=>$val){
			 $total_items=$total_items+$val->qty;
		 }
	 }
	return $total_items; 
} 

function totalCartPrice(){
	 $totalPrice="0";
	 if(count(OrderPriceCalc())>0){
		 foreach(OrderPriceCalc() as $k=>$val){
			 $totalPrice=$totalPrice+$val->totalPrice;
		 }
	 } 
	return $totalPrice; 
} 



function OrderPriceCalc(){
		$orderArr=array();
	 	if(count(ci_get_userdata('currentOrder'))>0){
		foreach(ci_get_userdata('currentOrder') as $k=>$val){
				$data_products=ftechPData($val['pid']);
				$data_products->totalPrice=($data_products->customerPrice*$val['qty']);
				$data_products->qty=$val['qty'];
				$data_products->sku=$val['pid'];
				$orderArr[]=$data_products;
		}
		}
	return $orderArr;	
}

 
function ftechPData($sku){
	$con = mysqli_connect("localhost","root","Shirt@123!","shirtchamp");
	$sql="Select pd.customerPrice,pd.sizeName,pd.color1,pd.colorSwatchImage,pd.unitWeight,st.baseCategory,st.title,st.styleImage,st.styleName,st.styleID from ci_products as pd inner join ci_styles as st on st.styleID=pd.styleID where  pd.sku ='".v."'";
		$result = mysqli_query($con,$sql);
		$row_cnt = mysqli_num_rows($result); 
 		if($row_cnt>0){
				 $arr=mysqli_fetch_all($result,MYSQLI_ASSOC);
				 return $arr;
		} else {
		return false;
	}
}
	 
	
?>