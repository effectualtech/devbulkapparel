<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
$abc="testing";
/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code



/*----------------CONSTANT USER DEFINED START HERE!-----------*/
/*@coder!*/

//define('CDN', 'https://s3.amazonaws.com/shirtchamp/');


define('BASE_PATH','http://bulkapparel.com',true);
define('DOWN_FOR_DEVELOPMENT','Website down for demo.Pleasevisit after some time!',true);


define('BASE_PATH_STYLE_JS','http://bulkapparel.com/public/',true);
define('IMAGE_DISPLAY_FRONT','http://bulkapparel.com/public/front/images/',true);

define('IMAGE_DISPLAY_ADMIN','http://bulkapparel.com/public/admin/images/',true);
define('JSMSGHIDETIME','5000',true);
define('LISTINGPERPAGE_ADMIN','8',true);
define('ADMIN_UPLOAD_PATH','/public/admin/upload/',true);
define('EDI_UPLOAD_PATH','/public/products/images/',true);

define('VIEW_LIST','View Listing',true);

/*@dashboard!*/
define('DASHBOARD_WELCOME','Welcome to admin dashboard!',true);
define('NON_LOGIN_USER','Please login with your login username and login passwrod!',true);

/*@forgot password!*/
define('FORGOT_PWD_P','You login password has been sent on your registered email id.',true);
define('FORGOT_PWD_E','There is some error in iput please check the email id and submit form again!',true);
define('FORGOT_PWD_W','Invalid email id enter. Please check the email id and submit again!',true);

/*@settings!*/
define('SETINGS_TITLE','Settings',true);
define('SETINGS_SUB_TITLE','Edit Settings',true);
define('SETTING_EDIT_E','You have been unsuccessful to update profile settings!',true);
define('SETTING_EDIT_P','You have been successfully update profile settings!',true);

/*@user!*/
define('USR_DEL_P','Seleted user record has been deleted successfully!',true);
define('USR_DEL_E','Seleted user record has been delete unsuccessful!',true);
define('USR_ACTIVATE_P','Seleted user status has been successfully change to activate!',true);
define('USR_ACTIVATE_E','Seleted user status has been change unsuccessful!',true);
define('USR_BLOCK_P','Seleted user status has been successfully change to block!',true);
define('USR_BLOCK_E','Seleted user status has been change unsuccessful to block!',true);
define('USR_UPDATE_P','Selected user account edit successfull!',true);
define('USR_UPDATE_E','Seleted user account edit unsuccessful !',true);
define('USR_MAIL_SENT_P','Mail sent successfully',true);
define('USR_ADD_P','New user creation successfull!',true);
define('USR_ADD_E','New user successfully created and welcome mail sent.',true);

/*@static cms page!*/
define('CMS_PG_TITLE',"Add New",true);
define('CMS_SUB_PG_TITLE',"Add New CMS Content Page",true);
define('CMS_NO_PG','No record found. Please create the page here firstly.',true);
define('CMS_PG_ADD_P','You have been successfully created new content page!',true); 
define('CMS_PG_ADD_E','There is some error during the new cms page add.Please check all posted filed data and submit again.',true);
define('CMS_PG_LIST_TITLE',"CMS Pages Listing",true);
define('CMS_PG_EDIT',"Content Page",true);
define('CMS_SUB_PG_EDIT',"Edit Content Page",true);
define('CMS_PG_ACTIVATE_P','Seleted content page successfull publish!',true);
define('CMS_PG_ACTIVATE_E','Selected content page status publish unsuccessful!',true);
define('CMS_PG_BLOCK_P','Seleted content page successfull un-publish!',true);
define('CMS_PG_BLOCK_E','Selected content page status un-publish unsuccessful!',true);
define('CMS_PG_DEL_P','Seleted content page record has been deleted successfully!',true);
define('CMS_PG_DEL_E','Seleted content page record has been delete unsuccessful!',true);

/*@site offline/online mode!*/
define('SITE_ON_OFF_TITLE',"Website Mode",true);
define('SITE_SUB_ON_OFF_TITLE',"Change Website Mode(Online/Offline)!",true);
define('SITE_UP_P','Website successfuuly put on online mode',true);
define('SITE_DOWN_P','Website successfully put on offline mode',true);
define('SITE_UP_E','There is some error during put website online mode!',true);
define('SITE_DOWN_E','There is some error during website put offline mode!',true);

/*@google analytic code!*/
define('ANALYTIC_TITLE',"Google Analytic Code",true);
define('ANALYTIC_SUB_TITLE',"Manage google analytic code",true);
define('AYALYTIC_ADD_P','Google analytic code successfully updated!',true);
define('AYALYTIC_ADD_E','Google analytic code update un-successfull!',true);

