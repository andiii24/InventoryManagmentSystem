@extends('layout.main')
@section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
      @php
        if($general_setting->theme == 'default.css'){
          $color = '#733686';
          $color_rgba = 'rgba(115, 54, 134, 0.8)';
        }
        elseif($general_setting->theme == 'green.css'){
            $color = '#2ecc71';
            $color_rgba = 'rgba(46, 204, 113, 0.8)';
        }
        elseif($general_setting->theme == 'blue.css'){
            $color = '#3498db';
            $color_rgba = 'rgba(52, 152, 219, 0.8)';
        }
        elseif($general_setting->theme == 'dark.css'){
            $color = '#34495e';
            $color_rgba = 'rgba(52, 73, 94, 0.8)';
        }
      @endphp
      <div class="row">
        <div class="container-fluid">
          <div class="col-md-12">
              <h1 style="display:none;">Abdulwasi Ahmed Import Export</h1>
            <div class="brand-text float-left mt-4">
                <h3>{{trans('file.welcome')}} <span>{{Auth::user()->name}}</span> </h3>
            </div>
            <div class="filter-toggle btn-group">
              <button class="btn btn-secondary date-btn " data-start_date="{{date('Y-m-d')}}" data-end_date="{{date('Y-m-d')}}">{{trans('file.Today')}}</button>
              <button class="btn btn-secondary date-btn" data-start_date="{{date('Y-m-d', strtotime(' -7 day'))}}" data-end_date="{{date('Y-m-d')}}">{{trans('file.Last 7 Days')}}</button>
              <button class="btn btn-secondary date-btn active" data-start_date="{{date('Y').'-'.date('m').'-'.'01'}}" data-end_date="{{date('Y-m-d')}}">{{trans('file.This Month')}}</button>
              <button class="btn btn-secondary date-btn" data-start_date="{{date('Y').'-01'.'-01'}}" data-end_date="{{date('Y').'-12'.'-31'}}">{{trans('file.This Year')}}</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Counts Section -->
      <section class="dashboard-counts">
        <div class="container-fluid">
          <div class="row">
           
            <div class="col-md-12 form-group">
              <div class="row"> 
                <!-- Count item widget-->
                <div class="col-sm-3" style="background-color: #3e70c9 !important;">
                  <div class="wrapper count-title">
                    <div class="icon"><i class="fa fa-cog" style="color: #453686"></i></div>
                    <div>
                        <div class="count-number product-data">{{number_format((float)$products, 2, '.', '')}}</div>
                        <div class="name"><strong style="color: #453686">Total Products</strong></div>
                    </div>
                  </div>
                </div>
                <!-- Count item widget-->
                <div class="col-sm-3" style="background-color: #3e70c9 !important;">
                    <div class="wrapper count-title">
                      <div class="icon"><i class="fa fa-car" style="color: #368661"></i></div>
                      <div> 
                          <div class="count-number vehicle-data">{{number_format((float)$vehicles, 2, '.', '')}}</div>
                          <div class="name"><strong style="color: #368661">{{ trans('file.vehicles') }}</strong></div>
                      </div>
                    </div>
                  </div>
                 <!-- Count item widget-->
                 <div class="col-sm-3" style="background-color: #f59345 !important; ">
                    <div class="wrapper count-title">
                      <div class="icon"><i class="fa fa-industry" style="color: #733686"></i></div>
                      <div>
                          <div class="count-number manufacture-data">{{number_format((float)$manufactures, 2, '.', '')}}</div>
                          <div class="name"><strong style="color: #733686">{{ trans('file.inpvehicles') }}</strong></div>
                      </div>
                    </div>
                  </div>
                  <!-- Count item widget-->
                  <div class="col-sm-3" style="background-color: #f59345 !important;">
                    <div class="wrapper count-title">
                      <div class="icon"><i class="fas fa-box" style="color: #297ff9"></i></div>
                      <div>
                          <div class="count-number raw-data">{{number_format((float)$raws, 2, '.', '')}} Carton</div>
                          <div class="name"><strong style="color: #297ff9">Raw Vehicles</strong></div>
                      </div>
                    </div>
                  </div>
                 <!-- Count item widget-->
                 <div class="col-sm-3" style="background-color: #001f3f!important;">
                    <div class="wrapper count-title">
                      <div class="icon"><i class="fa fa-product-hunt" style="color: #453686"></i></div>
                      <div>
                          <div class="count-number vehicle-p-data">{{number_format((float)$vehicleproducts, 2, '.', '')}}</div>
                          <div class="name"><strong style="color: #453686">Vehicle Products</strong></div>
                      </div>
                    </div>
                  </div>
                  <!-- Count item widget-->
                  <div class="col-sm-3" style="background-color: #001f3f!important;">
                    <div class="wrapper count-title">
                      <div class="icon"><i class="fa fa-shopping-cart" style="color: #368661"></i></div>
                      <div>
                          <div class="count-number v-profit-data">{{number_format((float)$vehiclesales, 2, '.', '')}} ETB</div>
                          <div class="name"><strong style="color: #368661">{{trans('file.vehiclesale')}}</strong></div>
                      </div>
                    </div>
                  </div>
                   <!-- Count item widget-->
                   <div class="col-sm-3" style="background-color: #43b968!important;">
                    <div class="wrapper count-title">
                      <div class="icon"><i class="fa fa-shopping-cart" style="color: #733686"></i></div>
                      <div>
                          <div class="count-number p-profit-data">{{number_format((float)$productsales, 2, '.', '')}} ETB</div>
                          <div class="name"><strong style="color: #733686">Product Sales</strong></div>
                      </div>
                    </div>
                  </div>
                <!-- Count item widget -->
                <div class="col-sm-3" style="background-color: #43b968!important;">
                    <div class="wrapper count-title">
                      <div class="icon"><i class="fa fa-shopping-cart" style="color: #297ff9"></i></div>
                      <div>
                          <div class="count-number t-profit-data">{{number_format((float)$productsales + $vehiclesales, 2, '.', '')}}  ETB</div>
                          <div class="name"><strong style="color: #297ff9">Total Sales</strong></div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>

          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header d-flex align-items-center">
                    <h4>Sale Report</h4>
                  </div>
                  <div class="card-body">
                    <canvas id="saleChart" data-sale_chart_value = "{{json_encode($yearly_sale_amount)}}" data-purchase_chart_value = "{{json_encode($yearly_purchase_amount)}}" data-label1="Vehicle Sale" data-label2="Product Sale"></canvas>
                  </div> 
                </div>
              </div>
            </div>
          </div>
      </section>


