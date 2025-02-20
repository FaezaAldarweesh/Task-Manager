<!DOCTYPE html>
<html>
<head>
    <title>Daily Task Status Update Report</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>Here is your task status update for today, including tasks with status changes in the last 24 hours:</p>

    <ul>
        @foreach ($tasks as $task)
            <li>
                <strong>{{ $task->title }}</strong>:
                @if ($task->statusUpdates->isNotEmpty())
                    Status changed from <em>{{ $task->statusUpdates->last()->previous_status }}</em> 
                    to <em>{{ $task->statusUpdates->last()->new_status }}</em> 
                    (Updated: {{ $task->statusUpdates->last()->created_at->format('d M Y H:i') }})
                @else
                    Current Status: <em>{{ $task->status }}</em>
                @endif
            </li>
        @endforeach
    </ul>
</body>
</html>
