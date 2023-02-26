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
            <h3 style="margin-left: 20px; font-family:georgia; "> <i style="color: rgb(56, 3, 87); margin-right: 10px;" class="fa fa-list-alt"></i>  Bank Stage List   </h3>
         </div>
    </div>
    <div class="table-responsive">
        <table id="category-table" class="table table-striped sale-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>PFI Date</th>
                    <th>PFI Number</th>
                    <th>Bank Name</th>
                    <th>Permit Number</th>
                    <th>Payment method</th>
                    <th>Payment Number</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
        </table> 
    </div>


<!--  Detail Modal -->
<div id="sale-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="container mt-3 pb-2 border-bottom">
                <div class="row">
                    <div class="col-md-12 d-print-none">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div>
                    <div class="col-md-12 text-center">
                        <h2 style="font-size: 15px; font-family:Arial;"> <u> PROFORMA INVOICE DETAILS</u></h2>
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
<!-- Add To Booking Modal -->
<div id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" action="#" id="form-add-book" >
        <div class="modal-header">
            <h1 id="exampleModalLabel" class="modal-title" style="font-size: 18px;">   Add  (<span id="book_pro_name"> </span>) From Bank Submit To Booking Stage </h1>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div> 
        <div class="modal-body"> 
           <div id="booking-container-form" class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="hidden" name="booking_id" id="booking_id">
                        <label><strong> BL/Airway Bill Number * </strong> </label>
                        <input type="text"  class="form-control"   id="bill_number" name="bill_number" required="true" placeholder="Enter BL/Airway Bill Number ...">
                        <div id="bill_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong> Commercial Invoice Number * </strong> </label>
                        <input type="text"  class="form-control"   id="commercial_invoice" name="commercial_invoice" required="true" placeholder="Enter Commercial Invoice Number ...">
                        <div id="commercial_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong> Number Of Container (Total) *  </strong> </label>
                        <input type="text" onkeypress="return onlyNumberKey(event)" class="form-control" required="true"   id="total_container" name="total_container"  placeholder="Enter total number of containers ...">
                        <div id="total_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div> 
                </div>

           </div>
           <div class="form-group" style="margin-left: 20px;">
            <input id="addBooking"  type="submit" value="Submit" class="btn btn-primary">
          </div>
        </div>
    </form>
      </div>
    </div>
</div>
<!-- Bank Submit Edit Modal -->
<div id="bankModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="form" class="modal-dialog">
    <div class="modal-content">
        <form class="form" id="form-submit-update" >
        <div class="modal-header">
            <h3 id="exampleModalLabel" class="modal-title"><i class="fa fa-bank"></i> Update Proforma (<span id="proforma_bank"> </span>)'s Bank Submit </h3>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body overflow-hidden">
            
            <div class="row">
                <div  class="col-md-6">
                    <div class="form-group">
                    <input type="hidden" id="submit_id" name="submit_id">
                    <label> <strong> Permit Number </strong> </label>
                    <input type="text" value="" id="permit_number" placeholder="Enter Permit Number..."  name="permit_number" class="form-control" step="any">
                    <div id="permit_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Payment Method </strong></label>
                        <select id="payment_method"   name="payment_method" class="form-control selectpicker" title="Select payment method...">
                        <option value="CAD">CAD</option>
                        <option value="LC">LC</option>
                        <option value="TT">TT</option>
                        </select> 
                        <div id="method_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>

                <div  class="col-md-6">
                    <div class="form-group">
                    <label> <strong> <span id="payment-text"> Payment </span> Number</strong> </label>
                    <input type="text" value="" id="payment_number"  name="payment_number" class="form-control" placeholder="Enter Payment Number..." step="any">
                    <div id="pnum_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"></div>
                    </div>
                </div>
                <div  class="col-md-6">
                    <div class="form-group" style="float:right; margin-right: 100px; margin-top: 30px;">
                        <input id="bank-update-btn"  type="submit" value="Update Bank Submit" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
    </div>
</div>

@foreach($proformas as $ExpDoc)
        <input type="hidden" name="ref[]" value="{{ $ExpDoc->bill_number }}">
