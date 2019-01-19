<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Products extends MY_Controller {
	/*@Function Name:__construct()
	**@Function Params: {none} 
	**@Function Purpose:{Load the core library and system file here for the proper exection of framework and member functions!}
	*/
	public function __construct(){
		parent::__construct();
		$this->load->model('admin/Productsmodel','Productsmodel');
		$this->load->helper(array('admin','cookie','style'));
	}
	
	 
	/*@Function Name:index()
	**@Function Params: {none} 
	**@Function Purpose:{Default function of class rund on first page load!}
	*/
	public function index(){
		moveto($this->class.'/products_style');
	}
	
	
	public 	function couponmgmt(){
		(is_login()==true?'':moveto('dashboard/login'));
		//Check user permission access 6=>product tab
		if(checkUserPermissionAccess(6) == 3) {
			$error_notfication = "You don't have permission to access coupon page.";
			set_flashmsg('flsh_msg',$error_notfication,'error');
			moveto('dashboard/me');
		}
		
		$start_from=0;
		$extrajs='coupons';
		$title="Coupons";
		$tpl_name=$this->method;
		$tbl='ci_coupons';
		$orderby='id';
		$list_perpage_admin=100;
		$base_url = base_url($this->class.'/'.$this->method);
        $post_data=$this->input->post();
	
		/*set order by sorting using post and session end here!*/
		$total_rows = $this->Productsmodel->record_coupons($tbl,$post_data,$dblfilter_params=false);

     	$this->data["results"] = $this->Productsmodel->fetch_coupons($tbl,$orderby,$dblfilter_params=false);
		//$this->data['perpage']=$per_page;
		//$this->data['allrecord']= $total_rows;
		//$this->data['current_page']=  $page==0?1:$page;
		//$this->data["links"] = $this->pagination->create_links();
		//if(isset($extrajs) && !empty($extrajs)){

		$this->data['extra_js']=array('public/admin/js/products/coupons.js','public/admin/js/products/jquery.tokeninput.js');		
		$this->data['extra_css']=array('public/admin/css/token-input.css','public/admin/css/token-input-facebook.css','public/admin/css/token-input-mac.css');
		//}
		$this->data['sitename']=$this->sitename;  
		$this->data['title']=$title;
		$this->data['content']=$this->class.'/'.$this->method;
		//$this->data['submethod']=$submethodifany;
		//Permission access
		$this->data['permissionAccess'] = checkUserPermissionAccess(6); //6=>product tab
		rendertpl('admin/template-dashboard',$this->data);
	}
 
	public function ajax_search_coupons() {
		$post=$this->input->post();
		if(isset($post) && !empty($post)){
			$strHtmlScript="";	

			$strHtmlCData="";
			$couponData=$this->Productsmodel->getCouponSData($post['skeywords']);
			if(isset($couponData) && !empty($couponData)) {	
				foreach($couponData as $c=>$cval) {
					$strHtmlCData.='<li>
                    <div class="ob-check">
                        <input type="checkbox" id="chkIds" name="chkIds[]" class="checkbox" value="'.$cval->id.'"/>
						
                    </div>
                    <div class="ob-list-info">
                        <div class="ob-list-row">
                            <div class="ob-list-info-name"><a href="javascript:void(0);" class="viewcouponid" data-cid="'.$cval->id.'">'.$cval->ccode.'</a></div>
                            <div class="ob-list-info-cost">';
							if($cval->dtype==1) $strHtmlCData.=SYMBOL.number_format($cval->cvalue,2);
							if($cval->dtype==2) $strHtmlCData.=$cval->cvalue.'%';
							$strHtmlCData.='</div>
                        </div>
                       <div class="ob-list-row-1">
                            <div class="ob-list-info-number">';
							if($cval->dtype==1) { $strHtmlCData.='Cart Discount'; }
							if($cval->dtype==2) { $strHtmlCData.='Cart % Discount'; }
							/*if($cval->dtype==3) { $strHtmlCData.='Product Discount'; }
							if($cval->dtype==4) { $strHtmlCData.='Product % Discount'; }*/
							$strHtmlCData.=($cval->cexpirydate!="0000-00-00")?' | <span>  '.$this->Productsmodel->stDate2($cval->cexpirydate).'</span>':'';
                            $strHtmlCData.='</div><div class="ob-list-info-status"><span>Status:</span> ';
							$cdate=time();
							$expdate=strtotime($cval->cexpirydate);
							if($expdate>=$cdate) {
                            $strHtmlCData.='<span style="color:#006600;font-weight:bold;">Active</span>';
							} else if($cval->cexpirydate=="0000-00-00") {
                            $strHtmlCData.='<span style="color:#006600;font-weight:bold;">Active</span>';
							} else {
							$strHtmlCData.='<span style="color:#FF0000;font-weight:bold;">Expired</span>';
							}
							
							$strHtmlCData.='</div>
                        </div>
                    </div>
                </li>';
	}
$response['result']=1;	
} else {
$strHtmlCData.='<li><div class="ob-list-info" style="color:#FF0000">No records found</div></li>';
$response['result']=0;
}
	

	
$strHtmlScript="<script>
				jQuery(document).ready(function(){

$(document).on('click', 'input.checkbox', function() {
    var arr_sort = new Array();
    $('.checkbox').each(function()
    {
        if( $(this).is(':checked') )
        {
			if($(this).val()!='on') {
            arr_sort.push($(this).val());
			}
        }
    });

});

$('#select1').on('click',function(){
		var allVals = [];					  
        if(this.checked){
            $('.checkbox').each(function(){
				if($(this).val()!='on') {						 
				allVals.push($(this).val());				 
				}
                this.checked = true;
            });
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
            });
        }
		
    });

$('.viewcouponid').on('click', function () {
	var base_url=$('#currentparent').val();
	$('.bsave').hide();
	 $('.vupid').css('display','block');
	$('#vid').html('View Coupon'); 
	$(this).parents('li').siblings().removeClass('selected');
$(this).parents('li').addClass('selected');
$('#cdetailsh').hide();
$('#cdetails').show();
	var couponid=$(this).attr('data-cid');
 	$.ajax({
			url: base_url+'viewCoupondata',
			method: 'post',
			data: 'couponid='+couponid,
			dataType: 'json',
			success: function(msg)
				{
					
					$('#cajaxdata').html(msg.msg);
					

					
				}
		});
	

});

   $(document).on('click', 'input.checkbox', function() {
														  
        if($('.checkbox:checked').length == $('.checkbox').length){
					
            $('#select1').prop('checked',true);
        }else{
            $('#select1').prop('checked',false);
        }
		 
    });
});
				</script>
				";	
	
	
	$response['cdata']=$strHtmlCData;
	$response['strHtmlScript']=$strHtmlScript;
   $response['msg']="Coupons has been saved successfully!";

echo json_encode($response);
}
}


public function ajax_save_coupons() {
//print_r($_POST);
$post=$this->input->post();

if(isset($_POST["allowfreeshipping"]) && $_POST["allowfreeshipping"]==1) $post['allowfreeshipping']=1; else $post['allowfreeshipping']=0; 
if(isset($_POST["individualonly"]) && $_POST["individualonly"]==1) $post['individualonly']=1; else $post['individualonly']=0;


if(isset($post) && !empty($post)){
 library('form_validation');
  $this->form_validation->set_rules('ccode', 'Coupon code', 'required');
  if ($this->form_validation->run() == FALSE){
   $response['result']=2;
   $response['msg']="Coupon code is required";
   } else {

    $status=$this->Productsmodel->savecoupondata($post); 
    if($status){
	$response['result']=1;
	$response['couponid']=$status;
	$strHtmlCData="";

	$couponData=$this->Productsmodel->getCouponData();
	foreach($couponData as $c=>$cval) {

	if($response['couponid']==$cval->id) {
	$strHtmlCData.='<li class="selected">';
	} else{
	$strHtmlCData.='<li>';
	}
	
	$strHtmlCData.='
                    <div class="ob-check">
                        <input type="checkbox" id="chkIds" name="chkIds[]" class="checkbox" value="'.$cval->id.'"/>
						
                    </div>
                    <div class="ob-list-info">
                        <div class="ob-list-row">
                            <div class="ob-list-info-name"><a href="javascript:void(0);" class="viewcouponid" data-cid="'.$cval->id.'">'.$cval->ccode.'</a></div>
                            <div class="ob-list-info-cost">';
							if($cval->dtype==1) $strHtmlCData.=SYMBOL.number_format($cval->cvalue,2);
							if($cval->dtype==2) $strHtmlCData.=$cval->cvalue.'%';
							$strHtmlCData.='</div>
                        </div>
                        <div class="ob-list-row-1">
                            <div class="ob-list-info-number">';
							if($cval->dtype==1) { $strHtmlCData.='Cart Discount'; }
							if($cval->dtype==2) { $strHtmlCData.='Cart % Discount'; }
							/*if($cval->dtype==3) { $strHtmlCData.='Product Discount'; }
							if($cval->dtype==4) { $strHtmlCData.='Product % Discount'; }*/
							$strHtmlCData.=($cval->cexpirydate!="0000-00-00")?' | <span>  '.$this->Productsmodel->stDate2($cval->cexpirydate).'</span>':'';
                            $strHtmlCData.='</div><div class="ob-list-info-status"><span>Status:</span> ';
							$cdate=time();
							$expdate=strtotime($cval->cexpirydate);
							if($expdate>=$cdate) {
                            $strHtmlCData.='<span style="color:#006600;font-weight:bold;">Active</span>';
							} else if($cval->cexpirydate=="0000-00-00") {
                            $strHtmlCData.='<span style="color:#006600;font-weight:bold;">Active</span>';
							} else {
							$strHtmlCData.='<span style="color:#FF0000;font-weight:bold;">Expired</span>';
							}
							
							$strHtmlCData.='</div>
                        </div>
                    </div>
                </li>';
	}
	
$strHtmlScript="";	
	
$strHtmlScript="<script>
				jQuery(document).ready(function(){
$('.ob-list ul li').click(function(){
  $(this).addClass('selected');
  $(this).siblings().removeClass('selected');
 });
$(document).on('click', 'input.checkbox', function() {
    var arr_sort = new Array();
    $('.checkbox').each(function()
    {
        if( $(this).is(':checked') )
        {
			if($(this).val()!='on') {
            arr_sort.push($(this).val());
			}
        }
    });

});

$('#select1').on('click',function(){
		var allVals = [];					  
        if(this.checked){
            $('.checkbox').each(function(){
				if($(this).val()!='on') {						 
				allVals.push($(this).val());				 
				}
                this.checked = true;
            });
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
            });
        }
		
    });

$('.viewcouponid').on('click', function () {
	var base_url=$('#currentparent').val();
	$('.bsave').hide();
	 $('.vupid').css('display','block');
	$('#vid').html('View Coupon'); 
	var couponid=$(this).attr('data-cid');
 	$.ajax({
			url: base_url+'viewCoupondata',
			method: 'post',
			data: 'couponid='+couponid,
			dataType: 'json',
			success: function(msg)
				{
					
					$('#cajaxdata').html(msg.msg);
					

					
				}
		});
	

});

   $(document).on('click', 'input.checkbox', function() {
														  
        if($('.checkbox:checked').length == $('.checkbox').length){
					
            $('#select1').prop('checked',true);
        }else{
            $('#select1').prop('checked',false);
        }
		 
    });
});
				</script>
				";	
	
	
	$response['cdata']=$strHtmlCData;
	$response['strHtmlScript']=$strHtmlScript;
   $response['msg']="Coupons has been saved successfully!";
   //  moveto($this->class.'/'.$this->method);
    } else {
	$response['result']=2;
   $response['msg']="Coupons has been saved un-successfull. Please check form fields and try againg!";	
    // moveto($this->class.'/'.$this->method);
    }
	
   }
echo json_encode($response,true);
 } 



}


	public function viewCoupondata(){
		//Check user permission access 6=>customer tab
		if(checkUserPermissionAccess(6) == 3) {
			$error_notfication = "You don't have permission to access view coupon page.";
			set_flashmsg('flsh_msg',$error_notfication,'error');
			moveto('dashboard/me');
		}
		$post=$this->input->post();
		$strHtml="";
		if(isset($post) && !empty($post)){
			$cdata=$this->Productsmodel->getCouponDatabyId($post['couponid']);
			/*$proInc=$this->Productsmodel->getCouponStyleIds($cdata[0]->productsInc);
			if(isset($proInc)) { $jsonProInc=json_encode($proInc); }
			$proExc=$this->Productsmodel->getCouponStyleIds($cdata[0]->productsExc);
			if(isset($proExc)) { $jsonProExc=json_encode($proExc); }*/

	$strHtml='<input type="hidden" id="coupoiId" name="coupoiId" value="'.(($cdata[0]->id!=false)?$cdata[0]->id:'').'" /><div class="anc-box">
                    <div class="anc-row">
                        <div class="anc-label">Coupon Code:</div>
                        <div class="anc-input"><input type="text" id="ccode" name="ccode" value="'.(($cdata[0]->ccode!=false)?$cdata[0]->ccode:'').'" disabled/><div id="cmsg" style="display:none; clear:both;"></div></div>
                    </div>
                    <div class="anc-row">
                        <div class="anc-label">Description:</div>
                        <div class="anc-input"><textarea id="description" name="description" readonly>'.(($cdata[0]->description!=false)?$cdata[0]->description:'').'</textarea></div>
                    </div>
                </div>
                <div class="anc-info-box">
                    <h2>General</h2>
                    <div class="anc-info-input-box">
					<div class="anc-row">
                        <div class="anc-col-left">
                            <div class="anc-info-label">Threshold Value</div>
                            <div class="anc-info-input">
                                <input type="text" id="thresholdvalue" name="thresholdvalue" value="'.(($cdata[0]->thresholdvalue!=false)?$cdata[0]->thresholdvalue:'0').'" placeholder="0" readonly/>
                            </div>
                        </div>
                       
                    </div>
					<div class="anc-row">
                        <div class="anc-col-left">
                            <div class="anc-info-label">Discount Type</div>
                            <div class="anc-info-input">
                                <select name="dtype" id="dtype" disabled>
                                    <option value="1" '.(($cdata[0]->dtype==1)?"selected":"").'>Cart Discount</option>
                                    <option value="2" '.(($cdata[0]->dtype==2)?"selected":"").'>Cart % Discount</option>
                                    
                                </select>
                            </div>
                        </div>
                        <div class="anc-col-right">
                            <div class="anc-info-label">Coupon Amount</div>
                            <div class="anc-info-input">
                                <input type="text" id="cvalue" name="cvalue" value="'.(($cdata[0]->cvalue!=false)?$cdata[0]->cvalue:'').'" placeholder="0" readonly/>
                            </div>
                        </div>
                    </div>
				
                    <div class="anc-row">
                        <div class="anc-col-left">
                            <div class="anc-info-label">Coupon Initiate Date</div>
                            <div class="anc-info-input">
                                <input type="text" id="cstartdate" name="cstartdate" value="'.(($cdata[0]->cstartdate!=false)?$this->Productsmodel->stDate2($cdata[0]->cstartdate):'').'" placeholder="YYYY-MM-DD" disabled/>  <span class="glyphicon glyphicon-calendar" id="de"></span>
                            </div>
                        </div>
                        <div class="anc-col-right">
                            <div class="anc-info-label">Coupon Expiry Date</div>
                            <div class="anc-info-input">
                                <input type="text" id="cexpirydate" name="cexpirydate" value="'.(($cdata[0]->cexpirydate!=false)?$this->Productsmodel->stDate2($cdata[0]->cexpirydate):'').'" placeholder="MM/DD/YY" disabled/> <span class="glyphicon glyphicon-calendar" id="de"></span>
                            </div>
                        </div>
                    </div>
                    <div class="anc-row">
                        <div class="anc-col-left">
                            <div class="anc-info-label">Allow Free Shipping</div>
                            <div class="anc-info-input">
                                <label><input type="checkbox" disabled id="allowfreeshipping" name="allowfreeshipping" '.(($cdata[0]->allowfreeshipping==1)?'checked':'').' value="'.(($cdata[0]->allowfreeshipping==1)?1:0).'"/>Check this box if the coupon grants free shipping. A <a href="javascript:void(0);">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).</label>
                            </div>
                        </div>
                        <div class="anc-col-right">
                            <div class="anc-info-label">Individual Use Only</div>
                            <div class="anc-info-input">
                                <label><input type="checkbox" disabled id="individualonly" name="individualonly" '.(($cdata[0]->individualonly==1)?'checked':'').' value="'.(($cdata[0]->individualonly==1)?1:0).'"/>Check this box if the coupon cannot be used in conjunction with other coupons.</label>
                            </div>
                        </div>
                    </div>
                </div></div>
                <div class="anc-info-box">
                    <h2>Usage Limits</h2>
                    <div class="anc-info-input-box">
                    
                    <div class="anc-row">
                        <div class="anc-col-left">
                            <div class="anc-info-label">Usage Limit Per Coupon</div>
                            <div class="anc-info-input">
                                <input type="text" id="limitpercoupon" name="limitpercoupon" placeholder="Unlimited usage" value="'.(($cdata[0]->limitpercoupon!=false)?$cdata[0]->limitpercoupon:'').'" readonly/>
                            </div>
                        </div>
                        <div class="anc-col-right">
                            <div class="anc-info-label">Usage Limit Per User</div>
                            <div class="anc-info-input">
                                <input type="text" id="limitperuser" name="limitperuser" placeholder="Unlimited usage" value="'.(($cdata[0]->limitperuser!=false)?$cdata[0]->limitperuser:'').'" readonly/>
                            </div>
                        </div>
                    </div>
                   </div> 
                       
                </div>
				<script>
function fnGetSID(aa)	{
alert(aa);
}			
				$( function() {
$("#allowfreeshipping").on("click", function () {
    $(this).val(this.checked ? 1 : 0);
});
$("#individualonly").on("click", function () {
    $(this).val(this.checked ? 1 : 0);
});
          


$("#thresholdvalue").on("keydown keyup", function(e){
if (parseInt($(this).val()) > 0) {
$(".bsave").show();
}  

});  

  
  

$("#ccode").blur(function() {
	$(".vupid").hide();					  
var base_url=$("#currentparent").val();
			$.ajax({
				url: base_url+"ajax_chk_coupons",
				type: "POST",
				data: {ccode: $("#ccode").val(),cid: $("#coupoiId").val()},
				dataType: "json",
			})
            .done(function(data) {
						   

					if(data.result==1){
					$("#cmsg").css("color","#FF0000");
					$("#cmsg").html(data.msg);
					$("#cmsg").show();	
					$(".bsave").hide();
					//$(".vupid").show();
					
					
					} else {
						$(".bsave").show();
						$(".vupid").hide();
						$("#cmsg").hide();
					}
            });
          				   
						   
});
				
    var dateFormat = "mm/dd/yy",
	
      from = $( "#cstartdate" )
        .datepicker({
          defaultDate: "+1w",
		 
          changeMonth: true
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#cexpirydate" ).datepicker({
        defaultDate: "+1w",
		
        changeMonth: true
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
      });
 
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
 
      return date;
    }
  } );





				</script>
				';
