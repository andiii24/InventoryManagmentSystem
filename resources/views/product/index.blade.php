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
        @if(in_array("products-add", $all_permission))
            <a href="{{route('products.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{__('file.add_product')}}</a>
            <a href="#" data-toggle="modal" data-target="#importProduct" class="btn btn-primary"><i class="dripicons-copy"></i> {{__('file.import_product')}}</a>
        @endif
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
                    <th>{{trans('file.Quantity')}}</th>
                    <th>{{trans('file.Unit')}}</th>
                    <th>{{trans('file.Product Price')}}</th>
                    <th>{{trans('file.Product Cost')}}</th>
                    <th>Warehouse</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>

        </table>
    </div>
</section>

<div id="importProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'product.import', 'method' => 'post', 'files' => true]) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">Import Product</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
           <p>{{trans('file.The correct column order is')}} ( name*, code*, brand, category*, unit*, piece_in_carton, quantity *, cost*, price*, warehouse*, product_detail) {{trans('file.and you must follow this')}}.</p>
           <i><b> Note: </b> The column piece_in_carton is filled only if unit is carton .</i>
      <p>     <i style="color:orange;">Unit can be Piece, Dozen and Carton </i> </p>
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
                        <label><strong>{{trans('file.Upload CSV File')}} *</strong></label>
                        {{Form::file('file', array('class' => 'form-control','required'))}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group"> 
                        <label><strong> {{trans('file.Sample File')}} </strong></label>
                        <a href="{{asset('/sample_file/sample_products.csv')}}" class="btn btn-info btn-block btn-md"><i class="dripicons-download"></i>  {{trans('file.Download')}}</a>
                    </div>
                </div>

           </div>
            {{Form::submit('Submit', ['class' => 'btn btn-primary'])}}
        </div>
        {!! Form::close() !!}
      </div>
    </div>
</div>
 <!-- Product Details View Modal -->
 <div id="product-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('Product Details')}}</h5>
      <!--    <button id="print-btn" onclick="kasu();" type="button" class="btn btn-default btn-sm ml-3"><i class="dripicons-print"></i> {{trans('file.Print')}}</button> -->
          <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
               <div class="row">
                <div class="col-md-6" id="product-content"></div>
               
                <div class="col-md-6" id="slider-content"></div>
               
               </div>


           
        </div>
      </div>
    </div>
</div>
     



@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>

<script>

    $("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #product-list-menu").addClass("active");

    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }


    var product_id = [];
    var warehouse = [];
    var htmltext;
    var kasu;
    var slidetext;
    var all_permission = <?php echo json_encode($all_permission) ?>;
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $( "#select_all" ).on( "change", function() {
        if ($(this).is(':checked')) {
            $("tbody input[type='checkbox']").prop('checked', true);
        }
        else {
            $("tbody input[type='checkbox']").prop('checked', false);
        }
    });


    $(document).on("click", "tr.product-link td:not(:first-child, :last-child)", function() {
        productDetails( $(this).parent().data('product') );
    });

    $(document).on("click", ".view", function(){
        var product = $(this).parent().parent().parent().parent().parent().data('product');
        productDetails(product);
    });

   function kasu() {
          var divToPrint=document.getElementById('product-details');
          var newWin=window.open('','Print-Window');
          newWin.document.open();
          newWin.document.write('<link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css"><style type="text/css">@media print {.modal-dialog { max-width: 1000px;} }</style><body onload="window.print()">'+divToPrint.innerHTML+'</body>');
          newWin.document.close();
          setTimeout(function(){newWin.close();},10);
         }
    function productDetails(product) {
        product[11] = product[11].replace(/@/g, '"');
        htmltext = slidetext = '';

        htmltext = 
                 '<br><p><strong>{{trans("file.name")}}: </strong>'+product[1]+'</p>' +
                 '<p><strong>{{trans("file.Code")}}: </strong>'+product[2]+ '</p>'+
                 '<p><strong>{{trans("file.Brand")}}: </strong>'+product[3]+'</p>'+
                 '<p><strong>{{trans("file.category")}}: </strong>'+product[4]+'</p>'+
                 '<p><strong>{{trans("file.Quantity")}}: </strong>'+product[17]+'</p>'+
                 '<p><strong>{{trans("file.Unit")}}: </strong>'+product[5]+'</p>'+
                 '<p><strong>Total Cost: </strong>'+product[19]+'</p>'+
                 '<p><strong>Total Price: </strong>'+product[18]+'</p>';
                 $('#product-content').html(htmltext);
    slidetext=   
                 '<p><strong>{{trans("file.Product Cost")}}: </strong>'+product[6]+'</p>'+
                 '<p><strong>{{trans("file.Product Price")}}: </strong>'+product[7]+'</p>'+
                 '<p><strong>{{trans("file.Tax")}}: </strong>'+product[8]+'</p>' +
                 '<p><strong>{{trans("file.Tax Method")}} : </strong>'+product[9]+'</p>' +
                 '<p><strong>{{trans("file.Alert Quantity")}} : </strong>'+product[10]+'</p>'+
                 '<p><strong>{{trans("file.Product Details")}}: </strong> </p>'+product[11]+ '<br>';
                
                 $('#slider-content').html(slidetext);
      
                 $('#product-details').modal('show');
      
      
    }
    
          // Table Data
          $(document).ready(function() {
        var table = $('#product-data-table').DataTable( {
            responsive: true,
            fixedHeader: {
                header: true,
                footer: true
            },
            "processing": true,
            "serverSide": true,
            "ajax":{
                url:"{{ route('product.data')}}",
                data:{
                "_token" : "{{ csrf_token() }}",
                },
                dataType: "json",
                type:"post"
            },
            "createdRow": function( row, data, dataIndex ) {
                $(row).addClass('product-link');
                $(row).attr('data-product', data['product']);
            },
            "columns": [
                {"data": "key"},
                {"data": "name"},
                {"data": "code"},
                {"data": "brand"},
                {"data": "category"},
                {"data": "qty"},
                {"data": "unit_id"},
                {"data": "price"},
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
                    'targets': [2, 6, 9,10]
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
                    text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                        stripHtml: false
                    },
                },
                {
                    extend: 'csv',
                    text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
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
                    text: '<i title="print" class="fa fa-print"></i>',
                    exportOptions: {
                        columns: ':visible:not(.not-exported)',
                        rows: ':visible',
                        stripHtml: false
                    }
                },
                {
                    text: '<i title="delete" class="dripicons-cross"></i>',
                    className: 'buttons-delete',
                    action: function ( e, dt, node, config ) {
                            product_id.length = 0;
                            $(':checkbox:checked').each(function(i){
                                if(i){
                                    var product_data = $(this).closest('tr').data('product');
                                    product_id[i-1] = product_data[12];
                                }
                            });
                            if(product_id.length ) {
                            swal.fire({ 
                            title: " Are you sure want to delete selected products ?",
                            text: "You will not be able to revert this action.",
                            type: "warning",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#0275d8",
                            confirmButtonText: "Yes, Delete it!",
                            closeOnConfirm: false,
                            preConfirm: function(result) {
                            console.log(product_id);
                            $.ajax({
                                type:'POST',
                                url:"{{ route('product.deleteselected')}}",
                                data:{
                                    "_token" : "{{ csrf_token() }}",
                                    productIdArray: product_id,
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
                                        }else{
                                            console.log(data.error);
                                        }
                                        
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                            dt.rows('selected').remove().draw(false);
                        }
                        else if(!product_id.length)
                        {
                            alert('No product is selected!');
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
    } );
});
    

    if(all_permission.indexOf("products-delete") == -1)
        $('.buttons-delete').addClass('d-none');

    $('select').selectpicker();

</script>
@endpush
