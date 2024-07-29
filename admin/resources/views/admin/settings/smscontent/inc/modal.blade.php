<div class="modal fade" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="AddModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('smscontent.add') }}" method="post" class="needs-validation"
            enctype="multipart/form-data" onsubmit="addSMSContent(this)" >
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