$response=array();
$response['msg']=$strHtml;
echo json_encode($response);
	exit;
	}
	}


	public function ajax_get_products(){
		$post=$this->input->get();
		if(isset($post['q']) && $post['q']!="") {
			$getProducts = $this->Productsmodel->searchStyles($post['q']);
			$arr = array();
			foreach($getProducts as $stinfo) {
				$arr[] = $stinfo;
			}

			# JSON-encode the response
			$json_response = json_encode($arr);
			echo $json_response;
			exit;
		}			
	}	

	
	/*@Function Name:sync_products_styles()
	**@Function Params: {none} 
	**@Function Purpose:{function fetch edi styles!}
	*/
	public function sync_products_styles(){  
		$this->load->library('edistyles');
		$respons=$this->edistyles->update_db_styles(ENDPOINT,'styles',USR,PWD);
		if(!empty($respons) && $respons==true){
			set_flashmsg('flsh_msg','Styles succesfully fetch from web!','success');
			moveto($this->class);
		}else{
			set_flashmsg('flsh_msg','Styles un-succesfull fetch from web!','error');
			moveto($this->class);
		}
	}
	
	
	/*@Function Name:sync_products_specs()
	**@Function Params: {none} 
	**@Function Purpose:{function fetch edi specs!}
	*/
	public function sync_products_specs(){
	$this->load->library('edistpecs');
		$respons=$this->edistpecs->update_db_specs(ENDPOINT,'specs',USR,PWD);
		if(!empty($respons) && $respons==true){
			set_flashmsg('flsh_msg','Specs succesfully fetch from web!','success');
			moveto($this->class);
		}else{
			set_flashmsg('flsh_msg','Specs un-succesfull fetch from web!','error');
			moveto($this->class);
		}
	}
	
	/*@Function Name:sync_products()
	**@Function Params: {none} 
	**@Function Purpose:{function fetch edi all products!}
	*/
	public function sync_products($styleID,$styleName){
		$this->load->library('ediproducts');
		$respons=$this->ediproducts->update_db_products(ENDPOINT,'products',$styleID,USR,PWD);
		if(!empty($respons) && $respons==true){
			set_flashmsg('flsh_msg','Products associated with stylename '.$styleName.' succesfully fetch from web!','success');
			moveto($this->class.'/products_style');
		}else{
			set_flashmsg('flsh_msg','Products associated with stylename '.$styleName.' un-succesfull fetch from web!','error');
			moveto($this->class.'/products_style');
		}
	}
	
	
	
	/*@Function Name:sync_products()
	**@Function Params: {none} 
	**@Function Purpose:{function fetch edi all products!}
	*/
	public function sync_products1(){
		$this->load->library('ediproducts1');
		$respons=$this->ediproducts1->update_db_products(ENDPOINT,'products',USR,PWD);
		if(!empty($respons) && $respons==true){
			set_flashmsg('flsh_msg','Products succesfully fetch from web!','success');
			moveto($this->class.'/products_style');
		}else{
			set_flashmsg('flsh_msg','Products un-succesfull fetch from web!','error');
			moveto($this->class.'/products_style');
		}
	}
	
	
	
	
	/*@Function Name:products_style()
	**@Function Params: {$start_from=0,$extrajs,$title,$tpl_name,$tbl,$orderby} 
	**@Function Purpose:{This core function used in listings!}
	*/ 
	public function products_styleBKP(){ 
	(is_login()==true?'':moveto('dashboard/login'));
		$start_from=0;
		$extrajs='products_style';
		$title=PROD_STYLE_LISTING_TITLE;
		$tpl_name=$this->method;
		$tbl='ci_styles';
		$orderby='styleID';
		$list_perpage_admin=300;
		$this->load->library('pagination');
		$base_url = base_url($this->class.'/'.$this->method);
        $post_data=$this->input->post();
		$sort_by="";
		$submethodifany=false;
		$lblsort=rtrim(trim($title)).'_';
		$this->data['sort_new']="";
			if(isset($post_data['sort_for']) && !empty($post_data['sort_for'])):
			$for=$post_data['sort_for'];
			$break_sort_order=explode('_',$post_data[$for.'_sort']);
			$this->data['sort_new']=($post_data[$for.'_sort']==$break_sort_order[0].'_asc'?$break_sort_order[0].'_desc':$break_sort_order[0].'_asc');
			$sort_by=$post_data[$for.'_sort'];
			set_userdata(array($lblsort.'sort_for'=>$for,$lblsort.'sort_by'=>$sort_by));
		endif;
		$sort_for=ci_get_userdata($lblsort.'sort_for');
		$sort_by=ci_get_userdata($lblsort.'sort_by');
		if(isset($sort_for) && !empty($sort_for)):
			$for=$sort_for;
			$break_sort_order=explode('_',$sort_by);
			$this->data['sort_new']=($sort_by==$break_sort_order[0].'_asc'?$break_sort_order[0].'_desc':$break_sort_order[0].'_asc');
			$sort_by=$sort_by;
			set_userdata(array($lblsort.'sort_for'=>$for,$lblsort.'sort_by'=>$sort_by));
		endif;
		/*set order by sorting using post and session end here!*/
		/*set order by sorting using post and session end here!*/
		$total_rows = $this->Productsmodel->record_count($tbl,$post_data);
		$prodlimit=ci_get_userdata('prodlimit');
		if(isset($prodlimit) && !empty($prodlimit) && isset($post_data['pageshowmenu']) && $post_data['pageshowmenu']==$prodlimit):
			$per_page=$prodlimit;
		elseif(isset($prodlimit) && !empty($prodlimit) && isset($post_data['pageshowmenu'])  && $post_data['pageshowmenu']!=$prodlimit):
			$per_page = $post_data['pageshowmenu'];
			set_userdata(array('prodlimit'=>$per_page));
		else:
 			if(isset($prodlimit) && !empty($prodlimit)):
				$per_page = $prodlimit;	
			else:			
				$per_page = 100;						
				set_userdata(array('prodlimit'=>$per_page));	
			endif;
		endif;
        $uri_segment = 3;
		$page = ($this->uri->segment(3) ? $this->uri->segment(3):0);
		$start=($page>0?$per_page*($page-1):0);
		$config=configpagination($base_url,$total_rows,$per_page,$uri_segment);
		$this->pagination->initialize($config);
     	$this->data["results"] = $this->Productsmodel->fetch_data($tbl,$orderby,$per_page, $start,$sort_by);
		$this->data['perpage']=$per_page;
		$this->data['allrecord']= $total_rows;
		$this->data['current_page']=  $page==0?1:$page;
		$this->data["links"] = $this->pagination->create_links();
		if(isset($extrajs) && !empty($extrajs)){
			$this->data['extra_js']=array('public/admin/js/products/'.$extrajs.'.js');	
		}
		$this->data['sitename']=$this->sitename;  
		$this->data['title']=$title;
		$this->data['content']=$this->class.'/'.$tpl_name;
		$this->data['submethod']=$submethodifany;
		rendertpl('admin/template-dashboard',$this->data);
	}
	
	/*@Function Name:specifications()
	**@Function Params: {$start_from=0,$extrajs,$title,$tpl_name,$tbl,$orderby} 
	**@Function Purpose:{This core function used in listings!}
	*/ 
	public function specifications(){ 
	(is_login()==true?'':moveto('dashboard/login'));
		$start_from=0;
		$extrajs='specifications';
		$title='Product Specs List';
		$tpl_name=$this->method;
		$tbl='ci_specs';
		$orderby='specID';
		$list_perpage_admin=300;
		$this->load->library('pagination');
		$base_url = base_url($this->class.'/'.$this->method);
        $post_data=$this->input->post();
		$sort_by="";
		$submethodifany=false;
		$lblsort=rtrim(trim($title)).'_';
		$this->data['sort_new']="";
			if(isset($post_data['sort_for']) && !empty($post_data['sort_for'])):
			$for=$post_data['sort_for'];
			$break_sort_order=explode('_',$post_data[$for.'_sort']);
			$this->data['sort_new']=($post_data[$for.'_sort']==$break_sort_order[0].'_asc'?$break_sort_order[0].'_desc':$break_sort_order[0].'_asc');
			$sort_by=$post_data[$for.'_sort'];
			set_userdata(array($lblsort.'sort_for'=>$for,$lblsort.'sort_by'=>$sort_by));
		endif;
		$sort_for=ci_get_userdata($lblsort.'sort_for');
		$sort_by=ci_get_userdata($lblsort.'sort_by');
		if(isset($sort_for) && !empty($sort_for)):
			$for=$sort_for;
			$break_sort_order=explode('_',$sort_by);
			$this->data['sort_new']=($sort_by==$break_sort_order[0].'_asc'?$break_sort_order[0].'_desc':$break_sort_order[0].'_asc');
			$sort_by=$sort_by;
			set_userdata(array($lblsort.'sort_for'=>$for,$lblsort.'sort_by'=>$sort_by));
		endif;
		/*set order by sorting using post and session end here!*/
		/*set order by sorting using post and session end here!*/
		$total_rows = $this->Productsmodel->record_count($tbl,$post_data);
		$prodlimit=ci_get_userdata('prodlimit');
		if(isset($prodlimit) && !empty($prodlimit) && isset($post_data['pageshowmenu']) && $post_data['pageshowmenu']==$prodlimit):
			$per_page=$prodlimit;
		elseif(isset($prodlimit) && !empty($prodlimit) && isset($post_data['pageshowmenu'])  && $post_data['pageshowmenu']!=$prodlimit):
			$per_page = $post_data['pageshowmenu'];
			set_userdata(array('prodlimit'=>$per_page));
		else:
 			if(isset($prodlimit) && !empty($prodlimit)):
				$per_page = $prodlimit;	
			else:			
				$per_page = 100;						
				set_userdata(array('prodlimit'=>$per_page));	
			endif;
		endif;
        $uri_segment = 3;
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3):0;
		$start=($page>0?$per_page*($page-1):0);
		$config=configpagination($base_url,$total_rows,$per_page,$uri_segment);
		$this->pagination->initialize($config);
     	$this->data["results"] = $this->Productsmodel->fetch_data($tbl,$orderby,$per_page, $start,$sort_by);
		$this->data['perpage']=$per_page;
		$this->data['allrecord']= $total_rows;
		$this->data['current_page']=  $page==0?1:$page;
		$this->data["links"] = $this->pagination->create_links();
		if(isset($extrajs) && !empty($extrajs)){
			$this->data['extra_js']=array('public/admin/js/products/'.$extrajs.'.js');	
		}
		$this->data['sitename']=$this->sitename;  
		$this->data['title']=$title;
		$this->data['content']=$this->class.'/'.$tpl_name;
		$this->data['submethod']=$submethodifany;
		rendertpl('admin/template-dashboard',$this->data);
	}
	


