@extends('layout.main')

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h1>{{trans('file.update_vehicle')}}</h1>
                        <br>
                        
                    </div>
                    <div class="card-body">
                        <h5 class="italic" style="font-size:13px;">These changes will be applied to all (Raw , In Progress and Finished vehicles ) under this vehicle</h5>
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <form id="product-update">
                            <input type="hidden" value="{{$vehicle->id ?? ''}}" name="edit_id">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Vehicle Name')}} *</strong> </label>
                                        <input type="text" name="name" class="form-control" id="name" value="{{$vehicle->name}}" aria-describedby="name" required>
                                        <div class="name-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Vehicle Code')}} *</strong> </label>
                                        <div class="input-group">
                                            <input type="text" name="code" class="form-control" value="{{$vehicle->code}}" id="code" aria-describedby="code" required>
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
                                          <select name="vehicle_brand_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Brand...">
                                            @foreach($brand as $brand1)
                                                <option value="{{$brand1->id}}"  {{ $brand1->id == $vehicle->brand_id  ? 'selected' : '' }} >{{$brand1->name}}</option>
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
                                            @foreach($category as $category1)
                                                <option value="{{$category1->id}}" {{ $category1->id == $vehicle->category_id  ? 'selected' : '' }}>{{$category1->name}}</option>
                                            @endforeach
                                          </select>
                                         
                                      </div>
    
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>PFI Number *</strong> </label>
                                        <div class="input-group">
                                            <input type="hidden" name="old_pfi" value="{{$vehiclePro->id}}">
                                          <select name="pfi_id" id="pfi_number" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Purchase PFI Number...">
                                            @foreach($proformas as $pro)
                                                <option value="{{$pro->id}}" {{ $pro->id == $vehiclePro->id  ? 'selected' : '' }}>{{$pro->pfi_number}}</option>
                                            @endforeach
                                          </select>
                                      </div> 
                                    </div>
                                </div>
                                <div id="cost" class="col-md-4">
                                     <div class="form-group"> 
                                        <label>{{trans('file.Vehicle Cost')}} *</strong> </label>
                                        <input type="number" id="icost" name="cost" value="{{$vehicle->cost}}" required class="form-control" step="any">
                                        <div class="cost-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                                    </div>
                                </div>
                                <div id="alert-qty" class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Alert Quantity')}}</strong> </label>
                                        <input type="number" name="alert_quantity"  value="{{$vehicle->alert_quantity ?? ''}}" class="form-control" step="any">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Warehouse')}} *</label>
                                        <select required id="warehouse" name="warehouse_id" class="selectpicker form-control" data-live-search="true" title="Select warehouse...">
                                            @foreach($warehouse as $warehouse1)
                                            <option value="{{$warehouse1->id}}" {{ $warehouse1->id == $vehicle->warehouse_id  ? 'selected' : '' }}>{{$warehouse1->name}}</option>
                                            @endforeach
                                        </select> 
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{trans('file.Vehicle Details')}}</label>
                                        <textarea name="product_details" class="form-control" rows="3">{{str_replace('@', '"', $vehicle->product_details)}}</textarea>
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

@endsection 
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
$("ul#vehicle").siblings('a').attr('aria-expanded','true');
$("ul#vehicle").addClass("show");
$("ul#vehicle #vehicle-list-menu").addClass("active");
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
    else if(!$('#icost').val() || $('#icost').val() == 0){
        var value = "Please enter vehicle purchase price ...";
        $(".cost-error-msg").css('display','block');
        $(".cost-error-msg").text(value);
        $(".code-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        return false;
    }
    else{
        $(".cost-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".code-error-msg").css('display','none');
        $(".name-error-msg").css('display','none');
        $(".qty-error-msg").css('display','none');
    }
    var data = $('#product-update').serialize();
    var aaie=$('#name').val();
    $.ajax({
        url:"{{ route('vehicle.update')}}",
        type:"POST",
        data:{
            "_token" : "{{ csrf_token() }}",
            "data":data,
        },
        success:function(data){
            if($.isEmptyObject(data.error)){
                var msg = 'Vehicle '+ aaie +' updated successfully.';
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
//end submit
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
