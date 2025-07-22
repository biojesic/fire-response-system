@component('mail::message')
    # ❌ Barangay Application Rejected

    Your application for **{{ $barangayName }}** was rejected.

    **Reason:**
    {{ $rejectionReason }}

    @if ($reapplyUrl)
        @component('mail::button', ['url' => $reapplyUrl, 'color' => 'red'])
            Reapply Now
        @endcomponent
        <small>Link expires in 7 days</small>
    @endif

    Regards,
    BFP System
@endcomponent
