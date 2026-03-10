<section>
    <p class="text-muted mb-4">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>

    <button type="button" class="btn btn-danger font-weight-bold px-4 shadow-sm" data-toggle="modal" data-target="#confirmUserDeletionModal">
        <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete Account') }}
    </button>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" role="dialog" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('Are you sure?') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="text-sm text-muted">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="form-group mt-3">
                            <label for="password" class="sr-only">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password" 
                                   class="form-control {{ $errors->userDeletion->has('password') ? 'is-invalid' : '' }}" 
                                   placeholder="{{ __('Password') }}" required>
                            @if ($errors->userDeletion->has('password'))
                                <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default border px-4 font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger px-4 font-weight-bold shadow-sm">
                            <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
