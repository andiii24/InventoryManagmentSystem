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
            <h3 style="margin-left: 20px; font-family:arial; "> <i style="color: rgb(56, 3, 87); margin-right: 10px;" class="fa fa-list-alt"></i> Custom Stage List   </h3>
         </div>
    </div>
    <div class="table-responsive">
        <table id="category-table" class="table table-striped sale-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>PFI Number</th>
                    <th>Item No</th>
                    <th>Total Item Quantity</th>
                    <th>Products</th>
                    <th>Vehicles</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
        </table> 
    </div>


<!--  Detail Modal -->
<div id="sale-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document"  class="modal-dialog">
        <div  class="modal-content">
            <div class="container mt-3 pb-2 border-bottom"> 
                <div class="row">
                    <div class="col-md-12 d-print-none">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="col-md-12 text-center">
                        <h2 style="font-size: 15px; font-family:Arial;">  PFI and Items Details </h2>
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
          
            <div id="footer-close" class="col-md-12 d-print-none">
                <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span class="btn btn-link">Close</span></button>
            </div>
            <br>

        </div>
    </div>
</div> 
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-stock-count-menu").addClass("active");
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".delete-count", function(){
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        swal.fire({ 
            title: "Are you sure want to delete proforma, "+name+"?",
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
                    url: "{{url('/purchase/count/delete')}}/" + id,
                    dataType: 'JSON',
                    success: function (results) {
                        if (results.success === true) {
                            swal.fire({ 
                                    title: "Success",
                                    text: "Proforma "+ name +" deleted successfully.",
                                    icon: "success",
                                    button: "Ok"
                                }).then(function(){ 
                                    window.location.href='{{ route('purchases.proforma-count') }}';
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
    $(document).on("click", "tr.proforma-link td:not(:first-child, :last-child)", function() {
        var sale = $(this).parent().data('proforma');
        saleDetails(sale);
    });
    $(document).on("click", ".pro-view", function(){
        var sale = $(this).parent().parent().parent().parent().parent().data('proforma');
        saleDetails(sale);
    });
    // View detail javascript
    $('#category-table').DataTable( {
        responsive: true,
        "processing": true,
        "serverSide": true,
        "ajax":{ 
            url:"{{ route('purchase.proforma-count-data')}}",
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
            {"data": "pfi_number"},
            {"data": "purchase_items"},
            {"data": "quantity"}, 
            {"data": "product"},
            {"data": "vehicle"},
            {"data": "options"}
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
        order:[['2', 'asc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0,1,3,4,5,6]
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
        var htmltext = '<div class="row"><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Supplier Name :&nbsp;&nbsp;</strong>'+sale[2]+'<br> <strong> PFI Number :&nbsp;&nbsp;</strong>'+sale[6]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Buyer Name :&nbsp;&nbsp;</strong>'+byi+'<br> <strong> PFI Date :&nbsp;&nbsp;</strong>'+sale[5]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Order Number :&nbsp;&nbsp;</strong>'+sale[4]+'<br> <strong> Created By :&nbsp;&nbsp;</strong>'+sale[9]+'</div></div>';
        var  htmltext1 =  '<div class="row"><div class="col-md-12"> <strong style="font-size:13px; font-family:times-new-roman;"> Payment Term :&nbsp;&nbsp;</strong> <span style="font-size:13px; font-family:times-new-roman;">  '+pti+'  </span> </div></div>';
        var htmlbankhead = '<div class="row"> <div class="col-md-12 text-center"> <h2 style="font-family:Arial; font-size:15px; margin-top:2px;">Bank Submit Detail</h2> </div> </div>';
        var htmlbanktext =  '<div class="row"><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Bank Name :&nbsp;&nbsp;</strong>'+sale[7]+'<br> <strong> Permit No :&nbsp;&nbsp;</strong>'+sale[11]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Payment Method :&nbsp;&nbsp;</strong>'+sale[12]+'<br> <strong> '+sale[12]+'  No :&nbsp;&nbsp;</strong>'+sale[13]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Submitted On :&nbsp;&nbsp;</strong>'+sale[14]+'<br> <strong> Submitted By :&nbsp;&nbsp;</strong>'+sale[15]+'</div></div>';
        var htmlbookhead = '<div class="row"> <div class="col-md-12 text-center"> <h2 style="font-family:Arial; font-size:15px; margin-top:2px;">Booking Detail</h2> </div> </div>';
        var htmlbooktext =  '<div id="list_container" class="row"><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Bl/Airway Bill No :&nbsp;&nbsp;</strong>'+sale[16]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> CI. Number :&nbsp;&nbsp;</strong>'+sale[17]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Booked On :&nbsp;&nbsp;</strong>'+sale[18]+'<strong> By :&nbsp;&nbsp;</strong>'+sale[19]+'</div></div>';
        var htmltransithead = '<div class="row"> <div class="col-md-12 text-center"> <h2 style="font-family:Arial; font-size:15px; margin-top:2px;">Transition Detail</h2> </div> </div>';
        var htmltransittext =  '<div class="row"><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Transitor Name :&nbsp;&nbsp;</strong>'+sale[20]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Operation No :&nbsp;&nbsp;</strong>'+sale[21]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Added On :&nbsp;&nbsp;</strong>'+sale[22]+'<strong> By :&nbsp;&nbsp;</strong>'+sale[23]+'</div></div>';
        var customhead = '<div class="row"> <div class="col-md-12 text-center"> <h2 style="font-family:Arial; font-size:15px; margin-top:2px;">Custom Detail</h2> </div> </div>';
        var customtext =  '<div class="row"><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Declaration No :&nbsp;&nbsp;</strong>'+sale[24]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Declared On :&nbsp;&nbsp;</strong>'+sale[25]+'</div><div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Declared By :&nbsp;&nbsp;</strong>'+sale[26]+'</div></div>';
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
               var conts = '<div class="col-md-4" style="font-size:13px; font-family:times-new-roman;"><strong> Container '+(index+1) +' No :&nbsp;&nbsp;</strong>'+ container[index] +'</div>';
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
        $('#proforma-content').append(customhead);
        $('#proforma-content').append(customtext);
     //   $('#proforma-content').append(itemhead);
        $('#sale-details').modal('show');
    }
</script>
@endpush