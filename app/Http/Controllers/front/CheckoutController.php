<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Session;
use App\Order;
use App\OrderDetails;
use App\Payment;
use App\User;
use App\Cart;
use App\Pincode;
use App\Promocode;
use App\Transaction;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class CheckoutController extends Controller
{

    /**
     * payment view
     */
    public function index()
    {
        return view('stripe-payment');
    }
    public function paywithvnpay(){
        $payment_content = "Thanh toán hóa đơn";
        session(['payment_content'=>$payment_content]);
        $info_customer = Session::get('info_customer');
        // $order_type = $info_customer['order_type'];
        $paid_amount = $info_customer['order_total'];
        return view('front.vnpay',compact('paid_amount','payment_content'));
    }
    public function vnpayPayment(Request $request){
        $getuserdata=User::where('id',Session::get('id'))
        ->get()->first(); 
        $totalMoney = $request->order_total;
        session(['info_customer'=>$request->all()]);
        if($totalMoney){
            // return redirect()->route('paywithvnpay',['totalMoney'=>$totalMoney]);
            return response()->json(['status'=>1,'message'=>'Đã có lỗi trong quá trình gửi mail, vui lòng thử lại...'],200);
        }
        else{
            return response()->json(['status'=>0,'message'=>'Đã có lỗi trong quá trình gửi mail, vui lòng thử lại...'],200);
        }
        
    }
    public function createPayment(Request $request){
        
        $info_customer = Session::get('info_customer');
        $info_recharge = Session::get('info_recharge');
        // dd($request->toArray());
        $vnp_TxnRef = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = $request->order_desc;
        $vnp_OrderType = $request->order_type;
        if (Session::has('info_recharge')) {
            $vnp_Amount = str_replace(',', '', $info_recharge['money'])  * 100;
        }
        else{
            $vnp_Amount = str_replace(',', '', $info_customer['order_total'])  * 100;
        }
        // $vnp_Amount = str_replace(',', '', $info_customer['order_total'])  * 100;
        // $vnp_Amount = str_replace(',', '', $info_recharge['money'])  * 100;
        $vnp_Locale = $request->language;
        $vnp_BankCode = $request->bank_code;
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        if (Session::has('info_recharge')) {
            $vnp_Returnurl = route('returnvnpay');
           
        }else{
            $vnp_Returnurl = route('vnpay.return');
          
        }  

        $inputData = array(
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => env('VNP_TMN_CODE'),
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType, 
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = env('VNP_URL') . "?" . $query;
        if (env('VNP_HASH_SECRET')) {
           // $vnpSecureHash = md5($vnp_HashSecret . $hashdata);
            $vnpSecureHash = hash('sha256', env('VNP_HASH_SECRET') . $hashdata);
            $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        }
        return redirect($vnp_Url);
    }
    public function vnpayReturn(Request $request){
        if($request->vnp_ResponseCode == '00'){
        $payment_content = Session::get('payment_content');
        $vnpayData = $request->all();
        $info_customer = Session::get('info_customer');
        $getuserdata=User::where('id',Session::get('id'))
        ->get()->first();
        $username = $getuserdata->name;

        if ($info_customer['discount_amount'] == "NaN") {
            $discount_amount = "0.00";
        } else {
            $discount_amount = $info_customer['discount_amount'];
        }    

        try {

            if ($info_customer['order_type'] == "2") {

                $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);

                if ($info_customer['order_type'] == "2") {
                    $delivery_charge = "0.00";
                    $address = "";
                    $lat = "";
                    $lang = "";
                    $building = "";
                    $landmark = "";
                    $postal_code = "";
                    $order_total = $info_customer['order_total']-$info_customer['delivery_charge'];
                } else {
                    $delivery_charge = $info_customer['delivery_charge'];
                    $address = $info_customer['address'];
                    $lat = $info_customer['lat'];
                    $lang = $$info_customer['lang'];
                    $order_total = $$info_customer['order_total'];
                    $building = $info_customer['building'];
                    $landmark = $info_customer['landmark'];
                    $postal_code = $info_customer['postal_code'];
                }

                $order = new Order;
                $order->order_number =$order_number;
                $order->user_id =Session::get('id');
                $order->order_total =$order_total;
                $order->payment_type ='4';
                $order->status ='1';
                $order->address =$address;
                $order->promocode =$info_customer['promocode'];
                $order->discount_amount =$discount_amount;
                $order->discount_pr =$info_customer['discount_pr'];
                $order->tax =$info_customer['tax'];
                $order->tax_amount =$info_customer['tax_amount'];
                $order->delivery_charge =$info_customer['delivery_charge'];
                $order->order_type =$info_customer['order_type'];
                $order->lat =$lat;
                $order->lang =$lang;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$postal_code;
                $order->order_notes =$info_customer['notes'];
                $order->order_from ='web';

                $order->save();

                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',Session::get('id'))
                ->get();
                foreach ($data as $value) {
                    $OrderPro = new OrderDetails;
                    $OrderPro->order_id = $order_id;
                    $OrderPro->user_id = $value['user_id'];
                    $OrderPro->item_id = $value['item_id'];
                    $OrderPro->price = $value['price'];
                    $OrderPro->qty = $value['qty'];
                    $OrderPro->item_notes = $value['item_notes'];
                    $OrderPro->addons_id = $value['addons_id'];
                    $OrderPro->save();
                }
                $cart=Cart::where('user_id', Session::get('id'))->delete();

                $count=Cart::where('user_id',Session::get('id'))->count();


                $vnpay = new Transaction;
                $vnpay->user_id = Session::get('id');
                $vnpay->order_id = $order_id;
                $vnpay->order_number = $order_number;  
                $vnpay->wallet = NULL;
                $vnpay->payment_id = '4';
                $vnpay->order_type = $info_customer['order_type'];
                $vnpay->transaction_type = '2';
                $vnpay->save();

                try{
                    $ordermessage='Đơn hàng của bạn "'.$order_number.'" đang chờ xác nhận';
                    $email=$getuserdata->email;
                    $name=$getuserdata->name;
                    $data=['ordermessage'=>$ordermessage,'email'=>$email,'name'=>$name];

                    Mail::send('Email.orderemail',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject($data['ordermessage']);
                        $message->to($data['email']);
                    } );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>'Đã có lỗi trong quá trình gửi email, hãy thử lại...'],200);
                }
                
                Session::put('cart', $count);

                session()->forget(['offer_amount','offer_code']);

                session()->forget('info_customer');

                session()->forget('payment_content');

                return view('front.vnpayreturn',compact('vnpayData','order_number','username','payment_content'));
                
                // return response()->json(['status'=>1,'message'=>'Đơn hàng của bạn đang chờ xác nhận'],200);
            } else {
                $pincode=Pincode::select('pincode')->where('pincode',$info_customer['postal_code'])
                ->get()->first();

                if(@$pincode['pincode'] == $info_customer['postal_code']) {
                    if(!empty($pincode))
                    {
                        $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);

                        if ($info_customer['order_type'] == "2") {
                            $delivery_charge = "0.00";
                            $address = "";
                            $lat = "";
                            $lang = "";
                            $building = "";
                            $landmark = "";
                            $postal_code = "";
                            $order_total = $info_customer['order_total']-$info_customer['delivery_charge'];
                        } else {
                            $delivery_charge = $info_customer['delivery_charge'];
                            $address = $info_customer['address'];
                            $lat = $info_customer['lat'];
                            $lang = $info_customer['lang'];
                            $order_total = $info_customer['order_total'];
                            $building = $info_customer['building'];
                            $landmark = $info_customer['landmark'];
                            $postal_code = $info_customer['postal_code'];
                        }

                        $order = new Order;
                        $order->order_number =$order_number;
                        $order->user_id =Session::get('id');
                        $order->order_total =$order_total;
                        $order->payment_type ='4';
                        $order->status ='1';
                        $order->address =$address;
                        $order->promocode =$info_customer['promocode'];
                        $order->discount_amount =$discount_amount;
                        $order->discount_pr =$info_customer['discount_pr'];
                        $order->tax =$info_customer['tax'];
                        $order->tax_amount =$info_customer['tax_amount'];
                        $order->delivery_charge =$delivery_charge;
                        $order->order_type =$info_customer['order_type'];
                        $order->lat =$lat;
                        $order->lang =$lang;
                        $order->building =$building;
                        $order->landmark =$landmark;
                        $order->pincode =$postal_code;
                        $order->order_notes =$info_customer['notes'];
                        $order->order_from ='web';

                        $order->save();

                        $order_id = DB::getPdo()->lastInsertId();
                        $data=Cart::where('cart.user_id',Session::get('id'))
                        ->get();
                        foreach ($data as $value) {
                            $OrderPro = new OrderDetails;
                            $OrderPro->order_id = $order_id;
                            $OrderPro->user_id = $value['user_id'];
                            $OrderPro->item_id = $value['item_id'];
                            $OrderPro->price = $value['price'];
                            $OrderPro->qty = $value['qty'];
                            $OrderPro->item_notes = $value['item_notes'];
                            $OrderPro->addons_id = $value['addons_id'];
                            $OrderPro->save();
                        }
                        $cart=Cart::where('user_id', Session::get('id'))->delete();

                        $count=Cart::where('user_id',Session::get('id'))->count();

                        $vnpay = new Transaction;
                        $vnpay->user_id = Session::get('id');
                        $vnpay->order_id = $order_id;
                        $vnpay->order_number = $order_number;
                        $vnpay->wallet = NULL;
                        $vnpay->payment_id = '4';
                        $vnpay->order_type = $info_customer['order_type'];
                        $vnpay->transaction_type = '2';
                        $vnpay->save();

                        try{
                            $ordermessage='Đơn hàng của bạn "'.$order_number.'" đang chờ xác nhận';
                            $email=$getuserdata->email;
                            $name=$getuserdata->name;
                            $data=['ordermessage'=>$ordermessage,'email'=>$email,'name'=>$name];

                            Mail::send('Email.orderemail',$data,function($message)use($data){
                                $message->from(env('MAIL_USERNAME'))->subject($data['ordermessage']);
                                $message->to($data['email']);
                            } );
                        }catch(\Swift_TransportException $e){
                            $response = $e->getMessage() ;
                            return response()->json(['status'=>0,'message'=>'Đã có lỗi trong quá trình gửi email, hãy thử lại...'],200);
                        }
                        
                        Session::put('cart', $count);

                        session()->forget(['offer_amount','offer_code']);

                        session()->forget('info_customer');

                        session()->forget('payment_content');

                        return view('front.vnpayreturn',compact('vnpayData','order_number','username','payment_content'));

                        
                        
                        // return response()->json(['status'=>1,'message'=>'Đơn hàng của bạn đang chờ xác nhận'],200);
                    }
                } else {
                    return response()->json(['status'=>0,'message'=>'Địa chỉ của bạn không nằm trong khu vực giao hàng của chúng tôi'],200);
                }

            }
            

        } catch (\Exception $e) {
            return  $e->getMessage();
            \Session::put('error',$e->getMessage());
            return redirect()->back();
        }
        }
        
    }
    
    public function charge(Request $request)
    {
        try {

            $getuserdata=User::where('id',Session::get('id'))
            ->get()->first();

            $location=User::select('lat','lang','map')->where('type','1')->first();

            if ($request->order_type == "2") {
                $deal_lat=$location->lat;
                $deal_long=$location->lang;
            } else {
                $deal_lat=$request->lat;
                $deal_long=$request->lang;
            }

            if (env('Environment') == 'sendbox') {
                if ($request->order_type == "2") {
                    $delivery_charge = "0.00";
                    $address = '451 - Lê Văn Việt, Tăng Nhơn Phú A, Quận 9, Thành phố Hồ Chí Minh';
                    $lat = '10.839654399999999';
                    $lang = '106.8040192';
                    $building = "";
                    $landmark = "";
                    $pincode = '70000';
                    $city = @$city;
                    $state = @$state;
                    $country = @$country;
                    $order_total = $request->order_total-$request->$delivery_charge;
                } else {
                    $delivery_charge = $request->delivery_charge;
                    $address = '451 - Lê Văn Việt, Tăng Nhơn Phú A, Quận 9, Thành phố Hồ Chí Minh';
                    $lat = '10.839654399999999';
                    $lang = '106.8040192';
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $city = @$city;
                    $state = @$state;
                    $country = @$country;
                    $pincode = '70000';
                }
            } else{
                $gmapkey = $location->map;

                // Make the HTTP request
                $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$deal_lat.','.$deal_long.'&sensor=false&key='.$gmapkey.'');

                $output= json_decode($geocode);
                $formattedAddress = @$output->results[0]->formatted_address;

                for($j=0;$j<count($output->results[0]->address_components);$j++){
                    $cn=array($output->results[0]->address_components[$j]->types[0]);
                    if(in_array("country", $cn)) {
                        $country = $output->results[0]->address_components[$j]->short_name;
                    }

                    if(in_array("postal_code", $cn)) {
                        $postal_code = $output->results[0]->address_components[$j]->long_name;
                    }

                    if(in_array("administrative_area_level_2", $cn)) {
                        $city = $output->results[0]->address_components[$j]->long_name;
                    }

                    if(in_array("administrative_area_level_1", $cn)) {
                        $state = $output->results[0]->address_components[$j]->short_name;
                    }
                }

                if ($request->order_type == "2") {
                     dd($formattedAddress);
                    $delivery_charge = "0.00";
                    $address = $formattedAddress;
                    $lat = $deal_lat;
                    $lang = $deal_long;
                    $building = "";
                    $landmark = "";
                    $city = @$city;
                    $state = @$state;
                    $country = @$country;
                    $pincode = $postal_code;
                    $order_total = $request->order_total-$request->$delivery_charge;
                } else {
                    $delivery_charge = $request->delivery_charge;
                    $address = $formattedAddress;
                    $lat = $deal_lat;
                    $lang = $deal_long;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $city = @$city;
                    $state = @$state;
                    $country = @$country;
                    $pincode = $postal_code;
                }
            }

            if ($request->discount_amount == "NaN") {
                $discount_amount = "0.00";
            } else {
                $discount_amount = $request->discount_amount;
            }

            $getpaymentdata=Payment::select('test_secret_key','live_secret_key','environment')->where('payment_name','Stripe')->first();

            if ($getpaymentdata->environment=='1') {
                $stripe_secret = $getpaymentdata->test_secret_key;
            } else {
                $stripe_secret = $getpaymentdata->live_secret_key;
            }

            Stripe::setApiKey($stripe_secret);

            if (env('Environment') == 'sendbox') {
                $customer = Customer::create(array(
                    'email' => $request->stripeEmail,
                    'source' => $request->stripeToken,
                    'name' => $getuserdata->name,
                    'address' => [
                        'line1' => 'New York, NY, USA',
                        'postal_code' => '10001',
                        'city' => 'New York',
                        'state' => 'NY',
                        'country' => 'US',
                    ],
                ));
            } else {
                $customer = Customer::create(array(
                    'email' => $request->stripeEmail,
                    'source' => $request->stripeToken,
                    'name' => $getuserdata->name,
                    'address' => [
                        'line1' => $address,
                        'postal_code' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'country' => $country,
                    ],
                ));
            }

            $charge = Charge::create(array(
                'customer' => $customer->id,
                'amount' => $order_total*100,
                'currency' => 'usd',
                'description' => 'Food Service',
            ));

            $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);


            $order = new Order;
            $order->order_number =$order_number;
            $order->user_id =Session::get('id');
            $order->order_total =$order_total;
            $order->razorpay_payment_id =$charge['id'];
            $order->payment_type ='2';
            $order->order_type =$request->order_type;
            $order->status ='1';
            $order->address =$address;
            $order->building =$building;
            $order->landmark =$landmark;
            $order->pincode =$pincode;
            $order->lat =$lat;
            $order->lang =$lang;
            $order->promocode =$request->promocode;
            $order->discount_amount =$discount_amount;
            $order->discount_pr =$request->discount_pr;
            $order->tax =$request->tax;
            $order->tax_amount =$request->tax_amount;
            $order->delivery_charge =$delivery_charge;
            $order->order_notes =$request->notes;
            $order->order_from ='web';
            $order->save();

            $order_id = DB::getPdo()->lastInsertId();
            $data=Cart::where('cart.user_id',Session::get('id'))
            ->get();

            foreach ($data as $value) {
                $OrderPro = new OrderDetails;
                $OrderPro->order_id = $order_id;
                $OrderPro->user_id = $value['user_id'];
                $OrderPro->item_id = $value['item_id'];
                $OrderPro->price = $value['price'];
                $OrderPro->qty = $value['qty'];
                $OrderPro->item_notes = $value['item_notes'];
                $OrderPro->save();
            }
            $cart=Cart::where('user_id', Session::get('id'))->delete();
            $count=Cart::where('user_id',Session::get('id'))->count();

            try{
                $ordermessage='Order "'.$order_number.'" has been placed';
                $email=$getuserdata->email;
                $name=$getuserdata->name;
                $data=['ordermessage'=>$ordermessage,'email'=>$email,'name'=>$name];

                Mail::send('Email.orderemail',$data,function($message)use($data){
                    $message->from(env('MAIL_USERNAME'))->subject($data['ordermessage']);
                    $message->to($data['email']);
                } );
            }catch(\Swift_TransportException $e){
                $response = $e->getMessage() ;
                return response()->json(['status'=>0,'message'=>'Đã có lỗi trong quá trình gửi mail, vui lòng thử lại...'],200);
            }
            
            Session::put('cart', $count);

            session()->forget(['offer_amount','offer_code']);

            return response()->json(['status'=>1,'message'=>'Đơn hàng đang chờ xác nhận'],200); 

            // return 'Charge successful, you get the course!';
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
    
}