<?php
include("includes/header_inc.php");
global $db;
global $clname,$clrcode,$clrcode2;

$slugurlid=getStyleIdbyslug($_REQUEST["stid"]);
if(isset($_REQUEST["stid"]) && $_REQUEST["stid"]!="") {
	/*
	$db->where ('styleID', $_REQUEST["stid"]);
	$pdetails = $db->get ('ci_styles');*/
	/*$styleD=explode('-',$_REQUEST["stid"]);
	$stid=end($styleD);*/
	$stid=getStyleIdbyslug($_REQUEST["stid"]);
	$result2 =getDetaislByStyleId($stid);
	$result_specs =getSpeciesByStyleId($stid);
	//pr($result_specs);die;

	$reskey=array();
	foreach($result2 as $kw=>$kval) {
		if($kw=="Black") {
			$reskey[$kw]=$kval;
		}if($kw=="White") {
			$reskey[$kw]=$kval;
		} else {
			$results[$kw]=$kval;
		}
	}
	

	function ksort_deep(&$array){
		ksort($array);
		foreach($array as &$value)
			if(is_array($value))
				ksort_deep($value);
	}

	// example of use:
	ksort_deep($results);
	foreach ($reskey as $key => $row) {
		// replace 0 with the field's index/key
		$dates[$key]  = $row;
	}
	
		
	array_multisort($dates, SORT_ASC, $reskey);
	
	if(!empty($results) && isset($results)) {
	$results=$results;
	} else {
	$results=array();
	}
	
	$result = array_merge($reskey,$results);

	/*echo "<pre>"	;
	//print_r($reskey);
	print_r($res);*/


	/*foreach($result2 as $kw=>$kval) {
	if($kw=="White") {
	$result[$kw]=$kval;
	} else if($kw=="Balck") {
	$result[$kw]=$kval;
	} else {
	$result[$kw]=$kval;
	}
	}

	echo "<pre>";
	print_r($result);

	die();*/

	$_SESSION['lstvisit']=base_url_site.$_REQUEST['cat'].'/'.$_REQUEST["stid"];
	$rquery = $db->rawQuery("SELECT count(rating_number) as score, FORMAT((total_points / rating_number),1) as average_rating FROM ci_product_rating WHERE style_id = '".$stid."' AND status = 1", array(''));

	global $headingTitle;
	global $brandN;
	global $styleNameH;
	global $isrelated;
	global $iscustomerviewed;
	global $headingPImage;
}


