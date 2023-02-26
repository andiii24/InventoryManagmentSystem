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
            <h3 style="margin-left: 20px; font-family:arial; "> <i style="color: rgb(56, 3, 87); margin-right: 10px;" class="fa fa-list-alt"></i>  Transition Stage List   </h3>
         </div>
    </div>
    <div class="table-responsive">
        <table id="category-table" class="table table-striped sale-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>PFI Date</th>
                    <th>PFI Number</th>
                    <th>Transitor Name</th>
                    <th>Operation Number</th>
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
          
            <div class="col-md-12 d-print-none">
                <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span class="btn btn-link">Close</span></button>
            </div>
            <br>

        </div>
    </div>
</div> 
<!-- Add To Custom Modal -->
<div id="customModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" action="#" id="form-add-custom" >
        <div class="modal-header">
            <h1 id="exampleModalLabel" class="modal-title">   Add (<span id="book_pro_name"> </span>) From Transition To Custom Stage </h1>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div> 
        <div class="modal-body">
           <div id="booking-container-form" class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="hidden" name="custom_id" id="custom_id">
                        <label><strong> Declaration Number * </strong> </label>
                        <input type="text"  class="form-control"   id="declaration_no" name="declaration_no" required="true" placeholder="Enter declaration number ...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong> </strong> </label>
                        <div id="declaration_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 14px; margin-top: 10px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
            </div>
            <div class="form-group">
             &nbsp; &nbsp; <input id="addToCustom"  type="submit" value="Submit" class="btn btn-primary">
            </div>
        </div>
    </form>
      </div>
    </div>
</div>

@foreach($proformas as $ExpDoc)
        <input type="hidden" name="ref[]" value="{{ $ExpDoc->declaration_no }}">
@endforeach
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-transition-menu").addClass("active");
    var billArray = new Array();
    $("input[name='ref[]']").each(function(){
        billArray.push($(this).val());
    });
    $("#addToCustom").click(function(e){
        e.preventDefault();
        if ($('#declaration_no').val() == "" || $('#declaration_no').val() == 0 ){
            var value = "Please Enter Declaration Number ...";
            $("#declaration_error_msg").css('display','block');
            $("#declaration_error_msg").text(value);
            $('#declaration_no').css('border-color','red');
            return false;
        } 
        else if(jQuery.inArray($('#declaration_no').val(),billArray) !== -1){
            var value = "Declaration Number Exist. Please add unique one.";
            $("#declaration_error_msg").css('display','block');
            $("#declaration_error_msg").text(value);
            $('#declaration_no').css('border-color','red');
            return false;
        }
        else{ 
            $("#declaration_error_msg").css('display','none');
            $('#declaration_no').css('border-color','green');
        }
        var data = $('#form-add-custom').serialize();
        $.ajax({
            url:"{{ route('purchase.proforma.custom.add')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            }, success:function(data){
                    var msg = 'Proforma Added To Custom Stage Successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.custom-stage') }}';
                    });
            }
        });
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".AddCustomDialog", function(){
          var id = $(this).data('id');
          var name = $(this).data('name');
          $("#customModal input[name='custom_id']").val(id);
          $("#customModal span[id='book_pro_name']").text(name);
          $("#customModal").modal('show');
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
            url:"{{ route('purchase.transition-data')}}",
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
            {"data": "transitor_name"},
            {"data": "operation_number"},
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
                'targets': [0,2,5]
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
        var htmlbooktext =  '<div id="list_container" class="row"><div class="col-md-4"  style="font-size:14px;"><strong> BL/Airway Bill No :&nbsp;&nbsp;</strong>'+sale[16]+'</div><div class="col-md-4"  style="font-size:14px;"><strong> Commercial Invoice No :&nbsp;&nbsp;</strong>'+sale[17]+'</div><div class="col-md-4"  style="font-size:14px;"><strong> Booked On :&nbsp;&nbsp;</strong>'+sale[18]+'<strong> By :&nbsp;&nbsp;</strong>'+sale[19]+'</div></div>';
        var htmltransithead = '<div class="row"> <div class="col-md-12 text-center"> <h2 style="font-family:georgia; font-size:18px; margin-top:10px;">Transition Detail</h2> </div> </div>';
        var htmltransittext =  '<div class="row"><div class="col-md-4"  style="font-size:14px;"><strong> Transitor Name :&nbsp;&nbsp;</strong>'+sale[20]+'</div><div class="col-md-4"  style="font-size:14px;"><strong> Operation Number :&nbsp;&nbsp;</strong>'+sale[21]+'</div><div class="col-md-4"  style="font-size:14px;"><strong> Added On :&nbsp;&nbsp;</strong>'+sale[22]+'<strong> By :&nbsp;&nbsp;</strong>'+sale[23]+'</div></div>';
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
               var conts = '<div class="col-md-4"  style="font-size:14px;"><strong> Container '+(index+1) +' No :&nbsp;&nbsp;</strong>'+ container[index] +'</div>';
                $('#list_container').append(conts);
            });
        });
        $('#proforma-content').html(htmltext);
        $('#proforma-content').append(htmltext1);
        $('#proforma-content').append(htmlbankhead);
        $('#proforma-content').append(htmlbanktext);
        $('#proforma-content').append(htmlbookhead);
        $('#proforma-content').append(htmlbooktext);
        $('#proforma-content').append(htmltransithead);
        $('#proforma-content').append(htmltransittext);
     //   $('#proforma-content').append(itemhead);
        $('#sale-details').modal('show');
    }
</script>
@endpush