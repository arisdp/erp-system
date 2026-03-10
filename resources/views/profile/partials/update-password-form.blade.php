<section>
    <p class="text-muted mb-4">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="current_password" class="font-weight-bold">{{ __('Current Password') }}</label>
            <input id="current_password" name="current_password" type="password" 
                   class="form-control {{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}" 
                   autocomplete="current-password">
            @if ($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="form-group">
            <label for="password" class="font-weight-bold">{{ __('New Password') }}</label>
            <input id="password" name="password" type="password" 
                   class="form-control {{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}" 
                   autocomplete="new-password">
            @if ($errors->updatePassword->has('password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="font-weight-bold">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" 
                   class="form-control {{ $errors->updatePassword->has('password_confirmation') ? 'is-invalid' : '' }}" 
                   autocomplete="new-password">
            @if ($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-secondary px-4 font-weight-bold shadow-sm text-white">
                <i class="fas fa-key mr-1"></i> {{ __('Update Password') }}
            </button>
        </div>
    </form>
</section>
