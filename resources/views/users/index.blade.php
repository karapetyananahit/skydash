<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{route('user.create')}}">Create User</a>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div>
                    <div class="container">
                        <table id="usersTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        @if ($user->profile_img)
                                            <img id="profileImage" src="{{ asset('storage/auth/' . $user->profile_img) }}" alt="Profile Picture" class="rounded-full w-10 h-10 object-cover" />
                                        @else
                                            <p id="noImageText">No image selected.</p>
                                        @endif
                                    </td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <a href="{{ route('user.edit', $user->id) }}">Edit</a> |
                                        <form action="{{ route('user.delete', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return" style="background: none; border: none; color: red; cursor: pointer;">
                                                Delete
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
    </div>
</x-app-layout>
