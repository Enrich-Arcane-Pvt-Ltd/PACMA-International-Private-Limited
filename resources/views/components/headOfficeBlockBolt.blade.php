<b>COMPANY NAME<span class="space">:</span></b><span class="space">{{ config('app.name') }}</span><br>
<b>ADDRESS<span style="padding-left:65px">:</span></b><span class="space">{{ (App\Setting::where('name', 'company_address')->first())->value }}</span><br>
<b>PHONE<span style="padding-left:83px">:</span></b><span class="space">{{ (App\Setting::where('name', 'company_telephone')->first())->value }}</span><br>
