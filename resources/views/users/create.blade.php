

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h1>Create New User</h1>

                <div class="content pt-5" id="kt_content">
                    <form class="pt-3" method="POST" action="{{ route('user.store') }}">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="username" class="form-control form-control-lg @error('username') is-invalid @enderror" id="exampleInputUsername1" placeholder="Username">
                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="exampleInputEmail1" placeholder="Email">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>

                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <select class="form-control form-control-lg" name="country" id="exampleFormControlSelect2">
                                <option disabled value="">Country</option>
                                <option value="usa">United States of America</option>
                                <option value="uk">United Kingdom</option>
                                <option value="india">India</option>
                                <option value="germany">Germany</option>
                                <option value="argentina">Argentina</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control form-control-lg  @error('password') is-invalid @enderror" id="exampleInputPassword1" placeholder="Password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">Create</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>


