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
            @foreach($idfManagers as $manager)
            <tr>
                <td>{{ $manager->name }}</td>
                <td>{{ $manager->entity_type }}</td>
                <td>{{ $manager->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>