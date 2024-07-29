@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h4>Content Management</h4>
    <br>
    @include('admin.settings.links_contentmanagement')
    <div class="container">
        <div class="panel-body">
                 <form method="post" method="{{ route('termsconditions.update') }}" enctype="multipart-formdata">
                    @csrf
                    <br>
                     <div class="row">
                     <div class="form-group">
                         <input type="text" class="form-control" name="language" value="{{$language->name}}" >
                         <input type="hidden" class="form-control" name="language_id" value="{{$language->id}}" >
                     </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <textarea id="summernote" name="content">
                                @if($termsandcond)
                                {!!$termsandcond->terms_and_conditions!!}
                                @endif
                                {{-- {!!$termsandcond->terms_and_conditions!!} --}}
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit">Update</button>
                    </div>
                  </form>



        </div>
    </div>
</div>
@push('scripts')
    <script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
    </script>
  @endpush
@endsection
