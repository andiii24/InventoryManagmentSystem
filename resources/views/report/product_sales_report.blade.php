@extends('layout.main')
@section('content')
    @if (session()->has('create_message'))
        <div class="alert alert-success alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('create_message') }}
        </div>
    @endif
    @if (session()->has('edit_message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('edit_message') }}</div>
    @endif
    @if (session()->has('import_message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('import_message') }}</div>
    @endif
    @if (session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif
    @if (session()->has('message'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
    @endif
    <div class="container-fluid text-center"style="padding: 2%; padding-left:4%">
        <div class="d-flex align-items-center py-1">
            <h3 style="margin-left: 20px;"> <i class="fa fa-file"></i>    Product Sale Report  </h3>
        </div>
    </div>
    <div class="container " style="padding-left:4%">
        <div class="row">
            <form method="post" action="{{ route('sale.product_sales_report.sortedByDate') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <label for="warehouse_option">Choose Ware House</label>
                        <select class="form-control" name="warehouse" id="ware_house">
                            <option value="0">All</option>
                            @foreach ($lims_warehouse_list as $w)
                                {{-- @php
                                $selected = '';
                                if ($w->id == 1) {
                                    $selected = 'selected="selected"';
                                }
                            @endphp --}}
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <section> --}}
                    {{-- <div class="col-md-6" style="">
                        <label>{{ trans('file.Choose Your Date') }}</label>
                        <div class="input-group">
                            <input type="text" value="{{ $starting_date }} To {{ $ending_date }}"
                                class="daterangepicker-field form-control" required />
                            <input type="hidden" name="start_date" />
                            <input type="hidden" name="end_date" />
                        </div>
                    </div>
                </div> --}}
                    {{-- <div class="col-md-3">
                        <label for="date_option">Choose Date</label>
                        <select class="form-control" name="date_picker" id="date">
                            <option value="0">All</option>
                            <option value="1">Today</option>
                            <option value="2">Week</option>
                            <option value="3">This Month</option>
                            <option value="4">This Year</option>

                        </select>
                    </div> --}}
                    {{-- <div class=" container row"> --}}

                    <div class="col-md-3">
                        <label for="date_Range">Date Range</label>

                        {{-- <h1>Laravel Bootstrap Datepicker</h1> --}}
                        <input class="date form-control" type="text" placeholder="from" id="start_date"
                            name="start_date" required>
                    </div>
                    <label style="color: transparent;width:0%">T</label>
                    <div class="col-md-3">
                        <label for="date_Range"style="color: transparent">Date Interval</label>
                        {{-- <h1>Laravel Bootstrap Datepicker</h1> --}}
                        <input class="date form-control" type="text" placeholder="To" id="end_date" name="end_date">
                    </div>

                    {{-- </div> --}}
                    <div style="padding-top: 3%;padding-left:2%">
                        <button type="submit" id="submit-btn" class="btn btn-primary btn-user">Go</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<br>
    <div class="tab-content" style="padding-left:1%">
        <div role="tabpanel" class="tab-pane fade show active" id="product_table">
            <div class="table-responsive mb-4">

                <table id="product-data-table" class="table table-bordered" style="width: 100%">
                    <thead>
                        <tr>
                            {{-- <th class="not-exported"></th> --}}
                            <th>Reference</th>
                            <th>Sold Date</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Paid Amount</th>
                            <th>Payment status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!$product_sale_id->isEmpty())
                        @foreach ($product_sale_id as $product_sale_id)
                            <tr>
                                <td>{{ $product_sale_id->reference_no }}</td>
                                <td>{{ $product_sale_id->created_at }}</td>
                                <td>{{ $product_sale_id->item }}</td>
                                <td>{{ $product_sale_id->total_qty }}</td>
                                <td>{{ $product_sale_id->total_price }}</td>
                                <td>{{ $product_sale_id->paid_amount }}</td>
                                <td>

                                    @if ($product_sale_id->payment_status == 1)
                                        <p class="badge badge-success"> Completed</p>
                                    @else
                                        <p class="badge badge-danger">Pending</p>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <div class="alert alert-info text-center"> No data</div>
                        @endif
                    </tbody>
                </table>
               
            </div>
        </div>
    </div>
    </section>
@endsection
@push('scripts')
    <script type="text/javascript">
    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #report-product-menu").addClass("active");
        $('.date').datepicker({
            format: 'yyyy-mm-dd'
        });

        // $('#submit-btn').click(function(e) {
        //     if ($('#start_date').val() == "" && $('#end_date').val() != "") {
        //         alert('Please set start date !');
        //         e.preventDefault();
        //         return false;
        //     } else {
        //         return true;
        //     }
        // });
    </script>
@endpush