public function weeklypsales(){ 
	(is_login()==true?'':moveto('dashboard/login'));
		$start_from=0;
		$extrajs='products_style';
		$title="Weekly Sales Product Listing";
		$tpl_name=$this->method;
		$tbl='ci_styles';
		$orderby='styleID';
		$list_perpage_admin=300;
		$this->load->library('pagination');
		$base_url = base_url($this->class.'/'.$this->method);
        $post_data=$this->input->post();
	
		$sort_by="";
		$submethodifany=false;
		$lblsort=rtrim(trim($title)).'_';
		$this->data['sort_new']="";
		if(isset($post_data['sort_for']) && !empty($post_data['sort_for'])):
			$for=$post_data['sort_for'];
			$break_sort_order=explode('_',$post_data[$for.'_sort']);
			$this->data['sort_new']=($post_data[$for.'_sort']==$break_sort_order[0].'_asc'?$break_sort_order[0].'_desc':$break_sort_order[0].'_asc');
			$sort_by=$post_data[$for.'_sort'];
			set_userdata(array($lblsort.'sort_for'=>$for,$lblsort.'sort_by'=>$sort_by));
		endif;
		$sort_for=ci_get_userdata($lblsort.'sort_for');
		$sort_by=ci_get_userdata($lblsort.'sort_by');
		if(isset($sort_for) && !empty($sort_for)):
			$for=$sort_for;
			$break_sort_order=explode('_',$sort_by);
			$this->data['sort_new']=($sort_by==$break_sort_order[0].'_asc'?$break_sort_order[0].'_desc':$break_sort_order[0].'_asc');
			$sort_by=$sort_by;
			set_userdata(array($lblsort.'sort_for'=>$for,$lblsort.'sort_by'=>$sort_by));
		endif;
		/*set order by sorting using post and session end here!*/
		
		//Filters Start
	$tplfilter_params=array();
		$dblfilter_params=array();
		if(isset($post_data['filterby_s']) && !empty($post_data['filterby'])){
		foreach($post_data['filterby_s'] as $keys=>$values){
			if(isset($keys) && !empty($keys) && $keys!='filternow' && $keys!='resetnow'){
				$tplfilter_params['filterby_s'][$keys]=$values;
				$dblfilter_params['filterby_s'][$keys]=$values;
				$this->data[$keys]=$values;
			}
			if(isset($keys) && !empty($keys) && $keys=='resetnow'){
				unset($filter_params);
				unset($dblfilter_params);
				unset_userdata('filterby_s');	
				moveto($this->class);
			}
		}
		} else {
			$filterby=ci_get_userdata('filterby_s');	
			if(isset($filterby) && !empty($filterby)){
			foreach($filterby as $keys=>$values){
				$tplfilter_params['filterby_s'][$keys]=$values;
				$dblfilter_params['filterby_s'][$keys]=$values;
				$this->data[$keys]=$values;
			}
			}
		}

		if(isset($tplfilter_params) && !empty($tplfilter_params) && count($tplfilter_params)>0){
			set_userdata($tplfilter_params);
		} 
		
			//print_r($tplfilter_params);
		//Filters End
		
		/*set order by sorting using post and session end here!*/
		$total_rows = $this->Productsmodel->record_countstyles_sales($tbl,$post_data,$dblfilter_params);
		//echo $this->db->last_query();die;
		$prodlimit=ci_get_userdata('prodlimit');
		if(isset($prodlimit) && !empty($prodlimit) && isset($post_data['pageshowmenu']) && $post_data['pageshowmenu']==$prodlimit):
			$per_page=$prodlimit;
		elseif(isset($prodlimit) && !empty($prodlimit) && isset($post_data['pageshowmenu'])  && $post_data['pageshowmenu']!=$prodlimit):
			$per_page = $post_data['pageshowmenu'];
			set_userdata(array('prodlimit'=>$per_page));
		else:
 			if(isset($prodlimit) && !empty($prodlimit)):
				$per_page = $prodlimit;	
			else:			
				$per_page = 100;						
				set_userdata(array('prodlimit'=>$per_page));	
			endif;
		endif;   
        $uri_segment = 3;
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3):0;
		$start=($page>0?$per_page*($page-1):0);
		$config=configpagination($base_url,$total_rows,$per_page,$uri_segment);
		$this->pagination->initialize($config);
     	$this->data["results"] = $this->Productsmodel->fetch_datastyles_sales($tbl,$orderby,$per_page, $start,$sort_by,$dblfilter_params);
		$this->data['perpage']=$per_page;
		$this->data['allrecord']= $total_rows;
		$this->data['current_page']=  $page==0?1:$page;
		$this->data["links"] = $this->pagination->create_links();
		if(isset($extrajs) && !empty($extrajs)){
			$this->data['extra_js']=array('public/admin/js/products/'.$extrajs.'.js');	
		}
		$this->data['sitename']=$this->sitename;  
		$this->data['title']=$title;
		$this->data['content']=$this->class.'/'.$tpl_name;
		$this->data['submethod']=$submethodifany;
		
		
	 
		rendertpl('admin/template-dashboard',$this->data);
	}


 
	
	/*@Function Name:style_export()
	**@Function Params: {} 
	**@Function Purpose:{}
	*/
	public function style_export(){
	(is_login()==true?'':moveto('dashboard/login'));
	$post_data=$this->input->post();	
	 	$selected_style="";
		if(isset($post_data) && !empty($post_data)){
			$value_at_0=$post_data['styles'][0];
			if($value_at_0=='selectall'){
				unset($post_data['styles'][0]);		
			}
		$selected_style=$post_data['styles'];
		}
		$record_category=$this->Productsmodel->fetch_style_record($selected_style); 
		$csvheading=array('id','styleID','partNumber','brandName','styleName','title','description','baseCategory','categories','catalogPageNumber','newStyle','comparableGroup','companionGroup','brandImage','styleImage');
		createcsv($record_category,$csvheading,'all_style_record_'.date('d-m-Y').'.csv');
	exit;
	}
	
	
	/*@Function Name:specs_export()
	**@Function Params: {} 
	**@Function Purpose:{}
	*/
	public function specs_export(){
	(is_login()==true?'':moveto('dashboard/login'));
	$post_data=$this->input->post();	
	 	$selected_style="";
		if(isset($post_data) && !empty($post_data)){
			$value_at_0=$post_data['specs'][0];
			if($value_at_0=='selectall'){
				unset($post_data['specs'][0]);		
			}
		$selected_style=$post_data['specs'];
		}
		$record_category=$this->Productsmodel->fetch_specs_record($selected_style); 
		$csvheading=array('specID','styleID','partNumber','brandName','styleName','sizeName','sizeOrder','specName','value');
		createcsv($record_category,$csvheading,'all_specs_record_'.date('d-m-Y').'.csv');
	}

	/*@Function Name:export()
	**@Function Params: {} 
	**@Function Purpose:{}
	*/
	public function export(){
	(is_login()==true?'':moveto('dashboard/login'));
	$post_data=$this->input->post();	
	 	$selected_style="";
		if(isset($post_data) && !empty($post_data)){
			$value_at_0=$post_data['products'][0];
			if($value_at_0=='selectall'){
				unset($post_data['products'][0]);		
			}
		$selected_style=$post_data['products'];
		}
		$record_category=$this->Productsmodel->fetch_products_record($selected_style); 
		
			$csvheading=array('skuID','skuID_Master','sku','gtin','yourSku','styleID','brandName','styleName','colorName','colorCode','colorPriceCodeName','colorGroup','colorGroupName','colorFamilyID','colorFamily','colorSwatchImage','colorSwatchTextColor','colorFrontImage','colorSideImage','colorBackImage','color1','color2','sizeName','sizeCode','sizeOrder','sizePriceCodeName','caseQty','unitWeight','piecePrice','dozenPrice','casePrice','salePrice','customerPrice','saleExpiration','qty','warehouses');
		createcsv($record_category,$csvheading,'all_products_record_'.date('d-m-Y').'.csv');
	exit;
	}

public function slugify($string, $replace = array(), $delimiter = '-') {
  // https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Utils/Slug.php
  if (!extension_loaded('iconv')) {
    throw new Exception('iconv module not loaded');
  }
  // Save the old locale and set the new locale to UTF-8
  $oldLocale = setlocale(LC_ALL, '0');
  setlocale(LC_ALL, 'en_US.UTF-8');
  $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
  if (!empty($replace)) {
    $clean = str_replace((array) $replace, ' ', $clean);
  }
  $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
  $clean = strtolower($clean);
  $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
  $clean = trim($clean, $delimiter);
  // Revert back to the old locale
  setlocale(LC_ALL, $oldLocale);
  return $clean;
}	
	

	/*@Function Name:editstyles()
	**@Function Params: {$styleID} 
	**@Function Purpose:{}
	*/
	public function editstyles($styleID){
		(is_login()==true?'':moveto('dashboard/login'));
		$post=$this->input->post();
		if(isset($post) && !empty($post)){
			library('form_validation');
			$this->form_validation->set_rules('title', 'Title', 'required');
			//$this->form_validation->set_rules('customslug', 'Custom SEO');
			$this->form_validation->set_rules('description', 'Description', 'required');
			if ($this->form_validation->run() == false){
				set_flashmsg('flsh_msg',validate(),'validation');
				moveto($this->class.'/'.$this->method.'/'.$styleID);
			} else {
			
				$post['customeseobyuser']=$post['customeseo'];
				$post['customeseo']=$this->slugify($post['customeseo']);
				
				if($this->Productsmodel->chkDuplicateslug($post['customeseo'],$styleID)==true && $post['customeseo']!=""){
				set_flashmsg('flsh_msg','Custom SEO is already exist.','error');
				moveto($this->class.'/'.$this->method.'/'.$styleID);
				} else {
				$response=$this->Productsmodel->customizeData($post,$styleID);
				if($response==true){
					set_flashmsg('flsh_msg','You have been successfully customized the product styles!','success');
					//echo $this->db->last_query();
					moveto($this->class.'/'.$this->method.'/'.$styleID);	
				} else {
					set_flashmsg('flsh_msg','Product style customization failed. You have been successfully customized the product styles!','error');
					moveto($this->class.'/'.$this->method.'/'.$styleID);
				}
				}
				//die();
				//die($post['customeseo']);
				
				
				
			}
			
		} else {
			$this->data['title']="Edit Style!";
			$this->data['styledata']=$this->Productsmodel->getStyledata($styleID);
			$this->data['styleID']=$styleID;
			//'ckeditor/ckeditor.js','ckeditor/sample.js',
			$this->data['extra_js']=array('public/admin/js/'.$this->class.'/'.$this->method.'.js','public/admin/js/'.$this->class.'/jquery.colorbox.js');
			//'ckeditor/neo.css','ckeditor/samples.css',
			$this->data['extra_css']=array('public/admin/css/colorbox.css');
			$this->data['content']=$this->class.'/'.$this->method;
			rendertpl('admin/template-dashboard',$this->data);
		}
	}
	


	public function managereviews($styleID){
(is_login()==true?'':moveto('dashboard/login'));
		
		
			//$this->data['extra_js']=array('public/admin/js/products/mreviews.js');	

		//echo $styleID;
		
		$this->data['styleID']=$styleID;
		$this->data['reviewdata']=$this->Productsmodel->getReviewData($styleID);
		$this->load->view('admin/products/managereviews.php', $this->data);
	 
		//rendertpl('admin/template-dashboard',$this->data);
	}

	
	public function removecustomstyles($styleID){
		$response=$this->Productsmodel->removecustomizeData($styleID);
		if($response==false){
			set_flashmsg('flsh_msg','There is some error during remove the customized data for style. Please try again!','error');
			moveto($this->class);	
		} else {
			set_flashmsg('flsh_msg','Customized data for style have been successfully removed!','success');
			moveto($this->class);	
		}
	}
	

	public function editstylesseo($styleID){
		(is_login()==true?'':moveto('dashboard/login'));
		 $post=$this->input->post();
 	if(isset($post) && !empty($post)){
 	library('form_validation');
	  $this->form_validation->set_rules('pagemetatitle', 'Meta Title', 'required');
	  $this->form_validation->set_rules('pagemetakeywords', 'Meta Keywords', 'required');
	  $this->form_validation->set_rules('pagemetadescription', 'Meta Description', 'required');
   if ($this->form_validation->run() == FALSE){
    set_flashmsg('flsh_msg',validate(),'validation');
    moveto($this->class.'/'.$this->method);
   } else {
    $status=$this->Productsmodel->ProductSEO($post,$styleID); 
    if($status==true){
     set_flashmsg('flsh_msg','Product SEO has been updated successfully!','success');
     moveto($this->class.'/products_style');
    } else {
     set_flashmsg('flsh_msg','Product SEO has been updated un-successfull. Please check form fields and try againg!','error');
     moveto($this->class.'/'.$this->method.'/'.$styleID);
    }
   }
 } else {
  $this->data['pagecontent']=$this->Productsmodel->fetch_style_record($styleID);
  $this->data['title']='Add/Edit Product SEO - '.$styleID;
  $this->data['subtitle']='Add/Edit Product SEO';
  $this->data['content']=$this->class.'/'.$this->method;
  rendertpl('admin/template-dashboard',$this->data);
	}
}	

	public function addeditweeklysales($styleID){
error_reporting(0);	
		(is_login()==true?'':moveto('dashboard/login'));
		 $post=$this->input->post();

	
		 
	 
		 
		 
 	if(isset($post) && !empty($post)){

 	library('form_validation');

	  $this->form_validation->set_rules('date_start', 'Start Date', 'required');
	  $this->form_validation->set_rules('date_end', 'End Date', 'required');
   if ($this->form_validation->run() == FALSE){
    set_flashmsg('flsh_msg',validate(),'validation');
    moveto($this->class.'/'.$this->method);
   } else {
    $status=$this->Productsmodel->ProductSale($post,$styleID); 
    if($status==true){
     set_flashmsg('flsh_msg','Product Sale has been updated successfully!','success');
     moveto($this->class.'/products_style');
    } else {
     set_flashmsg('flsh_msg','Product Sale has been updated un-successfull. Please check form fields and try againg!','error');
     moveto($this->class.'/'.$this->method.'/'.$styleID);
    }
   }
 } else {

 $this->data['extra_js']=array('public/admin/js/'.$this->class.'/products_style.js','public/admin/js/jquery.validationEngine-en.js','public/admin/js/jquery.validationEngine.js');
  $this->data['saledata']=$this->Productsmodel->fetch_product_record($styleID);
  $this->data['title']='Add/Edit Product for Sales (Style ID - '.$styleID.')';
  $this->data['subtitle']='Add/Edit Product for Sales';
  $this->data['content']=$this->class.'/'.$this->method;
  rendertpl('admin/template-dashboard',$this->data);
	}
}

	
	
	/*@Function Name:editproduct()
	**@Function Params: {$styleID,$sku} 
	**@Function Purpose:{}
	*/
	public function editproduct($styleID,$sku){
	(is_login()==true?'':moveto('dashboard/login'));
		$array=$this->input->post();
		if(isset($array) && !empty($array)){
			if($_FILES['colorFrontImage']['name']!=$_POST['oldimage']){
				$update=do_upload('/Images/Color/','colorFrontImage'); 
				$image='/Images/Color/'.$update['file_name'];
				$old_image=$array['oldimage'];	
				unset($array['oldimage']);
				if(strrpos($old_image,'_custom_')==true){
					chmod(0777,true);
					unlink($_SERVER['DOCUMENT_ROOT'].$old_image);
				}
			} else {
				$image=$old_image;	
			}
			$array['styleID']=$styleID;
			$array['sku']=$sku;
		 	$array['colorFrontImage']=$image;
		
			$response=$this->Productsmodel->customize_product_data($array);
			if($response==true){
				set_flashmsg('flsh_msg','You have been successfully customized the product!','success');
				moveto($this->class.'/'.$this->method.'/'.$styleID.'/'.$sku);	
			} else {
				set_flashmsg('flsh_msg','Product customization failed!','error');
				moveto($this->class.'/'.$this->method.'/'.$styleID.'/'.$sku);
			}
		} else {
			$this->data['title']="Edit Products Data!";
			$this->data['pdata']=$this->Productsmodel->getproductdata($styleID,$sku);
			$this->data['styleID']=$styleID;
			$this->data['sku']=$sku;
			$this->data['extra_js']=array('public/admin/js/'.$this->class.'/'.$this->method.'.js','public/admin/js/jquery.validationEngine-en.js','public/admin/js/jquery.validationEngine.js');
			$this->data['extra_css']=array('public/admin/css/validationEngine.jquery.css');
			$this->data['content']=$this->class.'/'.$this->method;
			rendertpl('admin/template-dashboard',$this->data);
		}
	}	
	
	
	public function pdetail($styleID,$sku,$custom=''){
		$prod_original=$this->Productsmodel->getproduct_originaldata($styleID,$sku);
		$prod_custom=$this->Productsmodel->getproductdata($styleID,$sku);
		$filter_custom_val=array();
		foreach($prod_custom as $k=>$items){
			$filter_custom_val[$k]=(empty($items)?$prod_original->$k:$items);
		}
		if($custom==1){
			$this->data['product_detail']=(object)$filter_custom_val;
		}else {
				$this->data['product_detail']=$prod_original;		
		}
		$this->data['title']="Product Detail View";
		$this->data['sku']=$sku;
		$this->data['styleID']=$styleID;
		$this->data['content']=$this->class.'/'.$this->method;
		rendertpl('admin/template-dashboard',$this->data);
	}

	public function inventory(){
	
	$post=$this->input->post();
	if(isset($post['updateBtn']) && $post['updateBtn']=="Sync Inventory") {
	$inventory=$this->Productsmodel->updateInventory($post,0);
	if($inventory==true){
		set_flashmsg('flsh_msg','Style updated successfully!','success');
		moveto($this->class.'/'.$this->method.'/'.$styleID.'/'.$sku);	
		} else {
		set_flashmsg('flsh_msg','Style updated un-succesfull!','error');
		moveto($this->class.'/'.$this->method.'/'.$styleID.'/'.$sku);
		}
	}
	if(isset($post['updateBtn']) && $post['updateBtn']=="Update Style") {
	$inventory=$this->Productsmodel->updateInventory($post,1);
	if($inventory==true){
		set_flashmsg('flsh_msg','Style updated successfully!','success');
		moveto($this->class.'/'.$this->method.'/'.$styleID.'/'.$sku);	
		} else {
		set_flashmsg('flsh_msg','Style updated un-succesfull!','error');
		moveto($this->class.'/'.$this->method.'/'.$styleID.'/'.$sku);
		}
	
	}


		$this->data['title']="Inventory Management";
		$this->data['content']=$this->class.'/'.$this->method;
		rendertpl('admin/template-dashboard',$this->data);
	}	
	
	public function ajax_get_all_sub_cat_style(){
		$this->load->model('front/Homemodel','hmodel');
		$allCat=$this->hmodel->get_edi_all_categories();
		$cat=array();
		foreach($allCat as $k=>$val){
			$cat[]=$val->name;
		}
		echo json_encode($cat);	
	exit;		
	}




	public function syncproductsinventory(){
	(is_login()==true?'':moveto('dashboard/login'));


	}

	
	
	public function sync_multiple_seleted_prod_sync(){
		(is_login()==true?'':moveto('dashboard/login'));
		$post_data=$this->input->post();	
	 	$selected_style="";
		if(isset($post_data) && !empty($post_data)){
			$value_at_0=$post_data['styles'][0];
			if($value_at_0=='selectall'){
				unset($post_data['styles'][0]);		
			}
		$selected_style=$post_data['styles'];
		}
		$return =false;
		foreach($selected_style as $key=>$styleID){
		$styleName=getstyle($styleID,'styleName');
		$url="http://bulkapparel.com/products/sync_products/".$styleID."/".$styleName;
			
		//@Curl Call to Fetch Products
			ini_set('max_execution_time','7000');
			ini_set('memory_limit','1400M');
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$retrive_response = curl_exec($ch);
			curl_close($ch);
			
	 
			if($retrive_response){
				$return=true;
			}

		//@Curl Call to Fetch Products
		}
		if($return==true){
				set_flashmsg('flsh_msg','Product fetch successfully!','success');
				moveto($this->class.'/'.$this->method.'/'.$styleID.'/'.$sku);	
			} else {
				set_flashmsg('flsh_msg','Product fetch un-succesfull!','error');
				moveto($this->class.'/'.$this->method.'/'.$styleID.'/'.$sku);
			}
		
		
	}
	
	public function bulkprices(){ 
		(is_login()==true?'':moveto('dashboard/login'));
		//Check user permission access 6=>Products tab 
		if(checkUserPermissionAccess(6) == 3) {
			$error_notfication = "You don't have permission to access bulk price page.";
			set_flashmsg('flsh_msg',$error_notfication,'error');
			moveto('dashboard/me');
		}
		$start_from=0;
		$extrajs='bulkprices';
		$title="Bulk Increase/Decrease Prices";
		$tpl_name=$this->method;
		
        $post_data=$this->input->post();

		if(isset($post_data) && !empty($post_data)) {
			library('form_validation');
			$this->form_validation->set_rules('percentagevalue', 'Percentage Value', 'required');
			if ($this->form_validation->run() == FALSE):
				set_flashmsg('flsh_msg',validate(),'validation');
				moveto($this->class.'/'.$this->method);
			else:
				if($post_data['lstvariation']==1) $lstvariation="increased";
				if($post_data['lstvariation']==0) $lstvariation="decreased";
				//die();
				$save_page = $this->Productsmodel->UpdateStyleSPBulk($post_data);
				
				if($save_page==true){
					set_flashmsg('flsh_msg',"Successfully updated and product,style prices $lstvariation by ".$post_data['percentagevalue']."% ",'success');
					moveto($this->class.'/'.$this->method);
				} else {
					set_flashmsg('flsh_msg',"Not Updated",'error');
					moveto($this->class.'/'.$this->method);
				}	
			endif;
		} else {


		if(isset($extrajs) && !empty($extrajs)){
			$this->data['extra_js']=array('public/admin/js/products/'.$extrajs.'.js');	
		}
		$this->data['sitename']=$this->sitename;  
		$this->data['title']=$title;
		$this->data['content']=$this->class.'/'.$tpl_name;
		//$this->data['submethod']=$submethodifany;
		
		
	 
		rendertpl('admin/template-dashboard',$this->data);
		
	}	
	}
	
