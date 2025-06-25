<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use App\Services\EmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class EmailController extends Controller
{
    use AuthorizesRequests;

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Get all emails for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @response Email[]
     */
    public function index()
    {
        return response()->json(Auth::user()->emails);
    }

    /**
     * Get a specific email record.
     *
     * @param \App\Models\Email $email
     * @return \App\Models\Email
     * 
     * @response Email
     */
    public function show(Email $email)
    {
        $this->authorize('view', $email);
        return $email;
    }

    /**
     * Create a new email record.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Email
     * 
     * @response Email
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('emails', 'email')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })
            ],
        ]);
        $validated['user_id'] = Auth::id();
        
        return Email::create($validated);
    }


    /**
     * Update a specific email record.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Email $email
     * @return \App\Models\Email
     * 
     * @response Email
     */
    public function update(Request $request, Email $email)
    {
        $this->authorize('update', $email);

        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        $email->update($validated);
        return $email;
    }

    /**
     * Delete a specific email record.
     *
     * @param \App\Models\Email $email
     * @return \Illuminate\Http\Response
     * 
     * @response 204 No Content
     */
    public function destroy(Email $email)
    {
        $this->authorize('delete', $email);
        $email->delete();
        
        return response()->noContent();
    }

    /**
     * Send welcome emails to all user's email addresses.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function sendWelcomeEmails()
    {
        $user = Auth::user();
        $this->emailService->sendWelcomeEmails($user);

        return response()->json(['message' => 'Welcome emails sent successfully']);
    }
}