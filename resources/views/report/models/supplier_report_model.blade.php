<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <table class="table table-striped mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>GRN Number</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grns as $grn)
                        <tr>
                            <td>
                                <a href="{{ url('purchase-order', $grn->order_id) }}" class="btn font-style btn-outline-primary">
                                    {{ Auth::user()->purchaseOrderNumberFormat($grn->order_id) }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('grn.show', $grn->id) }}" class="btn font-style btn-outline-primary">
                                    {{ Auth::user()->GrnNumberFormat($grn->id) }}
                                </a>
                            </td>
                            <td>{{ $grn->order_date }}</td>
                            <td>{{ $grn->total_amount }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#dataTable').DataTable({order:[[2,"desc"]]});
    });
</script>
