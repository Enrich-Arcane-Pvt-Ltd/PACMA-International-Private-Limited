<p>{{ (App\Setting::where('name', 'company_address')->first())->value }}</p>
<p>Tel : {{ (App\Setting::where('name', 'company_telephone')->first())->value }} &nbsp;&nbsp;/&nbsp;&nbsp; E-mail : {{ (App\Setting::where('name', 'company_email')->first())->value }}</p>
<p><span>Branch : </span><span>{{Auth::user()->branchName()}}</span></p>
