@extends('layout.main')

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.add_product')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <form id="product-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Product Name')}} *</strong> </label>
                                        <input type="text" name="name" class="form-control" id="name" aria-describedby="name" required>
                                        <div class="name-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Product Code')}} *</strong> </label>
                                        <div class="input-group">
                                            <input type="text" name="code" class="form-control" id="code" aria-describedby="code" required>
                                            <div class="input-group-append">
                                                <button id="genbutton" type="button" class="btn btn-sm btn-default" title="{{trans('file.Generate')}}"><i class="fa fa-refresh"></i></button>
                                            </div>
                                        </div>
                                        <div class="code-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Brand')}}</strong> </label>
                                        <div class="input-group">
                                          <select name="brand_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Brand...">
                                            @foreach($lims_brand_list as $brand)
                                                <option value="{{$brand->id}}">{{$brand->title}}</option>
                                            @endforeach
                                          </select>
                                      </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.category')}} *</strong> </label>
                                        <div class="input-group">
                                          <select id="category" name="category_id" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Category...">
                                            @foreach($lims_category_list as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                          </select>
                                      </div>
                                      <div class="cat-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>
                                        <div class="col-md-4 form-group">
                                                <label>{{trans('file.Product Unit')}} *</strong> </label>
                                                <div class="input-group">
                                                  <select required class="form-control selectpicker" id="product_unit" name="product_unit">
                                                    <option value="" disabled selected>Select Product Unit...</option>
                                                    <option value="1">{{trans('file.UnitPiece')}}</option>
                                                    <option value="2">{{trans('file.UnitDozen')}}</option>
                                                    <option value="3">{{trans('file.UnitCarton')}}</option>
                                                 
                                                  </select>
                                              </div>
                                              <div class="unit-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                        </div>
                                        <div id="piece_no" class="col-md-4 form-group">
                                                <label>{{trans('file.NoPiece')}}</strong> </label>
                                                <div class="input-group">
                                                  <input class="form-control" onkeypress="return onlyNumberKey(event)" required type="number" name="piece_no">
                                              </div>
                                        </div>
                                        <div id="dozen_no" class="col-md-4  form-group" >
                                            <label>{{trans('file.NoDozen')}}</strong> </label>
                                            <div class="input-group">
                                              <input class="form-control" onkeypress="return onlyNumberKey(event)" required type="number" name="dozen_no">
                                          </div>
                                          </div>
                                        <div id="carton_no" class="col-md-4 form-group">
                                            <label>{{trans('file.NoCarton')}}</strong> </label>
                                            <div class="input-group">
                                              <input class="form-control" onkeypress="return onlyNumberKey(event)" required type="number" id="carton_no" name="carton_no">
                                          </div>
                                        </div>
                                    <div id="carton_piece"  class="col-md-4 form-group">
                                        <label>{{trans('file.Carton Piece')}}</strong> </label>
                                        <div class="input-group">
                                          <input class="form-control" onkeypress="return onlyNumberKey(event)" required type="number"  name="piece_in_carton">
                                      </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PFI Number *</strong> </label>
                                        <div class="input-group">
                                          <select name="pfi_number" id="pfi_number" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Purchase PFI Number...">
                                            @foreach($proformas as $pro) 
                                                <option value="{{$pro->id}}">{{$pro->pfi_number}}</option>
                                            @endforeach
                                          </select>
                                      </div>
                                      <div class="pfi-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>                                                                
                                <div  class="col-md-4">
                                     <div class="form-group">
                                        <label>{{trans('file.Product Cost')}} *</strong> </label>
                                        <input type="number" onkeypress="return onlyNumberKey(event)" id="icost" name="cost" required class="form-control" step="any">
                                        <div class="cost-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Product Price')}} *</strong> </label>
                                        <input type="number" onkeypress="return onlyNumberKey(event)" id="price" name="price" required class="form-control" step="any">
                                        <div class="price-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Alert Quantity')}}</strong> </label>
                                        <input type="number" name="alert_quantity" class="form-control" step="any">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Warehouse')}} *</label>
                                        <select required id="warehouse" name="warehouse_id" class="selectpicker form-control" data-live-search="true" title="Select warehouse...">
                                            @foreach($lims_warehouse_list as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                            @endforeach
                                        </select> 
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mt-3">
                                        <input type="checkbox" checked name="featured" value="1">&nbsp;
                                        <label>{{trans('file.Featured')}}</label>
                                        <p class="italic">{{trans('file.Featured product will be displayed in POS')}}</p>
                                    </div> 
                                </div> 

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{trans('file.Product Details')}}</label>
                                        <textarea name="product_details" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="button" value="{{trans('file.submit')}}" id="submit-btn" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</section>

@foreach($product as $ExpDoc)
        <input type="hidden" name="ref[]" value="{{ $ExpDoc->name }}">
@endforeach
@foreach($product as $ExpDoc)
        <input type="hidden" name="dff[]" value="{{ $ExpDoc->code }}">
@endforeach
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
 function onlyNumberKey(evt) {
          
          // Only ASCII character in that range allowed
          var ASCIICode = (evt.which) ? evt.which : evt.keyCode
          if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
              return false;
          return true;
      }
    $("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #product-create-menu").addClass("active");
    $("#piece_no").hide();
    $("#dozen_no").hide();
    $("#carton_no").hide();
    $("#carton_piece").hide();
   
    var myArray = new Array();
    $("input[name='ref[]']").each(function(){
        myArray.push($(this).val());
    });
    var myArray1 = new Array();
    $("input[name='dff[]']").each(function(){
        myArray1.push($(this).val());
    });
// Submit
$("#submit-btn").click(function(e){
    e.preventDefault();
      tinyMCE.triggerSave();
    if($('#name').val() == ''){
        var value = "Please enter product name ...";
        $(".name-error-msg").css('display','block');
        $(".name-error-msg").text(value);
        return false;
    }
    else if(jQuery.inArray($('#name').val(),myArray) !== -1){
            var value = "Name already exists. Please add different one.";
            $(".name-error-msg").css('display','block');
            $(".name-error-msg").text(value);
            return false;
    }
    
    else if($('#code').val() == ''){
        var value = "Please enter product code ...";
        $(".code-error-msg").css('display','block');
        $(".code-error-msg").text(value);
        $(".name-error-msg").css('display','none');
        return false;
    }
    else if(jQuery.inArray($('#code').val(),myArray1) !== -1){
        var value = "Code already exists. Please add different one.";
        $(".code-error-msg").css('display','block');
        $(".code-error-msg").text(value);
        $(".name-error-msg").css('display','none');
        return false;
    }

    else if($('#category').val() == ''){
        var value = "Please select product category ...";
        $(".cat-error-msg").css('display','block');
        $(".cat-error-msg").text(value);
        $(".code-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        return false;
    }
    else if($('#product_unit').val() == ''){
        var value = "Please select product unit ...";
        $(".unit-error-msg").css('display','block');
        $(".unit-error-msg").text(value);
        $(".code-error-msg").css('display','none');
        $(".cat-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        return false;
    }
    else if($('#pfi_number').val() == ''){
        var value = "Please select purchase PFI number ...";
        $(".pfi-error-msg").css('display','block');
        $(".pfi-error-msg").text(value);
        $(".code-error-msg").css('display','none');
        $(".cat-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".unit-error-msg").css('display','none');
        return false;
    } 
    else if(!$('#icost').val()){
        var value = "Please enter product purchase price ...";
        $(".cost-error-msg").css('display','block');
        $(".cost-error-msg").text(value);
        $(".cat-error-msg").css('display','none');
        $(".unit-error-msg").css('display','none');
        $(".code-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".pfi-error-msg").css('display','none');
        return false;
    }
    else if(!$('#price').val()){
        var value = "Please enter product sell price ...";
        $(".price-error-msg").css('display','block');
        $(".price-error-msg").text(value);
        $(".cat-error-msg").css('display','none');
        $(".cost-error-msg").css('display','none');
        $(".unit-error-msg").css('display','none');
        $(".code-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".pfi-error-msg").css('display','none');
        return false;
    }
    else if($('#warehouse').val() == ''){
        alert('Please select warehouse !');
        return false;
    }
    else{
        $(".cost-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".cat-error-msg").css('display','none');
        $(".code-error-msg").css('display','none');
        $(".unit-error-msg").css('display','none');
        $(".price-error-msg").css('display','none');
        $(".pfi-error-msg").css('display','none');
    }
    var data = $('#product-form').serialize();

    $.ajax({
        url:"{{ route('product.store')}}",
        type:"POST",
        data:{
            "_token" : "{{ csrf_token() }}",
            "data":data,
        },
        success:function(data){
            if($.isEmptyObject(data.error)){
                var msg = 'Product added successfully.';
                swal.fire({
                    title: "Success",
                    text: msg, 
                    icon: "success",
                    button: "Ok"
                }).then(function(){ 
                    window.location.href = '/products';
                });
            }else{
                printErrorMsg(data.error);
            }
        }
    });
});

//end
    $('[data-toggle="tooltip"]').tooltip();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#genbutton').on("click", function(){
      $.get('gencode', function(data){
        $("input[name='code']").val(data);
      });
    });
    tinymce.init({
      selector: 'textarea',
      height: 130,
      plugins: [
        'advlist autolink lists link image charmap print preview anchor textcolor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste code wordcount'
      ],
      toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat',
      branding:false
    });
// Unit change
$('select[name="product_unit"]').on('change', function() {
        if($(this).val() == '1'){
            $("#piece_no").show();
            $("#dozen_no").hide();
            $("#carton_no").hide();
            $("#carton_piece").hide(); 
        }
        else if($(this).val() == '2'){
            $("#dozen_no").show();
            $("#carton_no").hide();
            $("#carton_piece").hide();
            $("#piece_no").hide();
        }
        else if($(this).val() == '3'){
            $("#carton_no").show();
            $("#piece_no").hide();
            $("#dozen_no").hide();
            $("#carton_piece").show();
        }

    });



    $(window).keydown(function(e){
        if (e.which == 13) {
            var $targ = $(e.target);

            if (!$targ.is("textarea") && !$targ.is(":button,:submit")) {
                var focusNext = false;
                $(this).find(":input:visible:not([disabled],[readonly]), a").each(function(){
                    if (this === e.target) {
                        focusNext = true;
                    }
                    else if (focusNext){
                        $(this).focus();
                        return false;
                    }
                });

                return false;
            }
        }
    });

    $(".dropzone").sortable({
        items:'.dz-preview',
        cursor: 'grab',
        opacity: 0.5,
        containment: '.dropzone',
        distance: 20,
        tolerance: 'pointer',
        stop: function () {
          var queue = myDropzone.getAcceptedFiles();
          newQueue = [];
          $('#imageUpload .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {
                var name = el.innerHTML;
                queue.forEach(function(file) {
                    if (file.name === name) {
                        newQueue.push(file);
                    }
                });
          });
          myDropzone.files = newQueue;
        }
    });


</script>
@endpush
