<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MailboxController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mails = Mail::where(function ($query) use ($user) {
            if (!$user->hasRole('admin')) {
                $query->where('to_email', $user->email);
            }
        })
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('mailbox.index', compact('mails'));
    }

    public function show($id)
    {
        $mail = Mail::findOrFail($id);
        
        if (!($mail->to_email == Auth::user()->email || Auth::user()->hasRole('admin'))) {
            abort(403, 'Unauthorized access to this mail.');
        }

        if (!$mail->is_read) {
            $mail->markAsRead();
        }

        $html = '<div class="mail-actions">
            <button class="btn btn-primary btn-sm" onclick="markAsRead(' . $mail->id . ')">
                <i class="fas fa-envelope-open"></i> Mark as Read
            </button>
            <button class="btn btn-secondary btn-sm" onclick="markAsUnread(' . $mail->id . ')">
                <i class="fas fa-envelope"></i> Mark as Unread
            </button>
            <button class="btn btn-danger btn-sm" onclick="deleteMail(' . $mail->id . ')">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>

        <div class="mail-header">
            <h4>{{ $mail->subject }}</h4>
            <div class="mail-meta">
                <div class="row">
                    <div class="col-md-6">
                        <strong>From:</strong> ' . $mail->from_name . ' &lt;' . $mail->from_email . '&gt;
                    </div>
                    <div class="col-md-6">
                        <strong>To:</strong> ' . ($mail->to_name ?: $mail->to_email) . '
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Date:</strong> ' . $mail->created_at->format('M d, Y H:i:s') . '
                    </div>
                    <div class="col-md-6">
                        <strong>Type:</strong> 
                        <span class="badge bg-' . ($mail->type == 'policy_created' ? 'success' : ($mail->type == 'policy_updated' ? 'warning' : 'info')) . '">
                            ' . ucfirst(str_replace('_', ' ', $mail->type)) . '
                        </span>
                    </div>
                </div>
            </div>
        </div>';

        return response()->json([
            'info' => $html,
            'content' => view('mailbox.show', compact('mail'))->render()
        ]);
    }

    public function markAsRead($id)
    {
        $mail = Mail::findOrFail($id);
        
        if ($mail->to_email !== Auth::user()->email) {
            abort(403, 'Unauthorized access to this mail.');
        }

        $mail->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAsUnread($id)
    {
        $mail = Mail::findOrFail($id);
        
        if ($mail->to_email !== Auth::user()->email) {
            abort(403, 'Unauthorized access to this mail.');
        }

        $mail->update(['is_read' => false, 'read_at' => null]);

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $mail = Mail::findOrFail($id);
        
        if ($mail->to_email !== Auth::user()->email) {
            abort(403, 'Unauthorized access to this mail.');
        }

        $mail->delete();

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = Mail::where('to_email', $user->email)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
