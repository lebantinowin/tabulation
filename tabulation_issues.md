# Tabulation System — Issues & Problems Report

---

## 🔴 Critical / Security Issues

### 1. Judges Can Score Any Contestant (No Event Isolation)
**File:** `ScoreController.php` → `store()`, `update()`, `edit()`

A judge assigned to **Event A** can submit scores for contestants in **Event B** — there is zero validation that the contestant being scored belongs to the judge's assigned event.

```php
// store() — only checks user_id, not event
$existingScore = Score::where('user_id', Auth::id())
    ->where('contestant_id', $request->contestant_id)
    ->where('criteria_id', $request->criteria_id)->first();
// ❌ No check: $contestant->event_id === $judge->event_id
```

**Risk:** Judges can intentionally or accidentally manipulate scores for events they were never assigned to.

---

### 2. Score Model Has Two Conflicting Judge ID Fields
**File:** `Score.php`, migrations

The `Score` model's `$fillable` has `judge_id`, but `ScoreController::store()` writes `user_id` instead:

```php
// ScoreController::store()
$data['user_id'] = Auth::id();   // ← stored as user_id

// Score::unlock() filters by
Score::where('user_id', Auth::id())   // ← uses user_id

// But Score model relationship is:
public function judge() {
    return $this->belongsTo(User::class, 'judge_id');  // ← uses judge_id
}
```

`judge_id` is in `$fillable` but is **never set**, and `user_id` is set but is **not in `$fillable`** — meaning it works only because of mass assignment exceptions or the column defaulting, making the `judge()` relationship always return `null`.

---

### 3. Public Results Page Has No Access Control
**File:** `routes/web.php` lines 84-86

```php
// Accessible to EVERYONE — no auth, no event status check
Route::get('/results', [TabulationController::class, 'publicIndex']);
Route::get('/results/{event}', [TabulationController::class, 'publicResults']);
```

Anyone can view full scoring breakdowns, judge scores per contestant, and rankings even while the event is **ongoing**. This can bias judges who open the URL during judging.

---

### 4. Score Override Has No Audit Trail
**File:** `TabulationController.php` → `override()` (lines 149-163)

An admin can permanently override any contestant's `total_score` with zero traceability:

```php
public function override(Request $request)
{
    $tabulation = Tabulation::updateOrCreate(
        ['contestant_id' => $request->contestant_id],
        ['total_score' => $request->total_score]
    );
    // ❌ No AuditLog::log() call here
    return back()->with('success', 'Score overridden successfully.');
}
```

---

### 5. Score Locking Can Be Bypassed via Direct `update()` Route
**File:** `ScoreController.php` → `update()` (line 176)

The `store()` method checks for locks, but the `update()` method on an *existing score record* also does — however, a judge could bypass this by deleting (`destroy()`) the score (which also checks for lock) and re-creating it. The lock check in `destroy()` is good but the flow isn't atomic.

---

## 🟠 Logic / Correctness Bugs

### 6. Criteria Filter Uses Wrong Status Value
**File:** `CriteriaController.php` → `create()` and `edit()` (lines 21, 57)

```php
$events = Event::where('status', 'active')->get();
```

But valid event statuses are defined as `upcoming`, `ongoing`, `completed` (from `EventController` validation). There is **no `active` status** — this query will always return **zero events**, making it impossible to assign criteria to any event via the dropdown.

---

### 7. Rank Is Not Persisted Back Into `$results` in `results()` 
**File:** `TabulationController.php` → `results()` (lines 61-63)

```php
foreach ($results as $index => $result) {
    $result['rank'] = $index + 1;  // ❌ modifies a copy — $results unchanged
}
```

Compare with `print()` which correctly uses `$results[$index]['rank'] = ...`. The `results` view will never receive a `rank` key for each item when using the admin tabulation view.

---

### 8. Criteria Weights Are Stored but Never Applied to Scoring
**File:** `TabulationController.php`, `Criteria.php`

Each criteria has a `weight` field (e.g., 40%, 30%, 30%), but all score calculations use a **plain sum/average** with no weighting:

```php
$totalScore = $scores->sum('score');     // ← ignores weight
$averageScore = $scores->avg('score');   // ← ignores weight
```

If an event is configured with criteria weights, the tabulation result will be **mathematically incorrect**.

---

### 9. Deleting a Judge Doesn't Handle Their Existing Scores
**File:** `JudgeController.php` → `destroy()` (lines 88-100)

