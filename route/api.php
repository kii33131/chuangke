<?php
use \think\facade\Route;
//Token
Route::rule('api/token/user', 'api/Token/getToken')->allowCrossDomain();
//user
    Route::rule('api/user/send_msg', 'api/User/sendMsg')->allowCrossDomain();
Route::rule('api/user/login', 'api/User/login')->allowCrossDomain();

Route::rule('api/user/bank', 'api/User/bank')->allowCrossDomain();
Route::rule('api/user/examine', 'api/User/examine')->allowCrossDomain();
Route::rule('api/user/receiving_task', 'api/User/receivingTask')->allowCrossDomain();
Route::rule('api/user/dealing_invitations', 'api/user/dealingInvitations')->allowCrossDomain();
Route::rule('api/user/confirmation_receipts', 'api/user/confirmationReceipts')->allowCrossDomain();
//businessInvoiceList

Route::rule('api/user/info', 'api/user/info')->allowCrossDomain();
Route::rule('api/user/info2', 'api/user/info2')->allowCrossDomain();

Route::rule('api/user/create_bank', 'api/user/bank')->allowCrossDomain();
Route::rule('api/user/bank_detail', 'api/user/bankDetail')->allowCrossDomain();
Route::rule('api/user/save_users', 'api/user/saveUsers')->allowCrossDomain();
Route::rule('api/user/edit_bank', 'api/user/editBank')->allowCrossDomain();
Route::rule('api/user/redirects', 'api/user/redirects')->allowCrossDomain();
Route::rule('api/user/redirecty', 'api/user/redirecty')->allowCrossDomain();
Route::rule('api/user/submit_content', 'api/user/submitContent')->allowCrossDomain();
Route::rule('api/user/content_list', 'api/user/contentlist')->allowCrossDomain();


//user/submit_content
//edit_userinfo
Route::rule('api/channel/index', 'api/Channel/index')->allowCrossDomain();

Route::rule('api/member/detail', 'api/Member/detail')->allowCrossDomain();
Route::rule('api/task/examine', 'api/Task/examine')->allowCrossDomain();
Route::rule('api/district/address_List', 'api/District/addressList')->allowCrossDomain();

//home
Route::rule('api/task/release', 'api/Task/release')->allowCrossDomain();
Route::rule('api/task/index', 'api/Task/index')->allowCrossDomain();
Route::rule('api/task/details', 'api/Task/details')->allowCrossDomain();
Route::rule('api/task/member_list', 'api/Task/memberList')->allowCrossDomain();
Route::rule('api/task/appoint_member_list', 'api/Task/appointMemberList')->allowCrossDomain();
Route::rule('api/task/lower_shelf', 'api/Task/lowerShelf')->allowCrossDomain();
Route::rule('api/task/resubmit', 'api/Task/resubmit')->allowCrossDomain();

//coupon
Route::rule('api/home/index', 'api/Home/index')->allowCrossDomain();
Route::rule('api/membertask/management_list', 'api/MemberTask/taskList')->allowCrossDomain();

Route::rule('api/memberschedule/submit_schedule', 'api/MemberSchedule/submitSchedule')->allowCrossDomain();
Route::rule('api/memberschedule/schedule_list', 'api/MemberSchedule/scheduleList')->allowCrossDomain();
Route::rule('api/memberschedule/drop', 'api/MemberSchedule/drop')->allowCrossDomain();
Route::rule('api/achievement/submitted', 'api/Achievement/submitted')->allowCrossDomain();
Route::rule('api/achievement/achievement_list', 'api/Achievement/achievementList')->allowCrossDomain();
Route::rule('api/achievement/achievement_list2', 'api/Achievement/achievementList2')->allowCrossDomain();
Route::rule('api/achievement/examine_list', 'api/Achievement/examineList')->allowCrossDomain();
Route::rule('api/achievement/detail', 'api/Achievement/detail')->allowCrossDomain();
Route::rule('api/achievement/examine', 'api/Achievement/examine')->allowCrossDomain();
    Route::rule('api/achievement/settlement_detail', 'api/Achievement/settlementDetail')->allowCrossDomain();
Route::rule('api/achievement/settlement_submission', 'api/Achievement/settlementSubmission')->allowCrossDomain();
Route::rule('api/achievement/management_list', 'api/Achievement/managementList')->allowCrossDomain();
Route::rule('api/achievement/pay_page', 'api/Achievement/payPage')->allowCrossDomain();
Route::rule('api/achievement/dopay', 'api/Achievement/dopay')->allowCrossDomain();
Route::rule('api/achievement/bank', 'api/Achievement/bank')->allowCrossDomain();
Route::rule('api/businessmember/member_list', 'api/BusinessMember/memberList')->allowCrossDomain();
Route::rule('api/achievement/user_confirmachievement', 'api/Achievement/userConfirmAchievement')->allowCrossDomain();
Route::rule('api/achievement/user_refuseachievement', 'api/Achievement/userRefuseAchievement')->allowCrossDomain();
Route::rule('api/achievement/businessinvoicelist', 'api/Achievement/businessInvoiceList')->allowCrossDomain();
Route::rule('api/achievement/memberinvoicelist', 'api/Achievement/memberInvoiceList')->allowCrossDomain();
Route::rule('api/achievement/billdetails', 'api/Achievement/billDetails')->allowCrossDomain();

Route::rule('api/businessmember/delete', 'api/BusinessMember/delete')->allowCrossDomain();

//memberInvoiceList

//userRefuseAchievement
Route::rule('api/business/detail', 'api/Business/detail')->allowCrossDomain();
Route::rule('api/business/update_business', 'api/Business/updateBusiness')->allowCrossDomain();
Route::rule('api/business/edit_business', 'api/Business/editBusiness')->allowCrossDomain();
Route::rule('api/user/untying', 'api/User/untying')->allowCrossDomain();
//untying
//Achievement
//curl http://www.adminchong.com/api/upload/img -F "image=@profile.jpg"
//upload
Route::rule('api/upload/img', 'api/Upload/img')->allowCrossDomain();
Route::rule('api/upload/imgs', 'api/Upload/imgs')->allowCrossDomain();
Route::rule('api/upload/imgstest', 'api/Upload/imgstest')->allowCrossDomain();
Route::rule('api/district/address', 'api/District/address')->allowCrossDomain();

Route::rule('api/district/addressinfo', 'api/District/addressInfo')->allowCrossDomain();

Route::rule('api/subject/index', 'api/Subject/index')->allowCrossDomain();

Route::rule('api/membertask/contrac_tist', 'api/MemberTask/contractList')->allowCrossDomain();

Route::rule('api/membertask/business_contract_list', 'api/MemberTask/businessContractList')->allowCrossDomain();

//redirect

//contractList

//deletePackage





