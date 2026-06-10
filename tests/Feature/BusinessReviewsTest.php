<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessReviewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_can_submit_pending_review_for_active_business(): void
    {
        $business = $this->business([
            'business_name' => 'Reviewed Active Business',
            'subscription_status' => 'active',
        ]);

        $this->post(route('businesses.reviews.store', $business), [
            'reviewer_name' => 'Maria Ivanova',
            'reviewer_email' => 'maria@example.com',
            'rating' => 5,
            'comment' => 'Excellent service and fast response.',
        ])->assertRedirect(route('businesses.show', $business));

        $this->assertDatabaseHas('reviews', [
            'business_id' => $business->id,
            'reviewer_name' => 'Maria Ivanova',
            'rating' => 5,
            'comment' => 'Excellent service and fast response.',
            'status' => Review::STATUS_PENDING,
        ]);
    }

    public function test_guest_can_submit_pending_review_for_trial_business(): void
    {
        $business = $this->business([
            'business_name' => 'Reviewed Trial Business',
            'subscription_status' => 'trial',
            'trial_started_at' => now()->subDay(),
            'trial_ends_at' => now()->addDays(10),
            'subscription_started_at' => null,
            'subscription_ends_at' => null,
        ]);

        $this->post(route('businesses.reviews.store', $business), [
            'reviewer_name' => 'Georgi Petrov',
            'rating' => 4,
            'comment' => 'Helpful team and clear communication.',
        ])->assertRedirect(route('businesses.show', $business));

        $this->assertDatabaseHas('reviews', [
            'business_id' => $business->id,
            'reviewer_name' => 'Georgi Petrov',
            'rating' => 4,
            'status' => Review::STATUS_PENDING,
        ]);
    }

    public function test_approved_review_is_visible_publicly(): void
    {
        $business = $this->business([
            'business_name' => 'Approved Review Business',
        ]);

        Review::create([
            'business_id' => $business->id,
            'reviewer_name' => 'Approved Reviewer',
            'rating' => 5,
            'comment' => 'Approved public review text.',
            'status' => Review::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        $this->get(route('businesses.show', $business))
            ->assertOk()
            ->assertSee('Approved Reviewer')
            ->assertSee('Approved public review text.')
            ->assertSee('5.0/5');
    }

    public function test_pending_review_is_not_visible_publicly(): void
    {
        $business = $this->business([
            'business_name' => 'Pending Review Business',
        ]);

        Review::create([
            'business_id' => $business->id,
            'reviewer_name' => 'Pending Reviewer',
            'rating' => 5,
            'comment' => 'Pending hidden review text.',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->get(route('businesses.show', $business))
            ->assertOk()
            ->assertDontSee('Pending Reviewer')
            ->assertDontSee('Pending hidden review text.');
    }

    public function test_expired_and_cancelled_businesses_do_not_accept_reviews(): void
    {
        $expired = $this->business([
            'business_name' => 'Expired Review Blocked Business',
            'subscription_status' => 'expired',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $cancelled = $this->business([
            'business_name' => 'Cancelled Review Blocked Business',
            'subscription_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        foreach ([$expired, $cancelled] as $business) {
            $this->post(route('businesses.reviews.store', $business), [
                'reviewer_name' => 'Blocked Reviewer',
                'rating' => 5,
                'comment' => 'This should not be accepted.',
            ])->assertNotFound();
        }

        $this->assertDatabaseMissing('reviews', [
            'reviewer_name' => 'Blocked Reviewer',
        ]);
    }

    public function test_admin_can_approve_and_reject_reviews(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $business = $this->business();

        $reviewToApprove = Review::create([
            'business_id' => $business->id,
            'reviewer_name' => 'Approve Me',
            'rating' => 5,
            'comment' => 'Please approve this.',
            'status' => Review::STATUS_PENDING,
        ]);

        $reviewToReject = Review::create([
            'business_id' => $business->id,
            'reviewer_name' => 'Reject Me',
            'rating' => 2,
            'comment' => 'Please reject this.',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.reviews.approve', $reviewToApprove))
            ->assertRedirect();

        $this->assertSame(Review::STATUS_APPROVED, $reviewToApprove->fresh()->status);
        $this->assertNotNull($reviewToApprove->fresh()->approved_at);

        $this->actingAs($admin)
            ->patch(route('admin.reviews.reject', $reviewToReject))
            ->assertRedirect();

        $this->assertSame(Review::STATUS_REJECTED, $reviewToReject->fresh()->status);
        $this->assertNull($reviewToReject->fresh()->approved_at);
    }

    public function test_business_owner_cannot_approve_reviews(): void
    {
        $business = $this->business();

        $review = Review::create([
            'business_id' => $business->id,
            'reviewer_name' => 'Needs Admin',
            'rating' => 5,
            'comment' => 'Only admin should approve.',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->actingAs($business)
            ->patch(route('admin.reviews.approve', $review))
            ->assertForbidden();

        $this->assertSame(Review::STATUS_PENDING, $review->fresh()->status);
    }

    private function business(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'business',
            'business_name' => 'BON Review Test Business',
            'business_category' => 'Автосервиз',
            'city' => 'София',
            'subscription_status' => 'active',
            'subscription_plan' => 'standard',
            'subscription_started_at' => now()->subDay(),
            'subscription_ends_at' => now()->addDays(30),
            'trial_started_at' => null,
            'trial_ends_at' => null,
            'cancelled_at' => null,
            'is_verified' => false,
            'verified_at' => null,
        ], $overrides));
    }
}
