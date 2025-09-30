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
            @foreach($custodianBanks as $bank)
            <tr>
                <td>{{ $bank->name }}</td>
                <td>{{ $bank->entity_type }}</td>
                <td>{{ $bank->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>