/*@title & meta!*/
define('TM_LISTING_TITLE','Titles & Metas',true);
define('TM_EDIT_TITLE','Title & Meta',true);
define('TM_SUB_EDIT_TITLE','Edit Page Title & Meta',true);
define('TM_ADD_TITLE','Add New',true);
define('TM_ADD_SUB_TITLE','Add New Page Title & Meta',true);
define('TM_ADD_P','Title & Meta has been successfully addedd to the page!',true);
define('TM_ADD_E','There is some error during the Title & Meta add to selected page!',true);
define('TM_EDIT_P','Selected page title & meta updated successfully!',true);
define('TM_EDIT_E','There is some error during the edit page title & meta!',true);
define('TM_DEL_P','Static page title & meta reset successfully!',true);
define('TM_DEL_E','Static page title & meta reset un-successful!',true);

/*@social links!*/
define('SOCIAL_TITLE','Social Link',true);
define('SOCIAL_LISTING','Social Links',true);
define('SOCIAL_ADDSUB_TITLE','Add Social Link',true);
define('SOCIAL_ADD_P','Seo link added successfully!',true);
define('SOCIAL_ADD_E','There is some error during the social link.',true);
define('SOCIAL_ADD_W','Something is going wrong. Either spcial link is not exsist or deleted by admin. Please create social link here!',true);
define('SOCIAL_EDIT_SUB_TITLE','Edit Social Link',true);
define('SOCIAL_EDIT_P','You have been successfully edit the social link',true);
define('SOCIAL_EDIT_E','Social link edit un-successful!',true);
define('SOCIAL_DEL_P','Social link has been deleted successfully!',true);
define('SOCIAL_DEL_E','Social link has been deleted un-successfull!',true);
define('SOCIAL_ACTIVE_P','Social status successfully change to active!',true);
define('SOCIAL_ACTIVE_E','Social status unsuccessful change to active!',true);
define('SOCIAL_INACTIVE_P','Social status change successfully to inactive!',true);
define('SOCIAL_INACTIVE_E','Social status unsuccessful change to inactive!',true);

/*@xml sitemap file upload!*/
define('XML_TITLE','XML Sitemap',true);
define('XML_SUB_TITLE',"Upload XML Sitemap File",true);


/*@Category*/
define('CAT_LISTING_TITLE','Category listing',true);
define('PROD_STYLE_LISTING_TITLE','Products style listing',true);
define('PROD_LISTING_TITLE','Products listing',true);
 

 

/*@@FRONT END CORE*/
define('MEMBER_SIGNUP','New User Registration!',true);
define('MEMBER_LOGIN_SIGNUP','Login / Register',true);
define('MEMBER_TEXT_OVER_FORM',"<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>",true);
define('MEMBER_FORGOT_PWD','Forgot Password',true);
define('LOGIN_FIRST','Please login here!',true);
define('MY_ACCOUNT','My Account',true);    

//Forgot password form
define('PASSWORD_SENT_ON_MAIL','Password sent successfuly o your registed email id.',true);
define('PASSWORD_WROMG_EMAIL_ID','You have been enter and invalid email id. Please check the email ad and submit again!',true);
define('FORGOT_PWDLINK_EXPIRED','Either your password reset link is wrong or expired.You will need to click on Forgot Password once again.',true);

//Choose new password
define('SETUP_PWD','Seteup new password!',true);
define('SELECT_NEWPWD','Choose New Password',true);
define('FORGOT_PWD_RETRIVE_PASS','Forgot password has been successfully updated!',true);
define('FORGOT_PWD_RETRIVE_FAIL','There is errro during forgot password reset. Please try after some time.!',true);

//Front end signup
define('SIGNUP_OK','You have been successfully refistered. A confirmation liunk sent on your registered email id.', true);
define('SIGNUP_FAIL','There is some error during registration, please contact side administrator or try after some time!',true);

//Account activate
define('ACCOUNT_ACTIVATEOK','Your account has been activate successfully!',true);
define('ACCOUNT_ACTIVATEFAIL','Please try after some time or contact site admin for trouble account not activated!',true);
define('ACOOUNTACTIVATIONLINKWRONG','Eiter link is expired or wrong link!',true);



//Edit user account
define('EDIT_ACCOUNT','Edit Account',true);

/*EDI API DETAIL*/
//define('ENDPOINT','https://api.ssactivewear.com/v2/');  
define('ENDPOINT','https://apidev.ssactivewear.com/v2/');
define('API_BASE_PATH','https://www.ssactivewear.com',true);
define('USR','35933',true);
define('PWD','7d13864f-4564-4ae6-88fa-42911ba1b4e6',true);

/* End of file constant.php */
/* Location: ./application/config/constant.php */
