<div class="form-group file_upload_section rank_div row" style="display: block;" id="rank_details">
    @forelse ($rank_details->chunk(4) as $items)
        <div class="row">
            @foreach ($items as $rank)
            <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            {{ $rank->name }}
                        </div>
                        <div class="card-body">
                            <input type="file" id="rank-pic-{{ $rank->id }}" name="rank_pic"
                                value="{{ old('rank_pic' . $rank->id) }}">
                        </div>
                    </div>
            </div>
            @endforeach
        </div>
    @empty
    @endforelse
</div>
