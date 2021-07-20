<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Item;
use App\ItemImages;
use App\Ingredients;
use App\Addons;
use App\Cart;
use Validator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index() {
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        $getingredients = Ingredients::where('is_deleted','2')->get();
        $getaddons = Addons::where('is_deleted','2')->where('is_available','1')->get();
        $getitem = Item::select('item.*')->with('category')->join('categories','item.cat_id','=','categories.id')->where('item.is_deleted','2')->where('categories.is_available','1')->get();

        return view('item', compact('getcategory','getitem','getingredients','getaddons'));
    }

    public function list()
    {
        $getitem = Item::select('item.*')->with('category')->join('categories','item.cat_id','=','categories.id')->where('item.is_deleted','2')->where('categories.is_available','1')->get();
        return view('theme.itemtable',compact('getitem'));
    }

    public function itemimages($id) {
        $getitemimages = ItemImages::where('item_id', $id)->get();
        $itemdetails = Item::join('categories','item.cat_id','=','categories.id')->where('item.id', $id)->get()->first();
        return view('item-images', compact('getitemimages','itemdetails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(),$rules=[
          'cat_id' => 'required',
          'item_name' => 'required|unique:item,item_name',
          'price' => 'required',
          'file.*' => 'required|mimes:jpeg,png,jpg',
          // 'addons_id'=>'required',
          // 'ingredients_id'=>'required'
        ],$messages = [
            'item_name.required'=>'Bạn chưa nhập tên sản phẩm',
          'cat_id.required'=>'Bạn chưa chọn loại sản phẩm',
          'price.required'=>'Bạn chưa nhập giá sản phẩm',
          'type.required'=>'Bạn chưa chọn phân loại',
          'item_name.unique'=>'Sản phẩm đã tồn tại',
          'file.*.mimes'=>'Hình ảnh phải có dạng jpeg, jpg, png'
          // 'addons_id.required'=>'Bạn chưa chọn sản phẩm thêm'
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
            $item = new Item;
            $item->cat_id =$request->cat_id;
            if($request->addons_id != NULL){
                 $item->addons_id =@implode(",",$request->addons_id);
            }else{
                $item->addons_id = NULL;
            }
            if($request->ingredients_id != NULL){
                $item->ingredients_id =@implode(",",$request->ingredients_id);
            }else{
                $item->ingredients_id = NULL;
            }

            $item->item_name =$request->item_name;
            $item->item_price =$request->price;
            $item->item_description =$request->description;
            $item->delivery_time =$request->delivery_time;
            $item->save();

            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach($files as $file){

                    $itemimage = new ItemImages;
                    $image = 'item-' . uniqid() . '.' . $file->getClientOriginalExtension();

                    

                    $file->move('public/images/item', $image);

                    $itemimage->item_id =$item->id;
                    $itemimage->image =$image;
                    $itemimage->save();
                }
            }

            $success_output = 'Thêm sản phẩm mới thành công!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public function storeimages(Request $request)
    {
        $validation = Validator::make($request->all(),$rules=[
          'file.*' => 'required|mimes:jpeg,png,jpg'
        ],$messages = [
            'file.*.mimes'=>'Hình ảnh phải có định dạng jpeg, png, jpg',
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
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach($files as $file){

                    $itemimage = new ItemImages;
                    $image = 'item-' . uniqid() . '.' . $file->getClientOriginalExtension();

                    $file->move('public/images/item', $image);

                    $itemimage->item_id =$request->itemid;
                    $itemimage->image =$image;
                    $itemimage->save();
                }
            }

            $success_output = 'Thêm hình ảnh mới thành công!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public function storeingredientsimages(Request $request)
    {
        $validation = Validator::make($request->all(),$rules=[
          'ingredients.*' => 'required|mimes:jpeg,png,jpg',
        ],$messages=[
            'ingredients.mimes'=>'Hình ảnh phải có định dạng jpeg,png,jpg'
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

            if ($request->hasFile('ingredients')) {
                $ingredients = $request->file('ingredients');
                foreach($ingredients as $file){

                    $itemimage = new Ingredients;
                    $image = 'ingredients-' . uniqid() . '.' . $file->getClientOriginalExtension();

                    

                    $file->move('public/images/ingredients', $image);

                    $itemimage->item_id =$request->itemid;
                    $itemimage->image =$image;
                    $itemimage->save();
                }
            }

            $success_output = 'Thêm hình ảnh mới thành công!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $item = Item::findorFail($request->id);
        $getitem = Item::where('id',$request->id)->first();
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Item fetch successfully', 'ResponseData' => $getitem], 200);
    }

    public function showimage(Request $request)
    {
        $getitem = ItemImages::where('id',$request->id)->first();
        if($getitem->image){
            $getitem->img=url('public/images/item/'.$getitem->image);
        }
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Item Image fetch successfully', 'ResponseData' => $getitem], 200);
    }

    public function showingredients(Request $request)
    {
        $getitem = Ingredients::where('id',$request->id)->first();
        if($getitem->image){
            $getitem->img=url('public/images/ingredients/'.$getitem->image);
        }
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Ingredients Image fetch successfully', 'ResponseData' => $getitem], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validation = Validator::make($request->all(),$rules=[
          'getcat_id' => 'required',
          'item_name' => 'required',
          'getprice' => 'required',
          ''
        ],$messages=[
            'item_name.required'=>'Bạn chưa nhập tên sản phẩm',
          'getcat_id.required'=>'Bạn chưa chọn loại sản phẩm',
          'getprice.required'=>'Bạn chưa nhập giá sản phẩm',
          'type.required'=>'Bạn chưa chọn phân loại'
        ]);

        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
            // dd($error_array);
        }
        else
        {
            $deletefromcart=Cart::where('item_id', $request->id)->delete();
            $item = new Item;
            $item->exists = true;
            $item->id = $request->id;

            $item->cat_id =$request->getcat_id;
            if($request->addons_id != NULL){
                 $item->addons_id =@implode(",",$request->addons_id);
            }else{
                $item->addons_id = NULL;
            }
            if($request->ingredients_id != NULL){
                $item->ingredients_id =@implode(",",$request->ingredients_id);
            }else{
                $item->ingredients_id = NULL;
            }
            $item->item_name =$request->item_name;
            $item->item_price =$request->getprice;
            $item->item_description =$request->getdescription;
            $item->delivery_time =$request->getdelivery_time;
            $item->save();           

            $success_output = 'Cập nhật sản phẩm thành công!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public function updateimage(Request $request)
    {
        $validation = Validator::make($request->all(),[
          'image' => 'image|mimes:jpeg,png,jpg'
        ]);

        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
            // dd($error_array);
        }
        else
        {
            $itemimage = new ItemImages;
            $itemimage->exists = true;
            $itemimage->id = $request->id;

            if(isset($request->image)){
                if($request->hasFile('image')){
                    $image = $request->file('image');
                    $image = 'item-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move('public/images/item', $image);
                    $itemimage->image=$image;
                    unlink(public_path('images/item/'.$request->old_img));
                }            
            }
            $itemimage->save();           

            $success_output = 'Cập nhật hình ảnh thành công!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public function updateingredients(Request $request)
    {
        $validation = Validator::make($request->all(),[
          'image' => 'image|mimes:jpeg,png,jpg'
        ]);

        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
            // dd($error_array);
        }
        else
        {
            $itemimage = new Ingredients;
            $itemimage->exists = true;
            $itemimage->id = $request->id;

            if(isset($request->image)){
                if($request->hasFile('image')){
                    $image = $request->file('image');
                    $image = 'ingredients-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move('public/images/ingredients', $image);
                    $itemimage->image=$image;
                    unlink(public_path('images/ingredients/'.$request->old_img));
                }            
            }
            $itemimage->save();           

            $success_output = 'Đã cập nhật nguyên liệu!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public function status(Request $request)
    {
        
        $UpdateDetails = Item::where('id', $request->id)
                    ->update(['item_status' => $request->status]);
        $UpdateCart = Cart::where('item_id', $request->id)
                            ->update(['is_available' => $request->status]);
        if ($UpdateDetails) {
            return 2;
        } else {
            return 0;
        }
    }

    public function delete(Request $request)
    {
        $UpdateDetails = Item::where('id', $request->id)
                    ->update(['is_deleted' => '1']);
        // $addons = Addons::where('item_id', $request->id)->update( array('is_deleted'=>'1') );
        $UpdateCart = Cart::where('item_id', $request->id)
                            ->delete();
        if ($UpdateDetails) {
            return 2;
        } else {
            return 0;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Request $request)
    // {
    //     $item=Item::where('id', $request->id)->delete();
    //     $itemimage=ItemImages::where('item_id', $request->id)->delete();
    //     $ingredientsimage=Ingredients::where('item_id', $request->id)->delete();
    //     if ($item) {
    //         return 1;
    //     } else {
    //         return 0;
    //     }
    // }
    public function destroyimage(Request $request)
    {
        $getitemimages = ItemImages::where('item_id', $request->item_id)->count();
        if ($getitemimages > 1) {
           $getimg = ItemImages::where('id',$request->id)->first();
           unlink(public_path('images/item/'.$getimg->image));

           $itemimage=ItemImages::where('id', $request->id)->delete();
           if ($itemimage) {
               return 1;
           } else {
               return 0;
           }
        } else {
            return 2;
        }
    }

    public function destroyingredients(Request $request)
    {
        $getimg = Ingredients::where('id',$request->id)->first();
        unlink(public_path('images/ingredients/'.$getimg->image));

        $ingredientsimage=Ingredients::where('id', $request->id)->delete();
        if ($ingredientsimage) {
            return 1;
        } else {
            return 0;
        }
    }
}
