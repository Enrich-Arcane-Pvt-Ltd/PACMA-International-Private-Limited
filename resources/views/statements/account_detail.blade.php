<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <table class="table table-striped mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th> {{ __('Details') }}</th>
                        <th> {{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                    @endphp
                    @foreach ($allAccounts as $account)
                        <tr>
                            <td>{{ $account[0]['name'] }}</td>
                            <td class="text-right">{{ number_format($account[0]['balance_amount'], 2) }}</td>
                        </tr>
                        @php
                           $total += $account[0]['balance_amount'];
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <th class="text-right"> {{ __('Total') }}</th>
                    <th class="text-right"> {{ number_format($total, 2) }}</th>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#dataTable').DataTable();
    });
</script>
