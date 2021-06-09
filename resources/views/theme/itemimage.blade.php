<div class="row" style="margin-top: 20px;">
    <?php
// dd($getitemimages);
foreach ($getitemimages as $itemimage) {

?>
    <div class="col-md-6 col-lg-3 dataid{{$itemimage->id}}" id="table-image">
        <div class="card">
            <img class="img-fluid" src='{!! asset("images/item/".$itemimage->image) !!}' style="max-height: 255px; min-height: 255px;">
            <div class="card-body" style="text-align:center;">
                <button type="button" onClick="EditDocument({{$itemimage->id}})" class="btn mb-2 btn-sm btn-primary">Sửa</button>
                @if (env('Environment') == 'sendbox')
                <button type="button" class="btn mb-2 btn-sm btn-danger" onclick="myFunction()">Xóa</button>
                @else
                <button type="submit" onclick="DeleteImage({{$itemimage->id}},{{$itemimage->item_id}})" class="btn mb-2 btn-sm btn-danger">Xóa</button>
                @endif
            </div>
        </div>
    </div>
    <?php
}
?>
</div>
