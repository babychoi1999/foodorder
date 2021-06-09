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
use App\Cart;
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
                        return Redirect::back()->with('danger', 'Tài khoản của bạn đã bị khóa bởi admin');
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
                        return Redirect::back()->with('danger', 'Đã có lỗi xảy ra trong quá trình gửi email.Hãy thử lại...');
                    }
                    return Redirect::to('/email-verify')->with('success', "Email đã được gửi tới địa chỉ email của bạn");
                }
            } else {
                return Redirect::back()->with('danger', 'Password is incorrect');
            }
        } else {
            return Redirect::back()->with('danger', 'Email không chính xác');
        }        
    }

    public function register(Request $request)
    {
        if (Session::get('facebook_id') OR Session::get('google_id')) {
            $validation = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required',
                'mobile' => 'required',
                'accept' =>'accepted'
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

                            $title='Email Verification Code';
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
                            return Redirect::to('/email-verify')->with('success', 'Email đã được gửi tới địa chỉ email của bạn');  
                        }catch(\Swift_TransportException $e){
                            $response = $e->getMessage() ;
                            return Redirect::back()->with('danger', 'Đã có lỗi xảy ra trong quá trình gửi email.Hãy thử lại...');
                        }  
                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        return Redirect::back()->with('danger', 'Đã có lỗi xảy ra trong quá trình gửi email.Hãy thử lại...');
                    }
                } else {
                    return redirect()->back()->with('danger', 'Mã giới thiệu không đúng');
                }
            }
            return Redirect::back()->withErrors(['msg', 'Đã có lỗi xảy ra']);
        } else {
            $validation = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|unique:users',
                'mobile' => 'required|unique:users',
                'password' => 'required|confirmed',
                'accept' =>'accepted'
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
                        $title='Email Verification Code';
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
                        return Redirect::to('/email-verify')->with('success', 'Email đã được gửi tới địa chỉ email của bạn');  
                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        return Redirect::back()->with('danger', 'Đã có lỗi xảy ra trong quá trình gửi email. Hãy thử lại...');
                    }
                } else {
                    return redirect()->back()->with('danger', 'Mã giới thiệu không chính xác');
                }
            }
            return redirect()->back()->with('danger', 'Đã có lỗi xảy ra');
        }
    }

    public function changePassword(request $request)
    {
        $validation = \Validator::make($request->all(), [
            'oldpassword'=>'required|min:6',
            'newpassword'=>'required|min:6',
            'confirmpassword'=>'required_with:newpassword|same:newpassword|min:6',
        ],[
            'oldpassword.required'=>'Bạn chưa nhập mật khẩu cũ',
            'newpassword.required'=>'Bạn chưa nhập mật khẩu mới',
            'confirmpassword.required'=>'Bạn chưa xác nhận mật khẩu mới'
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
            $error_array[]='Mật khẩu cũ và mới phải khác nhau';
        }
        else
        {
            $login=User::where('id','=',Session::get('id'))->first();

            if(\Hash::check($request->oldpassword,$login->password)){
                $data['password'] = Hash::make($request->newpassword);
                User::where('id', Session::get('id'))->update($data);
                Session::flash('message', '<div class="alert alert-success"><strong>Thành công!</strong> Đã thay đổi mật khẩu.!! </div>');
            }else{
                $error_array[]="Mật khẩu cũ không đúng.";
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
            'user_id.unique'=>'Bạn đã đánh giá rồi',
            'ratting.required'=>'Bạn chưa đánh giá',
            'comment.required'=>'Bạn chưa để lại bình luận'
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
            Session::flash('message', '<div class="alert alert-success"><strong>Thành công!</strong> Nhận xét của bạn đã được gửi tới chúng tôi.!! </div>');
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
                return Redirect::back()->with('danger', 'Email không tồn tại');
            } else {
                if ($checklogin->google_id != "" OR $checklogin->facebook_id != "") {
                    return Redirect::back()->with('danger', 'Tài khoản này đã đăng ký bằng email hoặc facebook');
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
                        return Redirect::back()->with('success', 'Mật khẩu mới đã được gửi tới email của bạn');
                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        // return Redirect::back()->with('danger', $response);
                        return Redirect::back()->with('danger', 'Đã có lỗi xảy ra trong quá trình gửi email. Hãy thử lại...');
                    }
                }
            }
        }
        return Redirect::back()->with('danger', 'Đã có lỗi xảy ra'); 
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
                    return Redirect::back()->with('danger', 'Mã OTP không đúng');
                }  
            } else {
                return Redirect::back()->with('danger', 'Địa chỉ Email không đúng');
            }            
        }
        return Redirect::back()->with('danger', 'Đã có lỗi xảy ra'); 
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
                return Redirect::back()->with('danger', 'Đã có lỗi xảy ra trong quá trình gửi email. Hãy thử lại...');
            }

            if (env('Environment') == 'sendbox') {
                session ( [
                    'otp' => $otp,
                ] );
            }

            return Redirect::to('/email-verify')->with('success', "Email đã được gửi tới địa chỉ email của bạn");

        } else {
            return Redirect::back()->with('danger', 'Địa chỉ email không đúng');
        }  
    }

    public function wallet(Request $request)
    {

        $walletamount=User::select('wallet')->where('id',Session::get('id'))->first();

        $transaction_data=Transaction::select('order_number','transaction_type','wallet',DB::raw('DATE_FORMAT(created_at, "%d %M %Y") as date'),'username','order_id')->where('user_id',Session::get('id'))->orderBy('id', 'DESC')->paginate(6);

        $getabout = About::where('id','=','1')->first();

        $getdata=User::select('currency')->where('type','1')->first();
        return view('front.wallet', compact('getabout','transaction_data','walletamount','getdata'));

    }

    public function logout() {
        Session::flush ();
        Auth::logout ();
        return Redirect::to('/');
    }
}
