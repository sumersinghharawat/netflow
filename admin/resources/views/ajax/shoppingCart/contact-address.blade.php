<div class="row">
    @forelse($addresses->chunk(3) as $chunk)
        @foreach($chunk as $item)
        <div class="col-md-4" id="address{{$item->id}}">
            <div class="modal-dialog " role="document">
                <div class="modal-content @if($item->is_default) bg-success bg-soft @endif">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $item->name }}</h5>
                        <form action="{{ route('cart.address-delete', $item->id) }}" method="post"
                            >
                            <noscript>
                                @csrf
                                @method('delete')
                            </noscript>
                            <button type="submit" class="btn-close" id="sa-warning" onclick="deleteAddress({{ $item->id }})" aria-label="Close">
                            </button>
                        </form>
                    </div>
                    <div class="modal-body">
                        <p>{{ $item->address }}</p>
                        <p>{{ $item->zip }}</p>
                        <p>{{ $item->city }}</p>
                        <p>{{ $item->mobile }}</p>
                    </div>
                    @if (!$item->is_default)
                    <div class="modal-footer">
                        <button type="button" id="default-btn{{$item->id}}" class="btn btn-secondary default-btn waves-effect waves-light" onclick="makeDefault({{ $item->id }})">{{__('cart.set_default')}}</button>
                    </div>
                    @endif

                </div><!-- /.modal-content -->
            </div>

            <!-- end col -->
        </div>
        @endforeach
    @empty
    <p>{{ __('common.no_address_found') }}</p>
    @endforelse
</div>
@push('scripts')
<script>
const deleteAddress = async (id) => {
    event.preventDefault()
    let confirm = await confirmSwal()
    if (confirm.isConfirmed == true) {
        let url = "{{route('cart.address-delete', ':id')}}";
        url    = url.replace(":id", id)
        const res = await $.post(url, {
            '_method':"delete",
        })

        notifySuccess(res.message)
        await $(`#address${id}`).remove()
    }
};
</script>
@endpush
