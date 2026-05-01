<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Contestant;
use App\Models\User;
use App\Models\Score;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index()
    {
        $deletedEvents = Event::onlyTrashed()->get();
        $deletedContestants = Contestant::onlyTrashed()->with('event')->get();
        $deletedJudges = User::onlyTrashed()->where('role', 'judge')->get();
        $deletedScores = Score::onlyTrashed()->with(['contestant', 'criteria', 'judge'])->get();

        return view('admin.trash.index', compact(
            'deletedEvents', 
            'deletedContestants', 
            'deletedJudges', 
            'deletedScores'
        ));
    }

    public function restoreEvent($id)
    {
        $event = Event::onlyTrashed()->findOrFail($id);
        $event->restore();
        AuditLog::log('event_restored', "Restored event: {$event->name}");
        return back()->with('success', 'Event restored successfully.');
    }

    public function forceDeleteEvent($id)
    {
        $event = Event::onlyTrashed()->findOrFail($id);
        $name = $event->name;
        $event->forceDelete();
        AuditLog::log('event_force_deleted', "Permanently deleted event: {$name}");
        return back()->with('success', 'Event permanently deleted.');
    }

    public function restoreContestant($id)
    {
        $contestant = Contestant::onlyTrashed()->findOrFail($id);
        $contestant->restore();
        AuditLog::log('contestant_restored', "Restored contestant: {$contestant->name}");
        return back()->with('success', 'Contestant restored successfully.');
    }

    public function forceDeleteContestant($id)
    {
        $contestant = Contestant::onlyTrashed()->findOrFail($id);
        $name = $contestant->name;
        $contestant->forceDelete();
        AuditLog::log('contestant_force_deleted', "Permanently deleted contestant: {$name}");
        return back()->with('success', 'Contestant permanently deleted.');
    }

    public function restoreJudge($id)
    {
        $judge = User::onlyTrashed()->findOrFail($id);
        $judge->restore();
        AuditLog::log('judge_restored', "Restored judge: {$judge->name}");
        return back()->with('success', 'Judge restored successfully.');
    }

    public function forceDeleteJudge($id)
    {
        $judge = User::onlyTrashed()->findOrFail($id);
        $name = $judge->name;
        $judge->forceDelete();
        AuditLog::log('judge_force_deleted', "Permanently deleted judge: {$name}");
        return back()->with('success', 'Judge permanently deleted.');
    }

    public function restoreScore($id)
    {
        $score = Score::onlyTrashed()->findOrFail($id);
        $score->restore();
        AuditLog::log('score_restored', "Restored score ID: {$score->id}");
        return back()->with('success', 'Score restored successfully.');
    }

    public function forceDeleteScore($id)
    {
        $score = Score::onlyTrashed()->findOrFail($id);
        $scoreId = $score->id;
        $score->forceDelete();
        AuditLog::log('score_force_deleted', "Permanently deleted score ID: {$scoreId}");
        return back()->with('success', 'Score permanently deleted.');
    }
}
