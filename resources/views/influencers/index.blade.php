@include('layouts.header')
@include('layouts.navigation')
<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Influencers List') }}
        </h2>
    </div>
</header>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <a href="{{ route('influencer.form') }}" class="btn btn-primary mb-3">Create User</a>
            <div class="card p-4">
                <table id="usersTable" class="table table-striped">
                    <thead class="bg-primary text-white thead-rounded">
                    <tr class="border border-primary">
                        <th>ID</th>
                        <th>User</th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($influencers as $influencer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ asset($influencer->image ? 'storage/auth/' . $influencer->image : 'images/default-profile.png')}}"
                                     alt="Profile Picture" class="rounded-circle" width="40" height="40">
                            </td>
                            <td>{{ $influencer->name }}</td>
                            <td>{{ $influencer->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="text-center">
                                <a href="{{ route('influencer.form', $influencer->id) }}" class="btn text-primary border border-primary rounded-2">
                                    <i class="mdi mdi-lead-pencil"></i>
                                </a>
                                <form action="{{ route('influencer.delete', $influencer->id) }}" method="POST" id="delete-form-{{ $influencer->id }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn text-danger border border-primary rounded-2" onclick="confirmDelete({{ $influencer->id }})">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + userId).submit();
                }
            });
        }
    </script>
@endsection
@include('layouts.footer')
