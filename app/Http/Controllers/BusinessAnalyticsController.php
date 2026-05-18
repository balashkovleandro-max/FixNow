<?php

namespace App\Http\Controllers;

use App\Models\BusinessAnalyticsEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class BusinessAnalyticsController extends Controller
{
    public function phone(User $user): RedirectResponse
    {
        $this->abortIfBusinessIsNotAccessible($user);

        if (!$user->phone) {
            return redirect()->route('businesses.show', $user);
        }

        $this->recordIfRealVisitor($user, BusinessAnalyticsEvent::PHONE_CLICK, [
            'source' => $this->sourceFromRequest('public_profile'),
        ]);

        $phone = preg_replace('/[^\d+]/', '', $user->phone);

        if (!$phone) {
            return redirect()->route('businesses.show', $user);
        }

        return redirect()->away('tel:' . $phone);
    }

    public function website(User $user): RedirectResponse
    {
        $this->abortIfBusinessIsNotAccessible($user);

        $website = $this->normalizedWebsiteUrl($user->website);

        if (!$website) {
            return redirect()->route('businesses.show', $user);
        }

        $this->recordIfRealVisitor($user, BusinessAnalyticsEvent::WEBSITE_CLICK, [
            'target' => $website,
        ]);

        return redirect()->away($website);
    }

    public function inquiry(User $user): RedirectResponse
    {
        $this->abortIfBusinessIsNotAccessible($user);

        $this->recordIfRealVisitor($user, BusinessAnalyticsEvent::INQUIRY_CLICK);

        return redirect()->to(route('businesses.show', [
            'user' => $user,
            'analytics_intent' => 'inquiry',
        ]) . '#send-request');
    }

    public function chat(User $user): RedirectResponse
    {
        $this->abortIfBusinessIsNotAccessible($user);

        $this->recordIfRealVisitor($user, BusinessAnalyticsEvent::CHAT_CLICK);

        return redirect()->to(route('businesses.show', [
            'user' => $user,
            'analytics_intent' => 'chat',
        ]) . '#contact');
    }

    public function social(User $user, string $platform): RedirectResponse
    {
        $this->abortIfBusinessIsNotAccessible($user);

        $target = $this->socialUrl($user, $platform);

        if (!$target) {
            return redirect()->route('businesses.show', $user);
        }

        $this->recordIfRealVisitor($user, BusinessAnalyticsEvent::SOCIAL_CLICK, [
            'platform' => $platform,
            'target' => $target,
        ]);

        return redirect()->away($target);
    }

    private function abortIfBusinessIsNotAccessible(User $user): void
    {
        abort_unless($user->isBusiness(), 404);

        if ($user->isPubliclyVisible() || $this->viewerIsOwnerOrAdmin($user)) {
            return;
        }

        abort(404);
    }

    private function recordIfRealVisitor(User $business, string $eventType, array $metadata = []): void
    {
        if ($this->viewerIsOwnerOrAdmin($business)) {
            return;
        }

        BusinessAnalyticsEvent::record($business, $eventType, auth()->user(), $metadata);
    }

    private function viewerIsOwnerOrAdmin(User $business): bool
    {
        $viewer = auth()->user();

        return $viewer && ($viewer->id === $business->id || $viewer->role === 'admin');
    }

    private function normalizedWebsiteUrl(?string $website): ?string
    {
        if (!$website) {
            return null;
        }

        $website = trim($website);

        if (!str_starts_with($website, 'http://') && !str_starts_with($website, 'https://')) {
            $website = 'https://' . $website;
        }

        $scheme = parse_url($website, PHP_URL_SCHEME);

        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $website;
    }

    private function socialUrl(User $user, string $platform): ?string
    {
        $platform = strtolower($platform);

        return match ($platform) {
            'facebook' => $this->normalizedWebsiteUrl(data_get($user, 'facebook') ?: data_get($user, 'фейсбук')),
            'instagram' => $this->normalizedWebsiteUrl(data_get($user, 'instagram') ?: data_get($user, 'инстаграм')),
            'whatsapp' => $this->whatsappUrl(data_get($user, 'whatsapp') ?: data_get($user, 'whatsapp_number')),
            'viber' => $this->viberUrl(data_get($user, 'viber') ?: data_get($user, 'viber_number')),
            default => null,
        };
    }

    private function whatsappUrl(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $digits = preg_replace('/[^\d]/', '', $value);

        return $digits ? 'https://wa.me/' . $digits : null;
    }

    private function viberUrl(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, 'viber://')) {
            return $value;
        }

        $phone = preg_replace('/[^\d+]/', '', $value);

        return $phone ? 'viber://chat?number=' . rawurlencode($phone) : null;
    }

    private function sourceFromRequest(string $default): string
    {
        $source = (string) request()->query('source', $default);

        return in_array($source, ['public_profile', 'business_card'], true)
            ? $source
            : $default;
    }
}