if(isset($result) && !empty($result) && $result!="") 
{
	$breadcurms=getBreadcurms($stid);
?>
	<link href="<?php echo base_url_site; ?>css/bootstrap.css" rel="stylesheet">
	<style>
	.rating-container, .rating-container + span {float:left;}
	.a-text-bold {font-weight: 700 !important;}
	.a-size-large {text-rendering: optimizelegibility;}
	.a-size-large {font-size: 21px !important;line-height: 1.3 !important;}
	.a-color-success {color: #008a00 !important;}
	
	.rating-md {font-size: 1.6em;}
	.crevleftrat {float:left;width:5%;}
	.crevmidtrat {float: left;margin-top: -6px; width: 15%;}
	.crevrigtrat {float:left;width:10%;}
	#books{border-bottom:none!important;}
	#books tr {background:none !important;}
	.rw-avg{float:left; width:100%; margin-bottom:10px; text-align:center; font-size:18px;}
	.review-head-tip a{color:#0a2972;}
	.review-head-tip a:hover{text-decoration:underline!important;}
	.descrption ul li{margin:2px 0px;}
	.crevleftrat, .crevmidtrat, .crevrigtrat{width:auto!important;}
	.product-holder:nth-child(2) .color-palate  {left:0px!important;}
	.product-holder:nth-child(2) .color-palate .color-wrapper-home::after{right:80%;}
	@media screen and (max-width : 1000px){
		.product-holder .color-palate  {left:0px!important;}
		.product-holder .color-palate .color-wrapper-home::after{right:80%;}
	}
	
	@media screen and (max-width : 768px){
		.rating-animate{text-align:center;}
		.rating-container, .rating-container + span {float:none;}
		.overall-rating{margin-top:0px; margin-bottom:20px;}
	}
	</style>

	<script>
    function testFunc(item) {
        var divs = document.getElementsByClassName("split-img");
        for (var i = 0; i < divs.length; i++) {
            divs[i].style.display = 'none';
        }
        var myDiv1 = item.href;
		
		var myclrcode = item.getAttribute('data-clrcode');
		var myclrname = item.getAttribute('data-clrname');
		$("#bgclr").css('background-color',myclrcode);
		$("#clrname").html(myclrname);
		
		var myDiv = myDiv1.split('*');
        var target = myDiv[0].split("#");
        document.getElementById(target[1]).style.display = "block";
		
    }

    function testDFunc(item) {
        var divs = document.getElementsByClassName("split-img");
        for (var i = 0; i < divs.length; i++) {
            divs[i].style.display = 'none';
        }

		var myclrcode = item.getAttribute('data-clrcode');
		var myclrname = item.getAttribute('data-clrname');
		$("#bgclr").css('background-color',myclrcode);
		$("#clrname").html(myclrname);
        var myDiv1 = item.getAttribute('data-href');
		var myDiv = myDiv1.split('*');
        var target = myDiv[0].split("#");
        document.getElementById(target[1]).style.display = "block";
		
    }

	$(document).ready(function(){
		/* 	$('.ft-live').click(function(){
			$('#image_window_close').attr('src', "http://bulkapparel.com/images/header-cart-icon.png");
		}); */
		
		
		$('.clear-rating').hide();
		$('.caption').hide();
		jQuery('.rating').attr('disabled', true);
		$('.rating').unbind();
		
		//Function is used to check color name has white space
		/*function hasWhiteSpace(s) {
		  return s.indexOf(' ') >= 0;
		}*/
		
		//@first default ajax to change the pice infor based on color family!
		$('.col').click(function(event){
			event.preventDefault();
			var hrf=$(this).attr('href');
			var item = hrf.split('*');
			
			var myclrcode = $(this).attr('data-clrcode');
			var myclrcode2 = $(this).attr('data-clrcode2');
			var myclrname = $(this).attr('data-clrname');
			$("#bgclr").css('background-color',myclrcode);
			$("#bg1").css('background-color',myclrcode);
			$("#bg2").css('background-color',myclrcode2);
			$("#clrname").html(myclrname);
			
			//Selected color name at color change
			/*var myclrnamehasSpace = hasWhiteSpace(myclrname);
			if(myclrnamehasSpace){
				var myclrNameId = myclrname.replace(/\s+/g, '-'); //replace space with hypen
				myclrNameId = myclrNameId.replace(/\//g, ''); //replace forward slash
			}else{
				var myclrNameId = myclrname;
			}
			
			//Hidden Field Color Name For Reseting previous color qty disabled class
			var hiddenFldSelClrName = $('#hiddenFldSelClrName').val();
			var chkHiddenColhasSpace = hasWhiteSpace(hiddenFldSelClrName);
			if(chkHiddenColhasSpace){
				var hiddenclrNameId = hiddenFldSelClrName.replace(/\s+/g, '-'); //replace space with hypen
				hiddenclrNameId = hiddenclrNameId.replace(/\//g, ''); //replace forward slash
			}else{
				var hiddenclrNameId = hiddenFldSelClrName;
			}
			
			if (!$('#bulkid').is(':empty')){
				$('#bulkAddtoCart tr#'+hiddenclrNameId).find('.numbersOnly').removeAttr("disabled");
				$('#bulkAddtoCart tr#'+myclrNameId).find('.numbersOnly').attr('disabled','disabled');
				$('#hiddenFldSelClrName').val(myclrname);
			}*/
			
			$.ajax({
				url: '<?php echo base_url_site; ?>AjaxCallOtherColor.php',
				method: "post",
				data: 'item='+item[1]+'&styleid=<?php echo $stid;?>',
				async: true,
				dataType: "json",
				success: function(msg) {
					//alert(msg.data);
					//$('.product-discription').html('');
					//$('.product-discription').html(msg.data);
					
					$('.scriptid').html(msg.data);
					$('.titleProduct').html(msg.datatitle);
					$('.rate-item').html(msg.datarate);
					$('.descrption').html(msg.datafeatures);
					$('.sizeid').html(msg.datasize);
					$('.pimage').html(msg.dataprice);
					
					$('#colfamily').val(msg.key);
					$('#styleID').val(msg.styleID);
					$('#sku').val(msg.sku);
					$('#size').val(msg.sizeName);
						
					$('#salePrice').val(msg.salePrice);
					$('#piecePrice').val(msg.piecePrice);
					$('#dozenPrice').val(msg.dozenPrice);
					$('#casePrice').val(msg.casePrice);
					$('#customerPrice').val(msg.customerPrice);
					$('#saleExpiration').val(msg.saleExpiration);
				}
			});
		});	
		
		$('.col1').click(function(event){ 
			event.preventDefault();
			var hrf=$(this).attr('data-href');
			var item = hrf.split('*');
			var myclrcode = $(this).attr('data-clrcode');
			var myclrcode2 = $(this).attr('data-clrcode2');
			var myclrid = $(this).attr('data-valc');
			var myclrname = $(this).attr('data-clrname'); 
			$("#bgclr").css('background-color',myclrcode);
			$("#bg1").css('background-color',myclrcode);
			$("#bg2").css('background-color',myclrcode2);
			$("#clrname").html(myclrname);
			
			//Selected color name at color change
			/*var myclrnamehasSpace = hasWhiteSpace(myclrname);
			if(myclrnamehasSpace){
				var myclrNameId = myclrname.replace(/\s+/g, '-'); //replace space with hypen
				myclrNameId = myclrNameId.replace(/\//g, ''); //replace forward slash
			}else{
				var myclrNameId = myclrname;
			}
			//Hidden Field Color Name For Reseting previous color qty disabled class
			var hiddenFldSelClrName = $('#hiddenFldSelClrName').val();
			var chkHiddenColhasSpace = hasWhiteSpace(hiddenFldSelClrName);
			if(chkHiddenColhasSpace){
				var hiddenclrNameId = hiddenFldSelClrName.replace(/\s+/g, '-'); //replace space with hypen
				hiddenclrNameId = hiddenclrNameId.replace(/\//g, ''); //replace forward slash
			}else{
				var hiddenclrNameId = hiddenFldSelClrName;
			}
			
			if (!$('#bulkid').is(':empty')){
				$('#bulkAddtoCart tr#'+hiddenclrNameId).find('.numbersOnly').removeAttr("disabled");
				$('#bulkAddtoCart tr#'+myclrNameId).find('.numbersOnly').attr('disabled','disabled');
				$('#hiddenFldSelClrName').val(myclrname);
			}*/
			
			$(".product-color-box ul li").removeClass('current');

			$('.product-color-box ul li a').each(function(){
				var val1 = $(this).data('valc');
				if(val1 == myclrid) {
					$(this).parent().addClass('current');
					//alert(val1);
				}
			});


			$(".loadid").show();
			$(".available").hide();
			$(".prid").css('display','none');
	
			$("input[type='number']").prop('disabled', true);
			$.ajax({
				url: '<?php echo base_url_site; ?>AjaxCallOtherColor.php',
				method: "post",
				data: 'item='+item[1]+'&styleid=<?php echo $stid;?>',
				async: true,
				dataType: "json",
				success: function(msg) {
					//alert(msg.data);
					//$('.product-discription').html('');
					//$('.product-discription').html(msg.data);
					
					$('.scriptid').html(msg.data);
					$('.titleProduct').html(msg.datatitle);
					$('.rate-item').html(msg.datarate);
					$('.descrption').html(msg.datafeatures);
					$(".loadid").hide();
					$("input[type='number']").prop('disabled', false);
					$(".prid").css('display','block');
					$(".available").show();
					$('.sizeid').html(msg.datasize);
					$('.pimage').html(msg.dataprice);
					
					$('#colfamily').val(msg.key);
					$('#styleID').val(msg.styleID);
					$('#sku').val(msg.sku);
					$('#size').val(msg.sizeName);
					
					$('#salePrice').val(msg.salePrice);
					$('#piecePrice').val(msg.piecePrice);
					$('#dozenPrice').val(msg.dozenPrice);
					$('#casePrice').val(msg.casePrice);
					$('#customerPrice').val(msg.customerPrice);
					$('#saleExpiration').val(msg.saleExpiration);
				}
			});
		});	
	
		//@ajax used to show infoe on size selected!
		$('.size').click(function(){
			var size=$(this).attr('title');
			var colfamily=$('#colfamily').val();
			var styleID=$('#styleID').val();
			//$(".item-description ul li a").removeClass('active');
			$.ajax({
				url: '<?php echo base_url_site; ?>AjaxCallOtherColor.php',
				method: "post",
				data: 'item='+colfamily+'&styleid=<?php echo $stid;?>'+'&sizeName='+size,
				dataType: "json",
				async: true,
				success: function(msg) {
				 	$('.priceSize').html('');
					$('.priceSize').html(msg.PPrice);
					$('.titleProduct').html('');
					$('.titleProduct').html(msg.title);
					$('#sku').val(msg.sku);
					$('#colfamily').val(msg.key);
					$('#styleID').val(msg.styleID);
					$('#size').val(msg.sizeName);
					$('#salePrice').val(msg.salePrice);
					$('#piecePrice').val(msg.piecePrice);
					$('#dozenPrice').val(msg.dozenPrice);
					$('#casePrice').val(msg.casePrice);
					$('#customerPrice').val(msg.customerPrice);
					$('#saleExpiration').val(msg.saleExpiration);
				}
			});
		});


		$('#stSImage').click(function(){ 
			//alert('test');
			$(this).children('img').show();	 
		});
		
		$(function(){
			$('.item-description ul li a').on('click', function(){
				$(this).parent().addClass('current').siblings().removeClass('current');
			});
		});
		//$( ".detail-thumb ul li a" ).parent().parent().parent().parent().children(".product-item-image").css( "background", "yellow" );
	
		$('.imgsidebar').children('li').children('a').children('img').click(function(){
			var selImg1=$(this).attr('src');
			if($('.pimg').parent().hasClass('product-item-image')){

				$(this).parent().parent().parent().parent().parent().children().children(".pimg").attr('src', selImg1);
				//$(this).parent().parent().parent().next('div').children('.pimg').attr('src', selImg1);
				//$(this).parent().parent().parent().parent().children('.pimg').attr('src', selImg1);
			}else{
				$('.pimg').parent().removeClass('product-item-image')
				$('.pimg').parent().addClass('product-item-image active')
			}
		});

		$('#addtoSCartbtn').click(function(){
			//jQuery('.loading').show();
			var valid=0;

			$("#SingleAddtoCart").find('input[type=number]').each(function(){
				if($(this).val() != "") valid+=1;
			});
			
			$("#bulkAddtoCart").find('input[type=text]').each(function(){
				if($(this).val() != "") valid+=1;
			});
			
			if(valid){
				var dataString = $("#SingleAddtoCart, #bulkAddtoCart").serialize(); 
				$.post("<?php echo base_url_site?>addon/bulkaddtocart.php",dataString , function(data) {
					
					if(data==1) {
						 // window.location.href="<?php echo base_url_site?>cart";
						 $(".AT3CListWrap").load("<?php echo base_url_site ?>./includes/mini_cart.php");
						  $(".totel-value").load("<?php echo base_url_site ?>./includes/header_cartdetail.php");
						  $("input:text").val("");
					     $("input[type=number]").val("");
						function Mymsg(msg,duration)
{
 var alt = document.createElement("div");
     alt.setAttribute("style","position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);background-color:#7092be; border: medium none;border-radius: 9px;font-size: 22px;font-weight:bold;color:white;padding: 40px 50px 40px 50px ;text-align: center;text-transform: uppercase;");
     alt.innerHTML = msg;
     setTimeout(function(){
      alt.parentNode.removeChild(alt);
     },duration);
     document.body.appendChild(alt);
	 
}
                 $(window).scrollTop(0);
                  Mymsg('Items added to the cart',2000)
                           
                             //window.location.href="<?php echo base_url_site?>cart";
                          
                          
						
						// $(window).load(function() { 
                          // $(".loading").fadeOut("slow"); 
                         // });
						
						//setTimeout(function(){
						//window.location.href="<?php echo base_url_site?>cart";
						//jQuery('.loading').hide();
						//},7000);
					}
					//jQuery('.loading').hide();
				});	
				
			}
			else {
				//jQuery('.loading').hide();
				$("#background-fade").delay(500).fadeIn(500);
				$(".pop-close").click(function(){
					$("#background-fade").fadeOut(500);
				});
				// alert("Error: You must fill in at least one field");
				return false;
			}
		});		
	
	
		$('#addtoWishList').click(function(){
			$.post("<?php echo base_url_site?>addon/addtowishlist.php", $("#wishid").serialize(), function(data) {
				var data=$.parseJSON(data);
				if(data.result=="0") {
					$('.successdiv').children('label').html('');
					$('.successdiv').css('display','block');
					$('.successdiv').children('label').html(data.msg);
					setTimeout(function(){ $('.successdiv').children('label').html(''); $('.successdiv').css('display','none');}, 5000);
				} 
				if(data.result=="1")  {
					console.log(data);
					$('.successdiv').children('label').html('');
					$('.successdiv').css('display','block');
					$('.successdiv').children('label').html(data.msg);
					setTimeout(function(){ $('.successdiv').children('label').html(''); $('.successdiv').css('display','none');}, 5000);
				} 
				if(data.result=="2")  {
					window.location.href="<?php echo base_url_site?>login?redirect_url=<?php echo redirect_url?>";
				}
			});	
		});	
	
		
		$('#removesuccess').click(function(){
			$('.successdiv').hide();
		});
		
		$('#removeerror').click(function(){
			$('.errordiv').hide();
		});	
	});	
	</script>
	<script language="javascript" type="text/javascript">
	$(function() {
		if($(window).width() >= 769){
			$("#sel-col").click(function(e){ 
				$("#target-col").slideToggle();
				e.stopPropagation();
			});
			$(document).click(function(e){
				$('#target-col').slideUp();
				e.stopPropagation();
			});
		}
		else{
			$("#sel-col").click(function(e){
				$("#target-col").slideDown();
				e.stopPropagation();
			});
			 
			$("#col-close").click(function(e){
				$("#target-col").slideUp();
				e.stopPropagation();
			}); 
			 
			$("#target-col li").click(function(e){
				$(this).parent().slideUp();
				e.stopPropagation();
			}); 
		}
		
		$(".numbersOnly").keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				 // Allow: Ctrl+A, Command+A
				(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
				 // Allow: home, end, left, right, down, up
				(e.keyCode >= 35 && e.keyCode <= 40)) {
					// let it happen, don't do anything
				return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});

		$("#rating_star").codexworld_rating_widget({
			starLength: '5',
			initialValue: '<?php echo intval($rquery[0]['average_rating'])?>',
			<?php if(isset($_SESSION["uid"]) && $_SESSION["uid"]!="") {?>	
			callbackFunctionName: 'processRating',
			<?php }?>	
			imageDirectory: '<?php echo base_url_site?>images',
			inputAttr: 'postID'
		});
	});

	function processRating(val, attrVal){
		$.ajax({
			type: 'POST',
			url: '<?php echo base_url_site?>addon/rating.php',
			data: 'postID=<?php echo $stid?>&ratingPoints='+val,
			async: true,
			dataType: 'json',
			success : function(data) {
				if (data[0].status == 'ok') {
					//alert('You have rated '+val+' to CodexWorld');
					$('#avgrat').text(data[0].average_rating);
					$('#totalrat').text(data[0].score);
					//$('#avgrat1').text(data[0].average_rating);
				   // $('#totalrat1').text(data[0].score);
				}else{
					alert('Some problem occured, please try again.');
				}
			}
		});
	}
	</script>
	

	<style type="text/css">
	.overall-rating{font-size: 14px;margin-top: 7px;color: #8e8d8d;}
	#background-fade{display:none; position:fixed; top:0px; left:0px; right:0px; bottom:0px; background-color: rgba(0, 0, 0, .5); z-index:9999999;}
	.outer-overlay{  position:absolute; top:15%; left:3%; right:3%; overflow:hidden;}
	.pop-up-holder{position:relative; box-shadow: 0px 0px 15px 2px rgb(0, 0, 0, .5); margin:30px auto; border-radius:5px; background:#fff; width:350px;}
	.pop-up-holder h3{ width:100%; position:relative; font-size:18px;padding: 10px; border-bottom: 1px solid #c9c9c9; box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box;}
	.pop-up-holder h3 span{position: absolute; right: 6px; top:6px; border-radius: 100%; text-align: center; width: 28px;  border: 1px solid #000; padding: 3px 0px; }
	.pop-img{text-align:center; border-bottom: 1px solid #c9c9c9;}
	.pop-up-close{text-align:right;}
	.pop-up-close button{ background:#d83939; color:#fff; border:none; border-radius:3px; text-align:center; margin-right:10px; padding:8px 20px; margin-top:8px; margin-bottom:8px;}	
	.brand{ display:none;}
	</style>

	<div class="loading" style="display:none;">Loading&#8230;</div>
        <!--HEADER END HERE-->
        <!--MAIN PAGE START HERE-->
        <div class="site-center">
            <div class="main-container">
                <!--BREADCRUM HERE-->
                <div class="breadcrum">
				<p><a href="<?php echo base_url_site;?>">Home</a> / 
				<?php if(isset($breadcurms['slugCategory']) && $breadcurms['slugCategory']!="") echo '<a href="'.base_url_site.$breadcurms['slugCategory'].'">'.$breadcurms['baseCategory']."</a> /"; ?>
                <?php if(isset($breadcurms['brandName']) && $breadcurms['brandName']!="") echo '<a href="'.base_url_site.'styles?brand='.$breadcurms['brandslug'].'">'.$breadcurms['brandName']."</a> /"; ?>
                <?php if(isset($breadcurms['styleName']) && $breadcurms['styleName']!="") echo $breadcurms['styleName']." <span class='mbdshow'>".$breadcurms['title']."</span>"; ?>
                
				</p></div>
                <!--BREADCRUM END-->
				<div class="successdiv" style="display:none;"><label> </label><span class="removesuccess" id="removesuccess">X</span></div>
				<div class="errordiv"  style="display:none;"><label></label><span class="removeerror" id="removeerror">X</span></div>
                <!--PRODUCT DETAIL BOX-->
                <div class="product-detail-box" itemscope itemtype="http://schema.org/Product">
                    <div class="product-image-detail">
					<?php
					$k=-1;
					$h=-1;
					?>
					<?php $arrimg=array();?>
					<?php
					$knumer=0;
					foreach($result as $key=>$val){ 
					?>
						<?php 
						foreach($val as $i=>$v) {
						?> 
							<?php if($i<1) {
							if($k==-1) {
							 ?>
								<div class="act split-img pimage"  id="img-<?php echo $k;?>" <?php echo(($k>-1)?'style="display:none;"':'');?>>
								<?php 
								echo product_images($v['colorFrontImage'],$v['colorSideImage'],$v['colorBackImage'],$result[$key][0]['styleImage'],$v['colorName'],$v['styleImageStatus'],$k,(($k>-1)?'false':'active'));
								
								?>
								</div>
							<?php
							}
							$k++; 
							$h++;
							}?> 
						<?php }?>   
					<?php }?> 
					</div>

					<!--add to cart data-->
					<?php $k1=0;?>
					<?php $n1=0;?>
					<?php foreach($result as $key=>$val){ 
						//$lowestStyleprice=fnlowestStyleprice($stid);
					?>
						<?php if($k1<1) { ?>
							<?php foreach($result[$key][0] as $p=>$v) { ?> 
								<?php if($n1<1) {
									$lowestStyleprice=fnlowestStyleprice2($stid,$key);
								?>
    
								<form method="post" action="<?php echo base_url_site.'cart';?>" id="addtocart"> 
									<input type="hidden" name="colfamily" id="colfamily" value="<?php echo $key;?>"/>
									<input type="hidden" name="sku" id="sku" value="<?php echo $result[$key][0]['sku'];?>"/>
									<input type="hidden" name="styleID" id="styleID" value="<?php echo $stid;?>"/>
									<input type="hidden" name="colfamily" id="colfamily" value="<?php echo $key;?>"/>
									<input type="hidden" name="size" id="size" value="<?php echo $result[$key][0]['sizeName'];?>"/>
									<input type="hidden" name="salePrice" id="salePrice" value="<?php echo $result[$key][0]['salePrice'];?>"/>
									<input type="hidden" name="piecePrice" id="piecePrice" value="<?php echo $result[$key]['piecePrice'];?>"/>
									<input type="hidden" name="dozenPrice" id="dozenPrice" value="<?php echo $result[$key][0]['dozenPrice'];?>"/>
									<input type="hidden" name="casePrice" id="casePrice" value="<?php echo $result[$key][0]['casePrice'];?>"/>
									<input type="hidden" name="customerPrice" id="customerPrice" value="<?php echo $result[$key][0]['pPrice'];?>"/>
									<input type="hidden" name="saleExpiration" id="saleExpiration" value="<?php echo $result[$key][0]['saleExpiration'];?>"/>
								</form>
								<?php } ?>
								<?php $n1++; ?>
							<?php } ?>
						<?php } ?>
					<?php $k1++; ?>
				<?php } ?>
				<!--add to cart data-->


				<?php $k=0;?>
				<?php $n=0;?>
				<?php $t=1;?>
				<?php $pr=1;?>
				<?php $col=1;?>
				<?php $n2=0;?>
	
				<?php foreach($result as $key=>$val){
				?>
					<?php if($k<1) { ?>
						<div class="pdetail">
							<div class="product-discription">
								<!--Name Cat Weight-->
								<?php foreach($result[$key][0] as $p=>$v) {?> 
									<?php if($n<1) {
									$headingTitle=$result[$key][0]['customTitle'];
									$headingPImage=$result[$key][0]['styleImage'];
									$styleNameH=$result[$key][0]['styleName'];
									$brandN=$result[$key][0]['brandName'];
									$isrelated=$result[$key][0]['relateditems'];
									$iscustomerviewed=$result[$key][0]['customerviewed'];
									?>
									<h2 class="titleProduct" itemprop="name" <?php echo(($t>1)?'style="display:none;"':'');?> id="title-<?php echo $t++;?>"><?php echo $result[$key][0]['customTitle'];?></h2>	
									<?php } ?>
								<?php $n++; } ?>
								<input value="<?php echo number_format((getTVoteRev($stid)/getCVoteRev($stid)),1); ?>" type="number" class="rating" min=0 max=5 step=0.1 data-size="md" data-stars="5" productId="<?php echo $stid; ?>"> 
								
								<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/aggregateRating"> 
									<div class="overall-rating review-head-tip" >
										<span id="totalrat">
											<span style="display:none" itemprop="ratingValue"><?php echo number_format((getTVoteRev($stid)/getCVoteRev($stid)),1); ?></span>
										   <?php if(getTCustomerRev($stid)!=0) {?>
										   <a href="#preview" style="text-decoration:none"><span itemprop="reviewCount"><?php echo getTCustomerRev($stid); ?></span> Customer Review</a>
										   <?php } //else {
												   if(!isset($_SESSION["uid"]) && $_SESSION["uid"]=="") {
												    echo ' | <span itemprop="reviewCount"><a href="'.base_url_site.'/login?redirect_url='.redirect_url.'" style="text-decoreation:none;">Write Review</a></span>';
												   }
										  // }?>
										</span> 
										<?php if(chkProReviewlistbyStyleId($_SESSION["uid"],$stid)==true) {   
										   echo ' | <a href="javascript:void(0);" style="text-decoreation:none;" onclick="fnReview();">Write Review</a>';
										} else {
										if(isset($_SESSION["uid"]) && $_SESSION["uid"]!="") {
										 echo ' | <a href="javascript:void(0);" style="text-decoreation:none;" onclick="fnReviewRev();">Write Review</a>';
										 //echo ' | <a href="#creview" style="text-decoration:none">Write Review</a>';
										 }
										 }
										 
										 ?> 
								</span></div></span>

		
								<?php /*?><div class="review-head-tip"><img src="<?php echo base_url_site;?>images/star.jpg" alt="star"><p><a href="#">10 Customer Reviews</a>|<a href="#">20 Answered Questions</a></p></div><?php */?>
								<!--Name Cat Weight-->
								
								<!--Price-->
    
        
		<div class="rate-item" itemprop="offers" itemscope itemtype="http://schema.org/Offer">Starting at 
		<?php foreach($val as $p1=>$v1) { ?> 
			<?php if($n2<1) {
			
			 ?>
			<h3 itemprop="price" content="<?php echo number_format($lowestStyleprice,2);?>" <?php echo(($pr>1)?'style="display:none;"':'');?> class="priceSize" id="price-<?php echo $pr++;?>" ><span itemprop="priceCurrency" content="USD" class="priceSize1"><?php echo SYMBOL.number_format($lowestStyleprice,2);?></span></h3> 
			<?php } ?>
	 	<?php $n2++; } ?>
		<!--Price-->
	
		<span>for color <?php echo $key;?></span></div>
		<div class="item-description">
			<h4>Feature</h4>
            <div class="descrption" itemprop="description">
			<?php
			$tags = array("<p>", "</p>", "<br>");
			 foreach($result[$key] as $i=>$v) { ?> 
			<?php if($i<1) { ?>
			<?php 
			
			
			$sdesc=str_replace($tags, "", $result[$key][0]['description']);
			echo $sdesc=str_replace('<li><strong>"<a style="float:none;" href="http://bit.ly/PolyScreenTips" target="_blank">CLICK HERE</a>" </strong><span style="text-decoration: underline;">for Polyester Printing Tips</span></li>', "", $sdesc);
			 ?>
			<?php } ?>
			<?php } ?>
            </div>
            
            <div class="product-color-box">
			<h3 id="col-exp">Choose Color</h3>

			<ul>
				<?php 
				$tu=1;
				$j=-1;
				?>
				<?php 
				//echo "<pre>result===";print_r($result); 
				foreach($result as $key=>$val){ ?>
					<?php foreach($val as $i=>$v) { ?> 
						<?php
						if($i<1) 
						{?>
							<?php 
							$pp=$j++;
							if($v['colorGroup']==$_POST['color']) {?>
								<script>
								$(document).ready(function () {
									document.getElementById("col-<?php echo $pp;?>").click();
								});
								</script>
								<li class="current"><a  class="col" id="col-<?php echo $pp;?>" data-valc=<?php echo $tu;?> href="#img-<?php echo $pp;?>*<?php echo $key;?>" style="background:<?php echo $v['color1'];?>" data-clrcode="<?php echo $v['color1'];?>" data-clrcode2="<?php echo $v['color2'];?>" data-clrname="<?php echo $v['colorName'];?>" alt="<?php echo $v['colorName'];?>" title="<?php echo $v['colorName'];?>">
                                 <?php if(isset($v['color2']) && $v['color2']!="") {?>
                                <div class="lft-color" style="background-color: <?php echo $v['color1'];?>;"></div>
                                <div class="rght-color" style="background-color: <?php echo $v['color2'];?>;"></div>
                                <?php }?>
                                
                                </a></li>
							<?php } else {
							if($j==0 && (!isset($_POST['color']) && $_POST['color']=="")) {
							echo '<li class="current">';
							} else {
							echo '<li class="">';
							}
							?>
								<a  class="col" id="col-<?php echo $pp;?>" data-valc=<?php echo $tu;?> href="#img-<?php echo $pp;?>*<?php echo $key;?>" style="background:<?php echo $v['color1'];?>" data-clrcode="<?php echo $v['color1'];?>" data-clrcode2="<?php echo $v['color2'];?>" data-clrname="<?php echo $v['colorName'];?>" alt="<?php echo $v['colorName'];?>" title="<?php echo $v['colorName'];?>">
                                 <?php if(isset($v['color2']) && $v['color2']!="") {?>
                                <div class="lft-color" style="background-color: <?php echo $v['color1'];?>;"></div>
                                <div class="rght-color" style="background-color: <?php echo $v['color2'];?>;"></div>
                                <?php }?>
                                </a></li>
							<?php } 
				
				if($pp<0) {
				
				$clname=$v['colorName'];
				$clrcode=$v['color1'];
				$clrcode2=$v['color2'];
				}
				
				}?> 
                 
				<?php
				$tu++;
				 }?>   
				<?php }?> 
			</ul>
		</div>
        <div id="prod-detail">
        <!-- <div class="cleft"><h3>Color</h3></div> -->
        <div class="color-dw cright">
    <div class="dw-box" id="sel-col">
    <div class="select-color-box" style="background:<?php echo $clrcode;?>" id="bgclr">
    <?php if(isset($clrcode2) && $clrcode2!="") {?>
                  <div class="select-lft-color" style="background-color: <?php echo $clrcode;?>;" id="bg1"></div>
  					<div class="select-rght-color" style="background-color: <?php echo $clrcode2;?>;" id="bg2"></div>
                  <?php }?>
    </div><span id="clrname"><?php echo $clname;?></span>
	<ul id="target-col">
<span id="col-close">Close</span>
				<?php
				$ty=1;
				 $j=-1;?>
				<?php foreach($result as $key=>$val){ ?>
				<?php foreach($val as $i=>$v) { ?> 
				<?php if($i<1) { ?>
               
					<?php $pp=$j++;
                  if($v['colorGroup']==$_POST['color']) {?>
                  <script>
				$(document).ready(function () {
				document.getElementById("col-<?php echo $pp;?>").click();
				});
				  </script>
                  <li  class="col1" id="col1-<?php echo $pp;?>" data-valc=<?php echo $ty;?> data-href="#img-<?php echo $pp;?>*<?php echo $key;?>" data-clrcode="<?php echo $v['color1'];?>" data-clrcode2="<?php echo $v['color2'];?>" data-clrname="<?php echo $v['colorName'];?>">
                  <div class="select-color-box-1" style="background:<?php echo $v['color1'];?>">
                     <?php if(isset($v['color2']) && $v['color2']!="") {?>
                  <div class="select-lft-color" style="background-color: <?php echo $v['color1'];?>;"></div>
  					<div class="select-rght-color" style="background-color: <?php echo $v['color2'];?>;"></div>
                  <?php }?>
                  
                    </div> 
                   <span><?php echo $v['colorName'];?></span></li>
				  <?php } else {?>
					 <li  class="col1" id="col1-<?php echo $pp;?>" data-valc=<?php echo $ty;?> data-href="#img-<?php echo $pp;?>*<?php echo $key;?>" data-clrcode="<?php echo $v['color1'];?>" data-clrcode2="<?php echo $v['color2'];?>" data-clrname="<?php echo $v['colorName'];?>"><div class="select-color-box-1" style="background:<?php echo $v['color1'];?>">
                     <?php if(isset($v['color2']) && $v['color2']!="") {?>
                  <div class="select-lft-color" style="background-color: <?php echo $v['color1'];?>;"></div>
  					<div class="select-rght-color" style="background-color: <?php echo $v['color2'];?>;"></div>
                  <?php }?>
                  
                    </div> 
                      <span><?php echo $v['colorName'];?></span></li>
				<?php } 
				}?> 
                 
				<?php
				$ty++;
				 }?>   
				<?php }?> 
                            
                            
							</ul>
							</div>
							</div>
          </div>   
                       

		</div>
        
        
</div>


	<?php  } ?>
	 <?php $k++; ?>
	<?php } ?>

                   
                    <div class="product-offers-box">
                        <div class="product-logo-box"> <span class="brand" itemprop="brand"><?php echo $result[$key][0]['brandName']; ?></span> <a href="<?php echo base_url_site?>styles?brand=<?php echo $result[$key][0]['brandslug']; ?>" style="text-decoration:none;"><img src="<?php echo base_url_pimages.$result[$key][0]['brandImage']; ?>" alt="<?php echo $result[$key][0]['brandName']; ?>" ></a></div>
                        <div class="product-advertisment">
                        <?php //if(isset(getDiscountOfferInfo())){
							$discountoffers=getDiscountOfferInfo();
							foreach($discountoffers as $k1=>$doffer) {
                            echo '<h2><a href="#">Save '.$doffer['dpercentage'].'% Over $'.number_format($doffer['dcost'],0).'</a></h2>';
						 }?>	
                        </div>
                        <!--<div class="shipping-offer"><h2>**FREE SHIPPING ON ORDER OVER <?php //echo SYMBOL.FREESHIPPING;?>**</h2></div>-->
                        <div class="shipping-offer"></div>
                        
						<?php $cprices=getColorPriceInfo($stid);
						if(!empty($cprices)) {
						?>
                       <div class="saving-box">
						<h2><?php if(isset($result[$key][0]['priceheading']) && $result[$key][0]['priceheading']!="") {
						 echo $result[$key][0]['priceheading'];
						 } else {
						 echo "Savings";
						 }
						  ?></h2>
                     
						<ul>
                        
                        <?php foreach($cprices as $cpval){
						//echo $cpval['minPrice'];
						if($cpval['colorPriceCodeName']=="Heathers" && intval($cpval['minPrice'])>0) {
						echo '<li><span> Gray :</span><h3>'.SYMBOL.number_format($cpval['minPrice'],2).'</h3></li>';
						} else {
						echo '<li><span> '.$cpval['colorPriceCodeName'].' :</span><h3>'.SYMBOL.number_format($cpval['minPrice'],2).'</h3></li>';
						}
						 }?>
                         <li><span>for Sizes :</span><h4>(S-XL)</h4></li>
						</ul>

						</div>
                        <?php }?>

                    </div>
                    
                    <form id="SingleAddtoCart" method="post" action="">     
<script>
jQuery(document).ready(function () {
    jQuery("input[type='number']").keyup(function () {
	    var myStock2 = jQuery(this).attr('data-qty');
		if (typeof myStock2 !== "undefined") {
			//alert(myStock2);
			var sizen2 = jQuery(this).attr('data-size');
			var value2 = jQuery(this).val();
			
			if (parseInt(value2) > parseInt(myStock2)) {
				//this.val(myStock2);
				jQuery(this).val(myStock2);
				jQuery("#qerrorid").show();
				jQuery("#qerrorid").css('color','#FF0000');
				//jQuery("#qerrorid").html('Only '+myStock2+' of size "'+sizen2+'" are available. We\'ll adjust the quantity for you.');
				jQuery("#qerrorid").html('Only '+myStock2+' of size "'+sizen2+'" are available. We\'ve adjusted the quantity to match our inventory.');
			
				//alert('Only '+myStock2+' of size "'+sizen2+'" are available. We\'ll adjust the quantity for you.');
			} 
		}	
			//jQuery('#stock-info-block').show();
    }).keyup();
});
</script>                       
         <div id="prod-detail">
        <div class="p-add-wrapper sizeid"><div class="p-quantity"><h3>Quantity <span id="qerrorid" style="font-size:14px; display:none;"></span></h3>
<?php 
$listsizes1=getSizesByStyleId($stid);
$totalSizes1=count($listsizes1);
$scPrices=getSizePByStyleId($stid,$clname);
for($j=0;$j<$totalSizes1;$j++) {	
if($scPrices[$j]['customerPrice']!="") {
if($scPrices[$j]['qty']!='0') {$stock_qty=$scPrices[$j]['qty']; } else { $stock_qty=0;}
?>    
   
        <div class="variant size_<?php echo $scPrices[$j]['sizeName'];?>" style="display: table-cell;"><label><?php echo $scPrices[$j]['sizeName'];?></label><div class="price"><img width="12" height="12" alt="Ajax loader" src="<?php echo base_url_site?>images/ajax-loader.gif" class="loadid" style="display: none;"><div class="prid"><?php echo SYMBOL.($scPrices[$j]['customerPrice']==''?number_format($scPrices[$j]['customerPrice'],2):number_format($scPrices[$j]['customerPrice'],2));?></div></div><div class="closeout-text"></div><input type="number" data-qty="<?php echo $stock_qty?>" data-size="<?php echo $scPrices[$j]['sizeName'];?>" validate="true" name="<?php echo str_replace(' ','*',str_replace('/','-',$scPrices[$j]['colorName']));?>[<?php echo $scPrices[$j]['sizeName'];?>][<?php echo $scPrices[$j]['sku'];?>][<?php echo $scPrices[$j]['styleID'];?>][]" min="0" id="<?php echo $scPrices[$j]['sizeName'];?>" data-size="<?php echo $scPrices[$j]['sizeName'];?>" data-max="0" max="<?php echo $scPrices[$j]['qty'];?>" <?php echo $scPrices[$j]['qty']==0?'disabled="disabled"':''; ?> class="numbersOnly"><div class="available"><?php echo $scPrices[$j]['qty']!='0'?'<span>'.$scPrices[$j]['qty'].'</span>':'<span class="stockOut">OUT</span>'?></div></div>
<?php }
 }?>     
    
        <div class="clear">
        	
        </div>
    	</div>
    </div>

          </div> 
		<div class="discription-control1 pro-add" >
			
	
        	<a href="<?php echo base_url_site?>cart" id="cartid">View Cart & Checkout</a>
			<a href="javascript:void(0);" id="addtoSCartbtn">Add to cart</a>
        </div>   
	</form>
		<a class="size_chart" href="javascript:void(0)"> 
								Size Chart <span class="glyphicon  glyphicon-triangle-bottom"></span>
		</a>
		<div class="myText" style="display:none">
				<table style="width:100%">
					<?php 
					$count=1;
			foreach($result_specs as $valueSpecs){
          			$arraySize = explode(",",$valueSpecs['size']);
          			$arrayValue = explode(",",$valueSpecs['value']);
				//pr($arraySize);
				//pr($arrayValue);
				?>
					<tr>
							<th></th>
							<?php 
								if($count==1){
									foreach($arraySize as $value){
									?>

									<th><?php echo !empty($value)?$value:'-';  ?></th>
									<?php
								}
							}
							?>
							
							
					</tr>
					<tr>    
							<td><?php
							//pr($valueSpecs);
							 echo $valueSpecs['specName'] ?></td>
							<?php 
							
									foreach($arrayValue as $value){
									?>

									<td><?php echo $value;  ?></td>
									<?php
								}
							
							?>
							
					</tr>
			<?php
             $count++;
			 }
			?>
					
				</table>
			
		</div>

 </div>                   
                </div>
                <!--PRODUCT DETAIL BOX END-->
            </div>   
        </div>
       <div class="scriptid">
       </div> 
        
        <div class="site-center">
            <div class="product-batch-box">
                <!-- Bulk Add To CArt Start Here-->

<input type="hidden" id="hiddenFldSelClrName" value="">
<div id="bulkid">
  
              

</div>                

<!-- Bulk Add To Cart End Here-->



                   
                            
<?php 
//print_r($result);
$spid="";
$i=1;
foreach($result as $key=>$val){
if($i==1 && $val[0]['companionGroup']!=0 && $val[0]['companionGroup']!="") {
$spid=$val[0]['companionGroup'];
}
$i++;
}

			
if(isset($isrelated) && $isrelated==0) {			
$params = array('');
if(isset($spid) && !empty($spid)) {

$psimilarproducts = $db->rawQuery("SELECT slug,slugCategory,styleImage,customTitle,styleID,brandImage,title,pPrice,pColors,pTotalColors,styleImageStatus,pmodelImage FROM `ci_styles` where companionGroup='".$spid."' and styleID<>$stid and pPrice>0 and isExistProduct=1 order by bestsellerrank asc LIMIT 0, 4", $params);



if(isset($psimilarproducts) && !empty($psimilarproducts)) {

echo '<div class="product-row">
                            <h2>Related Items</h2>';

foreach($psimilarproducts as $k=>$v) {
$pPrice= $v['pPrice'];
							
					if(intval($pPrice)>0) {
					
					
					if(isset($v['customeseo']) && $v['customeseo']!="") {
						$slug=$v['customeseo'];
						} else {
						$slug=$v['slug'];
						}
					?>
                    <div class="product-holder">
                                <div class="product-image-box">
                                <a href="<?php echo base_url_site.$v['slugCategory'].'/'.$v['slug'];?>">
							<?php 
                           
							if($v['styleImageStatus']==1) {
							
							if(!empty($v['pmodelImage'])) { 
							?>
							<img src="<?php echo fnCPMainImages($v['pmodelImage']);?>" alt="product-image"/>
							<?php  }
							
							} else {		
							if(!empty($v['styleImage'])) {?>
							<img src="<?php echo fnComPImages($v['styleImage']);?>" alt="product-image" />
							<?php }
							
							}
							
							
							

                       echo (isset($v['pPrice']) &&  empty($v['pPrice'])?'<span>SALE</span>':'');
                            ?>
                            </a>
                            </div>
                                <div class="product-info">
                                    <div class="name-row">
                                        <a href="<?php echo base_url_site.$v['slugCategory'].'/'.$v['slug'];?>"><div class="product-logo"><img src="<?php echo base_url_images.$v['brandImage']; ?>" alt="product-logo" width="54px" height="29px" /></div>
                                        <div class="product-name"><?php echo $v['customTitle']; ?></div></a>
                                    </div>
                                    <div class="cost-row">
    <div class="product-color"><button><?php 
    echo $v['pTotalColors'];   
    ?> colors <i class="fa fa-sort-down"></i></button>
    <div class="color-palate">
<div class="color-wrapper-home">
<form name="frmcolor" id="frmcolor" method="post">

<div class="color-title">Hover over color...</div>
<div class="color-box">
<?php
                            $color=explode(",",$v['pColors']);
							
                            foreach(@$color as $kk=>$colvv){
							$colrNSwtach=explode("^",@$colvv);
                            if(isset($colrNSwtach[2]) && intval($colrNSwtach[2])) {
							$clrId=$colrNSwtach[2];
							$ctitle=$colrNSwtach[3];
							} else {
							$clrId=$colrNSwtach[3];
							$ctitle=$colrNSwtach[4];
							}
							
                            ?>
 
<a href="javascript:void(0);" onclick="fnProductPage('<?php echo base_url_site.$v['slugCategory'].'/'.$slug;?>',<?php echo @$clrId;?>)" title="<?php echo @$ctitle; ?>" alt="<?php echo @$ctitle; ?>">
<div class="shirtcolor"  style="background-color:<?php echo @$colrNSwtach[0];?>">
<?php if((isset($colrNSwtach[0]) && $colrNSwtach[0]!="") && (isset($colrNSwtach[1]) && $colrNSwtach[1]!="")) {?>
<div class="shirtcolor-lft" style="background-color: <?php echo $colrNSwtach[0]; ?>;"></div>
<div class="shirtcolor-rght" style="background-color: <?php echo $colrNSwtach[1]; ?>;"></div>
<?php }?>
</div>
</a>
<?php } ?>
</div>
</form>
</div>
</div>
    </div>
                                        <div class="product-price" style="font-weight: normal;">
											<span class="starting_at" style="font-size:12px;color:#282829;">Starting at</span>
											<h3><?php echo SYMBOL.''.number_format($pPrice,2,'.','');?></h3>
										</div>
                                    </div>
                                </div>
                            </div>
                           <?php 
						   }
						   $rowc++;
						   }
						   
						   }
						  }
}
?>

                
                
                        </div>
						
                       
<?php 
//print_r($result);
$spid1="";
$w=1;
foreach($result as $key=>$val){
if($w==1 && $val[0]['comparableGroup']!=0 && $val[0]['comparableGroup']!="") {
$spid=$val[0]['comparableGroup'];
}
$w++;
}

if(isset($iscustomerviewed) && $iscustomerviewed==0) {
$params = array('');
if(isset($spid) && !empty($spid)) {

$pcwhoproducts = $db->rawQuery("SELECT slug,customTitle,slugCategory,styleImage,styleID,brandImage,title,pPrice,pColors,pTotalColors,styleImageStatus,pmodelImage FROM `ci_styles` where comparableGroup='".$spid."' and styleID<>$stid and pPrice>0 and isExistProduct=1 order by bestsellerrank asc LIMIT 0, 4", $params);



if(isset($pcwhoproducts) && !empty($pcwhoproducts)) {

echo '<div class="spacer"></div>
<div class="product-row">
                            <h2>Customers who viewed</h2>';

foreach($pcwhoproducts as $k=>$v) {
$pPrice= $v['pPrice'];
							
					if(intval($pPrice)>0) {
					
					if(isset($v['customeseo']) && $v['customeseo']!="") {
						$slug=$v['customeseo'];
						} else {
						$slug=$v['slug'];
						}
						
					?>
                    <div class="product-holder">
                                <div class="product-image-box">
                                <a href="<?php echo base_url_site.$v['slugCategory'].'/'.$v['slug'];?>">
							<?php 
                            
							if($v['styleImageStatus']==1) {
							
							if(!empty($v['pmodelImage'])) { 
							?>
							<img src="<?php echo fnCPMainImages($v['pmodelImage']);?>" alt="product-image"/>
							<?php  }
							
							} else {		
							if(!empty($v['styleImage'])) {?>
							<img src="<?php echo fnComPImages($v['styleImage']);?>" alt="product-image" />
							<?php }
							
							}
							

                       echo (isset($v['pPrice']) &&  empty($v['pPrice'])?'<span>SALE</span>':'');
                            ?>
                            </a>
                            </div>
                                <div class="product-info">
                                    <div class="name-row">
                                        <a href="<?php echo base_url_site.$v['slugCategory'].'/'.$v['slug'];?>"><div class="product-logo"><img src="<?php echo base_url_images.$v['brandImage']; ?>" alt="product-logo" width="54px" height="29px" /></div>
                                        <div class="product-name"><?php echo $v['customTitle']; ?></div></a>
                                    </div>
                                    <div class="cost-row">
    <div class="product-color"><button><?php 
    echo $v['pTotalColors'];   
    ?> colors <i class="fa fa-sort-down"></i></button>
    <div class="color-palate">
<div class="color-wrapper-home">
<form name="frmcolor" id="frmcolor" method="post">

<div class="color-title">Hover over color...</div>
<div class="color-box">
<?php
                            $color=explode(",",$v['pColors']);
							
                            foreach(@$color as $kk=>$colvv){
							$colrNSwtach=explode("^",@$colvv);
                           if(isset($colrNSwtach[2]) && intval($colrNSwtach[2])) {
							$clrId=$colrNSwtach[2];
							$ctitle=$colrNSwtach[3];
							} else {
							$clrId=$colrNSwtach[3];
							$ctitle=$colrNSwtach[4];
							}
							
                            ?>
 
<a href="javascript:void(0);" onclick="fnProductPage('<?php echo base_url_site.$v['slugCategory'].'/'.$slug;?>',<?php echo @$clrId;?>)" title="<?php echo @$ctitle; ?>" alt="<?php echo @$ctitle; ?>">
<div class="shirtcolor"  style="background-color:<?php echo @$colrNSwtach[0];?>">
<?php if((isset($colrNSwtach[0]) && $colrNSwtach[0]!="") && (isset($colrNSwtach[1]) && $colrNSwtach[1]!="")) {?>
<div class="shirtcolor-lft" style="background-color: <?php echo $colrNSwtach[0]; ?>;"></div>
<div class="shirtcolor-rght" style="background-color: <?php echo $colrNSwtach[1]; ?>;"></div>
<?php }?>
</div>
</a>
<?php } ?>
</div>
</form>
</div>
</div>
    </div>
                                        <div class="product-price" style="font-weight: normal;">
											<span class="starting_at" style="font-size:12px;color:#282829;">Starting at</span>
											<h3><?php echo SYMBOL.''.number_format($pPrice,2,'.','');?></h3>
										</div>
                                    </div>
                                </div>
                            </div>
                           <?php 
						   }
						   $rowc++;
						   }
				
					   
				 }
						  }

}
if(getTCustomerRev($stid)!=0) {	
echo '<div class="spacer"></div>';	
} else {
echo '<div class="spacer"></div>';	
}						  
if(getTCustomerRev($stid)!=0) {	
echo '<div class="spacer"></div>';						  
?>



<div class="product-row">
<h2>Customers Reviews</h2>
<a name="preview"></a>
<div class="review-box">
				<div class="rw-top-box">
				<div class="rw-one">
				
<input value="<?php echo number_format((getTVoteRev($stid)/getCVoteRev($stid)),1); ?>" type="number" class="rating" min=0 max=5 step=0.1 data-size="md" data-stars="5" productId=<?php echo $stid; ?>> 
<div class="rw-avg">Average Customer Rating</div>

				
                <br />
                <?php if(chkProReviewlistbyStyleId($_SESSION["uid"],$stid)==true) { ?>
				<button onclick="fnReview();">Write a Review</button>
                <?php }?>
				</div>
<?php
$reviewlist=getReviewlist($stid);
$totalReviews=getTCustomerRev($stid);
$cstar1=0;
$tstar1="";
$cstar2=0;
$tstar2="";
$cstar3=0;
$tstar3="";
$cstar4=0;
$tstar4="";
$cstar5=0;
$tstar5="";
/*echo '<pre>';
print_r($reviewlist);*/
foreach($reviewlist as $k=>$crev) {
if($crev['vote']>0 && $crev['vote']<=1) {
$cstar1=$cstar1+1;
$tstar1+=$crev['vote'];
}
if($crev['vote']>1 && $crev['vote']<=2) {
$cstar2=$cstar2+1;
$tstar2+=$crev['vote'];
}
if($crev['vote']>2 && $crev['vote']<=3) {

$cstar3=$cstar32+1;
$tstar3+=$crev['vote'];
}
if($crev['vote']>3 && $crev['vote']<=4) {
$cstar4=$cstar4+1;
$tstar4+=$crev['vote'];
}
if($crev['vote']>4 && $crev['vote']<=5) {
$cstar5=$cstar5+1;
$tstar5=$crev['vote'];
}

}

?>                
				<div class="rw-two">
				<h4>Customer Review</h4>
<table id="books">

<tr> 
<td>5 Star</td> 
<td id="book5" class="book_rating" title="<?php echo number_format((($cstar5/$totalReviews)*100),2)?>% of reviews of 5 Star" alt="<?php echo  number_format((($cstar5/$totalReviews)*100),2)?>% % of reviews of 5 Star"><span style="visibility:hidden;"><?php echo (($tstar5/$totalReviews)==0)?"":(($cstar5/$totalReviews)*100); ?></span></td> 
<td><?php echo $cstar5; ?></td>
</tr>
<tr> 
<td>4 Star</td> 
<td id="book4" class="book_rating" title="<?php echo number_format((($cstar4/$totalReviews)*100),2)?>% of reviews of 4 Star" alt="<?php echo  number_format((($cstar5/$totalReviews)*100),2)?>% of reviews of 4 Star"><span style="visibility:hidden;"><?php echo (($cstar4/$totalReviews)==0)?"":(($cstar4/$totalReviews)*100); ?></span></td> 
<td><?php echo $cstar4; ?></td>
</tr>
<tr> 
<td>3 Star</td> 
<td id="book3" class="book_rating" title="<?php echo number_format((($cstar3/$totalReviews)*100),2)?>% of reviews of 3 Star" alt="<?php echo  number_format((($cstar5/$totalReviews)*100),2)?>% of reviews of 3 Star"><span style="visibility:hidden;"><?php echo (($cstar3/$totalReviews)==0)?"":(($cstar3/$totalReviews)*100); ?></span></td> 
<td><?php echo $cstar3; ?></td>
</tr>
<tr> 
<td>2 Star</td> 
<td id="book2" class="book_rating" title="<?php echo number_format((($cstar2/$totalReviews)*100),2)?>% of reviews of 2 Star" alt="<?php echo  number_format((($cstar5/$totalReviews)*100),2)?>% of reviews of 2 Star"><span style="visibility:hidden;"><?php echo (($tstar2/$totalReviews)==0)?"":(($cstar2/$totalReviews)*100); ?></span></td> 
<td><?php echo $cstar2; ?></td>
</tr>
<tr> 
<td>1 Star</td> 
<td id="book1" class="book_rating" title="<?php echo number_format((($cstar1/$totalReviews)*100),2)?>% of reviews of 1 Star" alt="<?php echo  number_format((($cstar5/$totalReviews)*100),2)?>% of reviews of 1 Star"><span style="visibility:hidden;"><?php echo (($tstar1/$totalReviews)==0)?"":(($cstar1/$totalReviews)*100); ?></span></td> 
<td><?php echo $cstar1; ?></td>
</tr>
</table>
				</div>
				<div class="rw-three">
				<div class="cst-rw"><p><?php echo getTCustomerRev($stid);?><br>CUSTOMER REVIEW</p></div>
				<?php /*?><div class="cst-recom"><p><?php echo getTCountRec($stid);?><br>RECOMMEND</p></div><?php */?>
				</div>
				</div>



<!--<div class="write-rw-box">
				<h4>Your Reviews</h4>
                
                

</div>-->

<?php 


foreach($reviewlist as $k=>$rev) {
?>                
                
				<div class="customer-review">
				<div class="review-headline"><p><b><?php echo $rev["creviewheading"] ?></b></p></div>
				<div class="rw-details"><div class="crevleftrat">Rating</div> <div class="crevmidtrat"><input value="<?php echo getRatingByProductIdnId($rev["styleID"],$rev["id"]); ?>" type="number" class="rating" min=0 max=5 step=0.5 data-size="md" data-stars="5" productId=<?php echo $rev["styleID"]; ?>> </div><?php /*?><div class="crevrigtrat"> Recommend: <?php if(getCountRec($rev["styleID"],$rev["id"])!=0) { echo 'Yes'; } else { echo 'No';}?> </div><?php */?></div>
				<div class="customer-data"><p>By : <?php echo getCustomerName($rev["customerId"]);?> <b>|</b> On <?php echo date('M d Y',strtotime($rev["reviewdate"])); ?></p></div>
				<div class="customer-msg"><?php echo $rev["creview"] ?></a></div>
				<?php /*?><div class="review-feedback"><span id="recid"><?php
				if(getCountRec($rev["styleID"],$rev["id"])!=0) {
				 echo getCountRec($rev["styleID"],$rev["id"]); ?> people found this helpful.
                 <?php } ?>
                  Was this review helpful?</span> 
                  <?php if(isset($_SESSION["uid"]) && $_SESSION["uid"]!="") {?>
                  <a href="javascript:void(0)" class="recomid" product-id="<?php echo $rev["styleID"]; ?>" revid="<?php echo $rev["id"]; ?>">Yes</a>
                  <?php } else { ?>
                  
                  <a href="<?php echo  base_url_site;?>login?redirect_url=<?php echo base_url_site.$_REQUEST['cat'].'/'.$_REQUEST["stid"];?>" class="recomid1">Yes</a>
                  <?php }?>
                  </div><?php */?>
				</div>
				
<?php }?>			
				
				
				
				</div>
</div>
<?php /*?> <link rel="stylesheet" type="text/css" href="<?php echo base_url_site; ?>css/ratingbar.css" /> 
<script type="text/javascript" src="<?php echo base_url_site; ?>js/jquery.ratingbar.js"></script><?php */?>

<!-- Include easing for more animation styles -->
<script type="text/javascript" src="<?php echo base_url_site; ?>js/jquery.easing.1.3.js"></script>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {

	// Custom markup
  var ratingconfig = {
		animate:		true,
		duration:		1000,
		ease:			"easeOutBounce",
		maxRating: 		100,
		wrapperWidth:	300,
		showText: 		true,
		wrapperClass:	"wrapper_books",
		innerClass:		"inner_books",
		textClass: 		"rating_books"
	}
	$('.book_rating').ratingbar(ratingconfig);

  // Dynamically update the rating if the value changes
  $("#bumper").click(function() {
    var randomrating1=Math.floor(Math.random()*11)
    $("#book5 ." + ratingconfig.textClass).text(randomrating1);

    var randomrating2=Math.floor(Math.random()*11)
    $("#book4 ." + ratingconfig.textClass).text(randomrating2);

	var randomrating2=Math.floor(Math.random()*11)
    $("#book3 ." + ratingconfig.textClass).text(randomrating2);
	
	var randomrating2=Math.floor(Math.random()*11)
    $("#book2 ." + ratingconfig.textClass).text(randomrating2);
	
	var randomrating2=Math.floor(Math.random()*11)
    $("#book1 ." + ratingconfig.textClass).text(randomrating2);
	
	
    $('.book_rating').ratingbar('update', ratingconfig);
  });
	

$('.recomid').click(function(){
var stylid=$(this).attr('product-id');
var revid=$(this).attr('revid');
$(this).parent().children('#recid').html('Sending Feedback...');
var cval=$(this);
$.ajax({
url: "<?php echo base_url_site?>addon/recommend.php",
data: {stylid:stylid,revid:revid,type:'save'},
async: true,
success: function( data ) {
cval.hide();
cval.parent().children('#recid').html('');
cval.parent().children('#recid').css('color','#007600');
cval.parent().children('#recid').html(data);
},
error: function(e) {
console.log(e);
},
timeout: 30000  
});

});

});
</script>   
<?php }?>                
                
                        </div>
                        
            </div>
        </div>    

     
        <!--MAIN END HERE-->
         <!--FOOTER START HERE-->
<script type="text/javascript">

        function changeimage(event)
        {
        event = event || window.event; 

        var targetelement = event.target || event.srcElement;

        if(targetelement.tagName == "IMG")
        {

        document.getElementById("pk-image").src = targetelement.getAttribute("src");

        }

        }
		
        </script>         

<div id="background-fade">
<div class="outer-overlay">
<div class="pop-up-holder">
<h3>Tell Us How Many You Want.<span class="pop-close">x</span></h3>
<div class="pop-img"><img src="<?php echo base_url_site?>images/indication-box.jpg" alt=""/></div>
<div class="pop-up-close"><button class="pop-close">Close</button></div>
</div>
</div>
</div> 
<?php } elseif($slugurlid){?>
<style type="text/css">

.wrap1{
	margin:0 auto;
	width:768px;
	min-height:480px;
}
.logo1{
	text-align:center;
}
.logo1 img{
	width:350px;
}
.logo1 p{
	color:#272727;
	font-size:40px;
	margin-top:1px;
	font-family:roboto;
}	
	

@media screen and (max-width : 767px)
{
   .wrap1{width:100%;}

}

@media screen and (max-width : 500px)
{
.logo1 img{width:75%;} 
.logo1 p{font-size:25px;}
}	
</style>

<div class="site-center">
<div class=" empty-cart">
	<div class=" no-item">
			<span>This item is temporarily not available</span><br>You can continue shopping with some other products.
			 
			
<!---728x90--->
			<!--<div class="sub1">
			  <p><a href="#">Back </a></p>
			</div>-->
	</div>
	<div class="keep-shopping"><a id="keepshop" href="<?php echo base_url_site.$_REQUEST["cat"]; ?>">Continue Shopping</a></div>
 </div>
  </div>
 


<?php } else {?>
<style type="text/css">

.wrap1{
	margin:0 auto;
	width:768px;
	min-height:480px;
}
.logo1{
	text-align:center;
}
.logo1 img{
	width:350px;
}
.logo1 p{
	color:#272727;
	font-size:40px;
	margin-top:1px;
	font-family:roboto;
}	
	

@media screen and (max-width : 767px)
{
   .wrap1{width:100%;}

}

@media screen and (max-width : 500px)
{
.logo1 img{width:75%;} 
.logo1 p{font-size:25px;}
}	
</style>
<div class="wrap1">
	<div class="logo1">
			<p>OOPS! - Could not Find it</p>
			<img src="<?php echo base_url_site?>images/404-11.png">
			
<!---728x90--->
			<!--<div class="sub1">
			  <p><a href="#">Back </a></p>
			</div>-->
	</div>
 </div>

 
 

 

<?php
}
include("includes/footer_inc.php");
?>
<style type="text/css">
.myText{
	padding: 0px;
position: relative;
z-index: 13;
width: 39%;
color: rgb(10, 41, 114);
font-family: roboto condensed;
font-size: 12px;
background-color: #fff;
text-align: left;
border: 1px solid #fff;
border-radius: 5px;
width: 100%;
box-sizing: border-box;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
padding: 0px 8px;
}
.size_chart {
	width: 120px;
	height: 50px;
	cursor:pointer;
	margin-bottom:10px;
	margin-left:12px;
	z-index:10000;
}
table, th, td {

}

/*Why value() didn't work?
Because you are using button element and it doesn't have any value.
But if you use <input type="button" value="Hide"> instead of <button>, then you could use value() instead of text().
*/

/*Why should I use .trim() in my jquery code?
trim()function removes all newlines, spaces (including non-breaking spaces), and tabs from the beginning and end of the supplied string. If these whitespace characters occur in the middle of the string, they are preserved.
Now you will wonder that there is no white space in your text (Hide)!!
Yes, but as you wrote your text (your button label) in a new row and with a tab behind it and again a new row after it, your .text will have following value:
.text() === return + tab + YourText + return.
With .trim() you remove also those white spaces.
If you write the word Hide without any return and tab like this:
<button class="btn">Hide</button>
You won't need the .trim() function any more.

*/

</style>
<script>


$(document).ready(function(){ 

	

		$(document).on('click','.size_chart',function(){
			$(".myText").slideToggle('599');
		
		

    // return false;
	});


	//Function is used to check color name has white space
	/*function hasWhiteSpace(s) {
	  return s.indexOf(' ') >= 0;
	}*/
	
	var styleIdVal = '<?php echo $stid;?>';
	//var selectedclrname = $("#clrname").html();
	$.ajax({
		url: '<?php echo base_url_site; ?>addon/pbulkdetails.php',
		method: "post",
		data: 'styleID=<?php echo $stid;?>',
		async: true,
		dataType: "json",
		success: function(msg)	{
			$('#bulkid').html(msg.bulkdata);
			/*if (!$('#bulkid').is(':empty')){
				$('#hiddenFldSelClrName').val(selectedclrname); //set hidden filed color name at page load
				
				var selectedclrnamehasSpace = hasWhiteSpace(selectedclrname);
				if(selectedclrnamehasSpace){
					var selectedclrNameId = selectedclrname.replace(/\s+/g, '-'); //replace space with hypen
					selectedclrNameId = selectedclrNameId.replace(/\//g, ''); //replace forward slash
				}else{
					var selectedclrNameId = selectedclrname;
				}
				
				$('#bulkAddtoCart tr#'+selectedclrNameId).find('.numbersOnly').attr('disabled','disabled')
			}*/
		}
	});
});
</script>

<script>
	function fnReview() {
	window.location.href="<?php echo base_url_site?>product-review?stname=<?php echo str_replace(" ","_",$brandN)."_".$styleNameH;?>";
	}
	function fnReviewRev() {
	window.location.href="<?php echo base_url_site?>product-review?redirect_url=<?php echo redirect_url;?>&tp=new&styleID=<?php echo $stid;?>";
	}
	</script>