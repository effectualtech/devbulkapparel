<?php

ob_start();
include("includes/header_inc.php");
include("includes/PHPMailer.php");
include("includes/SMTP.php");
if((!isset($_SESSION['uid']) && empty($_SESSION['uid'])) && (!isset($_SESSION['cid']) && empty($_SESSION['cid']))){
	header('Location:'.$base_url_site.'login');
	exit;
}

 

if(isset($_REQUEST["PayerID"])&& !empty($_REQUEST["PayerID"]))
{
require('includes/config.php');
require('includes/paypal.php');

$paypal = new PayPal($config);

$result = $paypal->call(array(
  'method'  => 'DoExpressCheckoutPayment',
  'paymentrequest_0_paymentaction' => 'sale',
  'PAYMENTREQUEST_0_DESC' => 'Bulkapparel Order',
  'paymentrequest_0_amt'  => $_GET['amt'],
  'PAYMENTREQUEST_0_ITEMAMT'=>$_GET['amt'],
  'L_PAYMENTREQUEST_0_NAME0'=>'Item(s) Purchase From Bulkapparel',
  'L_PAYMENTREQUEST_0_DESC0'=>'Purchase From Bulkapparel',
  'L_PAYMENTREQUEST_0_AMT0'=>$_GET['amt'],
  'L_PAYMENTREQUEST_0_QTY0'=>'1',
  'L_PAYMENTREQUEST_0_ITEMCATEGORY0'=>'Physical',
  'PAYMENTREQUEST_0_INVNUM'=>$_GET['invoice'],
  'paymentrequest_0_currencycode'  => 'USD',
  'token'  => $_GET['token'],
  'payerid'  => $_GET['PayerID'],
));
//print_r($result);
if ($result['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed') 

{
      $tranid= $result['PAYMENTINFO_0_TRANSACTIONID'];
	if(isset($_REQUEST["orderId"])&& !empty($_REQUEST["orderId"]))
{
	$orderno=$_REQUEST["orderId"];
	
}

if((round(totalCartPrice())<=round(EDIRATE)) && (round(EDIRATE)!=0 || round(EDIRATE)!="0.00")) 

{
	
	
    $ordStat="Pending";
	$scordStat="Approved";
} else {
	$ordStat = "Pending";
	$scordStat = "Pending";
}
///////////////////////////////////////////////////////////////////////////////////////////////////




if(isset($_SESSION["uid"]) && $_SESSION["uid"]!="") {
	$user_id1 = $_SESSION["uid"];
} else {
	$user_id1 = $_SESSION["cid"];
}
$user_id = $user_id1;

if(isset($user_id) && $user_id!="") {
	global $db;
	$cshipdetails = $db->rawQueryOne ("select * from ci_address where cid=? and addressType=0 and ordId='".$orderno."'", array($user_id));
	$custname = explode("^",$cshipdetails['customer']);
	
	
	$cbilldetails = $db->rawQueryOne ("select * from ci_address where cid=? and addressType=1 and ordId='".$orderno."'", array($user_id));
	$custnameb=explode("^",$cbilldetails['customer']);
	
} else {
	$cshipdetails = $db->rawQueryOne ("select * from ci_address where cid=? and addressType=0 and ordId='".$orderno."'", array($user_id));
	$custname=explode("^",$cshipdetails['customer']);
	
	
	$cbilldetails = $db->rawQueryOne ("select * from ci_address where cid=? and addressType=1 and ordId='".$orderno."'", array($user_id));
	$custnameb=explode("^",$cbilldetails['customer']);
	
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////
	//if(isset($_GET['tax'])) $taxT=$_GET['tax']; else $taxT="0.00";
	//$ntotalAmt=$_POST['amt']+$taxT;
	//$total      = str_replace(",","",$ntotalAmt);
	//$cvv        = $_POST['ccode'];
	//$invoice    = substr(time(), 0, 6);
	

	
	
	
		
			$approval_code  = $_GET['token'];
			$avs_result     = "";
			$cvv_result     = "";
			$transaction_id = $tranid;
			$accountNumber  = $_GET['PayerID'];
			//Added other transaction info by Vipul at 8Dec2017
			$responseCode  = ""; //responseCode
			$cavvResultCode = "";//cavvResultCode
			$transHash = "";//transHash
			$accountType = "Paypal";//accountType
			$refId = "";
			$resultCode = "OK";
			$messageCode = "";
			$messageText = "Successful.";
			$transRespMsgCode = "";//transRespMsgCode
			$transRespMsgDescription = "Paypal";
			$customername="";
			$tQty=0;
			$tPrice=0;
			$isShipError=0;
			$isBillError=0;
			$isOrProdError=0;
			$isOrderError=0;
			$response=array();
			$estimateDelivery="";
			if(totalCartItems()>0) {
				$cartitems=OrderPriceCalc();	
				foreach($cartitems as $key=>$val) {
					$mImage1="";
						
					if(file_exists(dir_path.$val['colorFrontImage']) && $val['colorFrontImage']!=""){
						$mImage1=$val['colorFrontImage'];
					}
					if(file_exists(dir_path.$val['colorSideImage'])  && $val['colorSideImage']!=""){
						$mImage1=$val['colorSideImage'];
					}
					if(file_exists(dir_path.$val['colorBackImage'])  && $val['colorBackImage']!=""){
						$mImage1=$val['colorBackImage'];
					} else {
					$mImage1=$val['styleImage'];
					}
					
				if(isset($_SESSION["estimateDel"][$val['sku']]) && $_SESSION["estimateDel"][$val['sku']]!="") {
				$estimateDelivery="<img src='".base_url_site."images/truck-icon.gif' style='width:30px'> <strong>Est. Delivery - </strong> ".$_SESSION["estimateDel"][$val['sku']];
				}
					
					$data = array ('orderId' => $orderno,'styleID' => $val['styleID'],'pPrice' => $val['customerPrice'],'styleImage' => $val['styleImage'],'colorFrontImage' => $mImage1,'title' => $val['title'],'unitWeight' => $val['unitWeight'],'baseCategory' => $val['baseCategory'],'styleName' => $val['styleName'],'color1' => $val['color1'],'color2' => $val['color2'],'sizeName' => $val['sizeName'],'qty' => $val['qty'],'sku' => $val['sku'],'estimateDelivery' => $estimateDelivery);
					$tQty=$tQty+$val['qty'];
					$totalPrice=$val['qty']*$val['customerPrice'];
					$tPrice=$tPrice+$totalPrice;
					$insert = $db->insert("ci_order_products",$data);
				}
				$isOrProdError=1;
			}
			$totalbulkdiscount=0;			   
			foreach($cartitems as $key2=>$val2) {
				if(isset($val2['styleID'])) {	
					if(getCheckStyleIsBulk($val2['styleID'])==0) {
						$totalPrice=$val2['qty']*$val2['customerPrice'];
						$totalbulkdiscount=$totalbulkdiscount+$totalPrice;
					}
				}
			}					   
			$tbulkdiscount=0;
			$l=0;
			global $iscouponbulk;
			$iscouponbulk=0;
			$isbulk="";
			$isbulkT="";
			$discountoffers=getDiscountOfferInfo();
			$toRecords=count($discountoffers);
					  $m=0;
			for($h=0;$h<count($discountoffers);$h++) {
				if(($totalbulkdiscount>=$discountoffers[$h]['dcost']) && ($totalbulkdiscount<=$discountoffers[$h+1]['dcost'])) {
					$iscouponbulk=$discountoffers[$h]['iscoupon'];
					$tbulkdiscount=($totalbulkdiscount*($discountoffers[$h]['dpercentage']/100));
						
					$isbulkT.='<tr><td>Bulk Discount:</td><Td align="right"><span style="font-size:15px;color:#FF0000;">-</span> '.SYMBOL.number_format($tbulkdiscount,2).'</Td></tr>';
					$isbulkT=number_format($tbulkdiscount,2);
					$m=1;	
				}  else {
							if(($toRecords-1)==$h && $m==0 && $totalbulkdiscount>$discountoffers[$h]['dcost']) {
							$iscouponbulk=$discountoffers[$h]['iscoupon'];
					$tbulkdiscount=($totalbulkdiscount*($discountoffers[$h]['dpercentage']/100));
						
					$isbulkT.='<tr><td>Bulk Discount:</td><Td align="right"><span style="font-size:15px;color:#FF0000;">-</span> '.SYMBOL.number_format($tbulkdiscount,2).'</Td></tr>';
					$isbulkT=number_format($tbulkdiscount,2);	
							} 
				
				}
				
				
				$l++;
			}
			$strCoupon="";		
			$strCouponsh="";	
			$strCouponPrice="";	
			$tsubtotal=str_replace(",","",totalCartPrice());
			$totalAmt=$tsubtotal;
			if($tbulkdiscount>0 && $iscouponbulk==1) {	   
				if(isset($_SESSION['coupons']) && !empty($_SESSION['coupons'])) { 
					$totalSubTotal=$tsubtotal;
					$totalDiscount=0;
					foreach($_SESSION['coupons'] as $ky=>$cval) { 
						$couponDetails=getCouponAmount($ky); 
						if(isset($_SESSION["uid"]) && $_SESSION["uid"]!="") {
						$userusedc=getCouponUsedByUser($ky,$_SESSION["uid"]);
							if(isset($userusedc) && $userusedc!=0) {
									$totalused=$userusedc+1;
									$cdata1 = array ('used' => $totalused);
									$db->where ('couponcode', $ky);
									$db->where ('userid', $_SESSION["uid"]);
									$update = $db->update("ci_couponusedbyuser",$cdata1);
									} else {
									$cdata1 = array ('userid' => $_SESSION["uid"],'couponcode' => $ky,'used' => 1);
									$update = $db->insert("ci_couponusedbyuser",$cdata1);
									}
						} else {
									if(isset($couponDetails['usedpercoupon']) && $couponDetails['usedpercoupon']!=0) {
									$totalused1=$couponDetails['usedpercoupon']+1;
									$cdata1 = array ('usedpercoupon' => $totalused1);
									$update = $db->insert("ci_coupons",$cdata1);
									} else {
									$cdata1 = array ('usedpercoupon' => 1);
									$update = $db->insert("ci_coupons",$cdata1);
									}
						}
						
						foreach($cartitems as $key2=>$val2) {
							if(isset($val2['styleID'])) {	
								if(getCheckStyleIsCoupon($val2['styleID'])==0) {
									$totalPrice=$val2['qty']*$val2['customerPrice'];
									if($couponDetails['dtype']==1) {
										$cdiscountprice2=$couponDetails['cvalue'];
										$cartDiscount=$cartDiscount+$cdiscountprice2;
										$sumArray[$couponDetails['ccode']]=$cdiscountprice2;
									}
									if($couponDetails['dtype']==2) {
										$cpdiscountprice2=(($totalPrice*($couponDetails['cvalue'])/100));
										$cartPDiscount=$cartPDiscount+$cpdiscountprice2;
										$sumArray[$couponDetails['ccode']]=$cpdiscountprice2;
									}
								
								}
							}
						}
						if($couponDetails['dtype']==1) {
							$cdiscount=$cartDiscount;
						}
						if($couponDetails['dtype']==2) {
							$cdiscount=$cartPDiscount;
						}
						
					}
					foreach($sumArray as $kt=>$cprice) {         
						$strCoupon.=$kt.'^'.$cprice.',';
						$strCouponsh.='<tr><td>Coupon Code - '.$kt.':</td><Td align="right"><span style="font-size:15px;color:#FF0000;">-</span> '.SYMBOL.number_format($cprice,2).'</Td></tr>';
						$totalDiscount=$totalDiscount+$cprice;	
					} 
					$totalAmt=$totalSubTotal-$totalDiscount;
				}	
			} 
			else if($tbulkdiscount==0 && $iscouponbulk==0) {
				if(isset($_SESSION['coupons']) && !empty($_SESSION['coupons'])) { 
					$totalSubTotal=$tsubtotal;
					$totalDiscount=0;
					foreach($_SESSION['coupons'] as $ky=>$cval) { 
						$couponDetails=getCouponAmount($ky); 
						if(isset($_SESSION["uid"]) && $_SESSION["uid"]!="") {
						$userusedc=getCouponUsedByUser($ky,$_SESSION["uid"]);
							if(isset($userusedc) && $userusedc!=0) {
									$totalused=$userusedc+1;
									$cdata1 = array ('used' => $totalused);
									$db->where ('couponcode', $ky);
									$db->where ('userid', $_SESSION["uid"]);
									$update = $db->update("ci_couponusedbyuser",$cdata1);
									} else {
									$cdata1 = array ('userid' => $_SESSION["uid"],'couponcode' => $ky,'used' => 1);
									$update = $db->insert("ci_couponusedbyuser",$cdata1);
									}
						} else {
									if(isset($couponDetails['usedpercoupon']) && $couponDetails['usedpercoupon']!=0) {
									$totalused=$couponDetails['usedpercoupon']+1;
									$cdata1 = array ('usedpercoupon' => $totalused);
									$update = $db->insert("ci_coupons",$cdata1);
									} else {
									$cdata1 = array ('usedpercoupon' => 1);
									$update = $db->insert("ci_coupons",$cdata1);
									}
						}
						foreach($cartitems as $key2=>$val2) {
							if(isset($val2['styleID'])) {	
								if(getCheckStyleIsCoupon($val2['styleID'])==0) {
									$totalPrice=$val2['qty']*$val2['customerPrice'];
									if($couponDetails['dtype']==1) {
										$cdiscountprice2=$couponDetails['cvalue'];
										$cartDiscount=$cartDiscount+$cdiscountprice2;
										$sumArray[$couponDetails['ccode']]=$cdiscountprice2;
									}
									if($couponDetails['dtype']==2) {
										$cpdiscountprice2=(($totalPrice*($couponDetails['cvalue'])/100));
										$cartPDiscount=$cartPDiscount+$cpdiscountprice2;
										$sumArray[$couponDetails['ccode']]=$cpdiscountprice2;
									}
									
								}
							}
						}
						if($couponDetails['dtype']==1) {
							$cdiscount=$cartDiscount;
						}
						if($couponDetails['dtype']==2) {
							$cdiscount=$cartPDiscount;
						}
						
					}
					foreach($sumArray as $kt=>$cprice) {        
						$strCoupon.=$kt.'^'.$cprice.',';
						$strCouponsh.='<tr><td>Coupon Code - '.$kt.':</td><Td align="right"><span style="font-size:15px;color:#FF0000;">-</span> '.SYMBOL.number_format($cprice,2).'</Td></tr>';
						$totalDiscount=$totalDiscount+$cprice;	
					} 
					$totalAmt=$totalSubTotal-$totalDiscount;
				}	
			} 
			else {
				unset($_SESSION['coupons']);
			}
			if(totalCartPrice()>FREESHIPPING) {
				$shippingcharge="0.00";
				$totalAmt=$totalAmt;
			} else {
				$shippingcharge=SHIPPINGCHARGE;
				$totalAmt=$totalAmt+SHIPPINGCHARGE;
			}
			$totalAmt=$totalAmt-$tbulkdiscount;
			if($totalAmt<=0) {
				$totalAmt="0.00";
			}
			$totalAmt=$totalAmt+$_GET['tax'];
			





if(isset($_SESSION["estimateDeliveryDate"]) && $_SESSION["estimateDeliveryDate"]!="") {
$estimateDeliveryDate=$_SESSION["estimateDeliveryDate"];
} else {
$estimateDeliveryDate="";
}




$isuser=0;
if(isset($_SESSION["uid"]) && $_SESSION["uid"]!="") {
foreach($_SESSION['coupons'] as $ky=>$cval) {
$coupcode=$ky;
$userusedc=getCouponUsedByUser($coupcode,$_SESSION["uid"]);
$couponDetails1=getCouponAmount($coupcode); 
if(($userusedc==$couponDetails1['limitperuser']) && ($couponDetails1['limitperuser']!=0 && $userusedc!=0)) {
} else {
$isuser=1;
			if(isset($userusedc) && $userusedc!=0) {
			$totalused=$userusedc+1;
			$cdata1 = array ('used' => $totalused);
			$db->where ('couponcode', $coupcode);
			$db->where ('userid', $_SESSION["uid"]);
			$update = $db->update("ci_couponusedbyuser",$cdata1);
			} else {
			$cdata1 = array ('userid' => $_SESSION["uid"],'couponcode' => $coupcode,'used' => 1);
			$update = $db->insert("ci_couponusedbyuser",$cdata1);
			}

}
}

}

if(isset($_SESSION["coupons"]) && $_SESSION["coupons"]!="") {
foreach($_SESSION['coupons'] as $ky=>$cval) {
$couponDetails1=getCouponAmount($ky); 
	if($isuser!=1) {	
	$usedpercoupon=$couponDetails1['usedpercoupon']+1;
	
	if($usedpercoupon<=$couponDetails1['limitpercoupon']) {
		
		$cdata = array ('usedpercoupon' => $usedpercoupon);
		$db->where ('ccode', $ky);
        $update = $db->update("ci_coupons",$cdata);
		}
	}
}
}


             $invoice=$_GET['invoice'];
			 $usertype=$_GET['usertype'];
			
			$customerFirstName = $custnameb[0]; //shipping first name
			$customerLastName = $custnameb[1]; //shipping last name
			$customerEmail = $cbilldetails['email']; //shipping email
			
			$orderData1 = array ('customerOrderID' => $orderno,'invoiceNo' => $invoice,'transactionId' => (string)$transaction_id,'xcardno' => $accountNumber,'totalItems' => $tQty,'tax' => $_GET['tax'],'estimatedeliverydate' => $estimateDeliveryDate,'bulkDiscount' => $tbulkdiscount,'couponCode' => $strCoupon,'shippingcharge' => $shippingcharge,'totalAmount' => $totalAmt,'orderDate' => date('Y-m-d H:i:s'),'userType' =>$usertype,'paymentMethod' => 'Paypal','paymentStatus' => 'Completed','orderStatus' => $ordStat,'scOrderStatus' => $scordStat,'customerId' => $user_id,'email' => $customerEmail,'fname' => $customerFirstName,'lname' => $customerLastName,'ipaddr' => get_ip_address(),'approval_code' =>$approval_code,'avs_result' => "paypal",'cvv_result' => "paypal");
			$insert = $db->insert("ci_customer_orders",$orderData1);
			if($insert){
				$orderinserted = "order is inserted ". $db->getLastQuery();
				
				$orderTransInfo = array (
					'orderId' => $orderno,
					'orderAmount' =>$totalAmt,
					'paymentStatus' => 'Completed',
					'orderDate' =>date('Y-m-d H:i:s'),
					'refId' => (string)$refId,
					'resultCode' => (string)$resultCode,
					'messageCode' => (string)$messageCode,
					'messageText' => (string)$messageText,
					'responseCode' => (string)$responseCode,
					'authCode' => (string)$approval_code,
					'avsResultCode' => (string)$avs_result,
					'cvvResultCode' => (string)$cvv_result,
					'cavvResultCode' => (string)$cavvResultCode,
					'transactionId' => (string)$transaction_id,
					'transHash' => (string)$transHash,
					'accountNumber' => (string)$accountNumber,
					'accountType' =>(string)$accountType,
					'transRespMsgCode' =>(string)$transRespMsgCode,
					'transRespMsgDescription' =>(string)$transRespMsgDescription,
				);
				
				$insert1 = $db->insert("ci_transaction_details",$orderTransInfo);
			}
			else {
				$orderinserted = "order is not inserted ".$db->getLastError();
			}
			
			
			$address = "";
			$address.= $custname[0]." ".$custname[1].'<br>';
			$address.= $cshipdetails['email'].'<br>';
			$address.= $cshipdetails['address'].'<br>';
			if(isset($cshipdetails['address2']) && $cshipdetails['address2']!="") {
				$address.= $cshipdetails['address2'].($cshipdetails['address2']!=''?'<br>':'');
			}
			$address.= $cshipdetails['city'].',&nbsp;';
			$address.= $cshipdetails['state'].',&nbsp;'; 
			$address.= $cshipdetails['zip'].'<br>'; 
			$address.= ($cshipdetails['telAdd']!=''?$cshipdetails['telAdd'].'<br>':''); 
			
			$address1="";
			$address1.= $custnameb[0]." ".$custnameb[1].'<br>';
			$address1.= $cbilldetails['email'].'<br>';
			$address1.= $cbilldetails['address'].'<br>';
			if(isset($cbilldetails['address2']) && $cbilldetails['address2']!="") {
				$address1.= $cbilldetails['address2'].($cbilldetails['address2']!=''?'<br>':'');
			}
			$address1.= $cbilldetails['city'].',&nbsp;';
			$address1.= $cbilldetails['state'].',&nbsp;'; 
			$address1.= $cbilldetails['zip'].'<br>'; 

			$delieveryDate=$estimateDeliveryDate;
			
			$to = $cbilldetails['email']; //shipping email
			
			$subject = "BulkApparel.com Order Confirmation #".$orderno;
			$emailtemp="";

			$emailtemp='<style>body{ background-color:#f1f1f1; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#333333;}
			p,h1,h2,h3,h4,h5,h6{ margin:0; padding:0}
			.email-main{width:680px; max-width:680px; margin:auto;}
			.pimg img{ max-width:100%;}
			img{ max-width:100%;}
			</style>
			<table style="max-width:680px; width:100%;" width="680" cellpadding="0" cellspacing="0" border="0" align="center">
			<table width="98%" cellpadding="0" cellspacing="0" border="0" style="padding:8px 1% 8px 1%; background-color:#eaeaea;">
			<tr>
			<td align="center" style="background-color:#FFFFFF; padding-top:15px; padding-bottom:15px;"><a href="https://www.bulkapparel.com"><img src="'.base_url_site.'images/logo.jpg" /></a></td>
			</tr>
			<tr>
			<td align="center" style="background-color:#0a2972; padding-top:8px; padding-bottom:8px;"><h2 style="font-size:25px; color:#fff; padding-top:0px; padding-bottom:0px; margin-top:0px; margin-bottom:0px;">Order Confirmation <br /><span style="font-size:18px; color:#fff; margin-top:0px; padding-top:0px;">Order #'.$orderno.'</span></h2>
			</td>
			</tr>
			<tr>
			<td style="background-color:#FFFFFF;">

			<table style=" width:100%; padding-top:15px; padding-bottom:15px; padding-left:2%; padding-right:2%;">
			<tr>
			<td colspan="2" style=" padding-bottom:15px;"><b>Hi '.$custnameb[0].',</b><br />
			Thank you for shopping with <a href="'.base_url_site.'">bulkapparel.com!</a> Here is your order summary. </td>
			</tr>
			
			<tr>
			<td style="font-size:17px; margin-bottom:0px; padding-bottom:0px;" ><b>Shipping Address:</b></td>
			<td style="font-size:17px; margin-bottom:0px; padding-bottom:0px;"><b>Billing Address:</b></td>
			</tr>
			<tr>
			<td valign="top">'.$address1.'</td>
			<td valign="top">'.$address.'</td>
			</tr>
			</table>
			</td>
			</tr>
			<tr>
			<td><img height="15" src="'.base_url_site.'images/email-spacer.png" /></td>
			</tr>
			<tr>
			<td style="background-color:#0a2972; color:#fff; font-size:17px; padding-top:7px; padding-bottom:7px; padding-left:2%;"><b>Your Order </b></td>
			</tr>
			<tr>
			<td style="background-color:#FFFFFF;">
			<table style=" width:100%; padding-top:8px; padding-bottom:10px; padding-left:2%; padding-right:2%;">
			<tr>
			<td valign="top" width="35%">
			<table width="100%">
			<tr>
			<td valign="top">Product Total:</td>
			<Td align="right" valign="top">'.SYMBOL.number_format($tPrice,2).'</Td>
			</tr>';
			if(isset($tbulkdiscount) && intval($tbulkdiscount>0)) {
				
				$emailtemp.='<tr><td>Bulk Discount:</td><td align="right">-'.SYMBOL.$isbulkT.'</td></tr>';
			}
			if(isset($_SESSION['coupons']) && !empty($_SESSION['coupons'])) {
				$emailtemp.= $strCouponsh;
			}
			$emailtemp.='<tr>
			<td valign="top">Shipping:</td>
			<Td align="right" valign="top">'.SYMBOL.$shippingcharge.'</Td>
			</tr>';
			if(isset($_GET['tax']) && $_GET['tax']!="0.00") {
				$emailtemp.='<tr>
				<td valign="top">Tax:</td>
				<Td align="right" valign="top">'.SYMBOL.$_GET["tax"].'</Td>
				</tr>';
			}
			$emailtemp.='<tr>
			<td valign="top" style="font-size:16px;"><b>Total:</b></td>
			<td align="right" valign="top"  style="font-size:16px;"><b>'.SYMBOL.number_format($totalAmt,2).'</b></td>
			</tr>
			</table>
			</td>

			<td  width="10%"></td>

			<td valign="top" width="55%">
			<table width="100%">
			<tr>
			<td valign="top" width="25%" style="font-size:15px;"><b>Order #:</b></td>
			<td width="5%"></td>
			<td valign="top" width="70%" style="font-size:15px;"><b>'.$orderno.' </b></td>
			</tr>

			<tr>
			<td valign="top">Order Date: </td>
			<td></td>
			<td valign="top">'.date('l, F d', time()).'</td>
			</tr>

			<tr>
			<td valign="top">Payment:</td>
			<td></td>
			<td valign="top">'.$accountType.' '.$accountNumber.'</td>
			</tr>

			<tr>
			<td valign="top">Shipping:</td>
			<td></td>
			<td valign="top">UPS Ground or Fedex</td>
			</tr>

			<tr>
			<td valign="top" colspan=3><span style="color:#009900;">'.$delieveryDate.'</span></td>
			</tr>

			<tr>
			<td></td>
			<td></td>
			</tr>

			</table>
			</td>

			</tr>
			</table>
			</td>
			</tr>

			<tr>
			<td><img height="20" src="'.base_url_site.'images/email-spacer.png" /></td>
			</tr>

			<tr>
			<td style="font-size:15px; margin-top:0px; margin-bottom:0px; padding-top:0px; padding-bottom:0px;"><b> <span style="color:#009900;">'.$delieveryDate.'</span></b> </td>
			</tr>
			<tr>
			<td><img height="10" src="'.base_url_site.'images/email-spacer.png" /></td>
			</tr>';

			$cartitems = OrderPriceCalc();
            $tTotalP=0;		
			$p=1;
			foreach($cartitems as $key=>$val) {
				if(isset($val['styleID'])) {
					$totalPrice = $val['qty']*$val['customerPrice'];
					$tTotalP = $tTotalP+$totalPrice;	
					$slug = getSlugProduct($val['styleID']);
					$slugCat = getSlugCatProduct($val['styleID']);
													
					$emailtemp.='<tr>
						<td valign="top" style="background-color:#FFFFFF;">
						<table  style="width:100%; padding-top:20px; padding-bottom:20px; padding-left:2%; padding-right:2%;">
						<tr>
						<td valign="top" class="pimg" width="40%" valign="top"><a style="color:#047ec0; text-decoration:none;" href="'.base_url_site.$slugCat.'/'.$slug.'"><img width="200" style="width:100%;" src="'.base_url_images.$val['colorFrontImage'].'" /></a></td>
						<td width="5%"></td>
						<td valign="top" width="55%">
						<table width="100%">
						<tr>
						<td style="font-size:20px; color:#047ec0;"><b><a style="color:#047ec0; text-decoration:none;" href="'.base_url_site.$slugCat.'/'.$slug.'">'.getBrandNameByStyleID($val['styleID']).' '.$val['styleName'].' '.$val['title'].'</a></b></td>
						</tr>
						<tr><td><img height="5" src="'.base_url_site.'images/email-spacer.png" /></td></tr>
						<tr><td><img src="'.base_url_bimages.getBrandImgByStyleID($val['styleID']).'" width="100"></td></tr>
						
						<tr><td style="font-size:15px;"><b>'.$val['styleName'].'</b></td></tr>
					
						<tr><td style="font-size:16px;"><b>Color:</b> '.getcolorNameBySku($val['sku']).'</td></tr>
						
						<tr><td style="font-size:16px;"><b>Size:</b> '.$val['sizeName'].'</td></tr>
						
						<tr><td style="font-size:16px;"><b>Quantity:</b> '.$val['qty'].'</td></tr>
						
						<tr><td style="font-size:20px;"><b>Price:</b> '.SYMBOL.number_format($val['customerPrice'],2).'</td></tr>
						</table>
						</td>
						</tr>
						</table>
						</td>
						</tr>';
				}
			}

			$emailtemp.='</table>';

			$emailtemp.='<table width="98%" cellpadding="0" cellspacing="0" border="0" style="padding:10px 1%; margin-top:20px; margin-bottom:20px; background-color:#fff;">
			<tr>
			<td width="7%"><img src="'.base_url_site.'images/info.png" /></td>
			<td>Return, Refunds & Exchanges<br />
			<a href="https://www.bulkapparel.com/cms/page/help">Read More</a> </td>
			<td></td>
			<td width="7%"><img src="'.base_url_site.'images/letter.png" /></td>
			<td>Have a Question?<br />
			<a href="https://www.bulkapparel.com/customer-service">Contact Us </a></td>
			</tr>
			</table>

			<table width="98%" cellpadding="0" cellspacing="0" border="0" style="padding:0 1%; margin-top:10px; margin-bottom:10px;">
			<tr>
			<td align="center">Copyright '.date('Y').' bulkapparel.com </td>
			</tr>
			</table>

			</table>';
			//$header = "From:orders@bulkapparel.com \r\n"; //info@shirtchamp.com
			//$header .= "MIME-Version: 1.0\r\n";
			//$header .= "Content-type: text/html\r\n";
			$mail = new PHPMailer();

$mail->IsSMTP();
$mail->SMTPDebug = 0;
$mail->SMTPAuth = TRUE;
$mail->SMTPSecure = "tls";
$mail->Port     = 587;  
$mail->Username = "priyanka@bulkapparel.com";
$mail->Password = "7MqU>P&3";
$mail->Host     = "smtp-relay.gmail.com";
$mail->Mailer   = "smtp";
$mail->SetFrom("orders@bulkapparel.com", "Bulk Apparel");
$mail->AddAddress($to,$custname[0]." ".$custname[1]);
//$mail->AddAddress("recipient address");
$mail->Subject = $subject;
$mail->WordWrap   = 600;
$mail->MsgHTML($emailtemp);
$mail->IsHTML(true);

if(!$mail->Send())
{
}
			
			
			
			

			//$retval = mail ($to,$subject,$emailtemp,$header);
		//}
		
		unset($_SESSION["estimateDel"]);
		unset($_SESSION["estimateDeliveryDate"]);
			unset($_SESSION['currentOrder']);	
			unset($_SESSION['coupons']);	
			if(isset($_SESSION["uid"]) || isset($_COOKIE["csid"])) {
				if(isset($_COOKIE["csid"]) && $_COOKIE["csid"]!="") {
					$delscart = $db->rawQuery("delete from ci_abdoncart where abd_cokiesid ='".$_COOKIE["csid"]."' ", array (''));
				} else if(isset($_COOKIE["csid"]) && $_COOKIE["csid"]!="") { 
					$delscart = $db->rawQuery("delete from ci_abdoncart where abd_cid ='".$_SESSION["uid"]."' ", array (''));
				} else {
					$delscart = $db->rawQuery("delete from ci_abdoncart where abd_cid ='".$user_id."' ", array (''));
				}
			}
			unset($_COOKIE['csid']);
			setcookie("csid", '', time() + 3600 * 24*7, "/"); 
			if (isset($_SERVER['HTTP_COOKIE'])) {
				$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
				foreach($cookies as $cookie) {
					$parts = explode('=', $cookie);
					$name = trim($parts[0]);
					setcookie('csid', '', time() + 3600 * 24*7);
					setcookie('csid', '', time() + 3600 * 24*7, '/');
				}
			}
			
			$errorMsg['result']=0;
			$errorMsg['msg']= $xml->transactionResponse->messages->message->description;
			$errorMsg['orderid']=$orderno;
			$errorMsg['orderinserted'] = $orderinserted;	
	
}

else
	
	{
		
		 echo 'Handle payment execution failure';
		 include("includes/footer_inc.php");
		 exit;
		
	}


}
?>

<script>
function PrintElem(elem)
{
	var mywindow = window.open('', 'PRINT', 'height=800,width=900');
	mywindow.document.write('<html><head><title>' + document.title  + '</title>');
	if( navigator.userAgent.toLowerCase().indexOf('firefox') > -1 ){
	mywindow.document.write('<link rel="stylesheet" href="http://bulkapparel.com/css/print.css" type="text/css" />');
    }else if (navigator.appName == 'Microsoft Internet Explorer' ||  !!(navigator.userAgent.match(/Trident/) || navigator.userAgent.match(/rv:11/)) || (typeof $.browser !== "undefined" && $.browser.msie == 1))
    {
    mywindow.document.write('<link rel="stylesheet" href="http://bulkapparel.com/css/print.css" type="text/css" />');
    }
	
    mywindow.document.write('</head><body >');
    mywindow.document.write('<img src="http://bulkapparel.com/images/logo.jpg" alt="Shirtchamp">');
    mywindow.document.write(document.getElementById(elem).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    return true;
}
</script>
<!--HEADER END HERE-->
<!--MAIN PAGE START HERE-->
<div class="site-center">
	<div class="main-container">
		<!--BREADCRUM HERE-->
		<div class="breadcrum"><p><a href="<?php echo base_url_site;?>">Home</a>/<a href="javascript:void(0);">Order Confirmation</a></p></div>
		<!--BREADCRUM END-->
		<!--PRODUCT DETAIL BOX-->
		<div class="full-width-box">
			<div class="oc-container">
				<h2>Congratulations!</h2>
				<h3>Your order has been confirmed!</h3>
				<p>Thanks for shopping ! Your order hasn't shipped yet, but we'll send you and email when it does.</p>

				<div id="btn-bx"><a id="or-print-btn" href="javascript:void(0);" onClick="PrintElem('or-print');">Print</a></div>
				<div id="or-print"> 
				<?php  include("css/print.php");  ?>		   
				<?php
				$orDetails=getAllOrderPDetails($_REQUEST["orderId"]);
                 $orTDetails=getOrderDetails($_REQUEST["orderId"]);
                 if(empty($orTDetails)){
                 header('Location:'.$base_url_site.'dashboard');

                     }
				if(count($orDetails)>0) {

					$tax1="";
					$tax1=$orTDetails['tax'];

					$bulkDsicount1="0.00";
					$bulkDsicount1=$orTDetails['bulkDiscount']; 

					$couponDetails1=substr($orTDetails['couponCode'],0,-1); 

					$totalCoupon1=0;
					if(isset($couponDetails1) && $couponDetails1!="") {
						$coudetails1=explode(",",$couponDetails1);
						foreach($coudetails1 as $val1) {
							$couponinfo1=explode("^",$val1);
							$totalCoupon1=$totalCoupon1+$couponinfo1[1];
						}
					}	
	
	
					if(isset($tax1) && $tax1!="0.00") $tax1=$tax1;
					//echo $tax1;
					$totalPrice1=0;
					$totalAmount2=0;
					foreach($orDetails as $key1=>$val3) {
						$totalPrice1=$val3['qty']*$val3['pPrice'];
						$totalAmount2=$totalAmount2+$totalPrice1;
					}
  
					$totalAmt1=$totalAmount2-($totalCoupon1+$bulkDsicount1);

					if($totalAmt1<0) {
						$totalAmt1="0.00";
					}		
		
					if($totalAmount2>FREESHIPPING) {
						$shippingcharge="0.00";
						$totalAmt1=number_format(($totalAmt1+$tax1),2);
					} else {
						$shippingcharge=SHIPPINGCHARGE;
						$totalAmt1=number_format($totalAmt1,2)+SHIPPINGCHARGE+$tax1;
					}	
					//echo $shippingcharge;	
					//$totalAmt2=$totalAmt1+$tax1;

					if(isset($_SESSION["uid"]) && $_SESSION["uid"]!="") {
						$usrid=$_SESSION["uid"];
					} else {
						$usrid=$_SESSION["cid"];
					}
					?>                        
                    <div class="confirm-tb">
						<div class="account-content-holder shipbillinfo">
                            <div class="account-address-box">
                                <h2>Bill To</h2>
                                <p>
								<?php print_r(get_address_order($usrid,'0',$_REQUEST["orderId"])); ?></p>
                            </div>
                            <div class="account-address-box2">
                                <h2>Ship To</h2>
                                <p><?php print_r(get_address_order($usrid,'1',$_REQUEST["orderId"])); ?></p>
                            </div>
							<div id="spe-mob">
								<div class="account-address-box3">
									<!-- <h2>Est. Delivery Date</h2>-->
									<div  id="shipcostid">
									<?php echo "<span style='color:#000'><strong>Order Date </strong><br>".date('m/d/Y h:i a',strtotime($orTDetails['orderDate']))."</span><Br>";
										global $db;

										$days=array();
										$fdays=0;

										$afdays=getDaysTransistDays($_SESSION["zip"]);
										$fdays = max(array_filter($afdays));

										if(empty($orTDetails['estimatedeliverydate']) && $orTDetails['estimatedeliverydate']=="") {
											if($fdays>0){
												$response['result']=0;
												if($orTDetails['totalItems']>1) { $item="Items"; } else { $item="Item"; }
												echo fnDaysT($fdays,$item);
											}
										} else {
											echo $orTDetails['estimatedeliverydate'];
										}?>
									</div>
								</div>
								<div class="account-address-box4">
									<h2>Order Information</h2>
									<?php if(isset($_SESSION["uid"]) && $_SESSION["uid"]!="") {
									//echo $totalAmt1;
									?>	      
										<div class="oc-left"><p>Order : <a href="<?php echo base_url_site.'orderdetails?oId='.$_REQUEST["orderId"];?>">#<?php echo $_REQUEST["orderId"]?></a></p>
											<p>Total : <?php echo SYMBOL.$totalAmt1;?></p>
											<?php /*?><div class="oc-button"><a href="<?php echo base_url_site.'orderdetails?oId='.$_REQUEST["orderId"];?>">View Order Details</a></div><?php */?>
										</div>
									<?php } else { ?>
										<div class="oc-left"><p>Order : #<?php echo $_REQUEST["orderId"]?></p>
										<p>Total : <?php echo SYMBOL.$totalAmt1;?></p>
										</div>
									<?php }?>
								</div>
							</div>
						</div>
                        
						<h4>ORDER SUMMARY</h4>
						<table>
							<thead>
								<tr>
									<th></th>
									<th align="left"><strong>Item</strong></th>
									<th ><strong>Price</strong></th>
									<th><strong>Quantity</strong></th>
									<th><strong>Total</strong></th>
									</tr>
							</thead>
							<?php 
							$k=0;
							$totalAmount=0;
							$totalQty=0;
							foreach($orDetails as $key=>$val) {
								$totalPrice=$val['qty']*$val['pPrice'];
								$totalAmount=$totalAmount+$totalPrice;
								$totalQty=$totalQty+$val['qty'];
								$slug=getSlugProduct($val['styleID']);
								$slugCat=getSlugCatProduct($val['styleID']);
								$mImage1="";
								if(file_exists(dir_path.$val['colorFrontImage']) && $val['colorFrontImage']!="") { $mImage1=fnCPMainImages($val['colorFrontImage']); 
								if(!isset($mImage1)) { $mImage1=fnProImages($val['colorFrontImage']); }
								
								} 
							?>  
								<tr>
									<td><a href="<?php echo base_url_site.$slugCat.'/'.$slug;?>"><img src="<?php echo $mImage1;?>" alt="product"></a></td>
									<td>
										<ul>
											<li><a href="<?php echo base_url_site.$slugCat.'/'.$slug;?>"><?php echo getBrandNameByStyleID($val['styleID']).' '.$val['styleName'].' '.$val['title'].''?></a></li>
											<li>
												<p><b>Color:</b> <?php echo getcolorNameBySku($val['sku']);?> <?php if(isset($val['color2']) && $val['color2']!="") {?>
                                                    <span style="width:20px; height:20px; padding:0;">
													<a class="cart-lft-color" style="background-color: <?php echo $val['color1']?>;"></a>
													<a class="cart-rght-color" style="background-color: <?php echo $val['color2']?>;"></a>
                                                    </span>
													<?php } else {?>
                                                    <span style="background:<?php echo $val['color1'];?>;"></span>
                                                    <?php }?></p>
											</li>
											<li><p><b>Size:</b> <?php echo $val['sizeName'];?></p></li>
											<?php 
											if($_SESSION["zip"] !="") { ?>
											<!--<li><p><b>Sku:</b><?php //echo $val['sku'];?></p></li>-->
											
											<!--<li>
												<p>
												<b>Delivery within:</b>
												<?php
												/*$afdays = getDaysTransistDays_test($_SESSION["zip"],$val['sku']);
												$fdays = max(array_filter($afdays));
												if(!empty($fdays) && $fdays>1) {
													echo $fdays ."days";
												} else{
													echo $fdays ."day";
												} */?>
												</p>
											</li>-->
											
											<li>
												<p>
												<?php
												/*$afdays = getDaysTransistDays_test($_SESSION["zip"],$val['sku'],$val['qty']);
												$fdays = max(array_filter($afdays));
												if(totalCartItems()>1) { $item="Items"; } else { $item="Item"; }
												$result = fnDaysT($fdays,$item);
												$resulArr = explode("Total Items -",$result);
												if(!empty($resulArr)){ echo "<img src='".base_url_site."images/truck-icon.gif'> ".$resulArr[0]; }*/
												if(isset($val['estimateDelivery']) && $val['estimateDelivery']!="") {
												echo $val['estimateDelivery'];
												}
												?>
												</p>
											</li>
											<?php 
											}?>
									</td>
									<td><?php echo SYMBOL.number_format($val['pPrice'],2);?></td>
									<td><?php echo $val['qty'];?></td>
									<td><?php echo SYMBOL.number_format($totalPrice,2);?></td>
								</tr>
							<?php
							$k++;
							}
							$tax="";
							$tax=$orTDetails['tax'];
							?> 						
						</table>
                    </div>
				<?php 
				}?>                         
					<div class="order-control">
						<div class="oc-right">
						<?php 
						?>                    
                            <table>
								<tr>
									<td>Subtotal :</td>
									<td align="right"><?php echo SYMBOL.number_format($totalAmount,2);?></td>
								</tr>
								<?php
								$bulkDsicount="0.00";
								$bulkDsicount=$orTDetails['bulkDiscount']; 
								if(isset($bulkDsicount) && $bulkDsicount!="0.00") {
									?>
									<tr class="discClass">
										<td>Bulk Discount  :</td>
										<td align="right"><span style="font-size:15px;color:#FF0000;">-</span> <?php echo SYMBOL.number_format($bulkDsicount,2);?></td>
									</tr>
								<?php
								}?>  
                                <?php
								$couponDetails=substr($orTDetails['couponCode'],0,-1); 

								$totalCoupon=0;
								if(isset($couponDetails) && $couponDetails!="") {
									$coudetails=explode(",",$couponDetails);
									foreach($coudetails as $val) {
										$couponinfo=explode("^",$val);
										$totalCoupon=$totalCoupon+$couponinfo[1];
										?>
										<tr class="discClass">
											<td>Coupon Code - <?php echo $couponinfo[0];?>  :</td>
											<td align="right"><span style="font-size:15px;color:#FF0000;">-</span> <?php echo SYMBOL.number_format($couponinfo[1],2);?></td>
										</tr>
										<?php
									}
								}
								$netAmount=$totalAmount-($totalCoupon+$bulkDsicount);
								if($netAmount<0) {
									$netAmount="0.00";
								}				
								if($totalAmount>FREESHIPPING) {
								$shippingcharge="0.00";
								$netAmount=number_format(($netAmount+$tax),2);
								} else {
								$shippingcharge=SHIPPINGCHARGE;
								$netAmount=number_format($netAmount,2)+SHIPPINGCHARGE+$tax;
								}		
								//echo $netAmount;
								?> 
								<tr>
									<td>Shipping :</td>
									<td align="right"><?php echo SYMBOL.$shippingcharge;?></td>
								</tr>
							   <?php if(isset($tax) && $tax!="0.00") {?> 
								<tr>
									<td>Tax :</td>
									<td align="right"><?php echo SYMBOL.$tax;?></td>
								</tr>
								<?php } ?>
								
								<tr>
									<td><strong>Total :</strong></td>
									<td align="right"><strong><?php echo SYMBOL.($netAmount);?></strong></td>
								</tr>
                            </table>
                        </div>
                    </div>
				</div>
            </div>	  
        </div>
	</div>
    <!--PRODUCT DETAIL BOX END-->
    </div>   
</div> 
<!--MAIN END HERE-->
<!--FOOTER START HERE-->
<?php

include("includes/footer_inc.php");
?>