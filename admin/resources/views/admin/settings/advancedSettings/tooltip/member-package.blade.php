<div class="form-group file_upload_section package_div row" style="display: block;" id="membership_package">
    <br>
    @foreach ($membership_packages->chunk(4) as $items)
        <div class="row">
            @foreach ($items as $package)
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            {{ $package->name }}
                        </div>
                        <div class="card-body">
                            <input type="file" id="membership-pack-image-{{ $package->id }}" name="membership_pack_image" value="">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
