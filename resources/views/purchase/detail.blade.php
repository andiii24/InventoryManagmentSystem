@extends('layout.main') @section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">Products & Vehicles of PFI({{$PP->pfi_number}}) List</h3>
            </div>
        </div>
    </div>
    <ul class="nav nav-tabs ml-4 mt-3" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" href="#product-count" role="tab" data-toggle="tab"> <i class="fa fa-product-hunt"> </i> Products</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#vehicle-count" role="tab" data-toggle="tab"><i class="fa fa-car"> </i> Vehicles</a>
      </li>
    </ul>
    <br>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="product-count">
            <div class="table-responsive mb-4">
                <table id="product-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="not-exported-sale">#</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Quantity</th>
                            <th>Warehouse</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Products as $key=>$product)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{date('d-m-Y', strtotime($product->created_at))}}</td>
                            <td>{{$product->name}}</td>
                            <td>{{$product->code}}</td>
                            <td>{{$product->qty}}</td>
                            <td>{{App\Warehouse::find($product->warehouse_id)->name}}</td>
                            <td>{{App\User::find($product->user_id)->name}}</td>
                        </tr> 
                        @endforeach
                    </tbody> 
                    <tfoot class="tfoot active">
                        <tr>
                            <th></th>
                            <th>Total:</th>
                            <th></th>
                            <th></th>
                            <th>0.00</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="vehicle-count">
            <div class="table-responsive mb-4">
                <table id="vehicle-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="not-exported-purchase"></th>
                            <th>{{trans('file.Date')}}</th>
                            <th>Name</th>
                            <th>Chassis No</th>
                            <th>Engine No</th>
                            <th>Warehouse</th>
                            <th>Status</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Raws as $key=>$raw)
                        <tr>
                            <td style="color:rgb(227, 223, 14)">#</td>
                            <td>{{date('d-m-Y', strtotime($raw->created_at))}}</td>
                            <td>{{$raw->name}}  </td>
                            <td>{{$raw->chassis_no}}</td>
                            <td>{{$raw->engine_no}}</td>
                            <td>{{App\Warehouse::find($raw->warehouse_id)->name}}</td>
                            <td>Raw</td>
                            <td>{{App\User::find($raw->user_id)->name}}</td>
                        </tr>
                        @endforeach
                        @foreach($Manufactures as $key=>$manufacture)
                        <tr>
                            <td style="color:rgb(227, 223, 14)">#</td>
                            <td>{{date('d-m-Y', strtotime($manufacture->created_at))}}</td>
                            <td>{{$manufacture->name}} </td>
                            <td>{{$manufacture->chassis_no}}</td>
                            <td>{{$manufacture->engine_no}}</td>
                            <td>{{App\Warehouse::find($manufacture->warehouse_id)->name}}</td>
                            <td>In-Progress</td>
                            <td>{{App\User::find($manufacture->user_id)->name}}</td>
                        </tr>
                        @endforeach
                        @foreach($Finished_Goods as $key=>$good)
                        <tr>
                            <td style="color:rgb(227, 223, 14)">#</td>
                            <td>{{date('d-m-Y', strtotime($good->complete_date))}}</td>
                            <td>{{$good->name}} </td>
                            <td>{{$good->chassis_no}}</td>
                            <td>{{$good->engine_no}}</td>
                            <td>{{App\Warehouse::find($good->warehouse_id)->name}}</td>
                            <td>In-Progress</td>
                            <td>{{App\User::find($good->completed_by)->name}}</td>
                        </tr>
                        @endforeach
                        @foreach($Vehicles as $key=>$Vehicle)
                        <tr>
                            <td style="color:rgb(6, 77, 135)">#</td>
                            <td>{{date('d-m-Y', strtotime($product->created_at))}}</td>
                            <td>{{$Vehicle->name}}</td>
                            <td>{{$Vehicle->chassis_no}}</td>
                            <td>{{$Vehicle->engine_no}}</td>
                            <td>{{App\Warehouse::find($Vehicle->warehouse_id)->name}}</td>
                            <td>Product</td>
                            <td>{{App\User::find($Vehicle->user_id)->name}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="tfoot active">
                        <tr>
                            <th></th>
                            <th colspan="2">Total Vehicles:</th>
                            <th></th>
                            <th >{{ $Raws->sum('qty') + $Vehicles->sum('qty') + $Finished_Goods->sum('qty') + $Manufactures->sum('qty')  }}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>


@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-stock-count-menu").addClass("active");

    $('#product-table').DataTable( {
        "order": [],
        'columnDefs': [
            {
                "orderable": false,
                'targets': 0
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lB>rtip',
        buttons: [
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':visible:Not(.not-exported-sale)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_quotation(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_quotation(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_quotation(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_quotation(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_quotation(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum_quotation(dt, false);
                },
                footer:true
            },
            {
                extend: 'colvis',
                columns: ':gt(0)'
            }
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum_quotation(api, false);
        }
    } );

    function datatable_sum_quotation(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 4 ).footer() ).html(dt_selector.cells( rows, 4, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 4 ).footer() ).html(dt_selector.column( 4, {page:'current'} ).data().sum().toFixed(2));
        }
    }

    $('#vehicle-table').DataTable( {
        "order": [],
        'columnDefs': [
            {
                "orderable": false,
                "searchable": false,
                'targets': 0
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lB>rtip',
        buttons: [
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':visible:Not(.not-exported-purchase)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'colvis',
                columns: ':gt(0)'
            }
        ],

    } );

</script>
@endpush
