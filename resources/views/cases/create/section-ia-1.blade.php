<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Entity Type</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($investmentAdvisors as $advisor)
            <tr>
                <td>{{ $advisor->name }}</td>
                <td>{{ $advisor->entity_type }}</td>
                <td>{{ $advisor->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>