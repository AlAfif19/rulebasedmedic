@foreach($sections as $sectionTitle => $rows)
    <h2>{{ $sectionTitle }}</h2>
    @if(count($rows) === 0)
        <p class="muted">Tidak ada data.</p>
    @else
        <table>
            <thead>
                <tr>
                    @foreach(array_keys($rows[0]) as $heading)
                        <th>{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $value)
                            <td>{{ $value ?: '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endforeach
