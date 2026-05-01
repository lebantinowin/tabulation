# Tabulation System Fixes Task List

- [ ] **1. Security Fixes**
  - [ ] Update `ScoreController` methods to enforce event isolation
  - [ ] Replace `user_id` with `judge_id` in `ScoreController` and `Score` model
  - [ ] Add public results access gate in `web.php` and `TabulationController`
  - [ ] Replace `rand()` with `random_int()` in `JudgeController`
  - [ ] Add `AuditLog` to `TabulationController::override()`

- [ ] **2. Logic & Correctness Fixes**
  - [ ] Fix `Event::where('status', 'active')` in `CriteriaController`
  - [ ] Fix rank assignment bug in `TabulationController::results()`
  - [ ] Apply weighted scores calculation consistently in `TabulationController`
  - [ ] Integrate Tabulation overrides into the results calculation

- [ ] **3. Data Safety & Cleanup (Soft Deletes & DB)**
  - [ ] Create migration for Soft Deletes on `events`, `contestants`, `users`, `scores`
  - [ ] Add `SoftDeletes` trait to models
  - [ ] Nullify judge event assignments on delete if needed

- [ ] **4. MVC & UX Improvements**
  - [ ] Move queries from `judge/dashboard.blade.php` to `JudgeController::dashboard()`
  - [ ] Update `web.php` for `judge.dashboard` route

- [ ] **5. DRY & Image Path Resolution**
  - [ ] Add `image_url` accessor to `Contestant` model
  - [ ] Refactor `judge/dashboard.blade.php` to use `image_url`
  - [ ] Refactor `results/show.blade.php` to use `image_url`
  - [ ] Check and update other views for image path logic

- [ ] **6. Performance & Housekeeping**
  - [ ] Fix N+1 queries in `TabulationController`
  - [ ] Remove `subCriteria` relation from `Criteria` model
  - [ ] Remove `.env` from git tracking
  - [ ] Write `README.md`
