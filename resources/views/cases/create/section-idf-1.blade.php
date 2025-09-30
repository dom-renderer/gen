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
            @foreach($idfNames as $idf)
            <tr>
                <td>{{ $idf->name }}</td>
                <td>{{ $idf->entity_type }}</td>
                <td>{{ $idf->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>