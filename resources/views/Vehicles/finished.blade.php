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
        <div class="d-flex align-items-center py-1">
           <h2> Finished Goods List   <li style="color: rgb(4, 0, 128);" class="fa fa-list"> </li></h2>
        </div>
    </div>
    <div class="table-responsive">
        <table id="product-data-table" class="table" style="width: 100%">
            <thead>
                <tr>
                    <th># </th>
                    <th>{{trans('file.name')}}</th>
                    <th>{{trans('file.Code')}}</th>
                    <th>Chassis No</th> 
                    <th>Engine No</th>
                    <th>Purchase Price</th>
                    <th>Warehouse</th>
                    <th  class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>

        </table>
    </div>
</section>
<!-- Add to product Modal -->
<div id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" id="form-add-product" >
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"> Add Vehicle To POS (Product)  <i class="fa fa-car" aria-hidden="true"></i></h3>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" style="color: black;"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
           <div class="row">
                <div  class="col-md-6">
                    <div class="form-group">
                       <input type="hidden" name="pos_id" >
                       <label><strong> Sell Price *</strong> </label>
                       <input type="text" id="price" onkeypress="return onlyNumberKey(event)" name="price" placeholder="Enter vehicle sell price..." required class="form-control" step="any">
                       <div id="price_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 15px; font-family: Bodoni MT;"> </div>
                   </div>
               </div>
                <div class="col-md-6">
                    <div class="form-group"> 
                    <label><strong>Warehouse * </strong> </label>
                    <select id="warehouse_id"   name="warehouse_id" class="form-control selectpicker" title="Select warehouse...">
                    <option value="">Select Warehouse</option>
                      @foreach($warehouse as $warehouse1)
                      <option value="{{$warehouse1->id}}">{{$warehouse1->name}}</option>
                      @endforeach
                  </select> 
                  <div id="warehouse_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
                </div>
               </div>
               
           </div>
           <br>
           <div class="form-group text-right" style="margin-right: 100px;">
            <input id="createProduct"  type="submit" value="Add To POS" class="btn btn-info">
          </div>
        </div>
    </form>
      </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" id="form-update-vehicle">
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"> Update  Vehicle  <i class="fa fa-pen" aria-hidden="true"></i></h3>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" style="color: black;"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
           <div class="row">
                <div  class="col-md-6">
                    <div class="form-group">
                       <input type="hidden" name="edit_id" >
                       <label><strong> Chassis Number *</strong> </label>
                       <input type="text" id="chassis"  name="chassis" required class="form-control" step="any">
                       <div id="chassis_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 15px; font-family: Bodoni MT;"> </div>
                   </div>
               </div>
               <div  class="col-md-6">
                    <div class="form-group">
                    <label><strong> Engine Number *</strong> </label>
                    <input type="text" id="engine"  name="engine" required class="form-control" step="any">
                    <div id="engine_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 15px; font-family: Bodoni MT;"> </div>
                </div>   
               </div>     
           </div>
           <br>
           <div class="form-group text-right" style="margin-right: 100px;">
            <input id="submit-btn"  type="submit" value="Update" class="btn btn-primary">
          </div>
        </div>
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
$("ul#vehicle #vehicle-finished-menu").addClass("active");
function onlyNumberKey(evt) {
          var ASCIICode = (evt.which) ? evt.which : evt.keyCode
          if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
              return false;
          return true;
}
var allEngine = [];
var allChassis = [];
var allEngine1 = [];
var allChassis1 = [];
var allEngine2 = [];
var allChassis2 = [];
var engineArray = new Array();
var chassisArray = new Array();
    $("#createProduct").click(function(e){
        e.preventDefault();
        if(!$('#price').val() || $('#price').val() == 0){
            var value = "Please enter vehicle sell price ...";
            $("#price_error_msg").css('display','block');
            $("#price_error_msg").text(value);
            $("#warehouse_error_msg").css('display','none');
            $("#price").css('borde-color','red');
            $("#warehouse_id").css('borde-color','gray');
            return false;
        }
        else if($('#warehouse_id').val() == ''){
            var value = "Please select warehouse ...";
            $("#warehouse_error_msg").css('display','block');
            $("#warehouse_error_msg").text(value);
            $("#price_error_msg").css('display','none');
            $("#warehouse_id").css('borde-color','red');
            $("#price").css('borde-color','green');
            return false;
        }
        else{
            $("#warehouse_error_msg").css('display','none');
            $("#price_error_msg").css('display','none');
            $("#warehouse_id").css('borde-color','red');
            $("#price").css('borde-color','green');
        }
        var data = $('#form-add-product').serialize();

        $.ajax({
            url:"{{ route('vehicle.product.add')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data, 
            },
            success:function(data){
                if($.isEmptyObject(data.error)){
                    var msg = 'Vehicle is added to product (POS) successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        location.reload();
                    });
                }else{
                    printErrorMsg(data.error);
                }
            }
        });
    });
    $("#submit-btn").click(function(e){
        e.preventDefault();
        if($('#chassis').val() == ""){
            var value = "Please Enter Chassis No ...";
            $("#chassis_error_msg").css('display','block');
            $("#chassis_error_msg").text(value);
            $("#engine_error_msg").css('display','none');
            return false;
        }
        else if(jQuery.inArray($('#chassis').val(),chassisArray) !== -1){
            var value = "Chassis Number already exists. Please add unique one.";
            $("#chassis_error_msg").css('display','block');
            $("#chassis_error_msg").text(value);
            $("#engine_error_msg").css('display','none');
            return false;
        }
        else if($('#engine').val() == ""){
            var value = "Please Enter Engine Number ...";
            $("#engine_error_msg").css('display','block');
            $("#engine_error_msg").text(value);
            $("#chassis_error_msg").css('display','none');
            return false;
        }
        else if(jQuery.inArray($('#engine').val(),engineArray) !== -1){
            var value = "Engine Number already exists. Please add unique one.";
            $("#engine_error_msg").css('display','block');
            $("#engine_error_msg").text(value);
            $("#chassis_error_msg").css('display','none');
            return false;
        }
        else{
            $("#chassis_error_msg").css('display','none');
            $("#engine_error_msg").css('display','none');
        }
        var data = $('#form-update-vehicle').serialize();

        $.ajax({
            url:"{{ route('vehicle.finished.update')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data, 
            },
            success:function(data){
                if($.isEmptyObject(data.error)){
                    var msg = 'Vehicle data updated successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        location.reload();
                    });
                }else{
                    printErrorMsg(data.error);
                }
            }
        });
    });

    $(document).on("click", ".AddProduct", function(){
        var idd = $(this).data('id');
        var wrse = $(this).data('name');
        $("#addProductModal input[name='pos_id']").val(idd);
        $("#addProductModal select[name='warehouse_id']").val(wrse);
        $('.selectpicker').selectpicker('refresh');
        $("#addProductModal").modal('show');
    });
    var product_id = [];

    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".edit-btn", function(){
            var id = $(this).data('id');
            $("#editModal input[name='edit_id']").val(id);
            $.get('/vehicle/finished/get/' + id, function(data) {
                $("#editModal input[name='chassis']").val(data['edit_ch']);
                $("#editModal input[name='engine']").val(data['edit_en']);
               //manufacture
                allChassis = data[0];
                allEngine = data[1]; 
                //vehicles
                allChassis1 = data[2];
                allEngine1 = data[3];
                //product
                allChassis2 = data[4];
                allEngine2 = data[5];
                //Push All chassis To Array
                $.each(allChassis, function(index){
                    chassisArray.push(allChassis[index]);
                });
                $.each(allChassis1, function(index){
                    chassisArray.push(allChassis1[index]);
                });
                $.each(allChassis2, function(index){
                    chassisArray.push(allChassis2[index]);
                });
                 //Push All Engine To Array
                $.each(allEngine, function(index){
                    engineArray.push(allEngine[index]);
                });
                $.each(allEngine1, function(index){
                    engineArray.push(allEngine1[index]);
                });
                $.each(allEngine2, function(index){
                    engineArray.push(allEngine2[index]);
                });
                $('#editModal').modal('show');  
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
                url:"{{ route('finished.data')}}",
                data:{
                "_token" : "{{ csrf_token() }}",
                },
                dataType: "json",
                type:"post"
            },
            "createdRow": function( row, data, dataIndex ) {
                
                $(row).attr('data-id', data['id']);
            },
            "columns": [
                {"data": "key"},
                {"data": "name"},
                {"data": "code"},
                {"data": "chassis"},
                {"data": "engine"},
                {"data": "cost"},
                {"data": "warehouse"},
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
                    'targets': [ 0,2,5,6,7]
                },
              
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
                    extend: 'print',
                    text: '<i title="print" class="fa fa-print"> Print</i>',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                        stripHtml: false
                    }
                },
            
              { 
                    extend: 'colvis',
                    text: '<i title="column visibility" class="fa fa-eye"></i>',
                    columns: ':gt(0)'
                },
          
        ],
    } );
</script>
@endpush

