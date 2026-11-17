@extends('layouts.admin')
@section('page-title')
    {{__('Holidays')}}
@endsection

@push('css-page')
    <link href="{{ asset('assets/calender/calendar-gc.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')

    <section class="section">
        @if(\Auth::user()->type=='company')
            <div class="section-header">
                <h1>{{__('Holidays')}}</h1>
                <div class="section-header-button">
                    {{-- <a href="#" data-url="{{ route('holidays.create') }}" data-ajax-popup="true" data-title="{{__('Create New Holiday')}}" class="btn btn-primary btn-icon icon-left">{{__('Add Holiday')}}</a> --}}
                </div>
            </div>
        @endif
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('script-page')
    <script src="{{ asset('assets/calender/calendar-gc.min.js') }}"></script>

    <script>
        var currentMonth = new Date().getMonth();
        var currentYear = new Date().getFullYear();
        // if route parameter is not null then set month from route parameter holidays?month=May
        @if(isset($_GET['month']) && !empty($_GET['month']))
            var monthX = "{{$_GET['month']}}";
            currentMonth = new Date(Date.parse(monthX +" 20, 2020")).getMonth();
        @endif

        // events - holidays
        var events = [
            @foreach($holidays as $holiday)
                {
                    date: new Date('{{ $holiday->date }}'),
                    eventName: '{{ $holiday->name }}',
                    link: '{{ route('holidays.edit', $holiday->id) }}',
                    className: "badge bg-info text-white",
                    // dateColor: "red"
                    onclick(e, data) {
                        editHoliday({{ $holiday->id }}, '{{ date("Y-m-d", strtotime($holiday->date)) }}' , '{{ $holiday->name }}');
                    }
                },
            @endforeach
        ];


        var calendar = $("#calendar").calendarGC({
            events: events,
            dayNames: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            onclickDate: function (e, data) {
                var date = data.datejs;
                var formattedDate = date.getFullYear() + "-" + (numberToStr(date.getMonth() + 1)) + "-" + numberToStr(date.getDate());
                var month = date.toLocaleString('default', { month: 'long' });

                $('#commonModal').modal('show');
                var html = `
                    <form method="post" action="{{ route('holidays.store') }}">
                        <h4 class="mb-4">Add Holiday</h4>
                        @csrf
                        <input type="hidden" name="month" value="${ month }">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required value="Holiday">
                        </div>

                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="${ formattedDate  }" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>

                `
                $('#commonModal .modal-body').html(html);

            }
        });

        $(document).ready(function () {
            calendar.setDate(`${currentYear}-${numberToStr(currentMonth+1)}-10`);
        });

        function editHoliday(id, date, name){
            var date = new Date(date);
            var formattedDate = date.getFullYear() + "-" + (numberToStr(date.getMonth() + 1)) + "-" + numberToStr(date.getDate());
            var month = date.toLocaleString('default', { month: 'long' });

            $('#commonModal').modal('show');
            var html = `
                <form method="post" action="{{ route('holidays.edit') }}/${id}">
                    <h4 class="mb-4">Edit Holiday</h4>
                    @csrf
                    <input type="hidden" name="month" value="${ month }">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required value="${name}">
                    </div>

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="${ formattedDate  }" required>
                    </div>
                    <button type="submit" class="btn btn-primary me-3">Submit</button>
                    <a href="{{ route('holidays.delete') }}/${id}" class="btn btn-danger">Delete</a>
                </form>

            `
            $('#commonModal .modal-body').html(html);
        }

        function numberToStr(number){
            return number < 10 ? '0'+number : number;
        }
    </script>
@endpush
