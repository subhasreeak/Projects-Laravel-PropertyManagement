<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<div class="container">
    <h2 class="mb-4">Create New Property</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control"  required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description *</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Type *</label>
            <select name="type" class="form-select" required>
                <option value="">Choose Type</option>
                <option value="rent" >Rent</option>
                <option value="sale" >Sale</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Price *</label>
            <input type="number" name="price" step="0.01" class="form-control"  required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location *</label>
            <input type="text" name="location" class="form-control"  required>
        </div>

        <div class="mb-3">
            <label class="form-label">Region *</label>
            <select name="region_id" class="form-select" required>
                <option value="">Choose Region</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                        {{ $region->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Status *</label>
            <select name="status" class="form-select" required>
                <option value="">Choose Status</option>
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
            </select>
        </div>

       <div class="mb-3">
    <label class="form-label">Featured Image</label>
    <input type="file" name="featured_image" class="form-control" id="featured_image_input">
    <div id="image_preview" class="mt-2">
        <img id="preview_img" src="#" alt="Image Preview" style="max-width: 300px; display: none;" class="img-thumbnail">
    </div>
</div>


        <div class="mb-3">
            <button class="btn btn-primary">Save Property</button>
            <a href="{{ route('properties.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $('#featured_image_input').on('change', function () {
        let file = this.files[0];

        if (file && file.type.startsWith('image/')) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $('#preview_img').attr('src', e.target.result).show();
            };

            reader.readAsDataURL(file);
        } else {
            $('#preview_img').hide();
        }
    });
</script>