public function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

public function bulkcachepages(){ 
	(is_login()==true?'':moveto('dashboard/login'));
$bUrl=$this->config->item('siteurl');
$xmlData = file_get_contents($bUrl.'addon/pbulkdetails.php');
set_flashmsg('flsh_msg',"Successfully created new bulk cache pages",'success');
moveto($this->class.'/bulkprices');
return 1;	
}	

public function Sbulkcachepages(){ 
	(is_login()==true?'':moveto('dashboard/login'));
$bUrl=$this->config->item('siteurl');
$post_data=$this->input->post();
$xmlData = file_get_contents($bUrl.'addon/padminbulkdetails.php?styleID='.$post_data["styleID"]);
echo 1;	
}
	
	/*@Function Name:products_style()
	**@Function Params: {$start_from=0,$extrajs,$title,$tpl_name,$tbl,$orderby} 
	**@Function Purpose:{This core function used in listings!}
	*/ 
	public function products_style(){ 
		(is_login()==true?'':moveto('dashboard/login'));
		//Check user permission access 6=>product tab
		if(checkUserPermissionAccess(6) == 3) {
			$error_notfication = "You don't have permission to access product page.";
			set_flashmsg('flsh_msg',$error_notfication,'error');
			moveto('dashboard/me');
		}
		$start_from=0;
		$extrajs='products_style';
		$title=PROD_STYLE_LISTING_TITLE;
		$tpl_name=$this->method;
		$tbl='ci_styles';
		$orderby='styleID';
		$list_perpage_admin=300;
		$this->load->library('pagination');
		$base_url = base_url($this->class.'/'.$this->method);
        $post_data=$this->input->post();
	
		$sort_by="";
		$submethodifany=false;
		$this->data["results"] = $this->Productsmodel->fetch_datastyles($tbl);
		if(isset($extrajs) && !empty($extrajs)){
			$this->data['extra_js']=array('public/admin/js/products/'.$extrajs.'.js');	
		}
		$this->data['sitename']=$this->sitename;  
		$this->data['title']=$title;
		$this->data['content']=$this->class.'/'.$tpl_name;
		$this->data['submethod']=$submethodifany;
		rendertpl('admin/template-dashboard',$this->data);
	}
	
	
	
		public function filterStyleIdAjax(){
			$post=$this->input->post();
			$input = $post['input'];
			$matchString=$this->Productsmodel->filterStyleIdAjax($input);
				$html="";
				if (!empty($matchString)) { 
					$html.='<ul class="matchList" id="matchList">'; 
					foreach($matchString as $key=>$matchString) { 
						$matchStringBold = preg_replace('/('.$input.')/i', '<strong>$1</strong>', $matchString->styleID);  
						$html.='<li id="'.$matchString->styleID.'">'.$matchStringBold.'</li>'; 
					}
					$html.='</ul>';
				}
			echo $html;
			exit;
		}
		
		
		public function filterNameAjax(){
			$post=$this->input->post();
			$input = $post['input'];
			$AjaxName=$this->Productsmodel->filterNameAjax($input);
				$html="";
				if (!empty($AjaxName)) {  
					$html.='<ul class="matchList" id="matchList1">'; 
					foreach($AjaxName as $key=>$val) { 
						$matchStringBold = preg_replace('/('.$input.')/i', '<strong>$1</strong>', $val->styleName); 
						$html.='<li id="'.$val->styleName.'">'.$matchStringBold.'</li>'; 
					}
					$html.='</ul>';
				} 
			echo $html;
			exit;				
		}
		
		public function filterBaseCatAjax(){
			$post=$this->input->post();
			$input = $post['input'];
			$catbase=$this->Productsmodel->filterBaseCatAjax($input);
				$html="";
				if (!empty($catbase)) { 
					$html.='<ul class="matchList" id="matchList2">'; 
					foreach($catbase as $key=>$catbase) { 
						$matchStringBold = preg_replace('/('.$input.')/i', '<strong>$1</strong>', $catbase->basecat); 
						$html.='<li id="'.$catbase->basecat.'">'.$matchStringBold.'</li>'; 
					}
					$html.='</ul>';
				}
			echo $html;
			exit;			
		}
		
		
		public function ajaxRemoveStyleFilterSession(){
			$this->session->unset_userdata('styleIDFilter');
			$this->session->unset_userdata('styleNameFilter');
			$this->session->unset_userdata('baseCategoryFilter');
		echo 1; exit;	
		}
	
	


	
	
	
	
			/*@Function Name:allproducts()
	**@Function Params: {$start_from=0,$extrajs,$title,$tpl_name,$tbl,$orderby} 
	**@Function Purpose:{This core function used in listings!}
	*/ 
	public function allproducts($styleID){
	(is_login()==true?'':moveto('dashboard/login'));
		if(empty($styleID)){ 
			moveto($this->class.'/products_style'); 
		}else {
		$start_from=0;
		$extrajs='allproducts';
		$title=PROD_LISTING_TITLE;
		$tpl_name=$this->method;
		$tbl='ci_products';
		$orderby='styleID';
		$list_perpage_admin=300;
		$this->load->library('pagination');
		$base_url = base_url($this->class.'/'.$this->method.'/'.$styleID);
        $post_data=$this->input->post();
		$submethodifany='test';
		$sort_by="";
		$lblsort=rtrim(trim($title)).'_';
		$this->data['sort_new']="";
		if(isset($post_data['sort_for']) && !empty($post_data['sort_for'])):
			$for=$post_data['sort_for'];
			$break_sort_order=explode('_',$post_data[$for.'_sort']);
			$this->data['sort_new']=($post_data[$for.'_sort']==$break_sort_order[0].'_asc'?$break_sort_order[0].'_desc':$break_sort_order[0].'_asc');
			$sort_by=$post_data[$for.'_sort'];
			set_userdata(array($lblsort.'sort_for'=>$for,$lblsort.'sort_by'=>$sort_by));
		endif;
		$sort_for=ci_get_userdata($lblsort.'sort_for');
		$sort_by=ci_get_userdata($lblsort.'sort_by');
		if(isset($sort_for) && !empty($sort_for)):
			$for=$sort_for;
			$break_sort_order=explode('_',$sort_by);
			$this->data['sort_new']=($sort_by==$break_sort_order[0].'_asc'?$break_sort_order[0].'_desc':$break_sort_order[0].'_asc');
			$sort_by=$sort_by;
			set_userdata(array($lblsort.'sort_for'=>$for,$lblsort.'sort_by'=>$sort_by));
		endif;
		/*set order by sorting using post and session end here!*/
		
		
		
		
				//Filters Start
		$filterStyleId="";
		$filterSkuMaster="";
		$filterBrand="";
		$filterQty="";
		if(isset($post_data['filterStyleId']) && !empty($post_data['filterStyleId'])):
			if($filterStyleId==''){ set_userdata(array('filterStyleId'=>$post_data['filterStyleId']));	}
			if($filterStyleId!='' && $filterStyleId!=$post_data['filterStyleId']){ 		
				$this->session->unset_userdata('filterStyleId');  
				set_userdata(array('filterStyleId'=>$post_data['filterStyleId']));	
			}
			$filterStyleId=ci_get_userdata('filterStyleId');
		endif;
		if(isset($post_data['filterSkuMaster']) && !empty($post_data['filterSkuMaster'])):
			if($filterSkuMaster==''){ set_userdata(array('filterSkuMaster'=>$post_data['filterSkuMaster']));	}
			if($filterSkuMaster!='' && $filterSkuMaster!=$post_data['filterSkuMaster']){
				$this->session->unset_userdata('filterSkuMaster');
				set_userdata(array('filterSkuMaster'=>$post_data['filterSkuMaster']));	}
			$filterSkuMaster=ci_get_userdata('filterSkuMaster');
		endif;
		if(isset($post_data['filterBrand']) && !empty($post_data['filterBrand'])):
			if($filterBrand==''){ set_userdata(array('filterBrand'=>$post_data['filterBrand']));	}
			if($filterBrand!='' && $filterBrand!=$post_data['filterBrand']){
				$this->session->unset_userdata('filterBrand');
				set_userdata(array('filterBrand'=>$post_data['filterBrand']));	}
			$filterBrand=ci_get_userdata('filterBrand');
		endif;
		if(isset($post_data['filterQty']) && !empty($post_data['filterQty'])):
			if($filterQty==''){ set_userdata(array('filterQty'=>$post_data['filterQty']));	}
			if($filterQty!='' && $filterQty!=$post_data['filterQty']){
				$this->session->unset_userdata('filterQty');
				set_userdata(array('filterQty'=>$post_data['filterQty']));	}
			$filterQty=ci_get_userdata('filterQty');
		endif;
		
	 	
	 
		//Filters End
		
		
		
		
		/*set order by sorting using post and session end here!*/
		$total_rows = $this->Productsmodel->record_count_prod($styleID,$tbl,$post_data);
		$plimit=ci_get_userdata('plimit');
		if(isset($plimit) && !empty($plimit) && isset($post_data['pageshowmenu']) && $post_data['pageshowmenu']==$plimit):
			$per_page=$plimit;
		elseif(isset($plimit) && !empty($plimit) && isset($post_data['pageshowmenu'])  && $post_data['pageshowmenu']!=$plimit):
			$per_page = $post_data['pageshowmenu'];
			set_userdata(array('plimit'=>$per_page));
		else:
 			if(isset($plimit) && !empty($plimit)):
				$per_page = $plimit;	
			else:			
				$per_page = 100;						
				set_userdata(array('plimit'=>$per_page));	
			endif;
		endif;
        $uri_segment = 4;
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4):0;
		$start=($page>0?$per_page*($page-1):0);
		$config=configpagination($base_url,$total_rows,$per_page,$uri_segment);
		$this->pagination->initialize($config);
     	$this->data["results"] = $this->Productsmodel->fetch_data_prod($styleID,$tbl,$orderby,$per_page, $start,$sort_by);
		$this->data['perpage']=$per_page;
		$this->data['allrecord']= $total_rows;
		$this->data['current_page']=  $page==0?1:$page;
		$this->data["links"] = $this->pagination->create_links();
		if(isset($extrajs) && !empty($extrajs)){
			$this->data['extra_js']=array('public/admin/js/products/'.$extrajs.'.js');	
		}
		$this->data['sitename']=$this->sitename;  
		$this->data['title']=$title;
		$this->data['content']=$this->class.'/'.$tpl_name;
		$this->data['submethod']=$submethodifany;
		$this->data['styleID']=$styleID;
		$this->data['base_path']=BASE_PATH.'/'.implode('/',$this->uri->segment_array());
		
		
		rendertpl('admin/template-dashboard',$this->data);
		}	
	}
	

	public function getExtension($str) 
    {
            $i = strrpos($str,".");
            if (!$i) { return ""; }
            $l = strlen($str) - $i;
            $ext = substr($str,$i+1,$l);
            return $ext;
    }

	public function savestyles(){
		$post=$this->input->post();
		$postData=array();	
		$postProData=array();
		$rootPath=$_SERVER['DOCUMENT_ROOT'];		
		$uploadDir1 = $rootPath.'/styleImages/SCImages/Style-Main-480-600/';
		$uploadDir2 = $rootPath.'/styleImages/SCImages/Color-item-480-600/';
		$styleImpath="Images/Style/";
		$ColorImpath="Images/Color/";
		
	
		
		if(isset($_FILES['cimage1']['name']) and $_FILES['cimage1']['name']!="") { 
			$namef1 = @$_FILES['cimage1']['name'];
			$size1 = @$_FILES['cimage1']['size'];
			$tmp1 =  @$_FILES['cimage1']['tmp_name'];
			
			
			$namefs1=$post['styleID']."_fm.jpg";
			$name1=explode(".",$namefs1);
			$ext1 =  $this->getExtension($namefs1);
			
			$StyleImage =$name1[0].".".$ext1;
			if(file_exists($uploadDir1.$StyleImage) && $StyleImage!=""){
			//$StyleImage =$name1[0].rand(5, 15).".".$ext1;
			$StyleImage =$name1[0].".".$ext1;
			} else {
			$StyleImage =$name1[0].".".$ext1;
			}
			//$StyleImage = time()."1.".$ext1;
			$postData['styleImage']=$styleImpath.$StyleImage;
			$target1=$uploadDir1;
	
			move_uploaded_file($_FILES["cimage1"]["tmp_name"],$target1.$StyleImage);
		}

		///Start Upload Color Images	
		if(isset($post['sku']) && $post['sku']!="") {
			$sku=$post['sku'];		
			$productcolornames=$post['productcolornames'];
			if(isset($_FILES['cimage2']['name']) and $_FILES['cimage2']['name']!="") { 
				$namef2 = @$_FILES['cimage2']['name'];
				$size2 = @$_FILES['cimage2']['size'];
				$tmp2 =  @$_FILES['cimage2']['tmp_name'];
				$namefs2=basename($this->Productsmodel->getPImColName("colorFrontImage",$post['sku']));
				$ext2 =  $this->getExtension($namefs2);
				$name2=explode(".",$namefs2);
			$colorFrontImage =$name2[0].".".$ext2;
			if(file_exists($uploadDir2.$colorFrontImage) && $colorFrontImage!=""){
			//$colorFrontImage =$name2[0].rand(5, 15).".".$ext2;
			$colorFrontImage =$name2[0].".".$ext2;
			} else {
			$colorFrontImage =$name2[0].".".$ext2;
			}
				
				
				
				//$colorFrontImage = time()."2.".$ext2;
				$postProData['colorFrontImage']=$ColorImpath.$colorFrontImage;
				$target2=$uploadDir2;
				move_uploaded_file($_FILES["cimage2"]["tmp_name"],$target2.$colorFrontImage);
			}
	
			if(isset($_FILES['cimage3']['name']) and $_FILES['cimage3']['name']!="") { 
				$namef3 = @$_FILES['cimage3']['name'];
				$size3 = @$_FILES['cimage3']['size'];
				$tmp3 =  @$_FILES['cimage3']['tmp_name'];
				$namefs3=basename($this->Productsmodel->getPImColName("colorSideImage",$post['sku']));
				$ext3 =  $this->getExtension($namefs3);
				
				$name3=explode(".",$namefs3);
				$colorSideImage =$name3[0].".".$ext3;
				if(file_exists($uploadDir2.$colorSideImage) && $colorSideImage!=""){
				//$colorSideImage =$name3[0].rand(5, 15).".".$ext3;
				$colorSideImage =$name3[0].".".$ext3;
				} else {
				$colorSideImage =$name3[0].".".$ext3;
				}
				
				
				$postProData['colorSideImage']=$ColorImpath.$colorSideImage;
				$target3=$uploadDir2;
				move_uploaded_file($_FILES["cimage3"]["tmp_name"],$target3.$colorSideImage);
			}
		
			if(isset($_FILES['cimage4']['name']) and $_FILES['cimage4']['name']!="") { 
				$namef4 = @$_FILES['cimage4']['name'];
				$size4 = @$_FILES['cimage4']['size'];
				$tmp4 =  @$_FILES['cimage4']['tmp_name'];
				$namefs4=basename($this->Productsmodel->getPImColName("colorSideImage",$post['sku']));
				$ext4 =  $this->getExtension($namefs4);
				//$colorBackImage = time()."4.".$ext4;
				$name4=explode(".",$namefs4);
				$colorBackImage =$name4[0].".".$ext4;
				if(file_exists($uploadDir2.$colorBackImage) && $colorBackImage!=""){
				//$colorSideImage =$name4[0].rand(5, 15).".".$ext4;
				$colorSideImage =$name4[0].".".$ext4;
				} else {
				$colorSideImage =$name4[0].".".$ext4;
				}
				
				
				$postProData['colorBackImage']=$ColorImpath.$colorBackImage;
				$target4=$uploadDir2;
				move_uploaded_file($_FILES["cimage4"]["tmp_name"],$target4.$colorBackImage);
			}
			///End Upload Color Images		
		}		
		$postData['customTitle']=stripslashes($post['customTitle']);
		$styleID=$post['styleID'];
		if(isset($post['customseo']) && $post['customseo']!=""){
			$customeseo=$this->slugify($post['customseo']);
			$postData['customeseo']=$customeseo;
			$postData['customeseobyuser']=$post['customseo'];
			$postData['slug']="";
		} else {		
			$customeseo=$this->slugify($post['customTitle']);
			if($this->Productsmodel->chkDuplicateslug($customeseo,$styleID)==true && $customeseo!=""){
			$postData['customeseo']="";
			$postData['customeseobyuser']="";
				$postData['slug']=$customeseo.'1';
			} else {
			$postData['customeseo']="";
			$postData['customeseobyuser']="";
				$postData['slug']=$customeseo;
			}
		}
		$postData['pPrice']=$post['pPrice'];
		$postData['description']=stripslashes($post['description']);
		$postData['pagemetatitle']=$post['pagemetatitle'];
		$postData['pagemetakeywords']=$post['pagemetakeywords'];
		$postData['pagemetadescription']=$post['pagemetadescription'];
		$sndate = date('d-m-Y h:i:s');
		$sndate = strtotime($sndate);
		$sndate = strtotime("+".$post['synctime']." day", $sndate);
		$postData['synctime']=$sndate;
		$postData['syncday']=$post['synctime'];
		
		if(isset($post['styleImageStatus']) && $post['styleImageStatus']==1){
		$postData['styleImageStatus']=1;
		} else {
		$postData['styleImageStatus']=0;
		}
		
		if(isset($post['pitemsisactive']) && $post['pitemsisactive']==1){
		$postData['isExistProduct']=1;
		} else {
		$postData['isExistProduct']=0;
		}
		if(isset($post['relateditems']) && $post['relateditems']==1){
		$postData['relateditems']=1;
		} else {
		$postData['relateditems']=0;
		}
		if(isset($post['customerviewed']) && $post['customerviewed']==1){
		$postData['customerviewed']=1;
		} else {
		$postData['customerviewed']=0;
		}
		
		if(isset($post['isbulkdiscount']) && $post['isbulkdiscount']==1){
		$postData['isbulkdiscount']=1;
		} else {
		$postData['isbulkdiscount']=0;
		}
		if(isset($post['iscoupon']) && $post['iscoupon']==1){
		$postData['iscoupon']=1;
		} else {
		$postData['iscoupon']=0;
		}
		if(isset($post['issync']) && $post['issync']==1){
		$postData['issync']=1;
		} else {
		$postData['issync']=0;
		}
		
		/*echo "<pre>";
			print_r($postData);
			die();*/
		
		$response=array();
		if(!empty($postProData)) {
			/*echo "<pre>";
			print_r($postProData);
			die();*/
			$this->Productsmodel->UpdateStylePData($postProData,$productcolornames,$styleID);
		}
		if($this->Productsmodel->UpdateStyleData($postData,$styleID)==true){
			echo "Style data succesfully updated!";
		}else{
			echo "Style data un-succesfull updated!";
		}
		exit;	
	}

	public function ajaxRemoveProductsFilterSession(){
		$this->session->unset_userdata('filterStyleId');
		$this->session->unset_userdata('filterSkuMaster');
		$this->session->unset_userdata('filterBrand');
		$this->session->unset_userdata('filterQty');
		echo 1; exit;	
	}
		
		
	public function filterskuMasterIdAjax(){
		$post=$this->input->post();
		$input = $post['input'];
		$styleid = $post['styleid'];
		$skuID_Master=$this->Productsmodel->filterskuMasterIdAjax($input,$styleid);
		$html="";
		if (!empty($skuID_Master)) { 
			$html.='<ul class="matchList" id="matchList1">'; 
			foreach($skuID_Master as $key=>$val) { 
				$matchStringBold = preg_replace('/('.$input.')/i', '<strong>$1</strong>', $val->skuID_Master); 
				$html.='<li id="'.$val->skuID_Master.'">'.$matchStringBold.'</li>'; 
			}
			$html.='</ul>';
		}
		echo $html;
		exit;			
	}
			
	public function AjaxCallOtherColor(){
		$post=$this->input->post();
		$sku = $post['sku'];
		$styleid = $post['styleid'];
		$html="";	
		$bUrlImage=$this->config->item('siteurl')."styleImages/";
		$dir_path=$this->config->item('dir_path')."styleImages/";
		$result=$this->Productsmodel->getDetaislByStyleIdSKU($styleid,$sku);
		/*	print_r($result);
		die();	*/
		
		$html.='<input type="hidden" id="styleImageStatus" name="styleImageStatus" value="'.(($result->styleImageStatus==1)?1:0).'" /><div class="act split-img pimage" id="img--1">
                <div class="product-image exp-brd">
                    <div class="product-item-image active" id="mainimg">';
				if(file_exists($dir_path.$result->styleImage) && $result->styleImage!=""){
					$html.='<img  class="pimg" src="'.$bUrlImage.$result->styleImage.'" alt="'.$result->colorName.'" title="'.$result->colorName.'" >';
				}     
		$html.='</div>
				<div class="detail-thumb">
                    <ul class="imgsidebar">
						<li>';
					if(file_exists($dir_path.$result->styleImage) && $result->styleImage!=""){  
					   $html.='<a href="javascript:void(0);"><img id="imgc1" src="'.$bUrlImage.$result->styleImage.'" alt="'.$result->colorName.'" title="'.$result->colorName.'"></a>';
					} else {
						$html.='<a href="javascript:void(0);"><img id="imgc1" src="'.base_url('public/admin/images/empty.jpg').'" alt="'. $result->colorName.'" title="'.$result->colorName.'"></a>';
					}
					$html.='<label class="up-link" for="cimage1">Upload</label>
					<input  type="file" id="cimage1" name="cimage1" accept="image/*" />';
		  
					if($result->styleImageStatus==1) {		  
					$html.='<span class="out" id="hideim"><img src="'.base_url('public/admin/images/remove.png').'" alt="remove"></span><span class="img-on" id="showim" style="display:none;"><img src="'.base_url('public/admin/images/upload.png').'" alt="uploded"></span>';
					} else {
					$html.='<span class="out" id="hideim" style="display:none;"><img src="'.base_url('public/admin/images/remove.png').'" alt="remove"></span><span class="img-on" id="showim" ><img src="'.base_url('public/admin/images/upload.png').'" alt="uploded"></span>';
					}
					$html.='</li>
					<li>';
					if(file_exists($dir_path.$result->colorFrontImage) && $result->colorFrontImage!=""){
						$html.='<a href="javascript:void(0);"><img id="imgc2" src="'.$bUrlImage.$result->colorFrontImage.'" alt="'.$result->colorName.'" title="'.$result->colorName.'"></a>';
					} else {
						$html.='<a href="javascript:void(0);"><img id="imgc2" src="'.base_url('public/admin/images/empty.jpg').'" alt="'.$result->colorName.'" title="'.$result->colorName.'"></a>';
					}
					$html.='<label class="up-link" for="cimage2">Upload</label>
						   <input  type="file" id="cimage2" name="cimage2" accept="image/*" />
						   </li>
							<li>';
							if(file_exists($dir_path.$result->colorSideImage) && $result->colorSideImage!=""){ 
								$html.='<a href="javascript:void(0);"><img id="imgc3" src="'.$bUrlImage.$result->colorSideImage.'" alt="'.$result->colorName.'" title="'.$result->colorName.'"></a>';
							} else {
								$html.='<a href="javascript:void(0);"><img id="imgc3" src="'.base_url('public/admin/images/empty.jpg').'" alt="'.$result->colorName.'" title="'.$result->colorName.'"></a>';
							} 
							$html.='<label class="up-link" for="cimage3">Upload</label>
							<input  type="file" id="cimage3" name="cimage3" accept="image/*" />
							</li>
							<li>';
							if(file_exists($dir_path.$result->colorBackImage) && $result->colorBackImage!=""){
								$html.='<a href="javascript:void(0);"><img id="imgc4" src="'.$bUrlImage.$result->colorBackImage.'" alt="'.$result->colorName.'" title="'.$result->colorName.'"></a>';
							} else {
								$html.='<a href="javascript:void(0);"><img id="imgc4" src="'.base_url('public/admin/images/empty.jpg').'" alt="'. $result->colorName.'" title="'.$result->colorName.'"></a>';
							}
							$html.='<label class="up-link" for="cimage4">Upload</label>
							<input  type="file" id="cimage4" name="cimage4" accept="image" />
						  </li>
						</ul>
						<input type="hidden" name="sku" id="sku" value="'.$sku.'" />
						<input type="hidden" name="productcolornames" id="productcolornames" value="'.$result->colorName.'" />
						</div>
						</div>
						</div>';
						$htmlScript="";
						$htmlScript="<script src='".base_url('public/admin/js/jquery-1.11.1.min.js')."'></script><script>
						function readURL(input,aa) {
							if (input.files && input.files[0]) {
								var reader = new FileReader();
								reader.onload = function (e) {
									$('.pimg').attr('src', e.target.result);
									$('#imgc'+aa).attr('src', e.target.result);
								}
								reader.readAsDataURL(input.files[0]);
							}
						}
						$('document').ready(function(){
							$('.imgsidebar').children('li').children('a').children('img').click(function(){
								var selImg1=$(this).attr('src');
								if($('.pimg').parent().hasClass('product-item-image')){
									$(this).parent().parent().parent().parent().parent().children().children('.pimg').attr('src', selImg1);
								}else{
									$('.pimg').parent().removeClass('product-item-image')
									$('.pimg').parent().addClass('product-item-image active')
								}
							});
							$('#cimage1').change(function(){
								readURL(this,1);
							});
							$('#cimage2').change(function(){
								readURL(this,2);
							});
							$('#cimage3').change(function(){
								readURL(this,3);
							});
							
							$('#cimage4').change(function(){
								readURL(this,4);
							});

							$('#hideim').on('click', function () {
								//$('#styleImageStatus').val(1);
								$('#showim').show();
								$('#styleImageStatus').val(0);
								$('#hideim').hide();
							});
							
							$('#showim').on('click', function () {
								//$('#styleImageStatus').val(0);
								$('#styleImageStatus').val(1);
								$('#hideim').show();
								$('#showim').hide();
							});
						}); </script>";

		$arr1['data']=$html;	
		$arr1['dataScript']=$htmlScript;	
		echo json_encode($arr1);
		//echo $html;
		exit;			
	}


