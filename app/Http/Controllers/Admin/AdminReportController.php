<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index()
    {
        // admin dashboard stats
        $stats = [
            'total_users' => User::count(),
            'total_requests' => ServiceRequest::count(),
            'pending_requests' => ServiceRequest::where('status', 'pending')->count(),
            'completed_requests' => ServiceRequest::where('status', 'completed')->count(),
        ];

        return view('admin.reports.index', ['stats' => $stats]);
    }

    public function users()
    {
        // role distribution query
        $roles_distribution = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get();

        return view('admin.reports.users', ['roles_distribution' => $roles_distribution]);
    }

    public function requests()
    {
        // status distribution query
        $status_distribution = ServiceRequest::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return view('admin.reports.requests', ['status_distribution' => $status_distribution]);
    }

    public function exportUsers()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Created At']);

            User::with('profile')->chunk(100, function ($users) use ($file) {
                foreach ($users as $user) {
                    $name = $user->profile->full_name ?? $user->username;

                    // build row explicitly
                    $row = [];
                    $row[] = $user->id;
                    $row[] = $name;
                    $row[] = $user->email;
                    $row[] = $user->role;
                    $row[] = $user->created_at->format('Y-m-d H:i:s');

                    fputcsv($file, $row);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRequests()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="requests_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Seeker Name', 'Provider Name', 'Status', 'Service Date', 'Notes', 'Created At']);

            ServiceRequest::with(['seeker.profile', 'provider.profile'])->chunk(100, function ($requests) use ($file) {
                foreach ($requests as $request) {
                    $seekerName = 'Unknown';
                    if ($request->seeker) {
                        $seekerName = $request->seeker->profile->full_name ?? $request->seeker->username;
                    }

                    $providerName = 'Unknown';
                    if ($request->provider) {
                        $providerName = $request->provider->profile->full_name ?? $request->provider->username;
                    }

                    // build csv row
                    $row = [];
                    $row[] = $request->id;
                    $row[] = $seekerName;
                    $row[] = $providerName;
                    $row[] = $request->status;
                    $row[] = $request->service_date;
                    $row[] = $request->notes;
                    $row[] = $request->created_at->format('Y-m-d H:i:s');

                    fputcsv($file, $row);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
