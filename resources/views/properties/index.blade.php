<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Property List</h2>
        <a href="{{ route('properties.create') }}" class="btn btn-primary">+ Add Property</a>
    </div>

    <table class="table table-bordered" id="property-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Region</th>
                <th>Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editPropertyModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="editPropertyForm">
      @csrf
      @method('PUT')

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Property</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id">

          <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control">
          </div>

          <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label>Type</label>
            <select name="type" class="form-select">
              <option value="rent">Rent</option>
              <option value="sale">Sale</option>
            </select>
          </div>

          <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" class="form-control">
          </div>

          <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control">
          </div>

          <div class="mb-3">
            <label>Region</label>
            <select name="region_id" class="form-select">
              @foreach($regions as $region)
                <option value="{{ $region->id }}">{{ $region->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
              <option value="available">Available</option>
              <option value="pending">Pending</option>
              <option value="sold">Sold</option>
            </select>
          </div>
          <div class="mb-3">
    <label>Featured Image</label>
    <div>
        <img id="editPreview" src="" class="img-thumbnail mb-2" width="100">
    </div>
    <input type="file" name="featured_image" id="featured_image" class="form-control">
</div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script>
$(document).ready(function () {

    // Show image preview on file select
    $('#featured_image').on('change', function () {
        let file = this.files[0];

        if (file && file.type.startsWith('image/')) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $('#editPreview').attr('src', e.target.result).show();
            };

            reader.readAsDataURL(file);
        } else {
            $('#editPreview').hide();
        }
    });

    // Initialize DataTable
    const table = $('#property-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("properties.index") }}',
        columns: [
            { data: 'title', name: 'title' },
            { data: 'type', name: 'type' },
            { data: 'region', name: 'region.name' },
            { data: 'price', name: 'price' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Open Edit Modal
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');

        $.get(`/properties/${id}/edit`, function (data) {
            $('#editPropertyForm')[0].reset();

            $('#editPropertyForm input[name="id"]').val(data.id);
            $('#editPropertyForm input[name="title"]').val(data.title);
            $('#editPropertyForm textarea[name="description"]').val(data.description);
            $('#editPropertyForm select[name="type"]').val(data.type);
            $('#editPropertyForm input[name="price"]').val(data.price);
            $('#editPropertyForm input[name="location"]').val(data.location);
            $('#editPropertyForm select[name="region_id"]').val(data.region_id);
            $('#editPropertyForm select[name="status"]').val(data.status);

            if (data.featured_image) {
                $('#editPreview').attr('src', `/storage/${data.featured_image}`);
            } else {
                $('#editPreview').attr('src', '');
            }

            const modal = new bootstrap.Modal(document.getElementById('editPropertyModal'));
            modal.show();
        });
    });

    // Update
    $('#editPropertyForm').submit(function (e) {
        e.preventDefault();
        const id = $('#editPropertyForm input[name="id"]').val();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        $.ajax({
            url: `/properties/${id}`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editPropertyModal'));
                modal.hide();
                alert('Property Updated Successfully.');
                table.ajax.reload();
            },
            error: function (xhr) {
                alert('Update failed. Check validation.');
            }
        });
    });

    // Delete
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        if (confirm('Are you sure you want to delete this property?')) {
            $.ajax({
                url: `/properties/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function () {
                      alert('Property Deleted Successfully.');
                    table.ajax.reload();
                }
            });
        }
    });

    // Restore
    $(document).on('click', '.btn-restore', function () {
        const id = $(this).data('id');

        $.post(`/properties/restore/${id}`, {
            _token: '{{ csrf_token() }}'
        }, function () {
            alert('Property Restored Successfully.');
            table.ajax.reload();
        });
    });

});

</script>
