<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportIndexRequest;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index(ReportIndexRequest $request): View
    {
        return view('reports.index', $this->reportService->monthlyReport(
            Auth::id(),
            $request->validated('month')
        ));
    }
}
