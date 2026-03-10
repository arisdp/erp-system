<section>
    <p class="text-muted mb-4">
        {{ __("Update your account's profile information and email address.") }}
    </p>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="name" class="font-weight-bold">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="font-weight-bold">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-warning">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn btn-link btn-sm p-0 m-0 align-baseline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                </div>
            @endif
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                <i class="fas fa-save mr-1"></i> {{ __('Save') }}
            </button>
        </div>
    </form>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
</section>
