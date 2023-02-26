@extends('layout.main') @section('content')

@if($errors->has('name'))
<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
@endif
@if($errors->has('image'))
<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('image') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <!-- Trigger the modal with a button -->
        <button type="button" onclick=" $('#createModal').modal('show');" class="btn btn-info" ><i class="dripicons-plus"></i> {{trans("file.Add Category")}}</button>&nbsp;
    </div>
    <br> 
    <div class="table-responsive">
        <table id="category-table" class="table" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.category')}}</th>
                    <th>{{trans('file.Parent Category')}}</th>
                    <th>{{trans('file.Number of Product')}}</th>
                    <th>{{trans('file.Stock Quantity')}}</th>
                    <th>{{trans('file.Stock Worth (Price/Cost)')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
        </table>
    </div>
</section>
   
<!-- Edit Modal -->
<div id="editModal" tabindex="-1"  aria-hidden="true" class="modal fade show text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" action="#" id="kt_modal_new_address_form_update">
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Category')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
              <div class="col-md-6 form-group">
                <input type="hidden" name="edit_id" id="edit_id">
                <input type="hidden" name="pre_name" id="pre_name">
                  <label>{{trans('file.catname')}} *</label>
                  <input type="text" class="form-control" id="edit_name" name="edit_name"  placeholder="Type category name...">
                  <div id="edit_error_msg" class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;"> </div>
              </div>
              <br>
              <div class="col-md-6 form-group">
                  <label>{{trans('file.Parent Category')}}</label>
                  <select name="edit_parent_id" class="form-control selectpicker" id="edit_parent_id">
                      <option value="">No {{trans('file.parent')}}</option>
                      @foreach($lims_category_all as $category)
                      <option value="{{$category->id}}">{{$category->name}}</option>
                      @endforeach
                  </select>
              </div>
   <br>
          <div class="form-group">
              <input  id="catUpdate" type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
        </div>
          </div>
        </form>
      </div>
    </div>
  </div> 

<!-- Create Modal -->  
<div id="createModal" tabindex="-1" aria-hidden="true" class="modal fade show text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <form class="form" action="#" id="kt_modal_new_address_form_create">
        <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Category')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
            <div class="col-md-6 form-group">
                <label>{{trans('file.catname')}} *</label>
                <input type="text" class="form-control" id="cat_name" name="cat_name"  placeholder="Type category name...">
                <div class="print-error-msg text-danger" style="display:none; font-size: 13px; font-family: Bodoni MT;">
            </div>
            </div>
            <div class="col-md-6 form-group">
                <label>{{trans('file.Parent Category')}}</label>
                <select name="parent_id" class="form-control" id="parent">
                    <option value="">No {{trans('file.parent')}}</option>
                    @foreach($lims_category_all as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
            <br>
            <div class="form-group">
              <input id="catCreate"  type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
            </div>
        </div>
        </form>
      </div>
    </div>
</div>
@foreach($lims_category_all as $ExpDoc)
        <input type="hidden" name="ref[]" value="{{ $ExpDoc->name }}">
@endforeach
@endsection 
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.5.0/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $("ul#vehicle").siblings('a').attr('aria-expanded','true');
    $("ul#vehicle").addClass("show");
    $("ul#vehicle #vehicle-category-menu").addClass("active");
// New Javascript Added 
var myArray = new Array();
    $("input[name='ref[]']").each(function(){
        myArray.push($(this).val());
    });
// Category Create
$("#catCreate").click(function(e){
        e.preventDefault();
        $(".print-error-msg").css('display','none');
        if($('#cat_name').val() == ''){
            var value = "Please enter category name ...";
            $(".print-error-msg").css('display','block');
            $(".print-error-msg").text(value);
            return false;
        }
        else if(jQuery.inArray($('#cat_name').val(),myArray) !== -1){
            var value = "Category already exists. Please add different one.";
            $(".print-error-msg").css('display','block');
            $(".print-error-msg").text(value);
            return false;
        }else{
            $(".print-error-msg").css('display','none');
        }
        var data = $('#kt_modal_new_address_form_create').serialize();

        $.ajax({
            url:"{{ route('vehicle.category.store')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                if($.isEmptyObject(data.error)){
                    var msg = 'Category added successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('vehicle.categories') }}';
                    });
                }else{
                    printErrorMsg(data.error);
                }
            }
        });
    });
     // Category Update
     $("#catUpdate").click(function(e){
        e.preventDefault();
        $("#edit_error_msg").css('display','none');
        if($('#edit_name').val() == ''){
            var value = "Please enter category name ...";
            $("#edit_error_msg").css('display','block');
            $("#edit_error_msg").text(value);
            return false;
        }
        else if($('#edit_name').val() == $('#pre_name').val()){
            var value = "Sorry, There is no updates. Please add different one.";
            $("#edit_error_msg").css('display','block');
            $("#edit_error_msg").text(value);
            return false;
        }
        else if(jQuery.inArray($('#edit_name').val(),myArray) !== -1){
            var value = "Category already exists. Please add different one.";
            $("#edit_error_msg").css('display','block');
            $("#edit_error_msg").text(value);
            return false;
        }else{
            $(".print-error-msg").css('display','none');
        }
        var data = $('#kt_modal_new_address_form_update').serialize();

        $.ajax({
            url:"{{ route('vehicle.category.update')}}",
            type:"POST",
            data:{
                "_token" : "{{ csrf_token() }}",
                "data":data,
            },
            success:function(data){
                if($.isEmptyObject(data.error)){
                    var msg = 'Category Updated successfully.';
                    swal.fire({
                        title: "Success",
                        text: msg, 
                        icon: "success",
                        button: "Ok"
                    }).then(function(){ 
                        window.location.href='{{ route('vehicle.categories') }}';
                    });
                }else{
                    printErrorMsg(data.error);
                }
            }
        });
    });
    // Category Delete
    $(document).on("click", ".DeleteCategoryDialog", function(){
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        swal.fire({ 
            title: "If you delete category all vehicles under this category will also be deleted. Are you sure want to delete "+name+"?",
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
                    url: "{{url('/vehicle/category/delete')}}/" + id,
                    dataType: 'JSON',
                    success: function (results) {
                        if (results.success === true) {
                            swal.fire({
                                    title: "Success",
                                    text: "Category "+name+" deleted successfully.",
                                    icon: "success",
                                    button: "Ok"
                                }).then(function(){ 
                                    location.reload();
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
    //End
    function confirmDelete() {
      if (confirm("If you delete category all products under this category will also be deleted. Are you sure want to delete?")) {
          return true;
      }
      return false;
    }

    var category_id = [];

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".open-EditCategoryDialog", function(){
          var url= "{{url('/vehicle/category/edit')}}/";
          var id = $(this).data('id').toString();
          url = url.concat(id);

          $.get(url, function(data){
            $("#editModal input[name='edit_id']").val(data['id']);
            $("#editModal input[name='pre_name']").val(data['name']);
            $("#editModal input[name='edit_name']").val(data['name']);
            $("#editModal select[name='edit_parent_id']").val(data['parent_id']);
            $('.selectpicker').selectpicker('refresh');
          });
          $("#editModal").modal('show');
    });

    $('#category-table').DataTable( {
        responsive: true,
        "processing": true,
        "serverSide": true,
        "ajax":{
            url:"{{ route('vehicle.categories.data')}}",
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
            {"data": "parent_id"}, 
            {"data": "number_of_product"},
            {"data": "stock_qty"},
            {"data": "stock_worth"},
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
        order:[['1', 'asc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 2, 3, 4, 5, 6]
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
        buttons: [
            {
                text: '<button  style="color:#1a0000; border:none; background-color:inherit;"> Delete Selected <i title="delete selected" class="dripicons-cross"></i> </button>',
                className: 'btn btn-warning',
                action: function ( e, dt, node, config ) {
                        category_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                category_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(category_id.length ) {
                            swal.fire({ 
                            title: "If you delete categories, all vehicles under the categories will also be deleted. Are you sure want to delete ?",
                            text: "You will not be able to revert this action.",
                            type: "warning",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#0275d8",
                            confirmButtonText: "Yes, Delete it!",
                            closeOnConfirm: false,
                            preConfirm: function(result) {
                          
                            $.ajax({
                                type:'POST',
                                url:"{{ route('vehicles.category.deleteselected')}}",
                                data:{
                                    "_token" : "{{ csrf_token() }}",
                                    brandIdArray: category_id,
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
                                            window.location.href='{{ route('vehicle.categories') }}';
                                        });
                                        }else{
                                            console.log(data.error);
                                        }
                                        
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                           
                        }
                        else if(!category_id.length)
                        {
                            alert('No category is selected!');
                        }
                        else
                        {
                            alert('You cancelled the action !');
                        }
                      
                    
                   
                }
            },
          
        ],
    } );
</script>
@endpush
