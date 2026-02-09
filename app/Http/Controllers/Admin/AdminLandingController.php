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

    private function getStartDate($range)
    {
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
    }

    private function fetchStatsData($filters = [])
    {
        $total_residents = User::where('role', 'resident')->count();
        $total_seekers = User::where('role', 'seeker')->count();
        $total_services = UserSkill::count();
        $total_verified_providers = \App\Models\ProviderVerification::where('status', 'approved')->count();

        // get recent 5 users
        $recent_users = User::latest()->take(5)->get();

        // skill distribution for chart
        $skill_dist = DB::table('skill_categories as c')
            ->leftJoin('skills as s', 'c.id', '=', 's.category_id')
            ->leftJoin('user_skills as us', 's.id', '=', 'us.skill_id')
            ->select('c.name', DB::raw('count(us.id) as count'))
            ->groupBy('c.id', 'c.name')
            ->orderBy('count', 'desc')
            ->get();

        // CHART DATA

        // chart data init
        $monthly_users = ['labels' => [], 'data' => []];
        $request_stats = ['labels' => [], 'data' => []];
        $role_stats = ['labels' => [], 'data' => []];

        // 1. monthly user registration
        $userRange = $filters['user_range'] ?? '30d';
        $userStartDate = $this->getStartDate($userRange);

        $query = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'));
        if ($userStartDate) {
            $query->where('created_at', '>=', $userStartDate);
        }
        $monthly_users_data = $query->groupBy(DB::raw('DATE(created_at)'))->orderBy(DB::raw('DATE(created_at)'), 'asc')->get();

        // fill missing dates
        if ($userRange !== 'all' && $userRange !== 'year') {
            $start = $userStartDate ?: now()->subDays(30);
            $period = \Carbon\CarbonPeriod::create($start, now());
            foreach ($period as $date) {
                $formattedDate = $date->format('Y-m-d');
                $entry = null;
                foreach ($monthly_users_data as $item) {
                    if (substr($item->date, 0, 10) === $formattedDate) {
                        $entry = $item;
                        break;
                    }
                }

                $count = $entry ? $entry->count : 0;

                $monthly_users['labels'][] = $date->format('M d');
                $monthly_users['data'][] = $count;
            }
        } else {
            foreach ($monthly_users_data as $d) {
                $monthly_users['labels'][] = \Carbon\Carbon::parse($d->date)->format('M d');
                $monthly_users['data'][] = $d->count;
            }
        }

        // 2. service request status distribution
        $requestRange = $filters['request_range'] ?? '30d';
        $requestStartDate = $this->getStartDate($requestRange);

        $requestQuery = \App\Models\ServiceRequest::select('status', DB::raw('count(*) as count'));
        if ($requestStartDate) {
            $requestQuery->where('created_at', '>=', $requestStartDate);
        }
        $request_stats_data = $requestQuery->groupBy('status')->get();

        foreach ($request_stats_data as $stat) {
            $request_stats['labels'][] = $stat->status;
            $request_stats['data'][] = $stat->count;
        }

        // 3. user role distribution
        $roleRange = $filters['role_range'] ?? '30d';
        $roleStartDate = $this->getStartDate($roleRange);

        $roleQuery = User::select('role', DB::raw('count(*) as count'));
        if ($roleStartDate) {
            $roleQuery->where('created_at', '>=', $roleStartDate);
        }
        $role_stats_data = $roleQuery->groupBy('role')->get();

        foreach ($role_stats_data as $stat) {
            $label = ucfirst($stat->role);
            if ($stat->role === 'resident') {
                $label = 'Service Provider';
            } elseif ($stat->role === 'seeker') {
                $label = 'Service Seeker';
            }

            $role_stats['labels'][] = $label;
            $role_stats['data'][] = $stat->count;
        }

        // explicitly build return array
        $dashboardData = [];
        $dashboardData['total_residents'] = $total_residents;
        $dashboardData['total_seekers'] = $total_seekers;
        $dashboardData['total_services'] = $total_services;
        $dashboardData['skill_dist'] = $skill_dist;
        $dashboardData['recent_users'] = $recent_users;
        $dashboardData['monthly_users'] = $monthly_users;
        $dashboardData['request_stats'] = $request_stats;
        $dashboardData['role_stats'] = $role_stats;
        $dashboardData['total_verified_providers'] = $total_verified_providers;

        return $dashboardData;
    }

}