public function AjaxCallUpdateProduct(){
	$post=$this->input->post();

	$pstatus=array();
	$styles=array();
	$pcolorsn=array();
	$sku = $post['sku'];
	$styleID = $post['styleID'];

	$status = (isset($post['status']))?$post['status']:"";
	$oldcolorname = (isset($post['oldcolorname']))?$post['oldcolorname']:"";
	$type = $post['type'];
	$pname = $post['pname'];
	$procolorname = $post['procolorname'];

	if($type=='c') {
	$pstatus['colorStatus']=$status;
	$pcolors=$this->Productsmodel->getColName("pColorsName",$styleID);
	$colorNames=explode(",",$pcolors->pColorsName);
			if($status==1) {
			$newColor = array_diff($colorNames, array($pname));
			$colorValue=implode(",",$newColor);
			} if($status==0) {
			//print_r($sizeNames);
			$colorNames[] = $pname;
			$colorValue=implode(",",array_unique($colorNames));
			}
		$styles['pColorsName']=$colorValue;	
	
	}
	if($type=='s') {
	$pstatus['sizeStatus']=$status;
	$psize=$this->Productsmodel->getColName("pSizesId",$styleID);
	$sizeNames=explode(",",$psize->pSizesId);
			if($status==1) {
			$newSize = array_diff($sizeNames, array($pname));
			$sizeValue=implode(",",$newSize);
			} if($status==0) {
			//print_r($sizeNames);
			$sizeNames[] = $pname;
			$sizeValue=implode(",",array_unique($sizeNames));
			}
		$styles['pSizesId']=$sizeValue;	
	}
	
	if($type=='p') {
	$pstatus['customerPrice']=$pname;
	}
	if($type=='q') {
	$pstatus['qty']=$pname;
	}
	if($type=='h') {
	$pstatus['color1']=$pname;
	}
	if($type=='cname') {
	$pcolorsn['colorName']=$pname;
	//oldcolorname
	$pcolors=$this->Productsmodel->getColName("pColorsName",$styleID);
	$colorNames=explode(",",$pcolors->pColorsName);
	$colorNames = array_replace($colorNames,array_fill_keys(array_keys($colorNames, $oldcolorname),$pname));
	$styleColorvalue=implode(",",$colorNames);
	$styles['pColorsName']=$styleColorvalue;	
	
	}
	
	
	if($type=='s' or $type=='c' or $type=='cname') {
	$this->Productsmodel->UpdateStyleData($styles,$styleID);
	
	}

	if(!empty($pcolorsn)) {
	$pname=$post['oldcolorname'];
	if($this->Productsmodel->UpdateStylePColorName($pcolorsn,$pname,$styleID,'c')==true) {
	$response['result']=0;
		$response['msg']="Updated!";
		echo json_encode($response);
	}else{
	$response['result']=1;
		$response['msg']="Not Updated!";
		echo json_encode($response);
	}
	
	
	} else {
	$response=array();
	if($type=='h') {
	$chk=$this->Productsmodel->UpdateStylePStatus($pstatus,$procolorname,$styleID);
	} else if($type=='q' or $type=='p') {
	$chk=$this->Productsmodel->UpdateStyleQStatus($pstatus,$sku);
	} else {

	$chk=$this->Productsmodel->UpdateStylePSColorName($pstatus,$pname,$styleID,$type);
	}
	
	if($chk==true) {
		$response['result']=0;
		$response['msg']="Updated!";
	//echo "Succesfully Updated!";
		echo json_encode($response);
	}else{
	//echo "un-succesfull updated!";
		$response['result']=1;
		$response['msg']="Not Updated!";
		echo json_encode($response);
	}
	}
	exit;

}





