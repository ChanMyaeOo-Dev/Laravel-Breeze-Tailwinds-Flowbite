<x-layouts.kitchen title="Kitchen Login">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-sm">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-dark">Kitchen Display</h1>
                <p class="text-body mt-2">Sign in to view orders</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-default p-6">
                @if ($errors->any())
                    <div class="mb-4 p-3 text-sm rounded-lg bg-danger/10 text-danger border border-danger/20">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('kitchen.login.submit') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-dark mb-1">Username</label>
                        <input type="text" name="username" id="username"
                            class="w-full px-4 py-2.5 text-sm bg-light border border-default rounded-lg focus:ring-brand focus:border-brand text-dark"
                            value="{{ old('username') }}" required autofocus>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-dark mb-1">Password</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2.5 text-sm bg-light border border-default rounded-lg focus:ring-brand focus:border-brand text-dark"
                            required>
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center">
                        Sign In
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-body mt-6">Powered by SmartServe</p>
        </div>
    </div>
</x-layouts.kitchen>
