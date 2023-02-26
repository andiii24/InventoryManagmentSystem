@extends('layout.main') @section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
    <br>
    <div class="container-fluid"> 
        <!-- Trigger the modal with a button -->
        <div class="d-flex align-items-center py-1">
            <h3 style="margin-left: 20px; font-family:arial; "> <i style="color: rgb(56, 3, 87); margin-right: 10px;" class="fa fa-list-alt"></i>  Booking Stage List   </h3>
         </div>
    </div>
    <div class="table-responsive">
        <table id="category-table" class="table table-striped sale-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>PFI Date</th>
                    <th>PFI Number</th>
                    <th>BL/Airway Bill No</th>
                    <th>Commercial Invoice No</th>
                    <th>No. of Container</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
        </table> 
    </div>


<!--  Detail Modal -->
<div id="sale-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" style="max-width: 80%;" class="modal-dialog modal-fullscreen-xxl-down">
        <div  class="modal-content">
            <div class="container mt-3 pb-2 border-bottom">
                <div class="row">
                    <div class="col-md-12 d-print-none">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="col-md-12 text-center">
                        <h2 style="font-size: 15px; font-family:Arial;">  PROFORMA INVOICE DETAILS</h2>
                    </div>
                </div>
            </div>
            <div id="proforma-content" class="modal-body">
            </div>
            <table class="table table-bordered proforma-item-list">
                <thead>
                    <th>#</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Amount</th>
                    <th>Added By</th>
                </thead>
                <tbody>
               </tbody>
            </table>
            <br>
        </div>
    </div>
</div> 
<!-- Add To Transition Modal -->
<div id="transitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" action="#" id="form-add-transit" >
        <div class="modal-header">
            <h1 id="exampleModalLabel" class="modal-title">   Add (<span id="book_pro_name"> </span>) From Booking To Transit Stage </h1>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div> 
        <div class="modal-body">
           <div id="booking-container-form" class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="hidden" name="transit_id" id="transit_id">
                        <label><strong> Transitor Name * </strong> </label>
                        <input type="text"  class="form-control"   id="transitor_name" name="transitor_name" required="true" placeholder="Enter transitor name ...">
                        <div id="name_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong> Operation Number * </strong> </label>
                        <input type="text"  class="form-control"   id="operation_number" name="operation_number" required="true" placeholder="Enter operation number ...">
                        <div id="operation_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
         </div>
           <div class="form-group" style="margin-left: 20px;">
            <input id="addTransition"  type="submit" value="Submit" class="btn btn-primary">
          </div>
        </div>
    </form>
      </div>
    </div>
</div>

@foreach($proformas as $ExpDoc)
        <input type="hidden" name="ref[]" value="{{ $ExpDoc->operation_number }}">