public function ajax_chk_coupons(){
	$post=$this->input->post();
	$ccode = $post['ccode'];
	$cid = $post['cid'];
	$response=array();
	$chkcoups=$this->Productsmodel->chkCoupons($ccode,$cid);
	if($chkcoups==1) {
	$response['result']=1;
	$response['msg']=$ccode." already exist";
	} else {
	$response['result']=0;
	$response['msg']="";
	}
	
	echo json_encode($response);
		
}

			
		public function filterBrandAjax(){
			$post=$this->input->post();
			$input = $post['input'];
			$styleid = $post['styleid'];
			$Brands=$this->Productsmodel->filterBrandAjax($input,$styleid);
				$html="";
				if (!empty($Brands)) { 
					$html.='<ul class="matchList" id="matchList2">'; 
					foreach($Brands as $key=>$val) { 
						$matchStringBold = preg_replace('/('.$input.')/i', '<strong>$1</strong>', $val->brands); 
						$html.='<li id="'.$val->brands.'">'.$matchStringBold.'</li>'; 
					}
					$html.='</ul>';
				}
			echo $html;
			exit;			
		}


public function exportCoupons(){
		$post_data=$this->input->post();	
		//echo "<pre>post_data==";print_r($post_data);die;
		$selected_data="";
		if(isset($post_data['chkIds'][0]) && !empty($post_data['chkIds'][0])){
			$value_at_0=$post_data['chkIds'][0];
			if($value_at_0=='selectall'){
				unset($post_data['chkIds'][0]);		
			}
			//$selected_payments=$post_data['chkIds'];
			$selected_data = $post_data['chkIds'];
		}
		$record_coupons=$this->Productsmodel->fetch_coupon_record($selected_data); 
	 
		
		$csvheading=array('#','Coupon Code','Description','Discount Type','Coupon Amount','Coupon Intial Date','Coupon Expiry Date','Allow Free Shipping','Individual Use Only','Usage Limit Per Coupon','Usage Limit Per User');
		createcsv($record_coupons,$csvheading,'all_coupons_record_'.date('d-m-Y').'.csv');
	exit;
	}
		

public function deleteCoupons(){
		//Check permission For this section
		$post_data=$this->input->post();

			
		if(isset($post_data['chkIds']) && !empty($post_data['chkIds'])){
			$id=$post_data['chkIds'] ;
		}	
		$status=$this->Productsmodel->deleteCouponIds($id);	
		if($status==1):
 			set_flashmsg('flsh_msg',"Successfully deleted coupons data",'success');
			moveto($this->class.'/couponmgmt');
		else:
			set_flashmsg('flsh_msg',"Error: Selected coupons delete unsuccessfull!",'error');
			moveto($this->class.'/couponmgmt');
		endif;
	}
		

public function ajax_review_delete(){
		//Check permission For this section
		$post_data=$this->input->post();

			
		if(isset($post_data['chkIds']) && !empty($post_data['chkIds'])){
			$id=$post_data['chkIds'] ;
		}	
		$status=$this->Productsmodel->deleteReviews($id);	
		if($status==1):
 			
			set_flashmsg('flsh_msg',"Successfully deleted reviews data",'success');
			echo 1;
			
			//moveto($this->class.'/managereviews/'.$post_data['styleID']);
		else:
			
			set_flashmsg('flsh_msg',"Error: Selected reviews delete unsuccessfull!",'error');
			echo 0;
			//moveto($this->class.'/managereviews/'.$post_data['styleID']);
		endif;
	}



public function ajax_review_sdelete(){
		//Check permission For this section
		$post_data=$this->input->post();

			
		if(isset($post_data['revid']) && !empty($post_data['revid'])){
			$id=$post_data['revid'] ;
		}	
		$status=$this->Productsmodel->deleteSReviews($id);	
		if($status==true):
 			
			set_flashmsg('flsh_msg',"Successfully deleted review data",'success');
			echo 1;
			
			//moveto($this->class.'/managereviews/'.$post_data['styleID']);
		else:
			
			set_flashmsg('flsh_msg',"Error: Selected reviews delete unsuccessfull!",'error');
			echo 0;
			//moveto($this->class.'/managereviews/'.$post_data['styleID']);
		endif;
	}



public function ajax_review_mstatus(){
		//Check permission For this section
		$post_data=$this->input->post();

			
		if(isset($post_data['chkIds']) && !empty($post_data['chkIds'])){
			$id=$post_data['chkIds'] ;
		}	
		$status=$this->Productsmodel->updateRStatus($id,$post_data['mstatus']);	
		if($status==1):
 			
			set_flashmsg('flsh_msg',"Successfully change selected reviews status data",'success');
			echo 1;
			
			//moveto($this->class.'/managereviews/'.$post_data['styleID']);
		else:
			
			set_flashmsg('flsh_msg',"Error: Selected reviews status unsuccessfull!",'error');
			echo 0;
			//moveto($this->class.'/managereviews/'.$post_data['styleID']);
		endif;
	}




public function updatebulkprices(){
$post=$this->input->post();
$response=array();
if($post['lstvariation']==1) $lstvariation="increased";
if($post['lstvariation']==0) $lstvariation="decreased";
$flg=$this->Productsmodel->UpdateStylePBulk($post);
if($flg==true) {
$response['result']=1;
$response['msg']="Successfully updated bulk style and prdouct price ".$lstvariation." by ".$post['percentagevalue'].".";
} else {
$response['result']=0;
$response['msg']="";
}

	echo json_encode($response);

}


public function savepricecodes(){
$post=$this->input->post();
$response=array();
$data=array();
/*print_r($post);
die();*/
foreach($post['colorprices'] as $ky=>$pval) {
				foreach($pval as $ky2=>$pval2) {
				$chkstatus=$this->Productsmodel->updatePriceCodes($ky,$ky2,$pval2,$post['styleIDP']);
				}
}
$data=array('priceheading'=>$post['colorpriceHeading']);

$chkstatus=$this->Productsmodel->UpdateStylePHeading($data,$post['styleIDP']);

if($chkstatus) {
$response['result']=0;
$response['msg']="Successfully updated price color codes";
} else {
$response['result']=1;
$response['msg']="";
}

echo json_encode($response);
}

public function ajax_review_status(){
	$post=$this->input->post();
	$revid = $post['revid'];
	$status = $post['status'];
	//if($status==1) $st=0; else $st=0;
	$response=array();
	$chkstatus=$this->Productsmodel->updateReview($status,$revid);
	if($chkstatus==true) {
	if($status==0){
	$response['status']=1;
	$response['tstatus']="Disable";
	$response['tcolor']="#FF0000";
	$response['title']="Click for Disable";
	} else{
	$response['status']=0;
	$response['tstatus']="Enable";
	$response['tcolor']="#006600";
	$response['title']="Click for Enable";
	}
	$response['result']=1;
	$response['msg']="Successfully updated review";
	} else {
	$response['result']=0;
	$response['msg']="";
	}
	
	echo json_encode($response);
		
}



public function ajax_search_styles() {
$post=$this->input->post();
if(isset($post) && !empty($post)){
$strHtmlScript="";	

	$strHtmlCData="";
	$stylesData=$this->Productsmodel->getStylesData($post['skeywords']);
if(isset($stylesData) && !empty($stylesData)) {	
	foreach($stylesData as $c=>$cval) {
	$desc=strip_tags($cval->description);
	if (strlen($desc) > 50) {

    // truncate string
    $stringCut = substr($desc, 0, 50);

    // make sure it ends in a word so assassinate doesn't become ass...
    $desc = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
}
//<input type="checkbox" id="chkIds" name="chkIds[]" class="checkbox" value="'.$cval->styleID.'"/>
	$strHtmlCData.='<li>
                    <div class="ob-check">
                        
						
                    </div>
                    <div class="ob-list-info">
                        <div class="ob-list-row">
                            <div class="ob-list-info-name">'.$cval->styleName.'</div>
                            <div class="ob-list-info-cost">'.$cval->brandName.'</div>
                        </div>
						<div class="ob-list-row-1">'.$desc.'</div>
                        <div class="ob-list-row-1">
                            <div class="ob-list-info-number"><a href="javascript:void(0);" class="viewstyleid" data-sid="'.$cval->styleID.'">'.$cval->styleID.'</a></div><div class="ob-list-info-status">'.SYMBOL.$cval->pPrice.'</div>
                        </div>
                    </div>
                </li>';
	}
$response['result']=1;	
} else {
$strHtmlCData.='<li><div class="ob-list-info" style="color:#FF0000">No records found</div></li>';
$response['result']=0;
}

	
$strHtmlScript="<script>
				jQuery(document).ready(function(){

$(document).on('click', 'input.checkbox', function() {
    var arr_sort = new Array();
    $('.checkbox').each(function()
    {
        if( $(this).is(':checked') )
        {
			if($(this).val()!='on') {
            arr_sort.push($(this).val());
			}
        }
    });

});

$('#select1').on('click',function(){
		var allVals = [];					  
        if(this.checked){
            $('.checkbox').each(function(){
				if($(this).val()!='on') {						 
				allVals.push($(this).val());				 
				}
                this.checked = true;
            });
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
            });
        }
		
    });

$('.viewstyleid').on('click', function () {
								   
	var base_url=$('#currentparent').val();
 $(this).parents('li').siblings().removeClass('selected');
  $(this).parents('li').addClass('selected');
 $('#cdetailsh').hide();
 $('#cdetails').show();
 $('#loadingmessage').show();

	var sid=$(this).attr('data-sid');
 	$.ajax({
			url: base_url+'viewStyledata',
			method: 'post',
			data: 'styleID='+sid,
			dataType: 'json',
			success: function(msg)
				{
					$('#cajaxdata').html(msg.msg);

					$('#loadingmessage').hide();

					
				}
		});
	

});

   $(document).on('click', 'input.checkbox', function() {
														  
        if($('.checkbox:checked').length == $('.checkbox').length){
					
            $('#select1').prop('checked',true);
        }else{
            $('#select1').prop('checked',false);
        }
		 
    });
});
				</script>
				";	
	
	
	$response['cdata']=$strHtmlCData;
	$response['strHtmlScript']=$strHtmlScript;
   $response['msg']="Coupons has been saved successfully!";

echo json_encode($response);
}
}





