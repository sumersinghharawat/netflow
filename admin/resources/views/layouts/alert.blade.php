@if ($errors->any())
    @push('scripts')
        <script>
            $(document).ready(function() {
                toastr.error("{{ __($errors->all()[0]) }}", 'opps', {
                    timeOut: 5000,
                }, {
                    closeButton: true,
                })
            });
        </script>
    @endpush
@endif

@if (session('success'))
    @push('scripts')
        <script>
            $(document).ready(function() {
                let message = "{{ session('success') }}";
                toastr.success(message, {
                    timeOut: 5000,
                }, {
                    closeButton: true,
                }, {
                    progressBar: true,
                });
            })
        </script>
    @endpush
@endif

@if (session('error'))
    {{-- <div class="alert alert-danger mt-5">{{ session('error') }}</div> --}}
    @push('scripts')
        <script>
            $(document).ready(function() {
                let message = "{{ session('error') }}"
                toastr.error(message, 'opps', {
                    timeOut: 5000,
                }, {
                    closeButton: true,
                })
            });
        </script>
    @endpush
@endif
