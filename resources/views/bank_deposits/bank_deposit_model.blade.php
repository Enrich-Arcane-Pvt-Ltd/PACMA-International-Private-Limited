<div id="view_bank_deposit_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-2xl">
        <div class="modal-content ">

            <div class="modal-header">
                <h4 class="modal-title">Cash Report</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="col-md-12 overflow-x-scroll">
                    <table class="table w-100" id="financial_entries_table">
                        <thead>
                            <tr>
                                <th class="text-center white-space-nowrap">{{ 'Date' }}</th>
                                <th class="text-center white-space-nowrap">{{ 'Type' }}</th>
                                <th class="text-center white-space-nowrap">{{ 'Payment Method' }}</th>
                                <th class="text-center white-space-nowrap">{{ 'Receipt No.' }}</th>
                                <th class="text-center white-space-nowrap">{{ 'Invoice No' }}</th>
                                <th class="text-center white-space-nowrap">{{ 'Customer' }}</th>
                                <th class="text-center white-space-nowrap">{{ 'Product Code' }}</th>
                                <th class="text-right white-space-nowrap">{{ 'Debit' }} ({{ $currencySymbol }})
                                </th>
                                <th class="text-right white-space-nowrap">{{ 'Credit' }} ({{ $currencySymbol }})
                                </th>
                                <th class="text-center white-space-nowrap">{{ 'Remark' }}</th>
                            </tr>
                        </thead>

                        <tbody id="financial_entries_table_tbody"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
