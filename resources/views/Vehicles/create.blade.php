@extends('layout.main')

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h1>{{trans('file.add_vehicle')}}</h1>
                        <br>
                        
                    </div>
                    <div class="card-body">
                        <h5 class="italic">{{trans('file.Featured vehicle will be displayed in POS')}}</h5>
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <form id="product-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Vehicle Name')}} *</strong> </label>
                                        <input type="text" name="name" class="form-control" id="name" aria-describedby="name" required>
                                        <div class="name-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Vehicle Code')}} *</strong> </label>
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
                                        <label>Chassis Number *</strong> </label>
                                        <input type="text" name="chassis_no" class="form-control" id="chassis_no" aria-describedby="chassis" required>
                                        <div class="chassis-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div> 
                                <div class="col-md-4"> 
                                    <div class="form-group">
                                        <label>Engine Number *</strong> </label>
                                        <input type="text" name="engine_no" class="form-control" id="engine_no" aria-describedby="engine" required>
                                        <div class="engine-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Brand')}}</strong> </label>
                                        <div class="input-group">
                                          <select name="vehicle_brand_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Brand...">
                                            @foreach($lims_brand_list as $brand)
                                                <option value="{{$brand->id}}">{{$brand->name}}</option>
                                            @endforeach
                                          </select>
                                      </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.category')}} *</strong> </label>
                                        <div class="input-group">
                                          <select name="vehicle_category_id" id="category" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Category...">
                                            @foreach($lims_category_list as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                          </select>
                                         
                                      </div>
                                      <div class="cat-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
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
                                <div id="cost" class="col-md-4">
                                     <div class="form-group">
                                        <label>{{trans('file.Vehicle Cost')}} *</strong> </label>
                                        <input type="number" onkeypress="return onlyNumberKey(event)" id="icost" name="cost" required class="form-control" step="any">
                                        <div class="cost-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
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
                                    <div class="form-group">
                                        <label>{{trans('file.Vehicle Details')}}</label>
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
 
@foreach($vehicle as $ExpDoc)
        <input type="hidden" name="ref[]" value="{{ $ExpDoc->chassis_no }}">
        <input type="hidden" name="dff[]" value="{{ $ExpDoc->engine_no }}">
@endforeach
@foreach($list1 as $ExpDoc)
        <input type="hidden" name="cha[]" value="{{ $ExpDoc->chassis_no }}">
        <input type="hidden" name="eng[]" value="{{ $ExpDoc->engine_no }}">
@endforeach
@foreach($list2 as $ExpDoc)
<input type="hidden" name="cha1[]" value="{{ $ExpDoc->chassis_no }}">
<input type="hidden" name="eng1[]" value="{{ $ExpDoc->engine_no }}">
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
    $("ul#vehicle").siblings('a').attr('aria-expanded','true');
    $("ul#vehicle").addClass("show");
    $("ul#vehicle #vehicle-add-menu").addClass("active");
    var myArray = new Array();
    $("input[name='ref[]']").each(function(){
        myArray.push($(this).val());
    });
    $("input[name='cha[]']").each(function(){
        myArray.push($(this).val());
    });
    $("input[name='cha1[]']").each(function(){
        myArray.push($(this).val());
    });
    var myArray1 = new Array();
    $("input[name='dff[]']").each(function(){
        myArray1.push($(this).val());
    });
    $("input[name='eng[]']").each(function(){
        myArray1.push($(this).val());
    });
    $("input[name='eng1[]']").each(function(){
        myArray1.push($(this).val());
    });
// Submit
$("#submit-btn").click(function(e){
    e.preventDefault();
      tinyMCE.triggerSave();
    if($('#name').val() == ''){
        var value = "Please enter vehicle name ...";
        $(".name-error-msg").css('display','block');
        $(".name-error-msg").text(value);
        return false;
    }
    else if($('#code').val() == ''){
        var value = "Please enter vehicle code ...";
        $(".code-error-msg").css('display','block');
        $(".code-error-msg").text(value);
        $(".name-error-msg").css('display','none');
        return false;
    }
    else if($('#chassis_no').val() == ''){
        var value = "Please enter chassis number ...";
        $(".chassis-error-msg").css('display','block');
        $(".chassis-error-msg").text(value);
        $(".name-error-msg").css('display','none');
        $(".code-error-msg").css('display','none');
        return false;
    }
    else if(jQuery.inArray($('#chassis_no').val(),myArray) !== -1){
            var value = "Chassis No already exists. Please add unique one.";
            $(".chassis-error-msg").css('display','block');
            $(".chassis-error-msg").text(value);
            $(".name-error-msg").css('display','none');
            $(".code-error-msg").css('display','none');
            return false;
    }
    else if($('#engine_no').val() == ''){
        var value = "Please enter engine number ...";
        $(".engine-error-msg").css('display','block');
        $(".engine-error-msg").text(value);
        $(".name-error-msg").css('display','none');
        $(".chassis-error-msg").css('display','none');
        $(".code-error-msg").css('display','none');
        return false;
    }
    else if(jQuery.inArray($('#engine_no').val(),myArray1) !== -1){
        var value = "Engine No already exists. Please add different one.";
        $(".engine-error-msg").css('display','block');
        $(".engine-error-msg").text(value);
        $(".name-error-msg").css('display','none');
        $(".chassis-error-msg").css('display','none');
        $(".code-error-msg").css('display','none');
        return false;
    } 
    else if($('#category').val() == ''){
        var value = "Please select vehicle category ...";
        $(".cat-error-msg").css('display','block');
        $(".cat-error-msg").text(value);
        $(".code-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".chassis-error-msg").css('display','none');
        $(".engine-error-msg").css('display','none');
        return false;
    }  
    else if($('#pfi_number').val() == ''){
        var value = "Please select purchase PFI number ...";
        $(".pfi-error-msg").css('display','block');
        $(".pfi-error-msg").text(value);
        $(".code-error-msg").css('display','none');
        $(".cat-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".chassis-error-msg").css('display','none');
        $(".engine-error-msg").css('display','none');
        return false;
    } 
    else if(!$('#icost').val()){
        var value = "Please enter vehicle purchase price ...";
        $(".cost-error-msg").css('display','block');
        $(".cost-error-msg").text(value);
        $(".cat-error-msg").css('display','none');
        $(".code-error-msg").css('display','none');
        $(".pfi-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".chassis-error-msg").css('display','none');
        $(".engine-error-msg").css('display','none');
        return false;
    }
    else if($('#warehouse').val() == ''){
        alert('Please select warehouse !');
        return false;
    } 
    else{
        $(".cost-error-msg").css('display','none');
        $(".pfi-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".cat-error-msg").css('display','none');
        $(".code-error-msg").css('display','none');
        $(".chassis-error-msg").css('display','none');
        $(".engine-error-msg").css('display','none');
    }
    var data = $('#product-form').serialize();
    $.ajax({
        url:"{{ route('vehicle.store')}}",
        type:"POST",
        data:{
            "_token" : "{{ csrf_token() }}",
            "data":data,
        },
        success:function(data){
            if($.isEmptyObject(data.error)){
                var msg = 'Vehicle added successfully.';
                swal.fire({
                    title: "Success", 
                    text: msg, 
                    icon: "success",
                    button: "Ok"
                }).then(function(){ 
                    window.location.href='{{ route('vehicle.index') }}';
                });
            }else{
                printErrorMsg(data.error);
            }
        }
    });
});


    $("#variant-section").hide();
    $("#diffPrice-section").hide();

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
</script>
@endpush
