<x-guest-layout>
    <p class="login-box-msg">Sign in to start your session</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}"
                required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 w-100" />
        </div>

        <!-- Password -->
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required
                autocomplete="current-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 w-100" />
        </div>

        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">
                        Remember Me
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <!-- /.col -->
        </div>
    </form>

    <p class="mb-1 mt-3">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">I forgot my password</a>
        @endif
    </p>
</x-guest-layout>