@endforeach
@foreach($proformas as $ExpDoc)
        <input type="hidden" name="commercial[]" value="{{ $ExpDoc->commercial_invoice }}">
@endforeach
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-bank-stage-menu").addClass("active");
    function onlyNumberKey(evt) 
      {
          
          // Only ASCII character in that range allowed
          var ASCIICode = (evt.which) ? evt.which : evt.keyCode
          if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
              return false;
          return true;
      }
      $(document).ready(function () {
        $('#bookingModal input[name="total_container"]').val("");
        $('#bookingModal input[name="bill_number"]').val("");
        $('#bookingModal input[name="commercial_invoice"]').val("");
      });
    var billArray = new Array();
    $("input[name='ref[]']").each(function(){
        billArray.push($(this).val());
    });
    var commercialArray = new Array();
    $("input[name='commercial[]']").each(function(){
        commercialArray.push($(this).val());
    });
    $("#addBooking").click(function(e){
        e.preventDefault();
        var MyArray = new Array();
        $("input[name='container_number[]']").each(function(){
        MyArray.push($(this).val());
         });
        if ($('#bill_number').val() == "" || $('#bill_number').val() == 0 ){
            var value = "Please Enter BL/Airway Bill Number ...";
            $("#bill_error_msg").css('display','block');
            $("#bill_error_msg").text(value);
            $("#commercial_error_msg").css('display','none');
            $("#total_error_msg").css('display','none');
            $("#cont_number_error").css('display','none');
            $('#bill_number').css('border-color','red');
            $('#commercial_invoice').css('border-color','black');
            $('#total_container').css('border-color','black');
            $('.cont-numbers').css('border-color','black');
            return false;
        } 
        else if(jQuery.inArray($('#bill_number').val(),billArray) !== -1){
            var value = "BL/Airway Bill Number Exist. Please add unique one.";
            $("#bill_error_msg").css('display','block');
            $("#bill_error_msg").text(value);
            $("#commercial_error_msg").css('display','none');
            $("#total_error_msg").css('display','none');
            $("#cont_number_error").css('display','none');
            $('#bill_number').css('border-color','red');
            $('#commercial_invoice').css('border-color','black');
            $('#total_container').css('border-color','black');
            $('.cont-numbers').css('border-color','black');
            return false;
        }
        else if($('#commercial_invoice').val() == "" || $('#commercial_invoice').val() == 0){
            var value = "Please Enter Commercial Invoice Number ...";
            $("#commercial_error_msg").css('display','block');
            $("#commercial_error_msg").text(value);
            $("#bill_error_msg").css('display','none');
            $("#total_error_msg").css('display','none');
            $("#cont_number_error").css('display','none');
            $('#commercial_invoice').css('border-color','red');
            $('#bill_number').css('border-color','green');
            $('#total_container').css('border-color','black');
            $('.cont-numbers').css('border-color','black');
            return false;
        }
        else if(jQuery.inArray($('#commercial_invoice').val(),commercialArray) !== -1){
            var value = "Commercial Invoice Number Exist. Please add unique one.";
            $("#commercial_error_msg").css('display','block');
            $("#commercial_error_msg").text(value);
            $("#bill_error_msg").css('display','none');
            $("#total_error_msg").css('display','none');
            $("#cont_number_error").css('display','none');
            $('#commercial_invoice').css('border-color','red');
            $('#bill_number').css('border-color','green');
            $('#total_container').css('border-color','black');
            $('.cont-numbers').css('border-color','black');
            return false;
        }
        else if($('#total_container').val() == "" || $('#total_container').val() == 0){
            var value = "Please Enter Total Number Of Containers ...";
            $("#total_error_msg").css('display','block');
            $("#total_error_msg").text(value);
            $("#bill_error_msg").css('display','none');
            $("#commercial_error_msg").css('display','none');
            $("#cont_number_error").css('display','none');
            $('#total_container').css('border-color','red');
            $('#commercial_invoice').css('border-color','green');
            $('#bill_number').css('border-color','green');
            $('.cont-numbers').css('border-color','black');
            return false;
        }
        else if(jQuery.inArray("", MyArray) !== -1){
            var value = "Please Fill All The List Of Container Numbers ...";
            $("#cont_number_error").css('display','block');
            $('#cont_number_error span[id="error_span"]').text(value);
            $("#bill_error_msg").css('display','none');
            $("#commercial_error_msg").css('display','none');
            $("#total_error_msg").css('display','none');
            $('.cont-numbers').each(function() {
            if ($(this).val() == "") {
                $(this).css('border-color','red');
                return false;
            }
            else
            {
            $(this).css('border-color','green');
            }
            });
            
            $('#total_container').css('border-color','green');
            $('#commercial_invoice').css('border-color','green');
            $('#bill_number').css('border-color','green');
            return false;            
        }
        else{ 
            $("#bill_error_msg").css('display','none');
            $("#commercial_error_msg").css('display','none');
            $("#cont_number_error").css('display','none');
            $("#total_error_msg").css('display','none');
        }
        var data = $('#form-add-book').serialize();
        $.ajax({
            url:"{{ route('purchase.proforma.booking.add')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            }, success:function(data){
                    var msg = 'Proforma Added To Booking Stage Successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.booking') }}';
                    });
            } 
        });
    });
    $("#bank-update-btn").click(function(e){
          e.preventDefault();
        if ($('#bankModal input[name="permit_number"]').val() == "" ){
            var value = "Please Enter Permit Number  ...";
            $("#permit_error_msg").css('display','block');
            $("#permit_error_msg").text(value);
            $("#method_error_msg").css('display','none');
            $("#pnum_error_msg").css('display','none');
            return false;
        }
        else if ($('#bankModal select[name="payment_method"]').val() == "" ){
            var value = "Please Select Payment Method  ...";
            $("#method_error_msg").css('display','block');
            $("#method_error_msg").text(value);
            $("#permit_error_msg").css('display','none');
            $("#pnum_error_msg").css('display','none');
            return false;
        }
        else if ($('#bankModal input[name="payment_number"]').val() == "" ){
            var value = "Please Enter Payment Number  ...";
            $("#pnum_error_msg").css('display','block');
            $("#pnum_error_msg").text(value);
            $("#permit_error_msg").css('display','none');
            $("#method_error_msg").css('display','none');
            return false;
        }
        else{ 
            $("#method_error_msg").css('display','none');
            $("#pnum_error_msg").css('display','none');
            $("#permit_error_msg").css('display','none');
        }
        var data = $('#form-submit-update').serialize();
        var proNum = $('#bankModal span[id="proforma_bank"]').text();
        $.ajax({
            url:"{{ route('proforma.bank.submit.update')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                    var msg = 'Bank submit of proforma ('+proNum+') is updated successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('purchases.bank-submit') }}';
                    });
            }
        });
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#bookingModal input[name="total_container"]').on("input", function() 
      {
        var total = $(this).val();
         if(total == 0 || total == "")
         {
            var value = "Please enter number of container ...";
            $("#total_error_msg").css('display','block');
            $("#total_error_msg").text(value);
            $('#bookingModal input[name="total_container"]').css('border-color','red');
            $('#booking-container-form').children('#kas-container').each(function(i){
              $(this).remove();
               });
            $('#cont_number_error').remove();
         }
         else
         {  
            if($(this).val()>1000)
            {
             alert('Maximum Number Of Container Allowed is 1000 !!! ');
             $('#booking-container-form').children('#kas-container').each(function(i){
              $(this).remove();
               });
               $(this).val("");
             return false;
            }
             $('#booking-container-form').children('#kas-container').each(function(i){
              $(this).remove();
               });
            for (i = 1; i <= total; i++) 
            {
                $("#total_error_msg").css('display','none');
                var book_html =  '<div id="kas-container" style="display:block;" class="col-md-6"><div class="form-group"><label><strong> Container ('+i+') Number * </strong></label><input type="text" class="form-control cont-numbers" name="container_number[]" placeholder="Enter container '+i+' number ..."></div></div>';
                $('#booking-container-form').append(book_html);
            }
            var contError = '<div id="cont_number_error" class="col-md-12 text-center" style="display:none;"> <span id="error_span" class="print-error-msg text-danger" style="font-size: 14px; font-family: Bodoni MT;"> </span> </div>';
            $('#booking-container-form').append(contError);

         }
      });
    $(document).on("click", ".AddBookingDialog", function(){
          var id = $(this).data('id');
          var name = $(this).data('name');
          $("#bookingModal input[name='booking_id']").val(id);
          $("#bookingModal span[id='book_pro_name']").text(name);
          $("#bookingModal").modal('show');
    });
    $(document).on("click", ".edit-bank", function(){
        var id = $(this).data('id');
        var name = $(this).data('name');
        $("#bankModal input[name='submit_id']").val(id);
        $("#bankModal span[id='proforma_bank']").text(name);
        var url= "{{url('/purchase/bank/edit')}}/";
        url = url.concat(id);
        $.get(url, function(data){
        $("#bankModal input[name='permit_number']").val(data['permit_number']);
        $("#bankModal input[name='payment_number']").val(data['payment_number']);
        $("#bankModal select[name='payment_method']").val(data['payment_method']);
        $('.selectpicker').selectpicker('refresh');
        $("#bankModal span[id='payment-text']").html(data['payment_method']);
        });
        $("#bankModal").modal('show');
    });
    $('#bankModal select[name="payment_method"]').on("change", function() {
        var PayMthd = $(this).val();
        $('#bankModal span[id="payment-text"]').html(PayMthd);
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
            url:"{{ route('purchase.bank-stage.data')}}",
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
            {"data": "bank_name"},
            {"data": "permit_number"},
            {"data": "payment_method"},
            {"data": "payment_number"},
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
                'targets': [0,2,5,6,7]
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
        var htmltext = '<div class="row"><div class="col-md-6" style="font-size:14px;"><strong> Supplier Name :&nbsp;&nbsp;</strong>'+sale[2]+'<br> <strong> PFI Number :&nbsp;&nbsp;</strong>'+sale[6]+'<br> <strong> Order Number :&nbsp;&nbsp;</strong>'+sale[4]+'</div><div class="col-md-6" style="font-size:14px;"><strong> Buyer Name :&nbsp;&nbsp;</strong>'+byi+'<br> <strong> PFI Date :&nbsp;&nbsp;</strong>'+sale[5]+'<br> <strong> Created By :&nbsp;&nbsp;</strong>'+sale[9]+' ,<a href="#"> '+sale[10]+'</a></div></div>';
        var  htmltext1 =  '<div class="row"><div class="col-md-12" style="font-size:14px;"> <strong> Payment Term :&nbsp;&nbsp;</strong> <span style="font-family:georgia;">  '+pti+'  </span> </div></div>';
        var htmlbank = '<div class="row"> <div class="col-md-12 text-center"> <h2 style="font-family:Arial; font-size:14px;"><u>BANK SUBMIT DETAILS</u></h2> </div> </div>';
        var htmlbank1 =  '<div class="row"><div class="col-md-6" style="font-size:14px;"><strong> Bank Name :&nbsp;&nbsp;</strong>'+sale[7]+'<br> <strong> Payment Method :&nbsp;&nbsp;</strong>'+sale[12]+'<br> <strong> Submitted On :&nbsp;&nbsp;</strong>'+sale[14]+'</div><div class="col-md-6" style="font-size:14px;"><strong> Permit Number :&nbsp;&nbsp;</strong>'+sale[11]+'<br> <strong> '+sale[12]+'  Number :&nbsp;&nbsp;</strong>'+sale[13]+'<br> <strong> Submitted By :&nbsp;&nbsp;</strong>'+sale[15]+' ,<a href="#"> '+sale[16]+'</a></div></div>';
       // var itemhead = '<br><div class="row"> <div class="col-md-12 text-center"> <h2 style="font-family:Arial; font-size:14px;"><u>PROFORMA ITEM LIST</u></h2> </div> </div>';
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
      
        $('#proforma-content').html(htmltext);
        $('#proforma-content').append(htmltext1);
        $('#proforma-content').append(htmlbank);
        $('#proforma-content').append(htmlbank1);
     //   $('#proforma-content').append(itemhead);
        $('#sale-details').modal('show');
    }
</script>
@endpush