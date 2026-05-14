<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiskController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }
    
    public function overview()
    {
        return view('risk.overview');
    }

    public function auditTrail()
    {
        return redirect()->route('audits.index');
    }

    public function heatMap()
    {
        return view('risk.heat-map');
    }

    public function branchRanking()
    {
        return view('risk.branch-ranking');
    }

    public function fraudFeed()
    {
        return view('risk.fraud-feed');
    }

    public function recoveryEfficiency()
    {
        return view('risk.recovery-efficiency');
    }

    public function policyBreach()
    {
        return view('risk.policy-breach');
    }

    public function costValue()
    {
        return view('risk.cost-value');
    }

    public function geographicIntelligence()
    {
        return view('risk.geographic-intelligence');
    }

    public function escalationTracking()
    {
        return view('risk.escalation-tracking');
    }

    public function staffProfiles()
    {
        return view('risk.staff-profiles');
    }

    public function executiveSummary()
    {
        return view('risk.executive-summary');
    }

    public function decisionSla()
    {
        return view('risk.decision-sla');
    }
}