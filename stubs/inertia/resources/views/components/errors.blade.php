<div class="mx-auto max-w-2xl">
    @if (Session::has('success'))

        <v-alert border="left" dense dismissible outlined text type="success">
            {{ Session::get('success') }}
        </v-alert>

    @endif

    @if (Session::has('danger'))
        <v-alert border="left" dense dismissible outlined text type="error">
            {{ Session::get('danger') }}
        </v-alert>
    @endif

    @if (Session::has('warning'))
        <v-alert border="left" dense dismissible outlined text type="warning">
            {{ Session::get('warning') }}
        </v-alert>
    @endif

    @if (Session::has('info'))
        <v-alert border="left" dense dismissible outlined text type="info">
            {{ Session::get('info') }}
        </v-alert>
    @endif

    @if (count($errors) > 0)

        <div class="alert alert-danger" role="alert">
            <strong>Errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif
</div>