```php
$judge->delete();
```

No cascade or cleanup — all `Score` records for that judge become **orphaned** (their `user_id` refers to a deleted user). These scores still appear in tabulation results but have no associated judge, silently corrupting event results.

---

### 10. Deleting a Criteria Doesn't Cascade to Scores
**File:** `CriteriaController.php` → `destroy()` (lines 78-91)

Deleting a criteria leaves `Score` rows with a dangling `criteria_id`. These orphaned scores are still summed in tabulation, skewing results with phantom scores.

---

## 🟡 Design / UX Problems

### 11. Judge Dashboard Runs Business Logic in the Blade View
**File:** `resources/views/judge/dashboard.blade.php` (lines 22-26)

```php
@php
$judge = Auth::user();
$event = \App\Models\Event::find($judge->event_id);
$contestants = $event ? \App\Models\Contestant::where('event_id', $event->id)->get() : collect();
@endphp
```

Database queries embedded directly in a Blade template violate MVC separation and make this untestable. This data should be passed from the controller.

---

### 12. Image Path Resolution Logic Is Duplicated and Fragile
**File:** `judge/dashboard.blade.php` (lines 88-117 and 157-184)

The same 20-line image path resolution logic is copy-pasted **twice** in the same file, with slight variable name differences (`$imagePath`/`$fullPath` vs `$modalImagePath`/`$modalFullPath`). This should be a Blade component or a model accessor.

---

### 13. Admin Can Assign a Judge to Multiple Events via Inconsistent UI
**File:** `EventController.php` → `storeAssignedJudges()` (lines 156-179) vs `JudgeController.php` → `store()`/`update()`

A judge has a single `event_id` column on the `users` table. `storeAssignedJudges` reassigns judges by resetting `event_id` on the user, but `JudgeController::store()` also accepts `event_id` on creation. These two paths can conflict, and there's no UI-level warning that reassigning a judge removes them from a prior event.

---

### 14. Login Code Generator Uses `rand()` (Not Cryptographically Secure)
**File:** `JudgeController.php` (line 125)

```php
$code .= $characters[rand(0, strlen($characters) - 1)];
```

`rand()` is not cryptographically secure. Should use `random_int()` for login code generation to prevent predictable codes.

---

### 15. No Confirmation / Guard Before Deleting Events, Contestants, or Judges
**File:** `routes/web.php`, various controllers

The `destroy` routes for events, contestants, and judges have no soft-delete or confirmation-gate beyond a standard HTML form submit. Accidental deletions are permanent and unrecoverable since there's no soft-delete (`SoftDeletes` trait) anywhere in the models.

---

## 🔵 Code Quality / Maintainability

### 16. Massive N+1 Query Problem in Tabulation
**File:** `TabulationController.php` → `results()`, `publicResults()`, `print()`, `printCategory()`

For each contestant, for each criteria, a separate `Score::where(...)->get()` is fired — resulting in potentially hundreds of queries for large events:

```php
foreach ($contestants as $contestant) {
    foreach ($criterias as $criteria) {
        $criteriaScore = Score::where('contestant_id', $contestant->id)
            ->where('criteria_id', $criteria->id)->get(); // ← N×M queries
    }
}
```

Should be replaced with eager loading + in-memory grouping.

---

### 17. `SubCriteria` Model and Table Exist but Are Never Used
**File:** `app/Models/SubCriteria.php`, `Criteria.php` has `subCriteria()` relationship

The `SubCriteria` model is defined and Criteria has a `subCriteria()` relationship, but no controller, route, view, or migration for the sub-criteria table exists in the migrations list. This is dead code/schema bloat.

---

### 18. `Tabulation` Model Is Almost Entirely Unused
**File:** `app/Models/Tabulation.php`, `TabulationController.php`

The `Tabulation` model stores overridden scores and is used only by `override()`, `lock()`, and `message()`. All actual results computation ignores the `Tabulation` table entirely — meaning **admin overrides have no effect on displayed results**.

---

### 19. README Is Empty
**File:** `README.md`

```
# Tabulation
```

The README contains only the project title. No setup instructions, environment variables, or deployment guide.

---

### 20. `.env` Is Committed to the Repository
**File:** `.gitignore`

The `.gitignore` should exclude `.env`, but the `.env` file (1162 bytes) is present in the root. If this repo is pushed public or to a shared remote, **database credentials and app keys will be exposed**.
