<!DOCTYPE html>
<html lang="en">
<meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
@include('partials.admin.head')

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1 t">
            @include('partials.admin.header')

            @include('partials.admin.menu')
            <div class="main-content">
                @include('partials.admin.nav')
                @include('partials.admin.content')
            </div>

        </div>
    </div>
    <div id="commonModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modelCommanModelLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelCommanModelLabel"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
    @include('partials.admin.footer')
    @auth('web')
        <script src="{{ asset('assets/js/idle-timeout.js') }}"
            data-key="{{ session('idle_tab_key') }}"
            data-last="{{ session('idle_last_activity') * 1000 }}"
            data-now="{{ microtime(true) * 1000 }}"
            data-status="{{ route('session.status') }}"
            data-activity="{{ route('session.activity') }}"
            data-login="{{ route('login') }}"></script>
    @endauth

</body>

</html>
