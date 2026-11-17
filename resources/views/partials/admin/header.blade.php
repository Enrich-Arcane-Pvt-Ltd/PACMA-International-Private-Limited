@php
    $users=\Auth::user();
    $profile=asset(Storage::url('uploads/avatar/'));
    $currantLang = $users->currentLanguage();
    $languages=Utility::languages();
@endphp
{{-- <div class="bg-white"></div> --}}
<nav class=" navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto search-element" method="post">
        <div class="easy-autocomplete   "><input type="hidden" name="_token" value="ifSnVqGphjkOu1aqYvyflvadZqTOLssR8oVLlL9q" id="eac-5343" style="" autocomplete="off">
            <div class="easy-autocomplete-container" id="eac-container-eac-5343">
                <ul></ul>
            </div>
        </div>

        {{-- Sidebar toggle moved to nav.blade.php for clean alignment --}}
        {{-- <div class="d-sm-none d-lg-inline-block fs-40 fw-400 secondary-font mt-5 text-dark">{{__('Hi')}}, {{\Auth::user()->name}} !</div> --}}

    </form>



</nav>

