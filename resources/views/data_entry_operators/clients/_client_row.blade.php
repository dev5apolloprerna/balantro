<tr>
    <td>{{ $client->name }}</td>
    <td>{{ $client->email }}</td>
    <td>{{ $client->supervisors->count() ? $client->supervisors->pluck('name')->join(', ') : '-' }}</td>
    <td>{{ $client->managers->count() ? $client->managers->pluck('name')->join(', ') : '-' }}</td>
</tr>