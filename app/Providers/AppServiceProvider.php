<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Cart;
use Session;
use App\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // view()->composer(['front.vnpay','front.cart'],function($view){
        // $user_id  = Session::get('id');
        // $cartdata=Cart::with('itemimage')->select('cart.id','cart.qty','cart.price','cart.item_notes','item.item_name','cart.item_id','cart.addons_id')
        // ->join('item','cart.item_id','=','item.id')
        // ->where('cart.user_id',$user_id)
        // ->where('cart.is_available','=','1')
        // ->orderby('id','desc')->get();
        // $taxval=User::select('tax','delivery_charge','currency','map')->where('type','1')
        // ->get()->first();
        // foreach($cartdata as $cart){
        //     $data[] = array(
        //                     "total_price" => $cart->price
        //                 );
        // }
        // $order_total = array_sum(array_column(@$data, 'total_price'));
        // $taxprice = array_sum(array_column(@$data, 'total_price'))*$taxval->tax/100; 
        // $total = array_sum(array_column(@$data, 'total_price'))+$taxprice+$taxval->delivery_charge;
        // if(Session::has('offer_amount')){
        //     $totalMoney = $order_total+$taxval->delivery_charge+$taxprice-$order_total*Session::get('offer_amount')/100;          
        //     $order_total =  $order_total+$taxprice-$order_total*Session::get('offer_amount')/100;
        // }else{
        //     $totalMoney = $total;
        // }
        // $view->with(['totalMoney'=>$totalMoney,'total'=>$total,'money'=>'hello','order_total'=>$order_total]);
        // });
        
    }
}
