<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Advance Amount</th>
                            <th>Advanced Date</th>
                            <th>Returned Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $advance->customer->name ?? '' }}</td>
                            <td>{{ \Auth::user()->priceFormat($advance->advance_amount) }}</td>
                            <td>{{ \Auth::user()->dateFormat($advance->advance_date) }}</td>
                            <td>{{ \Auth::user()->dateFormat($advance->return_date) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
