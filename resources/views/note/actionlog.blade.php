@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Action Logs</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Note ID</th>
                <th>User ID</th>
                <th>Action Type</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($actionLogs as $log)
                <tr>
                    <td>{{ $log->note_id }}</td>
                    <td>{{ $log->user_id }}</td>
                    <td>{{ $log->action_type }}</td>
                    <td>{{ $log->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $actionLogs->links() }}
</div>
@endsection