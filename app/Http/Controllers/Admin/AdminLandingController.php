<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLandingController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->fetchStatsData($request->all());
        return view('admin.landing', $data);
    }

    public function getStats(Request $request)
    {
        $data = $this->fetchStatsData($request->all());
        return response()->json($data);
    }

    private function fetchStatsData($filters = [])
    {
        $total_residents = User::where('role', 'resident')->count();
        $total_seekers = User::where('role', 'seeker')->count();
        $total_services = UserSkill::count();
        $total_verified_providers = \App\Models\ProviderVerification::where('status', 'approved')->count();

        // Get recent 5 users
        $recent_users = User::latest()->take(5)->get();

        // Skill Distribution
        // Note: Skill distribution is typically all-time, but we could filter by user_skills.created_at if needed.
        // For now keeping it all-time or generic as it represents "supply".
        $skill_dist = DB::table('skill_categories as c')
            ->leftJoin('skills as s', 'c.id', '=', 's.category_id')
            ->leftJoin('user_skills as us', 's.id', '=', 'us.skill_id')
            ->select('c.name', DB::raw('count(us.id) as count'))
            ->groupBy('c.id', 'c.name')
            ->orderBy('count', 'desc')
            ->get();

        // CHART DATA

        // Helper date range function
        $getDateRange = function ($range) {
            switch ($range) {
                case '7d':
                    return now()->subDays(7);
                case '90d':
                    return now()->subDays(90);
                case 'year':
                    return now()->subYear();
                case 'all':
                    return null;
                case '30d':
                default:
                    return now()->subDays(30);
            }
        };

        // Initialize chart data structure to prevent errors if no data found
        $monthly_users = ['labels' => [], 'data' => []];
        $request_stats = ['labels' => [], 'data' => []];
        $role_stats = ['labels' => [], 'data' => []];

        // 1. Monthly User Registration
        $userRange = $filters['user_range'] ?? '30d';
        $userStartDate = $getDateRange($userRange);

        $query = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'));
        if ($userStartDate) {
            $query->where('created_at', '>=', $userStartDate);
        }
        $monthly_users_data = $query->groupBy(DB::raw('DATE(created_at)'))->orderBy(DB::raw('DATE(created_at)'), 'asc')->get();

        // Fill dates only if not 'all' and typically for shorter ranges
        if ($userRange !== 'all' && $userRange !== 'year') {
            $start = $userStartDate ?: now()->subDays(30);
            $period = \Carbon\CarbonPeriod::create($start, now());
            foreach ($period as $date) {
                $formattedDate = $date->format('Y-m-d');
                // Loose comparison to match date string from DB
                $entry = $monthly_users_data->first(function ($item) use ($formattedDate) {
                    return substr($item->date, 0, 10) === $formattedDate;
                });
                $count = $entry ? $entry->count : 0;

                $monthly_users['labels'][] = $date->format('M d');
                $monthly_users['data'][] = $count;
            }
        } else {
            // For longer ranges, just show existing data points
            $monthly_users['labels'] = $monthly_users_data->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'));
            $monthly_users['data'] = $monthly_users_data->pluck('count');
        }


        // 2. Service Request Status Distribution
        $requestRange = $filters['request_range'] ?? '30d';
        $requestStartDate = $getDateRange($requestRange);

        $requestQuery = \App\Models\ServiceRequest::select('status', DB::raw('count(*) as count'));
        if ($requestStartDate) {
            $requestQuery->where('created_at', '>=', $requestStartDate);
        }
        $request_stats_data = $requestQuery->groupBy('status')->get();

        $request_stats = [
            'labels' => $request_stats_data->pluck('status'),
            'data' => $request_stats_data->pluck('count'),
        ];

        // 3. User Role Distribution
        $roleRange = $filters['role_range'] ?? '30d'; // Filter users joined in range
        $roleStartDate = $getDateRange($roleRange);

        $roleQuery = User::select('role', DB::raw('count(*) as count'));
        if ($roleStartDate) {
            $roleQuery->where('created_at', '>=', $roleStartDate);
        }
        $role_stats_data = $roleQuery->groupBy('role')->get();

        $role_stats = [
            'labels' => $role_stats_data->pluck('role')->map(function ($r) {
                if ($r === 'resident')
                    return 'Service Provider';
                if ($r === 'seeker')
                    return 'Service Seeker';
                return ucfirst($r);
            }),
            'data' => $role_stats_data->pluck('count'),
        ];

        return compact(
            'total_residents',
            'total_seekers',
            'total_services',
            'skill_dist',
            'recent_users',
            'monthly_users',
            'request_stats',
            'role_stats',
            'total_verified_providers'
        );
    }

}
