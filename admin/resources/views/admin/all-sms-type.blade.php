@extends('layouts.header')
@section('content')
    <div class="container mt-5">
        @include('layouts.alert')
        <div class="row">
            <div class="row">
                <div class="col-md-8">
                    <h4>SMS Types</h4>
                </div>
                <div class="col-md-4">
                    {{-- <a type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#AddModal"
                        style="float: right;">
                        <i class="fa fa-plus">
                        </i>
                    </a> --}}
                </div>
            </div>

            <table class="table " id="sms_type">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Type</th>
                        <th scope="col">Variables</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $item)
                       <tr>
                            <th scope="row"> {{ $loop->index + $types->firstItem() }}</th>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->variables }}</td>
                            <td>
                                <a href="{{route('toggle.smstype',$item['id'])}}">

                                    @if ($item->status == 'active')
                                        <span class="alert-info">Active</span>
                                    @endif
                                    @if ($item->status == 'inactive')
                                        <span class="alert-warning">In active</span>
                                    @endif
                                </a>

                            </td>
                            <td>{{ Carbon\Carbon::parse($item->date)->toDateString() }}</td>
                            <td>

                                {{-- <form action="{{ route('smstype.delete', $item['id']) }}" method="post">
                                    @csrf

                                    <button onclick="confirm('Are you sure?')" type="submit" ><i
                                            class="fa fa-trash"></i></button>
                                </form> --}}
                                <a type="button" class="" data-bs-toggle="modal" data-bs-target="#EditModal"
                                    onclick="get_type({{ $item['id'] }})">
                                    <i class="fa fa-edit">
                                    </i>
                                </a>

                            </td>
                        </tr>

                    @empty
                        <tr>
                            No data found!
                        </tr>
                    @endforelse

                </tbody>

            </table>
            <div class="row">
                {{ $types->links() }}
            </div>


        </div>
        <div class="modal fade" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="AddModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="{{ route('smstype.add', null) }}" method="post" class="mt-3"
                    enctype="multipart/form-data" onsubmit="addSMStype(this)">
                    <noscript>
                        @csrf

                    </noscript>

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">New SMS Type</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <div class="form-group">
                                    <label>Type</label>
                                    <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                                    <input type="hidden" name="status" class="form-control" value="active">
                                    <input type="hidden" name="date" class="form-control"
                                        value="{{ Carbon\Carbon::now() }}">
                                </div>
                                <div class="form-group">
                                    <label>Variables</label>
                                    <textarea class="form-control" name="variables">{{ old('variables') }}</textarea>
                                </div>



                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="EditModal"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="{{ route('smstype.update') }}" id="edit_form" method="post" class="mt-3"
                    enctype="multipart/form-data" onsubmit="UpdateSMSType(this)">
                    <noscript>
                        @csrf
                        @method('put')
                        </noscript>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">New SMS Type</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <div class="form-group">
                                    <label>Type</label>
                                    <input type="text" name="type" class="form-control" value="" id="type">
                                    <input type="hidden" name="status" class="form-control" value="active">
                                    <input type="hidden" name="id" class="form-control" value="" id="type_id">
                                    <input type="hidden" name="date" class="form-control"
                                    value="{{ Carbon\Carbon::now() }}">

                                </div>
                                <div class="form-group">
                                    <label>Variables</label>
                                    <textarea class="form-control" name="variables" id="variables">{{ old('variables') }}</textarea>
                                </div>



                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>





    </div>
@endsection
 @push('scripts')
    <script>
         async function addSMStype(form)
         {
            event.preventDefault()
            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url     = form.action
            let data    = getForm(form)

            const res = await $.post(`${url}`, data)
            .catch( (err) => {
                if(err.status === 422) {
                    formvalidationError(form, err)
                }
            })
            $('#AddModal').modal('hide')
            $('#sms_type tr:last').after(res.data)
           // notifySuccess(res.message)
        }

        async function UpdateSMSType(form)
        {
            event.preventDefault()
            let data    = getForm(form)
            data._method = "put";
            let url         = form.action
            
            const res       = await $.post(`${url}`, data)
            .catch( (err) => {
                if(err.status === 422) {
                    formvalidationError(form, err)
                }
            })
            $('#EditModal').modal('hide')
            // console.log(formData);
        }




        function get_type(id) {
            var dataString = "id=" + id;


            $.ajax({
                type: "GET",
                url: "{{ route('get.sms.type')}}",
                data: dataString,
                success: function(result)
                {

                    document.getElementById('type').value = result.type;
                    document.getElementById('type_id').value = result.id;
                    document.getElementById('variables').innerText = result.variables;
                },
                error: function(passParams) {

                }
            });

        }
    </script>
@endpush