@endsection

@push('scripts')
<script type="text/javascript">
    // Show and hide color-switcher
    $(".color-switcher .switcher-button").on('click', function() {
        $(".color-switcher").toggleClass("show-color-switcher", "hide-color-switcher", 300);
    });

    // Color Skins
    $('a.color').on('click', function() {
        /*var title = $(this).attr('title');
        $('#style-colors').attr('href', 'css/skin-' + title + '.css');
        return false;*/
        $.get('setting/general_setting/change-theme/' + $(this).data('color'), function(data) {
        });
        var style_link= $('#custom-style').attr('href').replace(/([^-]*)$/, $(this).data('color') );
        $('#custom-style').attr('href', style_link);
    });

    $(".date-btn").on("click", function() {
        $(".date-btn").removeClass("active");
        $(this).addClass("active");
        var start_date = $(this).data('start_date');
        var end_date = $(this).data('end_date');
        $.get('dashboard-filter/' + start_date + '/' + end_date, function(data) {
            dashboardFilter(data);
        });
    });

    function dashboardFilter(data){
        $('.product-data').hide();
        $('.product-data').html(parseFloat(data[0]).toFixed(2));
        $('.product-data').show(500);

        $('.vehicle-data').hide();
        $('.vehicle-data').html(parseFloat(data[1]).toFixed(2));
        $('.vehicle-data').show(500);

        $('.manufacture-data').hide();
        $('.manufacture-data').html(parseFloat(data[3]).toFixed(2));
        $('.manufacture-data').show(500);

        $('.raw-data').hide();
        $('.raw-data').html(parseFloat(data[2]).toFixed(2));
        $('.raw-data').show(500);

        $('.vehicle-p-data').hide();
        $('.vehicle-p-data').html(parseFloat(data[4]).toFixed(2));
        $('.vehicle-p-data').show(500);

        $('.v-profit-data').hide();
        $('.v-profit-data').html(parseFloat(data[5]).toFixed(2));
        $('.v-profit-data').show(500);

        $('.p-profit-data').hide();
        $('.p-profit-data').html(parseFloat(data[6]).toFixed(2));
        $('.p-profit-data').show(500);

        $('.t-profit-data').hide();
        $('.t-profit-data').html(parseFloat(data[5] + data[6]).toFixed(2));
        $('.t-profit-data').show(500);
    }
</script>
@endpush
