@push('scripts')
    <script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.tailwindcss.js') }}"></script>
    <script>
        let table = new DataTable('#DataTable');
    </script>
@endpush
