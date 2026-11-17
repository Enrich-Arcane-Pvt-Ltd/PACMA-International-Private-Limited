<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Sale #</th>
                            <th>Invoice #</th>
                            <th>Advanced Date</th>
                            <th>Advance Amount</th>
                            <th>Sale Date</th>
                            <th>Sale Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $advance->customer->name ?? '' }}</td>
                            <td>{{ $advance->sales->sale_id ?? '' }}</td>
                            <td>{{ $advance->sales->invoice->invoice_code ?? '' }}</td>
                            <td>{{ \Auth::user()->dateFormat($advance->advance_date) }}</td>
                            <td>{{ \Auth::user()->priceFormat($advance->advance_amount) }}</td>
                            <td>{{ \Auth::user()->dateFormat($advance->sales->date_of_sale) ?? '' }}</td>
                            <td>{{ \Auth::user()->priceFormat($advance->sales->net_amount) ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
