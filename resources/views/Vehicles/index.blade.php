@extends('layout.main') @section('content')
@if(session()->has('create_message'))
    <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('create_message') }}</div>
@endif
@if(session()->has('edit_message'))
    <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('edit_message') }}</div>
@endif
@if(session()->has('import_message'))
    <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('import_message') }}</div>
@endif
@if(session()->has('not_permitted'))
    <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if(session()->has('message'))
    <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif

<section>
    <div class="container-fluid">
      
            <a href="{{route('vehicle.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{__('file.add_vehicle')}}</a>
            <a href="#" data-toggle="modal" data-target="#importProduct" class="btn btn-primary"><i class="dripicons-copy"></i> {{__('file.import_vehicle')}}</a>
    </div>
    <div class="table-responsive">
        <table id="product-data-table" class="table" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.name')}}</th>
                    <th>{{trans('file.Code')}}</th>
                    <th>{{trans('file.Brand')}}</th>
                    <th>{{trans('file.category')}}</th>
                    <th>Total</th> 
                    <th>Raw Vehicles</th>
                    <th>In Progress</th>
                    <th>Finished</th>
                    <th>Product</th>
                    <th  class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
           
           
        </table>
    </div>
</section>

<div id="importProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" enctype="multipart/form-data" method="POST" action="/vehicle/import" >
            {!! csrf_field() !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{__('file.import_vehicle')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
           <p>{{trans('file.The correct column order is')}} (name*, code*, brand, category*, chassis_no*, engine_no*, purchase_price*, warehouse*, product_details) {{trans('file.and you must follow this')}}.</p>
          
           <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label><strong> PFI Number *  </strong> </label>
                        <select id="import_proforma_id"  value=""  name="pfi_id" class="form-control selectpicker" required title="Please select PFI Number...">
                            <option value="">Please select proforma ...</option> 
                            @foreach($proformas as $warehouse1)
                            <option value="{{$warehouse1->id}}">{{$warehouse1->pfi_number}}</option> 
                            @endforeach
                        </select> 
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{trans('file.Upload CSV File')}} * </label>
                        <input type="file"  class="form-control" required  accept=".csv"  id="file" name="file">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label> {{trans('file.Sample File')}}</label>
                        <a href="{{asset('/sample_file/sample_vehicles.csv')}}" class="btn btn-info btn-block btn-md"><i class="dripicons-download"></i>  {{trans('file.Download')}}</a>
                      
                    </div>
                </div>
           </div>
           <div class="form-group">
            <input   type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
          </div>
        </div>
    </form>
      </div>
    </div>
</div> 
<!--  Detail Modal -->
<div id="car-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" style="max-width: 980px;;" class="modal-dialog modal-fullscreen-xxl-down">
        <div  class="modal-content">
            <div class="container mt-3 pb-2 border-bottom">
                <div class="row">
                    <div class="col-md-12 d-print-none">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="col-md-12 text-center">
                        <h2 style="font-size: 15px; font-family:Arial;">  VEHICLE DETAILS</h2>
                    </div>
                </div>
            </div>
            <div id="vehicle-content" class="modal-body">
            </div>
            <div class="col-md-12 text-center">
                <h2 style="font-size: 15px; font-family:Arial;"> List of Raw Vehicles</h2>
            </div>
            <table class="table table-bordered vehicle-list">
                <thead>
                    <th>#</th>
                    <th>Name</th>
                    <th>Chassis No</th>
                    <th>Engine No</th>
                    <th>Added By</th>
                    <th>Action</th>
                </thead>
                <tbody>
               </tbody>
            </table>
            <div class="col-md-12 text-center">
                <h2 style="font-size: 15px; font-family:Arial;"> List Of Vehicles In Progress</h2>
            </div>
            <table class="table table-bordered manufacture-list">
                <thead>
                    <th>#</th>
                    <th>Name</th>
                    <th>Chassis No</th>
                    <th>Engine No</th>
                    <th>Added By</th>
                    <th>Date</th>
                </thead>
                <tbody>
               </tbody>
            </table>
            <div class="col-md-12 text-center">
                <h2 style="font-size: 15px; font-family:Arial;"> List Of Finished(Manufactured) Vehicles</h2>
            </div>
            <table class="table table-bordered finished-list">
                <thead>
                    <th>#</th>
                    <th>Name</th>
                    <th>Chassis No</th>
                    <th>Engine No</th>
                    <th>Added By</th>
                    <th>Date</th>
                </thead>
                <tbody>
               </tbody>
            </table>
            <div class="col-md-12 text-center">
                <h2 style="font-size: 15px; font-family:Arial;"> List Of Vehicle Products</h2>
            </div>
            <table class="table table-bordered product-list">
                <thead>
                    <th>#</th>
                    <th>Name</th>
                    <th>Chassis No</th>
                    <th>Engine No</th>
                    <th>Added By</th>
                    <th>Date</th>
                </thead>
                <tbody>
               </tbody>
            </table>
          
            <br>

        </div>
    </div>
</div> 
<!-- Item Edit Modal -->
<div id="editVehicle" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" id="form-update-vehicle" >
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"><i class="fa fa-edit"> </i> Update  Raw Vehicle </h3>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <p class="italic" style="margin-left: 20px;"><small>All fields are required input fields</small></p>
        <div class="modal-body">
            
           <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <input type="hidden" name="chassis_old" id="chassis_old">
                        <input type="hidden" name="engine_old" id="engine_old">
                        <label><strong> Chassis Number * </strong> </label>
                        <input type="text" value="" class="form-control"    id="chassis_num" name="chassis_num" required="true" placeholder="Enter Chassis Number ...">
                        <div id="chassis_error_msg" class="chassis-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong> Engine Number * </strong> </label>
                        <input type="text" value="" class="form-control"   id="engine_num" name="engine_num" required="true" placeholder="Enter Engine Number ...">
                        <div id="engine_error_msg" class="engine-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
           </div>
           <div class="form-group" style="margin-left: 20px;">
            <input id="updateVehicle"  type="submit" value="Update Vehicle" class="btn btn-primary">
          </div>
          <br>
        </div>
    </form>
      </div>
    </div>
</div>
<!--- Add To Manufacture Modal -->
<div id="manufactureModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" id="form-add-manufacture">
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"> Add Vehicles To Manufacture (<span id="raw-name"> </span>) </h3>
          <button type="button"  data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" style="color:black;"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body"> 
           <div class="row" style="margin-left: 80px;">
                    <div class="form-group col-md-6"> 
                        <input type="hidden" name="manufacture_id" id="manufacture_id">
                        <label><strong> Warehouse * </strong> </label>
                         <select id="warehouse_id"   name="warehouse_id" class="form-control col-md-12 selectpicker" title="Select warehouse...">
                            @foreach($warehouse as $warehouse1)
                            <option value="{{$warehouse1->id}}">{{$warehouse1->name}}</option>
                            @endforeach
                         </select> 
                        <div id="warehouse_error_msg" class="chassis-error-msg text-danger" style="display:none; font-size: 16px; font-family: Bodoni MT;"></div>
                    </div>
           </div>
            <div class="row" style="margin-left: 80px;">
                <div class="form-group col-md-12">
                    <label class="d-flex align-items-center fs-6 fw-bold mb-2" ><strong>  Raw Vehicles * ( List format: Chassis No[Engine No]) </strong> </label>
                     <div class="form-check"> <label class="form-check-label" style="width: 100%; margin-left:20px; font-size:18px; font-family:times-new-roman; "><strong><input style="height:20px; width:20px;" class="form-check-input all-check" id="all_check"  type="checkbox"  value="" >&nbsp;&nbsp; <span> Select All</span> </strong> </label> </div>
                    <div  id="vehicle-checkboxes" class="form-check">
                       
                       
                    </div>
                </div>
           </div>
           <br>
        </div>
        <div class="modal-footer flex-left">
            <button style="margin-right: 20px;" type="submit" id="manufacture-btn" disabled class="btn btn-primary">
               <span class="indicator-label u-c">Add (0) Vehicles </span>
            </button>
        </div>
        <br>
    </form>
      </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script>
$("ul#vehicle").siblings('a').attr('aria-expanded','true');
$("ul#vehicle").addClass("show");
$("ul#vehicle #vehicle-list-menu").addClass("active");
var chassis = [];
var engine = [];
var user_id = [];
var idd = [];
var allEngine = [];
var allChassis = [];
var myArray1 = new Array();
var myArray = new Array();
    $(document).ready(function() {
        $(document).on("click", "#vehicle_check", function(){
            if(this.checked)
            {
                $(this).prop('checked', true);
            }
            else
            {
                $(this).prop('checked', false);
            }
            var Length = $('.vehicle-check:checked').length;
            if(Length == 0){
                $('#manufacture-btn').attr('disabled', 'disabled');
            }else{ 
                $('#manufacture-btn').removeAttr('disabled');
            }
            if ($('.vehicle-check:checked').length == $('.vehicle-check').length) {
                $(".all-check").prop('checked', true);
               }
            if ($('.vehicle-check:checked').length != $('.vehicle-check').length) {
                $(".all-check").prop('checked', false);
                }
            $('#manufacture-btn span').text('Add ('+Length+') Vehicles');

        });
    });
    $(document).ready(function(){
        $(document).on("click", "#all_check", function(){
            if(this.checked){
                $('.vehicle-check').each(function(){
                    $(".vehicle-check").prop('checked', true);
                })
            }else{
                $('.vehicle-check').each(function(){
                    $(".vehicle-check").prop('checked', false);
                })
            }
            var Length = $('.vehicle-check:checked').length;
            if(Length == 0){
                $('#manufacture-btn').attr('disabled', 'disabled');
            }else{ 
                $('#manufacture-btn').removeAttr('disabled');
            }
            $('#manufacture-btn span').text('Add ('+Length+') Vehicles');
        });
    }); 
    $("#manufacture-btn").click(function(e){
        e.preventDefault();
        if($('.vehicle-check:checked').length == 0){
            var value = "Please Select Vehicle First ...";
            $("#warehouse_error_msg").css('display','block');
            $("#warehouse_error_msg").text(value);
            return false;
        }
        else if($('#warehouse_id').val() == ""){
            var value = "Please Select Warehouse ...";
            $("#warehouse_error_msg").css('display','block');
            $("#warehouse_error_msg").text(value);
            return false;
        }
        else{
            $("#warehouse_error_msg").css('display','none');

        }
        var data = $('#form-add-manufacture').serialize();
        $.ajax({
            url:"{{ route('vehicle.manufacture.add')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                if($.isEmptyObject(data.error)){
                    var msg = 'Vehicle added to manufacture successfully.';
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
    $("#updateVehicle").click(function(e){
            e.preventDefault();
            $.each(allChassis, function(index){
                if($('#chassis_old').val() != allChassis[index])
                {
                    myArray.push(allChassis[index]);
                }
            });
            $.each(allEngine, function(index){
                if($('#engine_old').val() != allEngine[index])
                {
                myArray1.push(allEngine[index]);
                }
            });
            if($('#chassis_num').val() == ""){
                var value = "Please enter chassis number ...";
                $(".chassis-error-msg").css('display','block');
                $(".chassis-error-msg").text(value);
                $(".engine-error-msg").css('display','none');
                return false;
            }
            else if(jQuery.inArray($('#chassis_num').val(),myArray) !== -1){
                    var value = "Chassis Number already exists. Please add unique one.";
                    $(".chassis-error-msg").css('display','block');
                    $(".chassis-error-msg").text(value);
                    $(".engine-error-msg").css('display','none');
                    return false;
                }
            else if($('#engine_num').val() == ''){
                var value = "Please enter engine number ...";
                $(".engine-error-msg").css('display','block');
                $(".engine-error-msg").text(value);
                $(".chassis-error-msg").css('display','none');
                return false;
                }
            else if(jQuery.inArray($('#engine_num').val(),myArray1) !== -1){
                var value = "Engine No already exists. Please add different one.";
                $(".engine-error-msg").css('display','block');
                $(".engine-error-msg").text(value);
                $(".chassis-error-msg").css('display','none');
                return false;
                } 
            else{
                    $(".chassis-error-msg").css('display','none');
                    $(".engine-error-msg").css('display','none');
                }
            var data = $('#form-update-vehicle').serialize();
            $.ajax({
                url:"{{ route('vehicle.raw.update')}}",
                type:"POST",
                data:{
                    "_token" : "{{ csrf_token() }}",
                    "data":data,
                },
                success:function(data){
                    if($.isEmptyObject(data.error)){
                        var msg = 'Vehicle updated successfully.';
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
    $(document).on("click", "#DeleteVehicle", function(){
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        swal.fire({ 
            title: "If you delete this vehicle, all vehicles (raw , In progress , Finished and Product) under it will also be deleted. Are you sure you want to delete ?",
            text: "You will not be able to revert this action.",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0275d8",
            confirmButtonText: "Yes, Delete it!",
            closeOnConfirm: false,
            preConfirm: function(result) {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'GET',
                    url: "{{url('/vehicle/delete')}}/" + id,
                    dataType: 'JSON',
                    success: function (results) {
                        if (results.success === true) {
                            swal.fire({
                                    title: "Success",
                                    text: "Vehicle "+name+" deleted successfully.",
                                    icon: "success",
                                    button: "Ok"
                                }).then(function(){ 
                                    location.reload();
                                });
                        }else{
                            swal.fire({
                                title: "Error!",
                                text: "Failed to Delete!",
                                icon: "error",
                                button: "Ok",
                            }).then(function(){ 
                                    location.reload();
                                });
                        }

                    }
                })
            },
            allowOutsideClick: false
        });
    }); 

    var product_id = [];


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }); 
    // View detail javascript
    $(document).on("click", "tr.vehicle-link td:not(:first-child, :last-child)", function() {
        var vehicle = $(this).parent().data('vehicle');
        vehicleDetails(vehicle);
    });
    $(document).on("click", ".view-vehicle", function(){
        var vehicle = $(this).parent().parent().parent().parent().parent().data('vehicle');
        vehicleDetails(vehicle);
    }); 
    $("table.vehicle-list").on("click", ".edit-vehicle-btn", function(event) {
        $(".edit-vehicle-btn").attr('data-clicked', true);
        var id = $(this).data('id').toString();
        $.each(idd, function(index){
            if(idd[index] == parseFloat(id)){
                $('#editVehicle input[name="edit_id"]').val(idd[index]);
                $('#editVehicle input[name="chassis_num"]').val(chassis[index]);
                $('#editVehicle input[name="chassis_old"]').val(chassis[index]);
                $('#editVehicle input[name="engine_num"]').val(engine[index]);
                $('#editVehicle input[name="engine_old"]').val(engine[index]);
                return false;
            }
        });
        $('#car-details').modal('hide');
    });
    $("table.vehicle-list").on("click", ".delete-vehicle-btn", function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
       // var name = $(this).attr('data-name');
        swal.fire({ 
            title: "Are you sure want to delete the raw vehicle ?",
            text: "You will not be able to revert this action.",
            type: "warning",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0275d8",
            confirmButtonText: "Yes, Delete it!",
            closeOnConfirm: false,
            preConfirm: function(result) {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $('#car-details').modal('hide');
                $.ajax({
                    type: 'GET',
                    url: "{{url('/vehicle/raw/delete')}}/" + id,
                    dataType: 'JSON',
                    success: function (results) {
                        if (results.success === true) {
                            swal.fire({ 
                                    title: "Success",
                                    text: "Raw Vehicle Deleted Successfully.",
                                    icon: "success",
                                    button: "Ok"
                                }).then(function(){ 
                                window.location.href='{{ route('vehicle.index') }}';
                                });
                        }else{
                            swal.fire({
                                title: "Error!",
                                text: "Failed to Delete!",
                                icon: "error",
                                button: "Ok",
                            })
                        }

                    }
                })
            },
            allowOutsideClick: false
        });
    });
    $(document).on("click", ".AddtoManufacture", function(){
            var id = $(this).data('id');
            var car_name = $(this).data('name');
            $("#manufactureModal span[id='raw-name']").text(car_name);
            $("#manufactureModal input[name='manufacture_id']").val(id);
            $('#vehicle-checkboxes').html("");
            $.get('vehicles/getvehicle/' + id, function(data) {
                var car_id = data[0]; 
                var ware = data['warehouse'];
                var car_chassis = data[1];
                var car_engine = data[2];
                $.each(car_id, function(index){
                    var car_list = '<label class="form-check-label" style="width: 100%; margin-left:20px; font-size:21px; font-family:times-new-roman; "><input style="height:20px; width:20px;" id="vehicle_check" class="form-check-input vehicle-check"  type="checkbox" name="vehicle_id[]" value="'+car_id[index]+'" >&nbsp;&nbsp; <span> '+car_chassis[index]+' [ '+car_engine[index]+' ]</span></label>';
                    $('#vehicle-checkboxes').append(car_list);
                });
                $("#manufactureModal select[name='warehouse_id']").val(ware);
                $('.selectpicker').selectpicker('refresh');
                $('#manufactureModal').modal('show');  
                $('#manufactureModal input[type="checkbox"]').prop('checked', false);  
            });
    });
        var table = $('#product-data-table').DataTable( {
            responsive: true,
            fixedHeader: {
                header: true,
                footer: true
            },
            "processing": true,
            "serverSide": true,
            "ajax":{
                url:"{{ route('vehicles.data')}}",
                data:{
                "_token" : "{{ csrf_token() }}",
                },
                dataType: "json",
                type:"post"
            },
            "createdRow": function( row, data, dataIndex ) {
                $(row).addClass('vehicle-link');
                $(row).attr('data-vehicle', data['vehicle']);
            },
            "columns": [
                {"data": "key"},
                {"data": "name"},
                {"data": "code"},
                {"data": "brand"},
                {"data": "category"},
                {"data": "qty"},
                {"data": "raw_qty"},
                {"data": "manufacture"},
                {"data": "finished"},
                {"data": "product"},
                {"data": "options"},
            ],
            'language': {
                /*'searchPlaceholder': "{{trans('file.Type Product Name or Code...')}}",*/
                'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
                 "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                "search":  '{{trans("file.Search")}}',
                'paginate': {
                        'previous': '<i class="dripicons-chevron-left"></i>',
                        'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            order:[['1', 'asc']],
            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [0,6, 7,8,9,10]
                },
                {
                    'render': function(data, type, row, meta){
                        if(type === 'display'){
                            data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                        }

                       return data;
                    },
                    'checkboxes': {
                       'selectRow': true,
                       'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                    },
                    'targets': [0]
                }
            ],
            'select': { style: 'multi', selector: 'td:first-child'},
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: '<"row"lfB>rtip',
            buttons: [
                {
                    extend: 'pdf',
                    text: '<i title="export to pdf" class="fa fa-file-pdf-o"> PDF</i>',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                        stripHtml: false
                    },
                }, 
                {
                    extend: 'csv',
                    text: '<i title="export to csv" class="fa fa-file-text-o"> CSV</i>',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                        format: {
                            body: function ( data, row, column, node ) {
                                if (column === 0 && (data.indexOf('<img src=') !== -1)) {
                                    var regex = /<img.*?src=['"](.*?)['"]/;
                                    data = regex.exec(data)[1];
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    text: '<i title="print" class="fa fa-print"> Print</i>',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                        stripHtml: false
                    }
                },
                {
                    text: '<i title="delete selected" class="dripicons-cross"></i>',
                    className: 'buttons-delete',
                    action: function ( e, dt, node, config ) {
                            product_id.length = 0;
                            $(':checkbox:checked').each(function(i){
                                if(i){
                                    var sale = $(this).closest('tr').data('vehicle');
                                    product_id[i-1] = sale[0];
                                }
                            });
                            if(product_id.length ) {
                            swal.fire({ 
                            title: "If you delete these vehicles, all vehicles (raw , In progress , Finished and Product) under them will also be deleted. Are you sure you want to delete ?",
                            text: "You will not be able to revert this action.",
                            type: "warning",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#0275d8",
                            confirmButtonText: "Yes, Delete it!",
                            closeOnConfirm: false,
                            preConfirm: function(result) {    
                            $.ajax({
                                type:'POST',
                                url:"{{ route('vehicles.deleteselected')}}",
                                data:{
                                    "_token" : "{{ csrf_token() }}",
                                    brandIdArray: product_id,
                                },
                                success:function(data){                                   
                                     if($.isEmptyObject(data.error)){
                                        var msg = "Selected records deleted successfully.";
                                        swal.fire({
                                            title: "Success",
                                            text: msg, 
                                            icon: "success",
                                            button: "Ok"
                                        }).then(function(){ 
                                            window.location.href='{{ route('vehicle.index') }}';
                                        });
                                        }else{
                                            console.log(data.error);
                                        }
                                        
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                           
                        }
                        else if(!product_id.length)
                        {
                            alert('No record is selected!');
                        }
                        else
                        {
                            alert('You cancelled the action !');
                        }
                      
                    
                   
                }
            },
              {
                    extend: 'colvis',
                    text: '<i title="column visibility" class="fa fa-eye"></i>',
                    columns: ':gt(0)'
                },
          
        ],
        });
    function vehicleDetails(vehicle)
    {  
        var dtl = ""; 
        if(vehicle[10] != "")
        {
            dtl = vehicle[10];
        }
        else
        { 
            dtl = "N/A";
        }
        var htmltext = '<div class="row"><div class="col-md-4" style="font-size:14px;"><strong> Name :&nbsp;&nbsp;</strong>'+vehicle[1]+'<br> <strong> Brand :&nbsp;&nbsp;</strong>'+vehicle[3]+'<br> <strong> Warehouse :&nbsp;&nbsp;</strong>'+vehicle[7]+'</div><div class="col-md-4" style="font-size:14px;"><strong> Code :&nbsp;&nbsp;</strong>'+vehicle[2]+'<br> <strong> Purchase Price :&nbsp;&nbsp;</strong>'+vehicle[5]+ '<br> <strong> Created By :&nbsp;&nbsp;</strong>'+vehicle[8]+'</div><div class="col-md-4" style="font-size:14px;"><strong> Category :&nbsp;&nbsp;</strong>'+vehicle[4]+'<br> <strong> Alert Quantity :&nbsp;&nbsp;</strong>'+vehicle[6]+'<br> <strong> Created On :&nbsp;&nbsp;</strong>'+vehicle[9]+'</div></div>';
        var htmltext1 =  '<div class="row"><div class="col-md-12" style="font-size:14px;"> <strong> Vehicle Detail :&nbsp;&nbsp;</strong> <span>  '+dtl+'  </span> </div></div>';
          $.get('/vehicles/get/raw-vehicle/' + vehicle[0], function(data)
            {                      
                allChassis = data[4];
                allEngine = data[5];
                var myChassis = data[21];
                var myEngine = data[22];
                var proChassis = data[23];
                var proEngine = data[24];
                $.each(myChassis, function(index){
                    myArray.push(myChassis[index]);
                });
                $.each(proChassis, function(index){
                    myArray.push(proChassis[index]);
                });
                $.each(myEngine, function(index){
                    myArray1.push(myEngine[index]);
                });
                $.each(proEngine, function(index){
                    myArray1.push(proEngine[index]);
                });
                $(".vehicle-list tbody").remove();
                $(".manufacture-list tbody").remove();
                $(".finished-list tbody").remove();
                $(".product-list tbody").remove();
                if(data[1] == null && data[2] == null)
                {
                    var newBody = $("<tbody>");
                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td style="border:none;" colspan=3></td>';
                    cols += '<td style="border:none;" colspan=2>No raw vehicle data is avaliable.</td>';
                    cols += '<td style="border:none;" colspan=1></td>';
                    newRow.append(cols);
                    newBody.append(newRow);
                  $("table.vehicle-list").append(newBody);
                }
                else
                {
                    idd = data[0];
                    chassis = data[1];
                    engine = data[2];
                    user_id = data[3];
                    var newBody = $("<tbody>");
                    $.each(idd, function(index){
                        var newRow = $("<tr>");
                        var cols = '';
                        cols += '<td><strong>' + (index+1) + '</strong></td>';
                        cols += '<td>' + vehicle[1] + '</td>';
                        cols += '<td>' + chassis[index] + '</td>';
                        cols += '<td>' + engine[index] + '</td>';
                        cols += '<td>' + user_id[index] + '</td>';
                        cols += '<td><div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans("file.action")}}<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';
                        cols += '<li><button type="button" class="btn btn-link edit-vehicle-btn" data-id="' + idd[index] +'" data-clicked=false data-toggle="modal" data-target="#editVehicle"><i class="dripicons-document-edit"></i> {{trans("file.edit")}}</button></li> ';
                        cols += '<li><button type="button" class="btn btn-link delete-vehicle-btn" data-id="' + idd[index] +'"  ><i class="dripicons-trash"></i> Delete </button></li> ';
                        cols += '</ul></div></td>';
                        newRow.append(cols);
                        newBody.append(newRow);
                    });
                  $("table.vehicle-list").append(newBody);
                }
                if(data[7] == null && data[8] == null)
                {
                    var newBody = $("<tbody>");
                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td style="border:none;" colspan=3></td>';
                    cols += '<td style="border:none;" colspan=2>No vehicle In-progress data is avaliable.</td>';
                    cols += '<td style="border:none;" colspan=1></td>';
                    newRow.append(cols);
                    newBody.append(newRow);
                  $("table.manufacture-list").append(newBody);
                }
                else
                {
                    var midd = data[6];
                    var mchassis = data[7];
                    var mengine = data[8];
                    var muser_id = data[9];
                    var mdate = data[10];
                    var newBody = $("<tbody>");
                    $.each(midd, function(index){
                        var newRow = $("<tr>");
                        var cols = '';
                        cols += '<td><strong>' + (index+1) + '</strong></td>';
                        cols += '<td>' + vehicle[1] + '</td>';
                        cols += '<td>' + mchassis[index] + '</td>';
                        cols += '<td>' + mengine[index] + '</td>';
                        cols += '<td>' + muser_id[index] + '</td>';
                        cols += '<td>' + mdate[index] + '</td>';
                        newRow.append(cols);
                        newBody.append(newRow);
                    });
                    $("table.manufacture-list").append(newBody);
                }
                if(data[12] == null && data[13] == null)
                {
                    var newBody = $("<tbody>");
                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td style="border:none;" colspan=3></td>';
                    cols += '<td style="border:none;" colspan=2>No finished vehicle data is avaliable.</td>';
                    cols += '<td style="border:none;" colspan=1></td>';
                    newRow.append(cols);
                    newBody.append(newRow);
                 $("table.finished-list").append(newBody);
                }
                else
                {
                    var fidd = data[11];
                    var fchassis = data[12];
                    var fengine = data[13];
                    var fuser_id = data[14];
                    var fdate = data[15];
                    var newBody = $("<tbody>");
                    $.each(fidd, function(index){
                        var newRow = $("<tr>");
                        var cols = '';
                        cols += '<td><strong>' + (index+1) + '</strong></td>';
                        cols += '<td>' + vehicle[1] + '</td>';
                        cols += '<td>' + fchassis[index] + '</td>';
                        cols += '<td>' + fengine[index] + '</td>';
                        cols += '<td>' + fuser_id[index] + '</td>';
                        cols += '<td>' + fdate[index] + '</td>';
                        newRow.append(cols);
                        newBody.append(newRow);
                    });
                 $("table.finished-list").append(newBody);
                }
                if(data[17] == null && data[18] == null)
                {
                    var newBody = $("<tbody>");
                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td style="border:none;" colspan=3></td>';
                    cols += '<td style="border:none;" colspan=2>No vehicle product data is avaliable.</td>';
                    cols += '<td style="border:none;" colspan=1></td>';
                    newRow.append(cols);
                    newBody.append(newRow);
                 $("table.product-list").append(newBody);
                }
                else
                {
                    var pidd = data[16];
                    var pchassis = data[17];
                    var pengine = data[18];
                    var puser_id = data[19];
                    var pdate = data[20];
                    var newBody = $("<tbody>");
                    $.each(pidd, function(index){
                        var newRow = $("<tr>");
                        var cols = '';
                        cols += '<td><strong>' + (index+1) + '</strong></td>';
                        cols += '<td>' + vehicle[1] + '</td>';
                        cols += '<td>' + pchassis[index] + '</td>';
                        cols += '<td>' + pengine[index] + '</td>';
                        cols += '<td>' + puser_id[index] + '</td>';
                        cols += '<td>' + pdate[index] + '</td>';
                        newRow.append(cols);
                        newBody.append(newRow);
                    });
                 $("table.product-list").append(newBody);
                }
                
            });
        $('#vehicle-content').html("");
        $('#vehicle-content').append(htmltext);
        $('#vehicle-content').append(htmltext1);
        $('#car-details').modal('show');
    }

</script>
@endpush