@endforeach
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-booking-menu").addClass("active");
    var billArray = new Array();
    $("input[name='ref[]']").each(function(){
        billArray.push($(this).val());
    });
    $("#addTransition").click(function(e){
        e.preventDefault();
        if ($('#transitor_name').val() == "" || $('#bill_number').val() == 0 ){
            var value = "Please Enter Transitor Name ...";
            $("#name_error_msg").css('display','block');
            $("#name_error_msg").text(value);
            $("#operation_error_msg").css('display','none');
            $('#transitor_name').css('border-color','red');
            $('#operation_number').css('border-color','green');
            return false;
        }  
        else if($('#operation_number').val() == ""){
            var value = "Please Enter Operation Number ...";
            $("#operation_error_msg").css('display','block');
            $("#operation_error_msg").text(value);
            $("#name_error_msg").css('display','none');
            $('#operation_number').css('border-color','red');
            $('#transitor_name').css('border-color','green');
            return false;
        }
        else if(jQuery.inArray($('#operation_number').val(),billArray) !== -1){
            var value = "Operation Number Exist. Please add unique one.";
            $("#operation_error_msg").css('display','block');
            $("#operation_error_msg").text(value);
            $("#name_error_msg").css('display','none');
            $('#operation_number').css('border-color','red');
            $('#transitor_name').css('border-color','green');
            return false;
        }
        else{ 
            $("#operation_error_msg").css('display','none');
            $("#name_error_msg").css('display','none');
            $('#operation_number').css('border-color','green');
            $('#transitor_name').css('border-color','green');
        }
        var data = $('#form-add-transit').serialize();
        $.ajax({
            url:"{{ route('purchase.proforma.transit.add')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            }, success:function(data){
                    var msg = 'Proforma Added To Transit Stage Successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success", 
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.transition') }}';
                    });
            }
        });
    });
    $.ajaxSetup({ 
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".AddTransitDialog", function(){
          var id = $(this).data('id');
          var name = $(this).data('name');
          $("#transitModal input[name='transit_id']").val(id);
          $("#transitModal span[id='book_pro_name']").text(name);
          $("#transitModal").modal('show');
    });
    // View detail and view items javascript
    $(document).on("click", "tr.proforma-link td:not(:first-child, :last-child)", function() {
        var sale = $(this).parent().data('proforma');
        saleDetails(sale);
    });
    $(document).on("click", ".pro-view", function(){
        var sale = $(this).parent().parent().parent().parent().parent().data('proforma');
        saleDetails(sale);
    });


    $('#category-table').DataTable( {
        responsive: true,
        "processing": true,
        "serverSide": true,
        "ajax":{ 
            url:"{{ route('purchase.booking-data')}}",
            data:{
                "_token" : "{{ csrf_token() }}",
                }, 
            dataType: "json",
            type:"post" 
        }, 
        "createdRow": function( row, data, dataIndex ) {
            $(row).addClass('proforma-link');
            $(row).attr('data-proforma', data['proforma']);
        }, 
        "columns": [
            {"data": "key"},
            {"data": "pfi_date"},
            {"data": "pfi_number"},
            {"data": "bill_number"},
            {"data": "commercial_invoice"},
            {"data": "container_no"},
            {"data": "options"},
        ], 
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        order:[['1', 'desc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0,2,5,6]
            },
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],

        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );  
                      
    function saleDetails(sale){
        if(sale[3] != "")
        {
            var byi = sale[3];
        }
        else
        {
            var byi = "N/A"; 
        }
        if(sale[8] != "")
        {
            var pti = sale[8];
        }
        else
        {
            var pti = "N/A"; 
        }
        var htmltext = '<div class="row"><div class="col-md-4" style="font-size:14px;"><strong> Supplier Name :&nbsp;&nbsp;</strong>'+sale[2]+'<br> <strong> PFI Number :&nbsp;&nbsp;</strong>'+sale[6]+'</div><div class="col-md-4" style="font-size:14px;"><strong> Buyer Name :&nbsp;&nbsp;</strong>'+byi+'<br> <strong> PFI Date :&nbsp;&nbsp;</strong>'+sale[5]+'</div><div class="col-md-4" style="font-size:14px;"><strong> Order Number :&nbsp;&nbsp;</strong>'+sale[4]+'<br> <strong> Created By :&nbsp;&nbsp;</strong>'+sale[9]+'</div></div>';
        var  htmltext1 =  '<div class="row"><div class="col-md-12"> <strong> Payment Term :&nbsp;&nbsp;</strong> <span style="font-family:georgia;">  '+pti+'  </span> </div></div>';
        var htmlbankhead = '<div class="row"> <div class="col-md-12 text-center"> <h2 style="font-family:georgia; font-size:18px; margin-top:10px;">Bank Submit Detail</h2> </div> </div>';
        var htmlbanktext =  '<div class="row"><div class="col-md-4" style="font-size:14px;"><strong> Bank Name :&nbsp;&nbsp;</strong>'+sale[7]+'<br> <strong> Permit Number :&nbsp;&nbsp;</strong>'+sale[11]+'</div><div class="col-md-4" style="font-size:14px;"><strong> Payment Method :&nbsp;&nbsp;</strong>'+sale[12]+'<br> <strong> '+sale[12]+'  Number :&nbsp;&nbsp;</strong>'+sale[13]+'</div><div class="col-md-4" style="font-size:14px;"><strong> Submitted On :&nbsp;&nbsp;</strong>'+sale[14]+'<br> <strong> Submitted By :&nbsp;&nbsp;</strong>'+sale[15]+'</div></div>';
        var htmlbookhead = '<div class="row"> <div class="col-md-12 text-center"> <h2 style="font-family:georgia; font-size:18px; margin-top:10px;">Booking Detail</h2> </div> </div>';
        var htmlbooktext =  '<div id="list_container" class="row"><div class="col-md-4" style="font-size:14px;"><strong> BL/Airway Bill No :&nbsp;&nbsp;</strong>'+sale[16]+'</div><div class="col-md-4" style="font-size:14px;"><strong> Commercial Invoice No :&nbsp;&nbsp;</strong>'+sale[17]+'</div><div class="col-md-4" style="font-size:14px;"><strong> Booked On :&nbsp;&nbsp;</strong>'+sale[18]+'<strong> By :&nbsp;&nbsp;</strong>'+sale[19]+'</div></div>';
        $.get('/purchases/proforma_item/' + sale[1], function(data){
            $(".proforma-item-list tbody").remove();
            var description = data[0];
            var qty = data[1];
            var unit_price = data[2];
            var total = data[3];
            var user = data[5];
            var newBody = $("<tbody>");
            $.each(description, function(index){
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td><strong>' + (index+1) + '</strong></td>';
                cols += '<td>' + description[index] + '</td>';
                cols += '<td>' + qty[index] + '</td>';
                cols += '<td>' + unit_price[index] + '</td>';
                cols += '<td>' + total[index] + '</td>';
                cols += '<td>' + user[index] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
            });
            var qty_sum = data[1].reduce((a, b) => a + b).toLocaleString();
            var amt_sum = data[3].reduce((a, b) => a + b).toLocaleString();
            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=2><strong>{{trans("file.Total")}}:</strong></td>';
            cols += '<td> <strong>' + qty_sum + '</strong></td>';
            cols += '<td></td>';
            cols += '<td><strong>' + amt_sum + '</strong></td>';
            cols += '<td></td>';
            newRow.append(cols);
            newBody.append(newRow);
            $("table.proforma-item-list").append(newBody);
        });
        $.get('/purchases/booking_containers/' + sale[1], function(data){
            var container = data[0];
            $.each(container, function(index){
               var conts = '<div class="col-md-4" style="font-size:14px;"><strong> Container '+(index+1) +' No :&nbsp;&nbsp;</strong>'+ container[index] +'</div>';
                $('#list_container').append(conts);
            });
        });
        $('#proforma-content').html(htmltext);
        $('#proforma-content').append(htmltext1);
        $('#proforma-content').append(htmlbankhead);
        $('#proforma-content').append(htmlbanktext);
        $('#proforma-content').append(htmlbookhead);
        $('#proforma-content').append(htmlbooktext);
     //   $('#proforma-content').append(itemhead);
        $('#sale-details').modal('show');
    }
</script>
@endpush