<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\Item;
use App\Ratting;
use App\Slider;
use App\Banner;
use App\About;
use App\Contact;
use App\User;
use App\Pincode;
use Session;
use Validator;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getslider = Slider::all();
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        $user_id  = Session::get('id');
        $getfeaturedItem = Item::with(['category','itemimage'])->where('is_featured',1)->get();
        $getitem = Item::with(['category','itemimage'])->select('item.cat_id','item.id','item.item_name','item.item_price','item.item_description',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })
        ->where('item.item_status','1')
        ->where('item.is_deleted','2')
        ->orderby('cat_id')->get();
        $getreview = Ratting::with('users')->get();

        $getbanner = Banner::orderby('id','desc')->get();

        $getdata=User::select('currency')->where('type','1')->first();

        return view('front.home', compact('getslider','getcategory','getabout','getitem','getreview','getbanner','getdata','getfeaturedItem'));
    }

    public function contact(Request $request)
    {
        if($request->firstname == ""){
            return response()->json(["status"=>0,"message"=>"Bạn chưa nhập họ"],200);
        }
        if($request->lastname == ""){
            return response()->json(["status"=>0,"message"=>"Bạn chưa nhập tên"],200);
        }
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>"Bạn chưa nhập email"],200);
        }
        if($request->message == ""){
            return response()->json(["status"=>0,"message"=>"Bạn chưa nhập lời nhắn"],200);
        }
        $category = new Contact;
        $category->firstname =$request->firstname;
        $category->lastname =$request->lastname;
        $category->email =$request->email;
        $category->message =$request->message;
        $category->save();

        if ($category) {
            return response()->json(['status'=>1,'message'=>'Lời nhắn của bạn sẽ được gửi tới chúng tôi!'],200);
        } else {
            return response()->json(['status'=>2,'message'=>'Đã có lỗi xảy ra.'],200);
        }
    }

    public function checkpincode(Request $request)
    {

        $getdata=User::select('min_order_amount','max_order_amount','currency')->where('type','1')
        ->get()->first();

        if($request->postal_code != ""){
            $pincode=Pincode::select('pincode')->where('pincode',$request->postal_code)
                        ->get()->first();
            if(@$pincode['pincode'] == $request->postal_code) {
                if(!empty($pincode))
                {
                    if ($getdata->min_order_amount > $request->order_total) {
                        return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                    } elseif ($getdata->max_order_amount < $request->order_total) {
                        return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                    } else {
                        return response()->json(['status'=>1,'message'=>'Pincode is available for delivery'],200);
                    }                
                }
            } else {
                return response()->json(['status'=>0,'message'=>'Địa chỉ của bạn không nằm trong khu vực giao hàng của chúng tôi'],200);
            }
        } else {
            
            if ($getdata->min_order_amount > $request->order_total) {
                return response()->json(['status'=>0,'message'=>"Giá trị đơn hàng phải nằm trong khoảng: ".$getdata->min_order_amount."".$getdata->currency." and ".$getdata->max_order_amount."".$getdata->currency.""],200);
            } elseif ($getdata->max_order_amount < $request->order_total) {
                return response()->json(['status'=>0,'message'=>"Giá trị đơn hàng phải nằm trong khoảng: ".$getdata->min_order_amount."".$getdata->currency." and ".$getdata->max_order_amount."".$getdata->currency.""],200);
            } else {
                return response()->json(['status'=>1,'message'=>'Ok'],200);
            }   
        }
    }
    public function notallow() {
        return view('front.405'); 
    }
}
