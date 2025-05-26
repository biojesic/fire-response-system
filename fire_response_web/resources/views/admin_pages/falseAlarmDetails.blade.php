<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-270 pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Fire Report Details</h1>
            </div>
        </header>

        <h1>False Alarm Details</h1>
        <p><strong>Description:</strong> {{ $falseAlarmReport->description }}</p>
        <p><strong>Status:</strong> {{ $falseAlarmReport->status }}</p>
        <p><strong>Location:</strong> {{ $falseAlarmReport->location }}</p>
        <p><strong>Time Reported:</strong> {{ $falseAlarmReport->created_at->format('g:i a') }}</p>
        @if ($falseAlarmReport->marked_as_false_alarm_by)
            <p><strong>Marked as False Alarm by:</strong>
                {{ $falseAlarmReport->markedAsFalseAlarmBy->userFirstName }}
                {{ $falseAlarmReport->markedAsFalseAlarmBy->userLastName }}
            </p>
        @else
            <p><strong>Marked as False Alarm by:</strong> Unknown</p>
        @endif
        @if ($falseAlarmReport->marked_as_false_alarm_at)
            <p><strong>Marked as False Alarm at:</strong>
                {{ $falseAlarmReport->marked_as_false_alarm_at->format('g:i a') }}</p>
        @else
            <p><strong>Marked as False Alarm at:</strong> Not available</p>
        @endif

    </div>
</x-dashboard>
