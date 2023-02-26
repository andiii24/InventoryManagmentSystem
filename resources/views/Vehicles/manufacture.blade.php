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
           <h2> <li style="color: rgb(4, 0, 128);" class="fa fa-car"> </li>    Manufacture List   <li style="color: rgb(4, 0, 128);" class="fa fa-list"> </li></h2>
        </div>
    </div>
    <div class="table-responsive">
        <table id="product-data-table" class="table" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{trans('file.name')}}</th>
                    <th>{{trans('file.Code')}}</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Warehouse</th>
                    <th  class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>

        </table>
    </div>
</section>


<!-- Add Complete Modal -->
<div id="manufactureModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" id="form-add-finished">
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"> Add Vehicles To Finished Goods (<span id="raw-name"> </span>) </h3>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true" style="color:black;"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body"> 
            <div class="row" style="margin-left: 80px;">
                <input type="hidden" name="finish_id">
                <div class="form-group col-md-12">
                    <label class="d-flex align-items-center fs-6 fw-bold mb-2" ><strong> Vehicles In Progress* List format: Chassis No[Engine No] </strong> </label>
                     <div class="form-check"> <label class="form-check-label" style="width: 100%; margin-left:20px; font-size:16px; font-family:times-new-roman; "><strong><input style="height:20px; width:20px;" class="form-check-input all-check" id="all_check"  type="checkbox"  value="" >&nbsp;&nbsp; <span> Select All</span> </strong> </label> </div>
                    <div  id="vehicle-checkboxes" class="form-check">
                       
                       
                    </div>
                    <div id="warehouse_error_msg" class="chassis-error-msg text-danger" style="display:none; font-size: 16px; font-family: Bodoni MT;"></div>
                </div>
                <br>
                <div class="form-group">
                    <button style="float: left; margin-left: 200px; margin-bottom: 10px;" type="submit" id="manufacture-btn" disabled class="btn btn-primary">
                    <span class="indicator-label u-c">Add (0) Vehicles </span>
                    </button>
                    <br>
                 </div>
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
$("ul#vehicle #vehicle-manufacture-menu").addClass("active");
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
    });
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
    $(document).on("click", ".Add-Complete", function(){
            var id = $(this).data('id');
            var car_name = $(this).data('name');
            $("#manufactureModal span[id='raw-name']").text(car_name);
            $("#manufactureModal input[name='finish_id']").val(id);
            $('#vehicle-checkboxes').html("");
            $.get('/vehicle/manufacture/get/' + id, function(data) {
                var car_id = data[0];
                var car_chassis = data[1];
                var car_engine = data[2];
                $.each(car_id, function(index){
                    var car_list = '<label class="form-check-label" style="width: 100%; margin-left:20px; font-size:19px; font-family:times-new-roman; "><input style="height:20px; width:20px;" id="vehicle_check" class="form-check-input vehicle-check"  type="checkbox" name="vehicle_id[]" value="'+car_id[index]+'" >&nbsp;&nbsp; <span> '+car_chassis[index]+' [ '+car_engine[index]+' ]</span></label>';
                    $('#vehicle-checkboxes').append(car_list);
                });
                $('#manufactureModal').modal('show');  
                $('#manufactureModal input[type="checkbox"]').prop('checked', false);  
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
        else{
            $("#warehouse_error_msg").css('display','none');

        }
        var data = $('#form-add-finished').serialize();
        $.ajax({
            url:"{{ route('manufacture.complete.add')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                if($.isEmptyObject(data.error)){
                    var msg = 'Vehicles added to finished goods successfully.';
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


    var product_id = [];

    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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
                url:"{{ route('manufacture.data')}}",
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
                {"data": "brand"},
                {"data": "category"},
                {"data": "qty"},
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
                    'targets': [ 0,2,5,6, 7]
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

