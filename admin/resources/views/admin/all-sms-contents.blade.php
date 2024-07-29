@extends('layouts.header')
@section('content')
    <div class="container">
        @include('layouts.alert')
        <div class="row">
            <div class="col-md-8">
                <h4>SMS Content Management</h4>
            </div>
            <div class="col-md-4">
                {{-- <a type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#AddModal"
                    style="float: right;">
                    <i class="fa fa-plus">
                    </i>
                </a> --}}
                <button class="btn btn-success ms-2 btn-sm float-end" data-bs-toggle="modal" data-bs-target="#AddModal" type="button"><i
                    class="fa fa-plus"></i>Add
                </button>

            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-body">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach ($types as $item)
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabContent{{ $item->id }}"
                                    role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                    <span class="d-none d-sm-block text-capitalize">{{ $item->type }}</span>
                                </a>
                            </li>
                        @endforeach

                    </ul>
                    <div class="tab-content p-3 text-muted">
                        @foreach ($types as $data)
                            <div class="tab-pane" id="tabContent{{ $data->id }}" role="tabpanel">
                                <table class="table" id="smscontent">
                                    <tr>
                                       <td>Language</td>
                                        <td>Content</td>
                                        <td>Action</td>
                                    </tr>
                                <tbody>
                                    @forelse($data->content as $smscontent)
                                        @if($smscontent->sms_type_id == $data->id)
                                            <tr>
                                                <td>
                                                    {{$smscontent->Language->name_in_english}}
                                                </td>
                                                <td>{{$smscontent->sms_content}}</td>
                                                <td>
                                                    <a type="button" class="" data-bs-toggle="modal" data-bs-target="#EditModal"
                                                         onclick="get_content({{ $smscontent['id'] }})">
                                                            <i class="fa fa-edit">
                                                            </i>
                                                     </a>
                                                     <a class="btn btn-outline-danger deleteButton" title="delete"
                                                     id="{{ $item->id }}" onclick="formSubmit({{ $smscontent['id'] }})">
                                                     <i class="fas fa-trash"></i></a>
                                                     <form action="{{ route('smscontent.delete', $smscontent['id']) }}" method="post"
                                                        class="d-none" id="deleteForm{{ $smscontent['id'] }}">
                                                        @csrf
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                        @empty
                                        <tr>
                                            No data
                                        </tr>
                                    @endforelse



                                </tbody>

                                </table>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>


        {{-- @include('admin.settings.smscontent.inc.modal') --}}
        {{-- <div class="modal fade" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         --}}

         <div class="modal fade" id="AddModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">


            <div class="modal-dialog" role="document">
            <form action="{{ route('smscontent.add') }}" method="post" class="needs-validation" novalidate enctype="multipart/form-data" onsubmit="addSMSContent(this)" >

                    {{-- <form action="{{ route('smscontent.add') }}" method="post" class="needs-validation" enctype="multipart/form-data" novalidate > --}}

                        <noscript>
                            @csrf
                        </noscript>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">New SMS Content</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group">
                                    <label>SMS Type</label>
                                    <select class="form-control" name="sms_type_id" id="sms_type" >
                                        @foreach($types as $tt)
                                        <option value = "{{$tt->id}}" @if(old('sms_type_id') == $tt->id) selected @endif >{{$tt->type}}</option>
                                        @endforeach


                                    </select>

                                </div>
                                <div class="form-group">
                                    <label>Language</label>
                                    <select class="form-control" name="lang_id" id="lang" >
                                        @foreach($languages as $lang)
                                        <option value = "{{$lang->id}}" @if(old('lang_id') == $lang->id) selected @endif >{{$lang->name_in_english}}</option>
                                        @endforeach


                                    </select>

                                </div>

                                <div class="form-group">
                                    <label>Content</label>
                                    <textarea class="form-control" name="sms_content" required>{{ old('sms_content') }}</textarea>

                                    <input type="hidden" name="date" class="form-control"
                                        value="{{ Carbon\Carbon::now() }}">
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
            <form action="{{ route('smscontent.update') }}" id="edit_form" method="post" class="mt-3"
                enctype="multipart/form-data" onsubmit="UpdateSMSContent(this)">
                <noscript>
                @csrf
                @method('put')
                </noscript>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update SMS Type</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">


                            <div class="form-group">
                                <label>SMS Type</label>
                                <select class="form-control" name="sms_type_id" id="sms_type" disabled>
                                    @forelse($types as $smstype)
                                    <option value = "{{$smstype->id}}" @if(old('sms_type_id') == $smstype->id) selected @endif >{{$smstype->type}}</option>
                                    @empty
                                    @endforelse


                                </select>

                            </div>
                            <div class="form-group">
                                <label>Language</label>
                                <select class="form-control" name="lang_id" id="lang_id" disabled >
                                    @forelse($languages as $lang)
                                    <option value = "{{$lang->id}}" @if(old('lang_id') == $lang->id) selected @endif >{{$lang->name_in_english}}</option>
                                    @empty
                                    @endforelse
                                </select>
                                <input type="hidden" name="content" id="content_id" value="">

                            </div>

                            <div class="form-group">
                                <label>Content</label>
                                <textarea class="form-control" name="sms_content" id="sms_content">{{ old('sms_content') }}</textarea>



                            </div>
                            <div class="form-group">
                                <label>The variables those you can use are</label>
                                <textarea class="form-control" name="variables" id="variables" disabled>{{ old('variables') }}</textarea>


                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endsection
    @push('scripts')
        <script>
             function formSubmit(id) {

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#34c38f",
                        cancelButtonColor: "#f46a6a",
                        confirmButtonText: "Yes, delete it!",
                    }).then(function(t) {
                        if (t.isConfirmed == true) {
                            t.value &&
                                Swal.fire(
                                    "Deleted!",
                                    "Your file has been deleted.",
                                    "success"
                                );
                            let form = document.getElementById("deleteForm" + id);
                            form.submit();
                        }
                    });
                }
            $(document).ready(function() {
                $('.tab-pane').first().addClass('active');
            })

             function get_content(id)
             {
                 var dataString = "id=" + id;
                 $.ajax({
                     type: "GET",
                     url: "{{ route('get.sms.content')}}",
                     data: dataString,
                     success: function(result)
                     {
                         document.getElementById('content_id').value = result['id'];
                         document.getElementById('sms_type').value = result['type'];
                         $('#sms_type').trigger('change');
                         document.getElementById('lang_id').value = result['lang_id'];
                         $('#lang_id').trigger('change');
                         document.getElementById('sms_content').innerText = result['content'];
                         document.getElementById('variables').innerText = result['variables'];

                     },
                     error: function(passParams) {

                     }
                 });

             }
             async function UpdateSMSContent(form)
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
                 notifySuccess(res.message)
                console.log(res)
             console.log(formData);
        }
        async function addSMSContent(form)
             {


                event.preventDefault()

                    var formElements = new FormData(form);
                    for (var [key, value] of formElements)
                     {
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
                    $('#smscontent tr:last').after(res.data)
                    notifySuccess(res.message)

        }

         </script>
     @endpush