public function viewStyledata(){
	$post=$this->input->post();
	$strHtml="";
	if(isset($post) && !empty($post)){
		$styledata=$this->Productsmodel->getStyledata($post['styleID']);	
		$result2=$this->Productsmodel->getDetaislByStyleId($post['styleID']);

		//$result2 =getDetaislByStyleId($stid);
		$reskey=array();
		foreach($result2 as $kw1=>$kval) {
		$kwp=explode("/",$kw1);
		$tp=count($kwp);
		if($tp==1) {
			if($kw1=="Black") {
				$reskey[$kw1]=$kval;
			}
			if($kw1=="White") {
				$reskey[$kw1]=$kval;
			} else {
				$results[$kw1]=$kval;
			}
		} else {
		if((isset($kwp[0]) && $kwp[0]=="Black") || (isset($kwp[1]) && $kwp[1]=="Black") || (isset($kwp[2]) && $kwp[2]=="Black")) {
				$reskey[$kw1]=$kval;
			}
		if((isset($kwp[0]) && $kwp[0]=="White") || (isset($kwp1[1]) && $kwp[1]=="White") || (isset($kwp[2]) && $kwp[2]=="White")) {	
				$reskey[$kw1]=$kval;
			} else {
				$results[$kw1]=$kval;
			}
		}
		
			
		}
/*echo "<pre>";
print_r($reskey);
die();*/	
if(isset($reskey)) $reskey=$reskey; else $reskey=array();
	
		
		function ksort_deep(&$array){
			ksort($array);
			
			foreach($array as &$value)
				if(is_array($value))
					ksort_deep($value);
		}

		// example of use:
		if(isset($results)) ksort_deep($results); else $results=array();
		

		foreach ($reskey as $key => $row) {
			// replace 0 with the field's index/key
			$dates[$key]  = $row;
		}


if(isset($dates)) { array_multisort($dates, SORT_ASC, $reskey); }
		

		$result = array_merge($reskey,$results);
		$bUrlImage=$this->config->item('siteurl')."styleImages/SCImages/";
		$dir_path=$this->config->item('dir_path')."styleImages/SCImages/";	
		

		
		$attributes = array('class' => 'editstylescss', 'id' => 'editstylesid','role'	=> 'editstylesform');
		$battributes = array('class' => 'editbulk', 'id' => 'frmBPrice','role'	=> 'editbulkform');
		$strHtml='<h3 id="p-style-head">Bulk Price Increase/Decrease</h3><div class="row">'.form_open('', $battributes).'
			<div id="customjsmessage" style="display:none;"><div class="successdiv"><label for="success"></label></div></div>
			<div class="col-sm-4">
			<div class="form-group">
			<label for="Student">Price Percentage Variation:</label>
			<select name="lstvariation" id="lstvariation" class="form-control" >
				<option value="1">Increase</option>
				<option value="0">Decrease</option>
			</select>
            </div>
			</div>
			<div class="col-sm-4">
			<div class="form-group">
				<label for="Email">Percentage Value (%):</label>
				<input name="percentagevalue" class="form-control" />
            </div>
			</div>

			<div class="col-sm-4">
			<div class="form-group" style="margin-top: 19px;">
				<input name="updateBtn" value="Update" id="updateBBtn" class="btn btn-primary" type="button"/>
			</div>
			<input type="hidden" id="styleIBP" name="styleIBP" value="'.(($post['styleID']!=false)?$post['styleID']:'').'" />
			</div></form></div>
			<h3 id="p-style-head">Basic Style Details</h3>'.form_open('', $attributes);
			$strHtml.='<div id="customjsmessage1" style="display:none;"><div class="successdiv"><label for="success"></label></div></div><div class="product-bx"><input type="hidden" id="styleID" name="styleID" value="'.(($post['styleID']!=false)?$post['styleID']:'').'" /><span class="scriptid"></span>  
			<style>
			.dw-box:after {
			background-image: url('.base_url('public/admin/images/dd.png').') !important;
			}
			</style> 
				<div class="holder">
					<div class="product-image-detail">
					<input type="hidden" id="styleImageStatus" name="styleImageStatus" value="'.(($styledata->styleImageStatus==1)?1:0).'" />';
			$knumer=0;
			$k=0;
				foreach($result as $key=>$val){  
					foreach($val as $i=>$v) {
						if($i<1) {
			if($k==0) {

            
   $strHtml.='<div class="act split-img pimage" id="img--1">
                <div class="product-image exp-brd">
                    <div class="product-item-image active" id="mainimg">';

               if(file_exists($dir_path.str_replace("Images/Style","Style-Main-480-600",$styledata->styleImage)) && $styledata->styleImage!=""){
				$strHtml.='<img  class="pimg" src="'.$bUrlImage.str_replace("Images/Style","Style-Main-480-600",$styledata->styleImage).'" alt="'.$v['colorName'].'" title="'.$v['colorName'].'" >';
				}     
				                  
           $strHtml.='</div>
            
                    <div class="detail-thumb">
                        <ul class="imgsidebar">
                     
                            <li>';
            if(file_exists($dir_path.str_replace("Images/Style","Style-Main-480-600",$styledata->styleImage)) && $styledata->styleImage!=""){
                $strHtml.='<a href="javascript:void(0);"><img id="imgc1" src="'.$bUrlImage.str_replace("Images/Style","Style-Main-480-600",$styledata->styleImage).'" alt="'.$v['colorName'].'" title="'.$v['colorName'].'"></a>';
                 } else {
                 $strHtml.='<a href="javascript:void(0);"><img id="imgc1" src="'.base_url('public/admin/images/empty.jpg').'" alt="'.$v['colorName'].'" title="'.$v['colorName'].'"></a>';
                 }
               $strHtml.='<label class="up-link" for="cimage1">Upload</label>
	                       <input  type="file" id="cimage1" name="cimage1" accept="image/*" />';
			if($styledata->styleImageStatus==1) {                        
				$strHtml.='<span class="out" id="hideim"><img src="'.base_url('public/admin/images/remove.png').'" alt="remove"></span>
				<span class="img-on" id="showim" style="display:none;"><img src="'.base_url('public/admin/images/upload.png').'" alt="uploded"></span>';
			} else {
				$strHtml.='<span class="out" id="hideim" style="display:none;"><img src="'.base_url('public/admin/images/remove.png').'" alt="remove"></span>
				<span class="img-on" id="showim"><img src="'.base_url('public/admin/images/upload.png').'" alt="uploded"></span>';
			}

$strHtml.='</li> <li>';
if(file_exists($dir_path.str_replace("Images/Color","Color-item-480-600",$v['colorFrontImage'])) && $v['colorFrontImage']!=""){
$strHtml.='<a href="javascript:void(0);"><img id="imgc2" src="'.$bUrlImage.str_replace("Images/Color","Color-item-480-600",$v['colorFrontImage']).'" alt="'.$v['colorName'].'" title="'.$v['colorName'].'"></a>';
 } else {
$strHtml.='<a href="javascript:void(0);"><img id="imgc2" src="'.base_url('public/admin/images/empty.jpg').'" alt="'.$v['colorName'].'" title="'.$v['colorName'].'"></a>';
 }

$strHtml.='<label class="up-link" for="cimage2">Upload</label>
<input  type="file" id="cimage2" name="cimage2" accept="image/*" />
</li><li>';
if(file_exists($dir_path.str_replace("Images/Color","Color-item-480-600",$v['colorSideImage'])) && $v['colorSideImage']!=""){
$strHtml.='<a href="javascript:void(0);"><img id="imgc3" src="'.$bUrlImage.str_replace("Images/Color","Color-item-480-600",$v['colorSideImage']).'" alt="'.$v['colorName'].'" title="'.$v['colorName'].'"></a>';
} else {
$strHtml.='<a href="javascript:void(0);"><img id="imgc3" src="'.base_url('public/admin/images/empty.jpg').'" alt="'. $v['colorName'].'" title="'.$v['colorName'].'"></a>';
 }
$strHtml.='<label class="up-link" for="cimage3">Upload</label>
<input  type="file" id="cimage3" name="cimage3" accept="image/*" />
</li><li>';
 if(file_exists($dir_path.str_replace("Images/Color","Color-item-480-600",$v['colorBackImage'])) && $v['colorBackImage']!=""){
$strHtml.='<a href="javascript:void(0);"><img id="imgc4" src="'.$bUrlImage.str_replace("Images/Color","Color-item-480-600",$v['colorBackImage']).'" alt="'.$v['colorName'].'" title="'.$v['colorName'].'"></a>';
 } else {
$strHtml.='<a href="javascript:void(0);"><img id="imgc4" src="'.base_url('public/admin/images/empty.jpg').'" alt="'. $v['colorName'].'" title="'.$v['colorName'].'"></a>';
 }
$strHtml.='<label class="up-link" for="cimage4">Upload</label>
<input  type="file" id="cimage4" name="cimage4" accept="image/*" />
</li>
</ul>
<input type="hidden" id="productcolornames" name="productcolornames" value="'.$v['colorName'].'" />
<input type="hidden" name="sku" id="sku" value="'.$v['sku'].'" />
                    </div>
                </div>
            </div>';
 }
	}
	$k++; 
	}
	 }
  
$strHtml.='</div>
        
        <div class="product-right-bx">
            <div class="title-bx">
                <input type="text" id="customTitle" name="customTitle" value="'.$styledata->customTitle.'" placeholder="Style Title"/>
                
            </div>
			<div class="mng-bx"><a href="'.base_url('products/managereviews/'.$styledata->styleID).'" class="mpreview" id="mng-review">Manage Product Review</a></div>
      
            <div class="price-bx">
                <b>Style Price</b> <span><b>$</b></span><input type="text" readonly id="pPrice" name="pPrice" value="'.number_format($styledata->pPrice,2).'" />
            </div>
			<!--<div class="syns-bx">
				    <b>Sync By</b> <span><b>:</b></span>
                    <input placeholder="0 Day/Days" type="text" name="synctime" id="synctime" class="numbersOnly" value="'.$styledata->syncday.'"> <span></span>
					<a href="javascript:void(0);" style="display:none;">Sync Now</a>
				</div>-->
            <div class="feature-bx">
                <h3>Features</h3>
                 <textarea placeholder="Product Features" id="description" name="description">'.$styledata->description.'</textarea>
            </div>
            <div class="control-bx">
                <div class="ctrl-left">
                    <h3>Display Item :-</h3> <input type="checkbox" id="pitemsisactive" name="pitemsisactive" '.(($styledata->isExistProduct==0)?'checked':'').' value="'.(($styledata->isExistProduct==1)?1:0).'" ><label for="pitemsisactive"></label>
                    <br>
						<h3>Custom URL:</h3><div class="title-bx"><input type="text" name="customseo" id="customseo" value="'.$styledata->customeseo.'"/></div>
                </div>
                <div class="ctrl-right">
                    <h3>Related Items :-</h3> <input type="checkbox" id="relateditems" '.(($styledata->relateditems==1)?'checked':'').' name="relateditems" value="'.(($styledata->relateditems==1)?1:0).'"><label for="relateditems"></label>
                    <br>
                    <h3>Customer Who Viewed :-</h3> <input type="checkbox" id="customerviewed" name="customerviewed" '.(($styledata->customerviewed==1)?'checked':'').' value="'.(($styledata->customerviewed==1)?1:0).'"><label for="customerviewed"></label>
                </div>
            
            </div>
 <div class="sycs-box">
					<div class="sycs-left">
					<label><input type="checkbox" name="isbulkdiscount" '.(($styledata->isbulkdiscount==1)?'checked':'').'  id="isbulkdiscount" value="'.(($styledata->isbulkdiscount==1)?1:0).'"/><span>Don\'t allow bulk discount with this style.</span></label><br>
					<label><input type="checkbox" name="iscoupon" id="iscoupon" '.(($styledata->iscoupon==1)?'checked':'').' value="'.(($styledata->iscoupon==1)?1:0).'"/><span>Don\'t allow coupon use with this style.</span></label>
					</div>
					<div class="sycs-right">
					<label><input type="checkbox" name="issync" id="issync" '.(($styledata->issync==1)?'checked':'').' value="'.(($styledata->issync==1)?1:0).'"/><span>Don\'t sync.</span></label><br>
					</div>
					</div>   
            <div class="col-box">
                    <h3>Colors</h3>';
       $listColors2=$this->Productsmodel->getColorsByStyleId($styledata->styleID);
				//print_r($listColors2);

$i=0;
foreach($listColors2 as $clrs) {	
if($clrs['colorName']=="Black") {
$colorslists[]=$clrs;
} else if($clrs['colorName']=="White") {
$colorslists[]=$clrs;
} else {
$colorsliste[]=$clrs;
}
$i++;
}
if(!empty($colorslists)) $colorslists=$colorslists; else $colorslists=array();
if(isset($colorsliste)){
sort($colorsliste);
} else {
$colorsliste=array();
}
$colors=array_merge($colorslists,$colorsliste);




$strHtml.='<div class="color-hold">
                     <div class="color-dw">
                    <div class="dw-box" id="sel-col">
                    <div class="select-color-box" style="background-color:'.(isset($colors[0]['color1'])?$colors[0]['color1']:'').'" id="bgclr"></div><span id="clrname">'.(isset($colors[0]['colorName'])?$colors[0]['colorName']:'').'</span>
                    <ul id="target-col">';

				foreach($colors as $k=>$vc) {
		
$strHtml.='<li class="col1" data-clrcode="'.$vc['color1'].'" data-sku="'.$vc['sku'].'" data-clrname="'.$vc['colorName'].'"><div class="select-color-box-1" style="background:'.$vc['color1'].'"></div> '.$vc['colorName'].'</li>';
				
				
				}

$strHtml.='</ul>
                    </div>
                    </div>   
                    </div>
                </div>
        </div>
        
        </div>
  <div class="miss-img-bx holder">
                    <h4>Color with missing images.</h4>
                    <ul>';

				
				foreach($colors as $k1=>$vc1) {
				if((!file_exists($dir_path.$vc1['colorFrontImage']) or $vc1['colorFrontImage']=="") or (!file_exists($dir_path.$vc1['colorSideImage']) or $vc1['colorSideImage']=="") or (!file_exists($dir_path.$vc1['colorBackImage']) or $vc1['colorBackImage']=="")){		
$strHtml.='<li><a href="javascript:void(0);" class="col1" data-clrcode="'.$vc1['color1'].'" data-sku="'.$vc1['sku'].'" data-clrname="'.$vc1['colorName'].'" style="background:'.$vc1['color1'].'"></a></li>';
				}
				}
					

$strHtml.='</ul>
                </div>   
			  
        <div class="meta-box">
            <div class="meta-1">
                <div class="holder-1">
                    <h3>Meta Title</h3>
                    <input class="form-control" type="text" name="pagemetatitle" id="pagemetatitle" value="'.($styledata!=false?$styledata->pagemetatitle:'').'" placeholder="Please enter meta title."/>
                </div>
            </div>
            <div class="meta-2">
                <div class="holder-1">
                     <h3>Meta Keywords</h3>
                    <textarea class="form-control"  name="pagemetakeywords" id="pagemetakeywords" rows="3" placeholder="Please enter meta keywords.">'.($styledata!=false?$styledata->pagemetakeywords:'').'</textarea>
                </div>
            </div>
            <div class="meta-3">
                <div class="holder-1">
                    <h3>Meta Description</h3>
                    <textarea class="form-control" rows="3" name="pagemetadescription" id="pagemetadescription" placeholder="Please enter meta description.">'.($styledata!=false?$styledata->pagemetadescription:'').'</textarea>
                </div>
            </div>
        </div>
        <div class="save-bx"><button type="button" name="btnSubmit" id="btnSubmit">Save</button></div>';
$strHtml.=form_close();

$strHtml.='<div class="custom-col holder" style="width:auto;">
<h3>Color Code Prices</h3>
<form name="frmPrice" id="frmPrice" method="post">
<div id="customjsmessage" style="display:none;"><div class="successdiv"><label for="success"></label></div></div>

<input type="hidden" id="styleIDP" name="styleIDP" value="'.(($post['styleID']!=false)?$post['styleID']:'').'" />

                    <div class="c-col-row">
					<label>Headings : </label><input type="text" name="colorpriceHeading" id="colorpriceHeading" value="'.($styledata!=false?$styledata->priceheading:'').'">
					</div>';
					
$cprices=$this->Productsmodel->getColorPriceInfo($styledata->styleID);	
$strHtml.='<div class="c-col-row">';				
foreach($cprices as $cpval){	
if($cpval['colorPriceCodeName']=="Heathers" && intval($cpval['minPrice'])>0) {				
$strHtml.='<div class="c-col-col">
		<label>'.$cpval['colorPriceCodeName'].' : </label><span><b>'.SYMBOL.'</b></span><input type="text" readonly name="colorprices['.$cpval['colorPriceCodeName'].']['.$cpval['sku'].']" id="colorprice" value="'.number_format($cpval['minPrice'],2).'" class="number">
		</div>';
} else {
$strHtml.='<div class="c-col-col">
		<label>'.$cpval['colorPriceCodeName'].' : </label><span><b>'.SYMBOL.'</b></span><input type="text" readonly name="colorprices['.$cpval['colorPriceCodeName'].']['.$cpval['sku'].']" id="colorprice" value="'.number_format($cpval['minPrice'],2).'" class="number">
		</div>';
						}		
}					
$strHtml.='<div class="save-bx"><button type="button" name="btnPSubmit1" id="btnPSubmit1">Save</button></div></div>
                </form></div>';	
$strHtml.='<div class="table-bx">
<div><span style="color:#d51a2a;"><strong>Important*</strong> :- if any changes in bulk prices section, please click here for bulk cache update <a href="javascript:void(0);" id="bulkidp"><strong>Update Bulk</strong></a></span></div><br>
<div id="customjsmessage6" style="display:none;"><div class="successdiv"><label for="success">Successfully updated bulk cache pages of this style.</label></div></div><br>
 <input type="hidden" name="pStyleId" id="pStyleId" value="'.$styledata->styleID.'" />       
            <table>
                <thead>
                    <tr>
                        <th  style="text-align: center;">Color Name</th>
                        <th style="text-align: center;">Hex Code</th>';

                        $k=1;
                        $listsizes=$this->Productsmodel->getSizesByStyleId($styledata->styleID);
                        $totalSizes=count($listsizes);		
                        foreach($listsizes as $sized) {	
						if($sized['sizeStatus']==1) $checked='checked="checked"'; else $checked="";
                    $strHtml.='<th style="text-align: center;">'.$sized['sizeName'].'<br><input type="checkbox" id="myCheckbox-'.$k.'" data-pid-sku="'.$sized['sku'].'" data-value="'.$sized['sizeName'].'" data-type="s" '.$checked.' class="cStatus siStatus" value="'.$sized['sizeStatus'].'"><label for="myCheckbox-'.$k.'"></label></th>';	
						$k++;	
                        }
                        
            $strHtml.='<th style="text-align: center;">Action</th>
                    </tr>
                </thead>';


 $listColors3=$this->Productsmodel->getColorsByStyleId($styledata->styleID);
				//print_r($colors);
$colorslists=array();
$colorsliste=array();
$i=0;
foreach($listColors3 as $clrs) {	
if($clrs['colorName']=="Black") {
$colorslists[]=$clrs;
} else if($clrs['colorName']=="White") {
$colorslists[]=$clrs;
} else {
$colorsliste[]=$clrs;
}
$i++;
}
if(!empty($colorslists)) $colorslists=$colorslists; else $colorslists=array();
sort($colorsliste);
$listColors=array_merge($colorslists,$colorsliste);

foreach($listColors as $clrs) {	
                
 $strHtml.='<tr>
                    <td><input class="color-name" type="text" value="'.$clrs['colorName'].'" data-psku="'.$clrs['sku'].'" data-type="cname" data-oldv="'.$clrs['colorName'].'"/><span>Updated</span></td>
                    <td><input class="color-code" type="text" value="'.$clrs['color1'].'" data-psku="'.$clrs['sku'].'" data-type="h"/><span>Updated</span></td>';

$sPrices=$this->Productsmodel->getSizePByStyleId($styledata->styleID,$clrs['colorName']);
/*echo "<pre>";
print_r($sPrices);*/

for($j=0;$j<$totalSizes;$j++) {	

if(isset($sPrices[$j]['customerPrice'])) {

$strHtml.='<td>
<input type="text" data-psku="'.$sPrices[$j]['sku'].'" data-type="p"  value="'.(($sPrices[$j]['customerPrice']!=false && $sPrices[$j]['customerPrice']!="0.00")?number_format($sPrices[$j]['customerPrice'],2):'').'" class="numbersOnly tb-price" name="proPrice" '.($sPrices[$j]['qty']==0?'disabled="disabled"':'').'/><span>Updated</span>
<br><input type="text" placeholder="Qty" data-psku="'.$sPrices[$j]['sku'].'" data-type="q" class="tb-qty numbersOnly" value="'.($sPrices[$j]['qty']==0?'0':$sPrices[$j]['qty']).'"><span>Updated</span>
</td>';
} else {
$strHtml.='<td>NA</td>';
}
}
if($clrs['colorStatus']==1) $checked1='checked="checked"'; else $checked1="";
$strHtml.='<td><input type="checkbox" id="c'.$i.'" class="cStatus coStatus" data-value="'.$clrs['colorName'].'"  data-pid-sku="'.$clrs['sku'].'" data-type="c" '.$checked1.' value="'.$clrs['colorStatus'].'"><label for="c'.$i.'"></label></td>
</tr>';

$i++;
 }
               
$strHtml.='</table>
        </div>';


	
$strHtml.='	
	<script>var formID="editstylesid";</script>
	<script src="'.base_url('public/admin/js/products/jquery.colorbox.js').'"></script> 
	<link href="'.base_url('public/admin/css/colorbox.css').'" rel="stylesheet">

				<script>
				function readURL(input,aa) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(".pimg").attr("src", e.target.result);
			$("#imgc"+aa).attr("src", e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
$(function(){
	$("#sel-col").click(function(){
		$("#target-col").slideToggle();
	});	
});	

$("document").ready(function(){
	$(document).mouseup(function(e) {
		var container = $("#target-col");
		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.hide();
		}
	});
	
	$(".mpreview").colorbox({iframe:true, width:"100%", height:"100%"});							 
							 
$(".numbersOnly").keydown(function (e) {
	// Allow: backspace, delete, tab, escape, enter and .
	if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
		 // Allow: Ctrl+A, Command+A
		(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
		 // Allow: home, end, left, right, down, up
		(e.keyCode >= 35 && e.keyCode <= 40)) {
			
			 return;
	}
	// Ensure that it is a number and stop the keypress
	if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		e.preventDefault();
	}
});

$("#cimage1").change(function(){
    readURL(this,1);
});
$("#cimage2").change(function(){
    readURL(this,2);
});
$("#cimage3").change(function(){
    readURL(this,3);
});
$("#cimage4").change(function(){
    readURL(this,4);
});

$(".imgsidebar").children("li").children("a").children("img").click(function(){
	var selImg1=$(this).attr("src");
	if($(".pimg").parent().hasClass("product-item-image")){
		$(this).parent().parent().parent().parent().parent().children().children(".pimg").attr("src", selImg1);
	}else{
		$(".pimg").parent().removeClass("product-item-image")
		$(".pimg").parent().addClass("product-item-image active")
	}
});


$("#isbulkdiscount").on("click", function () {
    $(this).val(this.checked ? 1 : 0);
});
$("#iscoupon").on("click", function () {
    $(this).val(this.checked ? 1 : 0);
});
$("#issync").on("click", function () {
    $(this).val(this.checked ? 1 : 0);
});

$(".cStatus").on("click", function () {
	$(this).val(this.checked ? 1 : 0);
	var type=$(this).attr("data-type");
	if(type=="c"){
		var pname = $(this).closest("tr").find("td:eq(0) input").val();  
	} else {
		var pname=$(this).attr("data-value");
	}  
	var procolorname=$(this).closest("tr").find("td:eq(0) input").val();
	var sku=$(this).attr("data-pid-sku");
	var status=$(this).val();
	var styleID = $("#pStyleId").val();
	var base_url=$("#currentparent").val();
 	$.ajax({
		url: base_url+"AjaxCallUpdateProduct",
		method: "post",
		data: "sku="+sku+"&styleID="+styleID+"&status="+status+"&type="+type+"&pname="+pname+"&procolorname="+procolorname,
		dataType: "json",
		success: function(msg){
			console.log(msg);
			//setTimeout(function(){ cur.next(".table-bx table tr td span").show();}, 3000);
			//$(".product-image-detail").html(msg.data);
		}
	});
});

$(".number").keypress(function(event) {
  if ((event.which != 46 || $(this).val().indexOf(".") != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
});

$(".tb-price,.tb-qty, .color-code, .color-name").blur(function(){
	var procolorname=$(this).closest("tr").find("td:eq(0) input").val();						 
    var sku=$(this).attr("data-psku");
	var pname=$(this).val();
	var type=$(this).attr("data-type");
	var oldcol=$(this).attr("data-oldv");
	var styleID = $("#pStyleId").val();
	var cur = $(this);
	$(this).next(".table-bx table tr td span").show();
	$(this).next(".table-bx table tr td span").html("Updating");

	var base_url=$("#currentparent").val();
 	$.ajax({
		url: base_url+"AjaxCallUpdateProduct",
		method: "post",
		data: "sku="+sku+"&styleID="+styleID+"&type="+type+"&pname="+pname+"&oldcolorname="+oldcol+"&procolorname="+procolorname,
		dataType: "json",
		success: function(msg){
			if(msg.result=="0") {
				cur.next(".table-bx table tr td span").html("Updated");
				cur.next(".table-bx table tr td span").css({"display": "block"});
				setTimeout(function(){ cur.next(".table-bx table tr td span").css({"display": "none"});}, 1000);
			}
			//$(".scriptid").html(msg.dataScript);
			//$(".product-image-detail").html(msg.data);
		}
	});
}); 

$("#pitemsisactive").on("click", function () {
    $(this).val(this.checked ? 0 : 1);
});
$("#relateditems").on("click", function () {
    $(this).val(this.checked ? 1 : 0);
});
$("#customerviewed").on("click", function () {
    $(this).val(this.checked ? 1 : 0);
});
$("#hideim").on("click", function () { 
	var st=$("#styleImageStatus").val();	
	$("#styleImageStatus").val(0);
	$("#showim").show();
	$("#hideim").hide();
});
$("#showim").on("click", function () {
	var st=$("#styleImageStatus").val();
	$("#styleImageStatus").val(1);
	$("#showim").hide();
	$("#hideim").show();
});

$(".col1").click(function(event){
	event.preventDefault();
	var sku=$(this).attr("data-sku");
	var myclrcode = $(this).attr("data-clrcode");
	var myclrname = $(this).attr("data-clrname");
	$("#bgclr").css("background-color",myclrcode);
	$("#clrname").html(myclrname);
	var styleid=$("#styleID").val();

	$("#productcolornames").val(myclrname);
	var base_url=$("#currentparent").val();
 	$.ajax({
		url: base_url+"AjaxCallOtherColor",
		method: "post",
		data: "sku="+sku+"&styleid="+styleid,
		dataType: "json",
		success: function(msg){
			$(".scriptid").html(msg.dataScript);
			$(".product-image-detail").html(msg.data);
		}
	});
});	





$("#btnSubmit").click(function(){
 $("#loadingmessage").show();
var dataf = new FormData();
var files = $("#cimage1").get(0).files;

var cfiles1 = $("#cimage2").get(0).files;
var cfiles2 = $("#cimage3").get(0).files;
var cfiles3 = $("#cimage4").get(0).files;
if (files.length > 0) {
dataf.append("cimage1", files[0]);
} else {
dataf.append("cimage1", "");
}

if (cfiles1.length > 0) {
dataf.append("cimage2", cfiles1[0]);
} else {
dataf.append("cimage2", "");
}

if (cfiles2.length > 0) {
dataf.append("cimage3", cfiles2[0]);
} else {
dataf.append("cimage3", "");
}

if (cfiles3.length > 0) {
dataf.append("cimage4", cfiles3[0]);
} else {
dataf.append("cimage4", "");
}

dataf.append("customTitle",$("#customTitle").val());
dataf.append("pPrice", $("#pPrice").val());
dataf.append("sku", $("#sku").val());
dataf.append("styleID", $("#styleID").val());
dataf.append("styleImageStatus", $("#styleImageStatus").val());
dataf.append("description", $("#description").val());
dataf.append("pitemsisactive", $("#pitemsisactive").val());
dataf.append("customseo", $("#customseo").val());
dataf.append("productcolornames", $("#productcolornames").val());

dataf.append("relateditems", $("#relateditems").val());
dataf.append("isbulkdiscount", $("#isbulkdiscount").val());
dataf.append("iscoupon", $("#iscoupon").val());
dataf.append("issync", $("#issync").val());
dataf.append("synctime", $("#synctime").val());

dataf.append("customerviewed", $("#customerviewed").val());
dataf.append("pagemetatitle", $("#pagemetatitle").val());
dataf.append("pagemetakeywords", $("#pagemetakeywords").val());
dataf.append("pagemetadescription", $("#pagemetadescription").val());
var base_url=$("#currentparent").val();

jQuery.ajax({
url : base_url+"savestyles", 
type : "POST",              // Assuming creation of an entity
contentType : false,        // To force multipart/form-data
data : dataf,
processData : false,
success : function(data) {
console.log(data);
//var data=$.parseJSON(data);
$("#editstylesid #customjsmessage1").css("display","block");
		$("#editstylesid #customjsmessage1").children(".successdiv").children("label").text(data);
			var hidetime=3000;
			setTimeout(function() {
				$("#editstylesid #customjsmessage1").children(".successdiv").children("label").text("");
				$("#editstylesid #customjsmessage1").css("display","none");
			}, hidetime);

 $("#loadingmessage").hide();
      }
    });

	});


	$("#btnPSubmit1").click(function(){
	$("#loadingmessage").show();
	var base_url=$("#currentparent").val();
		$.post(base_url+"savepricecodes", $("#frmPrice").serialize(), function(data) {
		var data=$.parseJSON(data);
		if(data.result=="0") {
		$("#loadingmessage").hide();
		$("#frmPrice #customjsmessage").css("display","block");
		$("#frmPrice #customjsmessage").children(".successdiv").children("label").text(data.msg);
			var hidetime=3000;
			setTimeout(function() {
				$("#frmPrice #customjsmessage").children(".successdiv").children("label").text("");
				$("#frmPrice #customjsmessage").css("display","none");
			}, hidetime);

		} 
	});	
	});	


$("#updateBBtn").click(function(){

	$("#loadingmessage").show();
	var base_url=$("#currentparent").val();
		$.post(base_url+"updatebulkprices", $("#frmBPrice").serialize(), function(data) {
		var data=$.parseJSON(data);

		if(data.result=="1") {
		$("#loadingmessage").hide();
		$("#frmBPrice #customjsmessage").css("display","block");
		$("#frmBPrice #customjsmessage").children(".successdiv").children("label").text(data.msg);
			var hidetime=3000;
			setTimeout(function() {
				$("#frmBPrice #customjsmessage").children(".successdiv").children("label").text("");
				$("#frmBPrice #customjsmessage").css("display","none");
			}, hidetime);

	$("#cgetdataid li.selected .ob-list-info .ob-list-row-1 .ob-list-info-number a.viewstyleid").trigger("click");
		} 
	});	
	});	


$("#bulkidp").click(function(){

	$("#loadingmessage").show();
	var base_url=$("#currentparent").val();
		$.post(base_url+"Sbulkcachepages",{styleID:'.$styledata->styleID.'}, function(data) {
		//var data=$.parseJSON(data);

		if(data=="1") {
		$("#loadingmessage").hide();
			var hidetime=4000;
			$("#customjsmessage6").show();
			setTimeout(function() {
				$("#customjsmessage6").hide();
			}, hidetime);


		} 
	});	
	});	
	
	
});

				
  


				</script>
				';
$response=array();
$response['msg']=$strHtml;
echo json_encode($response);
	exit;
	}
	}



	
}
/* End of file products.php */
/* Location: ./application/controller/admin/products.php */