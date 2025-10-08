@extends('layouts.app')

@push('css')
<style>
.mail-list {
    max-height: 600px;
    overflow-y: auto;
}

.mail-item {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    transition: background-color 0.2s;
}

.mail-item:hover {
    background-color: #f8f9fa;
}

.mail-item.unread {
    background-color: #e3f2fd;
    border-left: 3px solid #2196f3;
}

.mail-item.selected {
    background-color: #e3f2fd;
}

.mail-sender {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.unread-indicator {
    width: 8px;
    height: 8px;
    background-color: #2196f3;
    border-radius: 50%;
    display: inline-block;
}

.mail-subject {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.mail-preview {
    color: #666;
    font-size: 0.9em;
    margin-bottom: 5px;
}

.mail-time {
    color: #999;
    font-size: 0.8em;
}

#mail-content {
    min-height: 400px;
}

.mail-actions {
    margin-bottom: 20px;
}

.mail-actions .btn {
    margin-right: 10px;
}
.mail-header {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.mail-meta {
    color: #666;
    font-size: 0.9em;
}

.mail-body {
    line-height: 1.6;
    color: #333;
}

.mail-body h1, .mail-body h2, .mail-body h3, .mail-body h4, .mail-body h5, .mail-body h6 {
    margin-top: 20px;
    margin-bottom: 10px;
}

.mail-body p {
    margin-bottom: 15px;
}

.mail-body ul, .mail-body ol {
    margin-bottom: 15px;
    padding-left: 20px;
}

.mail-body table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
}

.mail-body table th, .mail-body table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.mail-body table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.mail-metadata {
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
}

.mail-metadata pre {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    font-size: 0.8em;
}

.container-fluid, .container {
    padding: 0!important;
}

.container-fluid {
    max-width: 1470px;
}

</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Inbox</h5>
                </div>
                <div class="card-body p-0">
                    <div class="mail-list">
                        @forelse($mails as $mail)
                        <div class="mail-item {{ !$mail->is_read ? 'unread' : '' }}" data-mail-id="{{ $mail->id }}">
                            <div class="mail-item-content">
                                <div class="mail-sender">
                                    <strong>{{ $mail->from_name }}</strong>
                                    @if(!$mail->is_read)
                                        <span class="unread-indicator"></span>
                                    @endif
                                </div>
                                <div class="mail-subject">{{ $mail->subject }}</div>
                                <!-- <div class="mail-preview">{{ Str::limit(strip_tags($mail->body), 100) }}</div> -->
                                <div class="mail-time">{{ $mail->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center p-4">
                            <p class="text-muted">No mails found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Select a mail to view</h5>
                </div>
                <div class="card-body">
                    <div class="mail-meta-data">

                    </div>
                    <div id="mail-content" class="text-center">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-muted mb-3">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <p class="text-muted">Select a mail from the list to view its content</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@push('js')
<script>
let baseUrl = "{{ url('/') }}";

    function markAsRead(mailId) {
        $.ajax({
            url: `${baseUrl}/mailbox/${mailId}/mark-read`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function markAsUnread(mailId) {
        $.ajax({
            url: `${baseUrl}/mailbox/${mailId}/mark-unread`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                if (data.success) {
                    location.reload();
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function deleteMail(mailId) {
        if (confirm('Are you sure you want to delete this mail?')) {
            $.ajax({
                url: `${baseUrl}/mailbox/${mailId}/delete`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (data) {
                    if (data.success) {
                        location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    }

$(document).ready(function() {

    $('.mail-item').on('click', function() {
        const mailId = $(this).data('mailId');

        $('.mail-item').removeClass('selected');
        $(this).addClass('selected');
        
        $.ajax({
            url: `${baseUrl}/mailbox/${mailId}`,
            method: 'GET',
            success: function(response) {
                const iframe = document.createElement('iframe');
                iframe.style.width = '100%';
                iframe.style.height = '600px';
                iframe.style.border = 'none';
                iframe.srcdoc = response.content;

                $('.mail-meta-data').html(response.meta);
                $('#mail-content').html(iframe);
            },
            error: function(error) {
                console.error('Error loading mail:', error);
            }
        });
    });

});
</script>
@endpush
