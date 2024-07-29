<tr data-id="{{ $KycCategory->id }}" class="categories" id="category_{{ $KycCategory->id }}">
    <input type="hidden" value="{{ $KycCategory->id }}" data-field="id">
    <td data-field="Catogery">{{ $KycCategory->category }}
        @error('category')
        <span class="text-danger">
            {{ $message }}
        </span>
        @enderror
    </td>
    <td>
        <span class="text-danger" id="kyc_cat_err"></span>
    </td>
    <td class="d-none" data-field="id">{{ $KycCategory->id }}</td>
    <td style="width: 100px">
        <div class="d-flex">
            <a class="btn btn-outline-secondary btn-sm edit" title="Edit">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <form action="{{ route('kyc.category.delete', $KycCategory->id) }}" method="post"
                id="DeleteForm">
                <noscript>
                    @csrf
                    @method('delete')
                </noscript>
                <button type="submit" class="btn btn-outline-danger btn-sm ms-2" id="sa-warning" onclick="deleteCategory({{ $KycCategory->id }})">
                    <i class="bx bx-trash-alt"></i>
                </button>
            </form>
        </div>

    </td>
</tr>
<script>
    $(function() {
        var e = {};
        $(".table-edits tr").editable({
            edit: function(t) {
                $(".edit i", this)
                    .removeClass("fa-pencil-alt")
                    .addClass("fa-save")
                    .attr("title", "Save");
            },
            save: function(t) {
                let id = t.id;
                let url = "{{ route('kyc_category_update', ':id') }}";
                url = url.replace(':id', id);
                updateKycCategory(url, t.Catogery)
                    .catch((err) => {
                        if (err.status === 422) {
                            let error = err.responseJSON.errors
                            $('#kyc_cat_err').html(error.category[0])
                        }
                    })
                $(".edit i", this)
                    .removeClass("fa-save")
                    .addClass("fa-pencil-alt")
                    .attr("title", "Edit"),
                    this in e && (e[this].destroy(), delete e[this]);
            },
            cancel: function(t) {
                $(".edit i", this)
                    .removeClass("fa-save")
                    .addClass("fa-pencil-alt")
                    .attr("title", "Edit"),
                    this in e && (e[this].destroy(), delete e[this]);
            },
        });

    });
</script>
