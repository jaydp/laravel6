@extends('layout')
 
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>List of Tasks</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" id="create-task" href="javascript:void(0)"> Create New Task</a>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <table class="table table-bordered data-table">
        <thead>
            <tr>
				<th>No</th>
				<th>Title</th>
				<th>Description</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

<div class="modal fade" id="ajax-model" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="model-heading"></h4>
            </div>
            <div class="modal-body">
                <form id="task-form" name="task-form" class="form-horizontal">
                   <input type="hidden" name="task_id" id="task_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="title" name="title" placeholder="Please Enter Title" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-12">
                            <textarea id="description" name="description" required="" placeholder="Please Enter Description" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-primary" id="save-btn" value="create">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

	
<script type="text/javascript">
  jQuery(function () {
     
      jQuery.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          }
    });
    
    var table = jQuery('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('tasks-ajax.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'title', name: 'title'},
            {data: 'description', name: 'description'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
     
    jQuery('#create-task').click(function () {
        jQuery('#save-btn').val("create");
		jQuery('#save-btn').html("Save");
        jQuery('#task_id').val('');
        jQuery('#task-form').trigger("reset");
        jQuery('#model-heading').html("Create New Task");
        jQuery('#ajax-model').modal('show');
    });
    
    jQuery('body').on('click', '.edit-tasks', function () {
      var task_id = jQuery(this).data('id');
      jQuery.get("{{ route('tasks-ajax.index') }}" +'/' + task_id +'/edit', function (data) {
          jQuery('#model-heading').html("Edit Task");
          jQuery('#save-btn').val("edit");
          jQuery('#save-btn').html("Save");
          jQuery('#ajax-model').modal('show');
          jQuery('#task_id').val(data.id);
          jQuery('#title').val(data.title);
          jQuery('#description').val(data.description);
      })
   });
    
    jQuery('#save-btn').click(function (e) {
        e.preventDefault();
        jQuery(this).html('Sending..');
    
        jQuery.ajax({
          data: jQuery('#task-form').serialize(),
          url: "{{ route('tasks-ajax.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
     
              jQuery('#task-form').trigger("reset");
              jQuery('#ajax-model').modal('hide');
              table.draw();
         
          },
          error: function (data) {
              console.log('Error:', data);
              jQuery('#save-btn').html('Save');
          }
      });
    });
    
    jQuery('body').on('click', '.delete-tasks', function () {
     
        var task_id = jQuery(this).data("id");
        confirm("Are You sure want to delete !");
      
        jQuery.ajax({
            type: "DELETE",
            url: "{{ route('tasks-ajax.store') }}"+'/'+task_id,
            success: function (data) {
                table.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
     
  });
</script>
@endsection