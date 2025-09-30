<div class="mail-body mt-4">
    {!! $mail->body !!}
</div>

@if($mail->metadata)
<div class="mail-metadata mt-4">
    <h6>Additional Information:</h6>
    <div class="card">
        <div class="card-body">
            <pre class="mb-0">{{ json_encode($mail->metadata, JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endif