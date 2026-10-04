<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activation;
use App\Models\Destination;
use App\Models\Story;
use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatsController extends Controller
{
    /**
     * Dashboard overview stats (Admin only)
     */
    public function overview(Request $request)
    {
        // Ensure admin or community admin
        $user = $request->user();
        if (!$user || !in_array($user->role?->name, ['admin', 'community_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sevenDaysAgo = $now->copy()->subDays(7);

        // Core counts
        $totalUsers    = User::count();
        $newUsersMonth = User::where('created_at', '>=', $thirtyDaysAgo)->count();
        $newUsersWeek  = User::where('created_at', '>=', $sevenDaysAgo)->count();

        $totalActivations  = Activation::count();
        $activeActivations = Activation::where('is_active', true)->count();

        $totalDestinations  = Destination::count();
        $newDestinations    = Destination::where('created_at', '>=', $thirtyDaysAgo)->count();

        $totalStories    = Story::count();
        $pendingStories  = Story::where('status', 'pending')->count();
        $publishedStories = Story::where('status', 'published')->count();
        $newStories      = Story::where('created_at', '>=', $thirtyDaysAgo)->count();

        $totalComments = Comment::count();
        $totalLikes    = Like::count();

        // User growth: last 7 days per day
        $userGrowth = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', $sevenDaysAgo)
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date')
        ->get()
        ->keyBy('date')
        ->map(fn($r) => (int) $r->count);

        // Fill in missing days with 0
        $userGrowthFull = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $userGrowthFull[] = [
                'date'  => $day,
                'label' => $now->copy()->subDays($i)->locale('id')->isoFormat('D MMM'),
                'count' => $userGrowth[$day] ?? 0,
            ];
        }

        // Destination growth: last 7 days per day
        $destGrowth = Destination::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', $sevenDaysAgo)
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date')
        ->get()
        ->keyBy('date')
        ->map(fn($r) => (int) $r->count);

        $destGrowthFull = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $destGrowthFull[] = [
                'date'  => $day,
                'label' => $now->copy()->subDays($i)->locale('id')->isoFormat('D MMM'),
                'count' => $destGrowth[$day] ?? 0,
            ];
        }

        // Top 5 most liked destinations
        $topDestinations = Destination::with('category')
            ->orderBy('likes_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'category_id', 'likes_count', 'city'])
            ->map(fn($d) => [
                'id'         => $d->id,
                'name'       => $d->name,
                'city'       => $d->city,
                'category'   => $d->category?->name,
                'likes'      => (int) $d->likes_count,
            ]);

        // Recent user registrations (last 5)
        $recentUsers = User::with('role')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email', 'created_at', 'role_id'])
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'role'       => $u->role?->name ?? 'member',
                'joined_at'  => $u->created_at->diffForHumans(),
            ]);

        // Destinations by category
        $destinationsByCategory = Destination::select('category_id', DB::raw('COUNT(*) as count'))
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->map(fn($r) => [
                'category' => $r->category?->name ?? 'Lainnya',
                'count'    => (int) $r->count,
            ])
            ->sortByDesc('count')
            ->values();

        // Pending stories needing moderation
        $pendingStoriesList = Story::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get(['id', 'title', 'author_name', 'created_at', 'slug'])
            ->map(fn($s) => [
                'id'          => $s->id,
                'title'       => $s->title,
                'author'      => $s->author_name,
                'submitted'   => $s->created_at->diffForHumans(),
                'slug'        => $s->slug,
            ]);

        // Role distribution
        $roleStats = User::select('role_id', DB::raw('COUNT(*) as count'))
            ->with('role')
            ->groupBy('role_id')
            ->get()
            ->map(fn($r) => [
                'role'  => $r->role?->name ?? 'member',
                'count' => (int) $r->count,
            ]);

        return response()->json([
            'summary' => [
                'total_users'         => $totalUsers,
                'new_users_month'     => $newUsersMonth,
                'new_users_week'      => $newUsersWeek,
                'total_activations'   => $totalActivations,
                'active_activations'  => $activeActivations,
                'total_destinations'  => $totalDestinations,
                'new_destinations'    => $newDestinations,
                'total_stories'       => $totalStories,
                'pending_stories'     => $pendingStories,
                'published_stories'   => $publishedStories,
                'new_stories'         => $newStories,
                'total_comments'      => $totalComments,
                'total_likes'         => $totalLikes,
            ],
            'user_growth'              => $userGrowthFull,
            'destination_growth'       => $destGrowthFull,
            'top_destinations'         => $topDestinations,
            'recent_users'             => $recentUsers,
            'destinations_by_category' => $destinationsByCategory,
            'pending_stories'          => $pendingStoriesList,
            'role_stats'               => $roleStats,
        ]);
    }
}
