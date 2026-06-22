<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvitationAssistantRequest;
use App\Services\InvitationCopyAssistantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class InvitationAssistantController extends Controller
{
    public function __construct(
        private readonly InvitationCopyAssistantService $invitationCopyAssistantService,
    ) {
    }

    public function generate(InvitationAssistantRequest $request): RedirectResponse
    {
        $generated = $this->invitationCopyAssistantService->generate($request->validated());

        $input = array_merge(
            Arr::except($request->all(), ['_token', '_method']),
            Arr::only($generated, [
                'opening_text',
                'invitation_text',
                'closing_text',
                'bride_bio',
                'groom_bio',
                'no_gift_message',
                'gift_instructions',
            ]),
            [
                'ai_style_brief' => $generated['ai_style_brief'],
                'broadcast_message_template_seed' => $generated['broadcast_message_template'],
                'wizard_step' => 'content',
            ],
        );

        return back()
            ->withInput($input)
            ->with('assistant_style_brief', $generated['ai_style_brief'])
            ->with('status', 'Draft copy dan style brief berhasil digenerate.');
    }
}
