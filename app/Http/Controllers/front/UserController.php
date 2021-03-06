<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use Auth;
use App\User;
use App\Ratting;
use App\About;
use App\Transaction;
use App\Pincode;
use App\Cart;
use App\Address;
use App\Payment;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $getabout = About::where('id','=','1')->first();
        return view('front.login', compact('getabout'));
    }

    public function signup() {
        $getabout = About::where('id','=','1')->first();
        return view('front.signup', compact('getabout'));
    }


    public function login(Request $request)
    {
        $login=User::where('email',$request['email'])->where('type','=','2')->first();

        $getdata=User::select('referral_amount')->where('type','1')->first();
        
        if(!empty($login)) {
        
            if(Hash::check($request->get('password'),$login->password)) {   
                if($login->is_verified == '1') {
                    if($login->is_available == '1') {
                        // Check item in Cart
                        $cart=Cart::where('user_id',$login->id)->count();

                        session ( [ 
                            'id' => $login->id, 
                            'name' => $login->name,
                            'email' => $login->email,
                            'profile_image' => $login->profile_image,
                            'referral_code' => $login->referral_code,
                            'referral_amount' => $getdata->referral_amount,
                            'cart' => $cart,
                        ] );

                        return Redirect::to('/');
                    } else {
                        return Redirect::back()->with('danger', 'T??i kho???n c???a b???n ???? b??? kh??a b???i admin');
                    }
                } else {

                    $otp = rand ( 100000 , 999999 );
                    try{

                        $title='Email Verification Code';
                        $email=$request->email;
                        $data=['title'=>$title,'email'=>$email,'otp'=>$otp];

                        Mail::send('Email.emailverification',$data,function($message)use($data){
                            $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                            $message->to($data['email']);
                        } );

                        if (env('Environment') == 'sendbox') {
                            session ( [
                                'email' => $login->email,
                                'password' => $login->password,
                                'otp' => $otp,
                            ] );
                        } else {
                            session ( [
                                'email' => $login->email,
                                'password' => $login->password,
                            ] );
                        }

                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        return Redirect::back()->with('danger', '???? c?? l???i x???y ra trong qu?? tr??nh g???i email.H??y th??? l???i...');
                    }
                    return Redirect::to('/email-verify')->with('success', "Email ???? ???????c g???i t???i ?????a ch??? email c???a b???n");
                }
            } else {
                return Redirect::back()->with('danger', 'M???t kh???u kh??ng ch??nh x??c');
            }
        } else {
            return Redirect::back()->with('danger', 'Email kh??ng ch??nh x??c');
        }        
    }

    public function register(Request $request)
    {
        if (Session::get('facebook_id') OR Session::get('google_id')) {
            $validation = Validator::make($request->all(),$rules = [
                'name' => 'required',
                'email' => 'required',
                'mobile' => 'required',
                'accept' =>'accepted'
            ],$messages = [
                'name.required'=>'B???n ch??a nh???p h??? t??n',
                'email.required'=>'B???n ch??a nh???p email',
                'mobile.required'=>'B???n ch??a nh???p s??? ??i???n tho???i'
            ]);
            if ($validation->fails())
            {
                return Redirect::back()->withErrors($validation, 'login')->withInput();
            }
            else
            {
                $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
                $referral_code = substr(str_shuffle($str_result), 0, 10); 
                $otp = rand ( 100000 , 999999 );

                $checkreferral=User::select('id','name','referral_code','wallet')->where('referral_code',$request['referral_code'])->first();

                if (@$checkreferral->referral_code == $request['referral_code']) {

                    $users=User::where('email',$request->email)->get()->first();
                    try{
                        try{
                            $otp = rand ( 100000 , 999999 );

                            $title='M?? x??c th???c email';
                            $email=$request->email;
                            $data=['title'=>$title,'email'=>$email,'otp'=>$otp];

                            Mail::send('Email.emailverification',$data,function($message)use($data){
                                $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                                $message->to($data['email']);
                            } );
                            
                            User::where('email', $request->email)->update(['otp'=>$otp,'mobile'=>$request->mobile,'referral_code'=>$referral_code]);

                            if ($request['referral_code'] != "") {
                                $getdata=User::select('referral_amount')->where('type','1')->get()->first();

                                $wallet = $checkreferral->wallet + $getdata->referral_amount;

                                if ($wallet) {
                                    $UpdateWalletDetails = User::where('id', $checkreferral->id)
                                    ->update(['wallet' => $wallet]);

                                    $from_Wallet = new Transaction;
                                    $from_Wallet->user_id = $checkreferral->id;
                                    $from_Wallet->order_id = null;
                                    $from_Wallet->order_number = null;
                                    $from_Wallet->wallet = $getdata->referral_amount;
                                    $from_Wallet->payment_id = null;
                                    $from_Wallet->order_type = '0';
                                    $from_Wallet->transaction_type = '3';
                                    $from_Wallet->username = $request->name;
                                    $from_Wallet->save();
                                }

                                if ($getdata->referral_amount) {
                                    $UpdateWallet = User::where('id', $users->id)
                                    ->update(['wallet' => $getdata->referral_amount]);

                                    $to_Wallet = new Transaction;
                                    $to_Wallet->user_id = $users->id;
                                    $to_Wallet->order_id = null;
                                    $to_Wallet->order_number = null;
                                    $to_Wallet->wallet = $getdata->referral_amount;
                                    $to_Wallet->payment_id = null;
                                    $to_Wallet->order_type = '0';
                                    $to_Wallet->transaction_type = '3';
                                    $to_Wallet->username = $checkreferral->name;
                                    $to_Wallet->save();
                                }
                            }

                            if (env('Environment') == 'sendbox') {
                                session ( [
                                    'email' => $request->email,
                                    'otp' => $otp,
                                ] );
                            } else {
                                session ( [
                                    'email' => $request->email,
                                ] );
                            }
                            return Redirect::to('/email-verify')->with('success', 'Email ???? ???????c g???i t???i ?????a ch??? email c???a b???n');  
                        }catch(\Swift_TransportException $e){
                            $response = $e->getMessage() ;
                            return Redirect::back()->with('danger', '???? c?? l???i x???y ra trong qu?? tr??nh g???i email.H??y th??? l???i...');
                        }  
                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        return Redirect::back()->with('danger', '???? c?? l???i x???y ra trong qu?? tr??nh g???i email.H??y th??? l???i...');
                    }
                } else {
                    return redirect()->back()->with('danger', 'M?? gi???i thi???u kh??ng ????ng');
                }
            }
            return Redirect::back()->withErrors(['msg', '???? c?? l???i x???y ra']);
        } else {
            $validation = Validator::make($request->all(),$rules = [
                'name' => 'required',
                'email' => 'required|unique:users',
                'mobile' => 'required|unique:users',
                'password' => 'required|confirmed',
                'accept' =>'accepted'
            ],$messages = [
                'name.required'=>'B???n ch??a nh???p h??? t??n',
                'email.required'=>'B???n ch??a nh???p email',
                'email.unique'=>'Email n??y ???? ???????c ????ng k??',
                'mobile.required'=>'B???n ch??a nh???p s??? ??i???n tho???i',
                'mobile.unique'=>'S??? ??i???n tho???i ???? ???????c ????ng k??',
                'password.required'=>'B???n ch??a nh???p m???t kh???u',
                'password.confirmed'=>'X??c nh???n m???t kh???u kh??ng ch??nh x??c'
            ]);
            if ($validation->fails())
            {
                return Redirect::back()->withErrors($validation, 'login')->withInput();
            }
            else
            {
                $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
                $referral_code = substr(str_shuffle($str_result), 0, 10); 
                $otp = rand ( 100000 , 999999 );

                $checkreferral=User::select('id','name','referral_code','wallet')->where('referral_code',$request['referral_code'])->first();

                if (@$checkreferral->referral_code == $request['referral_code']) {

                    try{
                        $title='M?? x??c th???c email';
                        $email=$request->email;
                        $data=['title'=>$title,'email'=>$email,'otp'=>$otp];

                        Mail::send('Email.emailverification',$data,function($message)use($data){
                            $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                            $message->to($data['email']);
                        } );

                        $user = new User;
                        $user->name =$request->name;
                        $user->email =$request->email;
                        $user->mobile =$request->mobile;
                        $user->profile_image ='unknown.png';
                        $user->login_type ='email';
                        $user->otp=$otp;
                        $user->type ='2';
                        $user->referral_code=$referral_code;
                        $user->password =Hash::make($request->password);
                        $user->save();

                        if ($request['referral_code'] != "") {
                            $getdata=User::select('referral_amount')->where('type','1')->get()->first();

                            $wallet = $checkreferral->wallet + $getdata->referral_amount;

                            if ($wallet) {
                                $UpdateWalletDetails = User::where('id', $checkreferral->id)
                                ->update(['wallet' => $wallet]);

                                $from_Wallet = new Transaction;
                                $from_Wallet->user_id = $checkreferral->id;
                                $from_Wallet->order_id = null;
                                $from_Wallet->order_number = null;
                                $from_Wallet->wallet = $getdata->referral_amount;
                                $from_Wallet->payment_id = null;
                                $from_Wallet->order_type = '0';
                                $from_Wallet->transaction_type = '3';
                                $from_Wallet->username = $user->name;
                                $from_Wallet->save();
                            }

                            if ($getdata->referral_amount) {
                                $UpdateWallet = User::where('id', $user->id)
                                ->update(['wallet' => $getdata->referral_amount]);

                                $to_Wallet = new Transaction;
                                $to_Wallet->user_id = $user->id;
                                $to_Wallet->order_id = null;
                                $to_Wallet->order_number = null;
                                $to_Wallet->wallet = $getdata->referral_amount;
                                $to_Wallet->payment_id = null;
                                $to_Wallet->order_type = '0';
                                $to_Wallet->transaction_type = '3';
                                $to_Wallet->username = $checkreferral->name;
                                $to_Wallet->save();
                            }
                        }

                        if (env('Environment') == 'sendbox') {
                            session ( [
                                'email' => $request->email,
                                'otp' => $otp,
                            ] );
                        } else {
                            session ( [
                                'email' => $request->email,
                            ] );
                        }
                        return Redirect::to('/email-verify')->with('success', 'Email ???? ???????c g???i t???i ?????a ch??? email c???a b???n');  
                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        return Redirect::back()->with('danger', '???? c?? l???i x???y ra trong qu?? tr??nh g???i email. H??y th??? l???i...');
                    }
                } else {
                    return redirect()->back()->with('danger', 'M?? gi???i thi???u kh??ng ch??nh x??c');
                }
            }
            return redirect()->back()->with('danger', '???? c?? l???i x???y ra');
        }
    }

    public function changePassword(request $request)
    {
        $validation = \Validator::make($request->all(), [
            'oldpassword'=>'required|min:6',
            'newpassword'=>'required|min:6',
            'confirmpassword'=>'required_with:newpassword|same:newpassword|min:6',
        ],[
            'oldpassword.required'=>'B???n ch??a nh???p m???t kh???u c??',
            'newpassword.required'=>'B???n ch??a nh???p m???t kh???u m???i',
            'confirmpassword.required'=>'B???n ch??a x??c nh???n m???t kh???u m???i'
        ]);
         
        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        }
        else if($request->oldpassword==$request->newpassword)
        {
            $error_array[]='M???t kh???u c?? v?? m???i ph???i kh??c nhau';
        }
        else
        {
            $login=User::where('id','=',Session::get('id'))->first();

            if(\Hash::check($request->oldpassword,$login->password)){
                $data['password'] = Hash::make($request->newpassword);
                User::where('id', Session::get('id'))->update($data);
                Session::flash('message', '<div class="alert alert-success"><strong>Th??nh c??ng!</strong> ???? thay ?????i m???t kh???u.!! </div>');
            }else{
                $error_array[]="M???t kh???u c?? kh??ng ????ng.";
            }
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        return json_encode($output);  

    }

    public function addreview(request $request)
    {
        $validation = \Validator::make($request->all(), [
            'user_id' => 'required|unique:ratting',
            'ratting'=>'required',
            'comment'=>'required',
        ],[
            'user_id.unique'=>'B???n ???? ????nh gi?? r???i',
            'ratting.required'=>'B???n ch??a ????nh gi??',
            'comment.required'=>'B???n ch??a ????? l???i b??nh lu???n'
        ]);
         
        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        }
        else
        {
            $user = new Ratting;
            $user->user_id =$request->user_id;
            $user->ratting =$request->ratting;
            $user->comment =$request->comment;
            $user->save();
            Session::flash('message', '<div class="alert alert-success"><strong>Th??nh c??ng!</strong> Nh???n x??t c???a b???n ???? ???????c g???i t???i ch??ng t??i.!! </div>');
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        
        return json_encode($output);  

    }

    public function forgot_password() {
        $getabout = About::where('id','=','1')->first();
        return view('front.forgot-password', compact('getabout'));
    }

    public function forgotpassword(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'email' => 'required'
        ],[
            'email.required'=>'B???n ch??a nh???p email ????? l???y l???i m???t kh???u'
        ]);
        if ($validation->fails())
        {
            return Redirect::back()->withErrors($validation, 'login')->withInput();
        }
        else
        {
            $checklogin=User::where('email',$request->email)->first();
            
            if(empty($checklogin))
            {
                return Redirect::back()->with('danger', 'Email kh??ng t???n t???i');
            } else {
                if ($checklogin->google_id != "" OR $checklogin->facebook_id != "") {
                    return Redirect::back()->with('danger', 'T??i kho???n n??y ???? ????ng k?? b???ng email ho???c facebook');
                } else {
                    try{
                        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 8 ); 
                        $newpassword['password'] = Hash::make($password);
                        $update = User::where('email', $request['email'])->update($newpassword);
                        
                        $title='Password Reset';
                        $email=$checklogin->email;
                        $name=$checklogin->name;
                        $data=['title'=>$title,'email'=>$email,'name'=>$name,'password'=>$password];

                        Mail::send('Email.email',$data,function($message)use($data){
                            $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                            $message->to($data['email']);
                        } );
                        return Redirect::back()->with('success', 'M???t kh???u m???i ???? ???????c g???i t???i email c???a b???n');
                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        // return Redirect::back()->with('danger', $response);
                        return Redirect::back()->with('danger', '???? c?? l???i x???y ra trong qu?? tr??nh g???i email. H??y th??? l???i...');
                    }
                }
            }
        }
        return Redirect::back()->with('danger', '???? c?? l???i x???y ra'); 
    }

    public function email_verify() {
        $getabout = About::where('id','=','1')->first();
        return view('front.email-verify', compact('getabout'));
    }

    public function email_verification(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'email' => 'required',
            'otp' => 'required',
        ],[
            'email.required'=>'B???n ch??a nh???p email',
            'otp.required'=>'B???n ch??a nh???p m?? otp'
        ]);
        if ($validation->fails())
        {
            return Redirect::back()->withErrors($validation, 'email-verify')->withInput();
        }
        else
        {
            $checkuser=User::where('email',$request->email)->first();
            $getdata=User::select('referral_amount')->where('type','1')->first();

            if (!empty($checkuser)) {
                if ($checkuser->otp == $request->otp) {
                    $update=User::where('email',$request['email'])->update(['otp'=>NULL,'is_verified'=>'1']);

                    $cart=Cart::where('user_id',$checkuser->id)->count();
                    session ( [ 
                        'id' => $checkuser->id, 
                        'name' => $checkuser->name,
                        'email' => $checkuser->email,
                        'referral_code' => $checkuser->referral_code,
                        'referral_amount' => $getdata->referral_amount,
                        'profile_image' => 'unknown.png',
                        'cart' => $cart,
                    ] );

                    return Redirect::to('/');

                } else {
                    return Redirect::back()->with('danger', 'M?? OTP kh??ng ????ng');
                }  
            } else {
                return Redirect::back()->with('danger', '?????a ch??? Email kh??ng ????ng');
            }            
        }
        return Redirect::back()->with('danger', '???? c?? l???i x???y ra'); 
    }

    public function resend_email()
    {
        $checkuser=User::where('email',Session::get('email'))->first();

        if (!empty($checkuser)) {
            try{
                $otp = rand ( 100000 , 999999 );

                $update=User::where('email',Session::get('email'))->update(['otp'=>$otp,'is_verified'=>'2']);

                $title='Email Verification Code';
                $email=Session::get('email');
                $data=['title'=>$title,'email'=>$email,'otp'=>$otp];
                Mail::send('Email.emailverification',$data,function($message)use($data){
                    $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                    $message->to($data['email']);
                } );
            }catch(\Swift_TransportException $e){
                $response = $e->getMessage() ;
                // return Redirect::back()->with('danger', $response);
                return Redirect::back()->with('danger', '???? c?? l???i x???y ra trong qu?? tr??nh g???i email. H??y th??? l???i...');
            }

            if (env('Environment') == 'sendbox') {
                session ( [
                    'otp' => $otp,
                ] );
            }

            return Redirect::to('/email-verify')->with('success', "Email ???? ???????c g???i t???i ?????a ch??? email c???a b???n");

        } else {
            return Redirect::back()->with('danger', '?????a ch??? email kh??ng ????ng');
        }  
    }

    public function wallet(Request $request)
    {

        $walletamount=User::select('wallet')->where('id',Session::get('id'))->first();

        $transaction_data=Transaction::select('order_number','transaction_type','wallet',DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") as date'),'username','order_id')->where('user_id',Session::get('id'))->where('payment_id',NULL)->orderBy('id', 'DESC')->paginate(6);

        $getabout = About::where('id','=','1')->first();

        $getdata=User::select('currency')->where('type','1')->first();

        $getpaymentdata=Payment::select('payment_name','environment')->where('is_available','1')->orderBy('id', 'DESC')->get();
        return view('front.wallet', compact('getabout','transaction_data','walletamount','getdata','getpaymentdata'));

    }
    public function paywithvnpay(){
        $payment_content = "N???p ti???n v??o v??";
        $info_recharge = Session::get('info_recharge');
        session(['payment_content'=>$payment_content]);
        $paid_amount = $info_recharge['money'];
        return view('front.vnpay',compact('payment_content','paid_amount'));
    }
    public function recharge(Request $request){
        $username = User::select('name')->where('id',Session::get('id'))->first();
        $rechargeMoney = $request->money;
        session(['info_recharge'=>$request->all()]);
        // $request->session()->put('info_recharge',$username);
        if($rechargeMoney){
            // return redirect()->route('paywithvnpay',['totalMoney'=>$totalMoney]);
            return response()->json(['status'=>1,'message'=>'Th??nh c??ng...'],200);
        }
        else{
            return response()->json(['status'=>0,'message'=>'???? c?? l???i, vui l??ng th??? l???i...'],200);
        }
    }
    public function createPayment(Request $request){
        // $info_recharge = Session::get('info_recharge');
        // // dd($request->toArray());
        // $vnp_TxnRef = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10); //M?? ????n h??ng. Trong th???c t??? Merchant c???n insert ????n h??ng v??o DB v?? g???i m?? n??y sang VNPAY
        // $vnp_OrderInfo = $request->order_desc;
        // $vnp_OrderType = $request->order_type;
        // $vnp_Amount = str_replace(',', '', $info_recharge['money'])  * 100;
        // $vnp_Locale = $request->language;
        // $vnp_BankCode = $request->bank_code;
        // $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        // $inputData = array(
        //     "vnp_Version" => "2.0.0",
        //     "vnp_TmnCode" => env('VNP_TMN_CODE'),
        //     "vnp_Amount" => $vnp_Amount,
        //     "vnp_Command" => "pay",
        //     "vnp_CreateDate" => date('YmdHis'),
        //     "vnp_CurrCode" => "VND",
        //     "vnp_IpAddr" => $vnp_IpAddr,
        //     "vnp_Locale" => $vnp_Locale,
        //     "vnp_OrderInfo" => $vnp_OrderInfo,
        //     "vnp_OrderType" => $vnp_OrderType,
        //     "vnp_ReturnUrl" => route('returnvnpay'),
        //     "vnp_TxnRef" => $vnp_TxnRef,
        // );

        // if (isset($vnp_BankCode) && $vnp_BankCode != "") {
        //     $inputData['vnp_BankCode'] = $vnp_BankCode;
        // }
        // ksort($inputData);
        // $query = "";
        // $i = 0;
        // $hashdata = "";
        // foreach ($inputData as $key => $value) {
        //     if ($i == 1) {
        //         $hashdata .= '&' . $key . "=" . $value;
        //     } else {
        //         $hashdata .= $key . "=" . $value;
        //         $i = 1;
        //     }
        //     $query .= urlencode($key) . "=" . urlencode($value) . '&';
        // }

        // $vnp_Url = env('VNP_URL') . "?" . $query;
        // if (env('VNP_HASH_SECRET')) {
        //    // $vnpSecureHash = md5($vnp_HashSecret . $hashdata);
        //     $vnpSecureHash = hash('sha256', env('VNP_HASH_SECRET') . $hashdata);
        //     $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        // }
        // return redirect($vnp_Url);
    }
    public function vnpayReturn(Request $request){
        // try {
            
        // } catch (Exception $e) {
            
        // }
        if($request->vnp_ResponseCode == '00'){
            $payment_content = Session::get('payment_content');
            $info_recharge = Session::get('info_recharge');
            $vnpayData = $request->all();
            $getuserdata=User::where('id',Session::get('id'))
            ->get()->first();
            $username = $getuserdata->name;

            try {
                $userWallet = User::select('id','name','referral_code','wallet')->where('id',Session::get('id'))->first();
                $wallet = $userWallet->wallet + $info_recharge['money'];
                if ($wallet) {
                    $UpdateWalletDetails = User::where('id', $userWallet->id)
                    ->update(['wallet' => $wallet]);

                    $from_Wallet = new Transaction;
                    $from_Wallet->user_id = $userWallet->id;
                    $from_Wallet->order_id = null;
                    $from_Wallet->order_number = null;
                    $from_Wallet->wallet = $info_recharge['money'];
                    $from_Wallet->payment_id = null;
                    $from_Wallet->order_type = '0';
                    $from_Wallet->transaction_type = '4';
                    $from_Wallet->username = $userWallet->name;
                    $from_Wallet->save();
                }
            } catch (Exception $e) {
                $response = $e->getMessage() ;
                return response()->json(['status'=>0,'message'=>'???? c?? l???i trong x???y ra, h??y th??? l???i...'],200);
            }
            session()->forget('info_recharge');

            session()->forget('payment_content');

            return view('front.vnpayreturn',compact('vnpayData','username','payment_content'));

            
        }
    }
    public function address(Request $request)
    {
        $addressdata=Address::where('user_id',Session::get('id'))->orderBy('id', 'DESC')->paginate(6);

        $getabout = About::where('id','=','1')->first();

        $getdata=User::select('currency')->where('type','1')->first();

        $getpincode = Pincode::get();
        return view('front.address', compact('getabout','addressdata','getdata','getpincode'));
    }

    public function show(Request $request)
    {
        $getaddress = Address::where('id',$request->id)->first();
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Data has been added', 'ResponseData' => $getaddress], 200);
    }

    public function addaddress(Request $request)
    {
        $pincode=Pincode::select('pincode')->where('pincode',$request->pincode)
        ->get()->first();

        if(@$pincode['pincode'] == $request->pincode) {
            try {

                $address = new Address;
                $address->user_id = Session::get('id');
                $address->address_type = $request->address_type;
                $address->address = $request->address;
                $address->lat = $request->lat;
                $address->lang = $request->lang;
                $address->city = $request->city;
                $address->state = $request->state;
                $address->country = $request->country;
                $address->landmark = $request->landmark;
                $address->building = $request->building;
                $address->pincode = $request->pincode;
                $address->save();

                return Redirect::back()->with('success', 'Th??m ?????a ch??? th??nh c??ng');
                
            } catch (Exception $e) {
                $response = $e->getMessage() ;
                return Redirect::back()->with('danger', '???? c?? l???i x???y ra');
            }
        } else {
            return Redirect::back()->with('danger', '?????a ch??? c???a b???n n???m ngo??i khu v???c giao h??ng c???a ch??ng t??i!');
        }
    }

    public function editaddress(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'address_type' => 'required',
            'address' => 'required',
            'landmark' =>'required',
            'building' =>'required',
            'pincode' =>'required'
        ],[
            'address_type.required'=>'B???n ch??a ch???n lo???i ?????a ch???',
            'address.required'=>'B???n ch??a nh???p ?????a ch???',
            'landmark.required'=>'B???n ch??a nh???p ?????a ??i???m',
            'pincode.required'=>'B???n ch??a nh???p m?? v??ng',
            'building.required'=>'B???n ch??a nh???p s??? nh??'
        ]);
        if ($validation->fails())
        {
            return Redirect::back()->withErrors($validation, 'login')->withInput();
        }
        else
        {
            $pincode=Pincode::select('pincode')->where('pincode',$request->pincode)
            ->get()->first();

            if(@$pincode['pincode'] == $request->pincode) {

                try {

                    Address::where('id', $request->id)->update(['address_type'=>$request->address_type,'address'=>$request->address,'lat'=>$request->lat,'lang'=>$request->lang,'city'=>$request->city,'state'=>$request->state,'country'=>$request->country,'landmark'=>$request->landmark,'building'=>$request->building,'pincode'=>$request->pincode]);


                    return Redirect::back()->with('success', 'C???p nh???t ?????a ch??? th??nh c??ng');
                    
                } catch (Exception $e) {
                    $response = $e->getMessage() ;
                    return Redirect::back()->with('danger', '???? c?? l???i x???y ra');
                }
            } else {
                return Redirect::back()->with('danger', '?????a ch??? c???a b???n kh??ng n???m trong khu v???c giao h??ng c???a ch??ng t??i');
            }

        }
        return Redirect::back()->with('danger', '???? c?? l???i x???y ra');
    }

    public function delete(Request $request)
    {
        $address=Address::where('id', $request->id)->where('user_id', $request->user_id)->delete();
        if ($address) {
            return 1;
        } else {
            return 0;
        }
    }

    public function logout() {
        Session::flush ();
        Auth::logout ();
        return Redirect::to('/');
    }
}
