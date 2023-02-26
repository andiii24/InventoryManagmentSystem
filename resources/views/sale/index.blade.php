@extends('layout.main') @section('content')
@if(session()->has('message')) 
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <div class="card">
            <div class="d-flex align-items-center py-1">
                <h3 style="margin-left: 20px;"> <i class="fa fa-shopping-cart"></i>    {{trans('file.Sale List')}}   <i class="fa fa-list "></i> </h3>
             </div>
            {!! Form::open(['route' => 'sales.index', 'method' => 'get']) !!}
            <div class="row ml-1 mt-2">
                <div class="col-md-3">
                    <div class="form-group">
                        <label><strong>{{trans('file.Date')}}</strong></label>
                        <input type="text" class="daterangepicker-field form-control" value="{{$starting_date}} To {{$ending_date}}" required />
                        <input type="hidden" name="starting_date" value="{{$starting_date}}" />
                        <input type="hidden" name="ending_date" value="{{$ending_date}}" />
                    </div>
                </div>
                <div class="col-md-2 @if(\Auth::user()->role_id > 2){{'d-none'}}@endif">
                    <div class="form-group">
                        <label><strong>{{trans('file.Warehouse')}}</strong></label>
                        <select id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" >
                            <option value="0">{{trans('file.All Warehouse')}}</option>
                            @foreach($lims_warehouse_list as $warehouse)
                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label><strong>Product Type</strong></label>
                        <select id="sale-status" class="form-control" name="sale_status">
                            <option value="0">{{trans('file.All')}}</option>
                            <option value="1">Products</option>
                            <option value="2">Vehicles</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label><strong>{{trans('file.Payment Status')}}</strong></label>
                        <select id="payment-status" class="form-control" name="payment_status">
                            <option value="0">{{trans('file.All')}}</option>
                            <option value="1">{{trans('file.Completed')}}</option>
                            <option value="2">{{trans('file.Pending')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 mt-3">
                    <div class="form-group">
                        <label><strong></strong></label>
                        <button class="btn btn-primary" id="filter-btn" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        @if(in_array("sales-add", $all_permission))
       <!--     <a href="{{route('sales.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Sale')}}</a>&nbsp; -->
            
        @endif
    </div>
    <div class="table-responsive">
        <table id="sale-table" class="table sale-list" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>Product  </th>
                    <th>Customer</th>
                    <th>Item</th> 
                    <th>Quantity</th>
                    <th>{{trans('file.grand total')}}</th>
                    <th>{{trans('file.Paid')}}</th>
                    <th>{{trans('file.Payment Status')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead> 

            <tfoot class="tfoot active">
                <th></th>
                <th>{{trans('file.Total')}}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</section>

<div id="sale-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="container mt-3 pb-2 border-bottom">
                <div class="row">
                    <div class="col-md-6 d-print-none">
                        <button id="print-btn" type="button" class="btn btn-default btn-sm"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>
                    </div>
                    <div class="col-md-6 d-print-none">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div> 
                    <div class="col-md-12">
                        <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">{{$general_setting->site_title}}</h3>
                    </div>
                    <div class="col-md-12 text-center">
                        <i style="font-size: 15px;">{{trans('file.Sale Details')}}</i>
                    </div>
                </div>
            </div>
            <div id="sale-content" class="modal-body">
            </div>
            <br> 
            <table class="table table-bordered product-sale-list">
                <thead>
                    <th>#</th>
                    <th>{{trans('file.product')}}</th>
                    <th>Quantity</th>
                    <th>Unit </th>
                    <th>Price</th>
                    <th class="sell-unit">Sell Unit</th>
                    <th class="sell-price">Unit Price</th>
                    <th>Tax</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="sale-footer" class="modal-body"></div>
        </div>
    </div>
</div>
<div id="vehicle-sale-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="container mt-3 pb-2 border-bottom">
                <div class="row">
                    <div class="col-md-6 d-print-none">
                        <button id="vehicle-print-btn" type="button" class="btn btn-default btn-sm"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>
                    </div> 
                    <div class="col-md-6 d-print-none">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                    </div> 
                    <div class="col-md-12">
                        <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">{{$general_setting->site_title}}</h3>
                    </div>
                    <div class="col-md-12 text-center">
                        <i style="font-size: 15px;">{{trans('file.Sale Details')}}</i>
                    </div>
                </div>
            </div>
            <div id="vehicle-sale-content" class="modal-body">
            </div>
            <br> 
            <table class="table table-bordered vehicle-sale-list">
                <thead>
                    <th>#</th>
                    <th>Vehicle</th>
                    <th>Chassis No</th>
                    <th>Engine No </th>
                    <th>Price</th>
                    <th>Tax</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="vehicle-sale-footer" class="modal-body"></div>
        </div>
    </div>
</div>

<div id="view-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.All')}} {{trans('file.Payment')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <br> 
                <table class="table table-striped payment-list">
                    <thead class="thead-dark">
                        <tr>
                            <th>{{trans('file.date')}}</th>
                            <th>{{trans('file.reference')}}</th>
                            <th>{{trans('file.Amount')}}</th>
                            <th>Payment Method</th>
                            <th class="banku">Bank</th>
                            <th>Due</th>
                            <th>Created By</th> 
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </div>
</div>

<div id="add-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Payment')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'sale.add-payment', 'method' => 'post', 'files' => true, 'class' => 'payment-form' ]) !!}
                    <div class="row"> 
                        <input type="hidden" id="add_paid_amount" name="add_paid_amount">
                        <div class="col-md-6">
                            <label>Grand Total</label>
                            <input type="text" name="grand_amount" readonly class="form-control numkey" step="any" required>
                        </div>
                        <div class="col-md-6"> 
                            <label>{{trans('file.Recieved Amount')}} *</label>
                            <input type="text" name="paying_amount" readonly class="form-control numkey" step="any" required>
                        </div>
                        <div class="col-md-6">
                            <label>{{trans('file.Paying Amount')}} *</label>
                            <input type="text" onkeypress="return onlyNumberKey(event)" id="amount" name="amount" class="form-control" placeholder="please enter amount"  step="any" required>
                        </div>
                        <div class="col-md-6 mt-1">
                            <label>Due Amount (Change) : </label>
                            <p class="add-change ml-2">0.00</p>
                        </div>
                        <div class="col-md-6 mt-1">
                            <label>{{trans('file.Paid By')}}</label>
                            <select name="paid_by_id" class="form-control selectpicker">
                                <option value="1">Cash</option>
                                <option value="2">Deposit</option>        
                            </select>   
                        </div>
                        <div class="col-md-6 mt-1" id="bank_div">
                                           
                            <label>{{trans('file.Deposited Bank')}} *</label>
                            <select name="bank_id" id="bank_id" required class="form-control selectpicker">
                                @foreach($banks as $customer)  
                                    <option value="{{$customer->id}}">{{$customer->title}}</option>
                                @endforeach
                               
                            </select>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label>{{trans('file.Payment Note')}}</label>
                        <textarea rows="3" class="form-control" name="payment_note"></textarea>
                    </div>

                    <input type="hidden" name="sale_id">
                    <input type="hidden" name="add_sale_id">
                    <button type="submit" id="pay-ment-btn" class="btn btn-primary">{{trans('file.submit')}}</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div> 

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">

    $("ul#sale").siblings('a').attr('aria-expanded','true');
    $("ul#sale").addClass("show");
    $("ul#sale #sale-list-menu").addClass("active");
    var public_key = <?php echo json_encode($lims_pos_setting_data->stripe_public_key) ?>;
    var all_permission = <?php echo json_encode($all_permission) ?>;
    var reward_point_setting = <?php echo json_encode($lims_reward_point_setting_data) ?>;
    var sale_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;
    var starting_date = <?php echo json_encode($starting_date); ?>;
    var ending_date = <?php echo json_encode($ending_date); ?>;
    var warehouse_id = <?php echo json_encode($warehouse_id); ?>;
    var sale_status = <?php echo json_encode($sale_status); ?>;
    var payment_status = <?php echo json_encode($payment_status); ?>;
    var current_date = <?php echo json_encode(date("Y-m-d")) ?>;
    var payment_date = [];
    var payment_reference = [];
    var paid_amount = [];
    var paying_method = [];
    var payment_id = [];
    var payment_note = [];
    var account = [];
    var banks = [];
    var dues = [];
    var users = [];
    var notes = [];
    var deposit;
  
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#warehouse_id").val(warehouse_id);
    $("#sale-status").val(sale_status);
    $("#payment-status").val(payment_status);
    function onlyNumberKey(evt) {
          
          // Only ASCII character in that range allowed
          var ASCIICode = (evt.which) ? evt.which : evt.keyCode
          if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
              return false;
          return true;
      }
    $(".daterangepicker-field").daterangepicker({
      callback: function(startDate, endDate, period){
        var starting_date = startDate.format('YYYY-MM-DD');
        var ending_date = endDate.format('YYYY-MM-DD');
        var title = starting_date + ' To ' + ending_date;
        $(this).val(title);
        $('input[name="starting_date"]').val(starting_date);
        $('input[name="ending_date"]').val(ending_date);
      }
    });

    $('#add-payment').modal('hide');
    $('.selectpicker').selectpicker('refresh');

    $(document).on("click", "tr.sale-link td:not(:first-child, :last-child)", function() {
        var sale = $(this).parent().data('sale');
        saleDetails(sale);
    });

    $(document).on("click", ".view", function(){
        var sale = $(this).parent().parent().parent().parent().parent().data('sale');
        saleDetails(sale);
    });

    $(document).on("click", "#print-btn", function() {
        var divContents = document.getElementById("sale-details").innerHTML;
        var a = window.open('');
        a.document.write('<html>');
        a.document.write('<body>');
        a.document.write('<style>body{font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;}.d-print-none{display:none}.text-center{text-align:center}.row{width:100%;margin-right: -15px;margin-left: -15px;}.col-md-12{width:100%;display:block;padding: 5px 15px;}.col-md-6{width: auto%;position:relative;padding: 5px 15px;}table{width:100%;margin-top:30px;}th{text-aligh:left}td{padding:10px}table,th,td{border: 1px solid black; border-collapse: collapse;}</style><style>@media print {.modal-dialog { max-width: 1000px;} }</style>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        setTimeout(function(){a.close();},10);
        a.print();
    });
    $(document).on("click", "#vehicle-print-btn", function() {
        var divContents = document.getElementById("vehicle-sale-details").innerHTML;
        var a = window.open('');
        a.document.write('<html>');
        a.document.write('<body>');
        a.document.write('<style>body{font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;}.d-print-none{display:none}.text-center{text-align:center}.row{width:100%;margin-right: -15px;margin-left: -15px;}.col-md-12{width:100%;display:block;padding: 5px 15px;}.col-md-6{width: 50%;position:relative;padding: 5px 15px;}table{width:100%;margin-top:30px;}th{text-aligh:left}td{padding:10px}table,th,td{border: 1px solid black; border-collapse: collapse;}</style><style>@media print {.modal-dialog { max-width: 1000px;} }</style>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        setTimeout(function(){a.close();},10);
        a.print();
    });

    $(document).on("click", "table.sale-list tbody .add-payment", function() {
        $("#bank_div").hide();

        var sale_id = $(this).data('id').toString();
        $('input[name="sale_id"]').val(sale_id);
        $('input[name="add_sale_id"]').val(sale_id);
        $('select[name="paid_by_id"]').val(1);
        $('.selectpicker').selectpicker('refresh');
        var sale = $(this).closest('tr').data('sale');
        var total= parseFloat(sale[10]);
        var paid= parseFloat(sale[11]);
        var credit = total - paid;
        $('input[name="paying_amount"]').val(paid);
        $('input[name="grand_amount"]').val(total);
        $('input[name="add_paid_amount"]').val(paid);
        $('#add-payment input[name="amount"]').val('');
        $(".add-change").text(parseFloat( total - paid).toFixed(2)); 
        $('#add-payment').modal('show');
        
    });

    $(document).on("click", "table.sale-list tbody .get-payment", function(event) {
        rowindex = $(this).closest('tr').index();
        //deposit = $('table.sale-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.deposit').val();
        var id = $(this).data('id').toString();
        $.get('sales/getpayment/' + id, function(data) { 
            $(".payment-list tbody").remove();
            var newBody = $("<tbody>");
            payment_date  = data[0];
            payment_reference = data[1];
            paid_amount = data[2];
            paying_method = data[3];
            banks = data[4];
            dues = data[5];
            users = data[6];
            notes = data[7];
            $.each(payment_date, function(index){
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td>' + payment_date[index] + '</td>';
                cols += '<td>' + payment_reference[index] + '</td>';
                cols += '<td>' + paid_amount[index] + '</td>';
                cols += '<td>' + paying_method[index] + '</td>';
                cols += '<td>' + banks[index] + '</td>';
                cols += '<td>' + dues[index] + '</td>';
                cols += '<td>' + users[index] + '</td>'; 
                newRow.append(cols);
                newBody.append(newRow);
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td colspan=1><strong>Payment Note:</strong></td>';
                cols += '<td colspan=4> <textarea rows="1" cols="10" readonly class="form-control">' + notes[index] + '</textarea></td>'; 
                newRow.append(cols);
                newBody.append(newRow);
                $("table.payment-list").append(newBody);
            }); 
            $('#view-payment').modal('show');
        });
    });


    $('select[name="paid_by_id"]').on("change", function() {
        var id = $(this).val();
        $('#add-payment select[name="bank_id"]').attr('required', false);
       // $(".payment-form").off("submit");
        if(id == 2){
            $("#bank_div").show();
            $('#add-payment select[name="bank_id"]').attr('required', true);
        }
        else
        {
            $("#bank_div").hide();
            $('#add-payment select[name="bank_id"]').attr('required', false);
        }
       
    });



    $('#add-payment input[name="amount"]').on("input", function() {
        var amount = parseFloat($(this).val());
        //$(".payment-form").off("submit");
        var kafalame = parseFloat($('#add-payment input[name="add_paid_amount"]').val());
        var wali = parseFloat($('#add-payment input[name="grand_amount"]').val());
        var sum = parseFloat(kafalame + amount);
        if(sum > wali)
        {
            alert('Paying amount cannot be bigger than due amount !');
            $('input[name="paying_amount"]').val(kafalame);
            $(this).val('');
            $(".add-change").text(parseFloat( wali - kafalame).toFixed(2)); 
        }
       else if( $(this).val() == '' || $(this).val() == 0)
       {
        alert('Amount cannot be zero or empty !');
        $('#add-payment input[name="amount"]').val('');
        $('#add-payment input[name="amount"]').css('border-color','red');
       }
        else
        {
            $('input[name="paying_amount"]').val(sum);
            $(".add-change").text(parseFloat( wali - sum).toFixed(2)); 
        }
      
    });

    $(document).on('click', '#pay-ment-btn', function(e) {
        e.preventDefault();
        if( $('#add-payment input[name="amount"]').val() == '' ||  $('#add-payment input[name="amount"]').val() == 0 ) {
            $('.payment-form').off('submit');
            alert('Please enter paying amount !');
            $('#add-payment input[name="amount"]').val('');
            $('#add-payment input[name="amount"]').css('border-color','red');
        }
        else
        {
           // $('#add-payment select[name="paid_by_id"]').prop('disabled', false);
           $('.payment-form').submit();
        }

        
    });

    $('#sale-table').DataTable( { 
        "processing": true,
        "serverSide": true,
        "ajax":{
            url:"sales/sale-data",
            data:{
                all_permission: all_permission,
                starting_date: starting_date,
                ending_date: ending_date,
                warehouse_id: warehouse_id,
                sale_status: sale_status,
                payment_status: payment_status
            },
            dataType: "json",
            type:"post"
        },
        /*rowId: function(data) {
              return 'row_'+data['id'];
        },*/
        "createdRow": function( row, data, dataIndex ) {
            //alert(data);
            $(row).addClass('sale-link');
            $(row).attr('data-sale', data['sale']);
        },
        "columns": [
            {"data": "key"},
            {"data": "date"},
            {"data": "reference_no"},
            {"data": "type"},  
            {"data": "customer"},
            {"data": "item"},
            {"data": "quantity"},
            {"data": "grand_total"},
            {"data": "paid_amount"},
            {"data": "payment_status"}, 
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
                'targets': [0, 3, 4, 5, 6, 9, 10]
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
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        rowId: 'ObjectID',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
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
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
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
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                        sale_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var sale = $(this).closest('tr').data('sale');
                                 if(sale[11] >= sale[10])
                                 {
                                    sale_id[i-1] = sale[5];
                                  
                                 }
                                 else
                                 {
                                    alert('Sales with incomplete payment cannot be deleted .');
                                 }
                                
                            }
                        });
                        if(sale_id.length){
                            swal.fire({ 
                            title: "Sales with incomplete payment cannot be deleted. Are you sure want to delete ?",
                            text: "You will not be able to revert this action.",
                            type: "warning",
                            icon: "info",
                            showCancelButton: true,
                            confirmButtonColor: "#0275d8",
                            confirmButtonText: "Yes, Delete it!" ,
                            closeOnConfirm: false,
                            preConfirm: function(result) {
                          
                            $.ajax({
                                type:'POST',
                                url:"{{ route('sales.deleteselected')}}",
                                data:{
                                    "_token" : "{{ csrf_token() }}",
                                     saleIdArray: sale_id
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
                                            location.reload();
                                        });
                                        }
                                        
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                        }
                        else if(!sale_id.length)
                            alert('Nothing is selected!');
                    
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    } );


    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
           // $( dt_selector.column( 9 ).footer() ).html(dt_selector.cells( rows, 9, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
           // $( dt_selector.column( 9 ).footer() ).html(dt_selector.cells( rows, 9, { page: 'current' } ).data().sum().toFixed(2));
        }
    }

    function saleDetails(sale){
        $("#sale-details input[name='sale_id']").val(sale[5]);
        $(".product-sale-list tbody").remove();
        $(".vehicle-sale-list tbody").remove();
        var htmltext = '<strong>{{trans("file.Date")}}: </strong>'+sale[0]+'<br><strong>{{trans("file.reference")}}: </strong>'+sale[1]+'<br><strong>{{trans("file.Warehouse")}}: </strong>'+sale[17]+'<br><strong>{{trans("file.Payment Status")}}: </strong>'+sale[2]+'<br><br><div class="row"><div class="col-md-6" ><strong>  By :</strong><br>'+sale[14]+'<br>'+sale[15]+'<br>'+sale[16]+'</div><div class="col-md-6"><div class="float-right"><strong>{{trans("file.To")}}:</strong><br>'+sale[3]+'<br>'+sale[4]+'<br></div></div></div>';
        $.get('sales/product_sale/' + sale[5], function(data){
            var name_code = data[0];
            var qty = data[1];  
            var punit = data[2];
            var pprice = data[3];
            var sunit = data[4];
            var sprice = data[5];
            var tax = data[6];
            var subtotal = data[7]; 
            var type = data[8];
            var chassis = data[9];
            var engine = data[10];
            if(sale[18] == 1 || type == 1)
            {
                var newBody = $("<tbody>");
               $.each(name_code, function(index){
                    var newRow = $("<tr>");
                    var cols = '';
                    cols += '<td><strong>' + (index+1) + '</strong></td>';
                    cols += '<td>' + name_code[index] + '</td>';
                    cols += '<td>' + qty[index] + '</td>';
                    cols += '<td>' + punit[index] + '</td>';
                    cols += '<td>' + pprice[index] + '</td>';
                    cols += '<td class="sell-unit1">' + sunit[index] + '</td>';
                    cols += '<td class="sell-price1">' + sprice[index] + '</td>';
                    cols += '<td>' + tax[index] + '</td>';
                    cols += '<td>' + subtotal[index] + '</td>';
                    newRow.append(cols);
                    newBody.append(newRow);
                });

                var newRow = $("<tr>");
                cols = '';
                cols += '<td colspan=7><strong>{{trans("file.Total")}}:</strong></td>';
                cols += '<td>' + sale[8] + '</td>';
                cols += '<td>' + sale[9] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);

                var newRow = $("<tr>");
                cols = '';
                cols += '<td colspan=8><strong>Total Tax:</strong></td>';
                cols += '<td>' + sale[8] +  '</td>';
                newRow.append(cols);
                newBody.append(newRow);

                var newRow = $("<tr>");
                cols = '';
                cols += '<td colspan=8><strong>Total Price:</strong></td>';
                cols += '<td>' + sale[9] +  '</td>';
                newRow.append(cols);
                newBody.append(newRow);

                var newRow = $("<tr>");
                cols = '';
                cols += '<td colspan=8><strong>{{trans("file.grand total")}}:</strong></td>';
                cols += '<td>' + sale[10] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);

                var newRow = $("<tr>");
                cols = '';
                cols += '<td colspan=8><strong>{{trans("file.Paid Amount")}}:</strong></td>';
                cols += '<td>' + sale[11] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);

                var newRow = $("<tr>");
                cols = '';
                cols += '<td colspan=8><strong>{{trans("file.Due")}}:</strong></td>';
                cols += '<td>' + parseFloat(sale[10] - sale[11]).toFixed(2) + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
                $("table.product-sale-list").append(newBody);
                var htmlfooter = '<p><strong>{{trans("file.Sale Note")}}:</strong> '+sale[12]+'</p><p><strong>{{trans("file.Staff Note")}}:</strong> '+sale[13]+'</p>';
                $('#sale-content').html(htmltext);
                $('#sale-footer').html(htmlfooter);  
                $('#vehicle-sale-details').modal('hide');
                $('#sale-details').modal('show');
            }
            if(sale[18] == 2 || type == 2)
            {
                var newBody = $("<tbody>");
                $.each(name_code, function(index){
                        var newRow = $("<tr>");
                        var cols = '';
                        cols += '<td><strong>' + (index+1) + '</strong></td>';
                        cols += '<td>' + name_code[index] + '</td>';
                        cols += '<td>' + chassis[index] + '</td>';
                        cols += '<td>' + engine[index] + '</td>';
                        cols += '<td>' + pprice[index] + '</td>';
                        cols += '<td>' + tax[index] + '</td>';
                        cols += '<td>' + subtotal[index] + '</td>';
                        newRow.append(cols);
                        newBody.append(newRow);
                    });
                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td colspan=5><strong>{{trans("file.Total")}}:</strong></td>';
                    cols += '<td>' + sale[8] + '</td>';
                    cols += '<td>' + sale[9] + '</td>';
                    newRow.append(cols);
                    newBody.append(newRow);

                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td colspan=6><strong>Total Tax:</strong></td>';
                    cols += '<td>' + sale[8] +  '</td>';
                    newRow.append(cols);
                    newBody.append(newRow);

                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td colspan=6><strong>Total Price:</strong></td>';
                    cols += '<td>' + sale[9] +  '</td>';
                    newRow.append(cols);
                    newBody.append(newRow);

                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td colspan=6><strong>{{trans("file.grand total")}}:</strong></td>';
                    cols += '<td>' + sale[10] + '</td>';
                    newRow.append(cols);
                    newBody.append(newRow);

                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td colspan=6><strong>{{trans("file.Paid Amount")}}:</strong></td>';
                    cols += '<td>' + sale[11] + '</td>';
                    newRow.append(cols);
                    newBody.append(newRow);

                    var newRow = $("<tr>");
                    cols = '';
                    cols += '<td colspan=6><strong>{{trans("file.Due")}}:</strong></td>';
                    cols += '<td>' + parseFloat(sale[10] - sale[11]).toFixed(2) + '</td>';
                    newRow.append(cols);
                    newBody.append(newRow);
                    $("table.vehicle-sale-list").append(newBody);
                    var htmlfooter = '<p><strong>{{trans("file.Sale Note")}}:</strong> '+sale[12]+'</p><p><strong>{{trans("file.Staff Note")}}:</strong> '+sale[13]+'</p>';
                    $('#vehicle-sale-content').html(htmltext);
                    $('#vehicle-sale-footer').html(htmlfooter);  
                    $('#sale-details').modal('hide');
                    $('#vehicle-sale-details').modal('show');
            }
        });
       
    }



    if(all_permission.indexOf("sales-delete") == -1)
        $('.buttons-delete').addClass('d-none');

        function confirmDelete() {
            if (confirm("Are you sure want to delete?")) {
                return true;
            }
            return false;
        }
 
    function confirmPaymentDelete() {
        if (confirm("Are you sure want to delete? If you delete this money will be refunded.")) {
            return true;
        }
        return false;
    } 

</script>
@endpush
 