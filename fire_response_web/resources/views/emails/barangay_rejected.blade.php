@component('mail::message')
    # Barangay Application Rejected

    Dear Barangay Administrator,

    Your application for **{{ $barangayName }}** has been reviewed and rejected.

    **Reason for Rejection:**
    {{ $rejectionReason }}

    @if ($canReapply)
        👉 [Click here to reapply]({{ route('barangay.reapply') }}?token={{ $reapplyToken }})
    @else
        This decision is final and cannot be appealed.
    @endif

    For questions, please contact BFP Support.

    Regards,
    Bureau of Fire Protection
@endcomponent
