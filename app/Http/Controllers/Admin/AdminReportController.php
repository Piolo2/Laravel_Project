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
        // General Stats for Dashboard/Reports Landing
        $stats = [
            'total_users' => User::count(),
            'total_requests' => ServiceRequest::count(),
            'pending_requests' => ServiceRequest::where('status', 'pending')->count(),
            'completed_requests' => ServiceRequest::where('status', 'completed')->count(), // Assuming 'completed' or 'payed'
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function users()
    {
        // User growth or role distribution
        $roles_distribution = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get();

        return view('admin.reports.users', compact('roles_distribution'));
    }

    public function requests()
    {
        // Request status distribution
        $status_distribution = ServiceRequest::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return view('admin.reports.requests', compact('status_distribution'));
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
                    fputcsv($file, [
                        $user->id,
                        $name,
                        $user->email,
                        $user->role,
                        $user->created_at->format('Y-m-d H:i:s'),
                    ]);
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
                    $seekerName = $request->seeker->profile->full_name ?? $request->seeker->username ?? 'Unknown';
                    $providerName = $request->provider->profile->full_name ?? $request->provider->username ?? 'Unknown';

                    fputcsv($file, [
                        $request->id,
                        $seekerName,
                        $providerName,
                        $request->status,
                        $request->service_date,
                        $request->notes,
                        $request->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
